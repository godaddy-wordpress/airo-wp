<?php
/**
 * ToolRegistry tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Infrastructure;

use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\RuntimeContainer;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\ToolRegistry;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for ToolRegistry — the single source of truth for the MCP tool list.
 */
final class ToolRegistryTest extends TestCase {

	/**
	 * Every entry in TOOLS is a real class.
	 */
	public function test_every_declared_tool_class_exists(): void {
		foreach ( ToolRegistry::TOOLS as $tool_class ) {
			$this->assertTrue( class_exists( $tool_class ), "missing class {$tool_class}" );
		}
	}

	/**
	 * Every entry is a BaseTool, so register() is guaranteed to exist.
	 */
	public function test_every_declared_tool_extends_base_tool(): void {
		foreach ( ToolRegistry::TOOLS as $tool_class ) {
			$this->assertTrue(
				is_subclass_of( $tool_class, BaseTool::class ),
				"{$tool_class} does not extend BaseTool"
			);
		}
	}

	/**
	 * Every tool declares a TOOL_ID under the airo-wp namespace.
	 *
	 * A foreign prefix here would mean a tool was copied in without being retargeted.
	 */
	public function test_every_tool_id_is_namespaced_to_airo_wp(): void {
		foreach ( ToolRegistry::TOOLS as $tool_class ) {
			$this->assertTrue( defined( "{$tool_class}::TOOL_ID" ), "{$tool_class} has no TOOL_ID" );
			$this->assertStringStartsWith( 'airo-wp/', $tool_class::TOOL_ID, "{$tool_class} has a foreign TOOL_ID" );
		}
	}

	/**
	 * No tool is listed twice, and no two tools share a TOOL_ID.
	 *
	 * A duplicate would register the same ability twice and shadow one of them.
	 */
	public function test_tool_classes_and_ids_are_unique(): void {
		$classes = ToolRegistry::TOOLS;
		$this->assertSame( array_values( array_unique( $classes ) ), array_values( $classes ), 'duplicate tool class' );

		$ids = array();

		foreach ( $classes as $tool_class ) {
			$ids[] = $tool_class::TOOL_ID;
		}

		$this->assertSame( array_values( array_unique( $ids ) ), $ids, 'duplicate TOOL_ID' );
	}

	/**
	 * tool_ids() returns one ID per declared tool, in declaration order, without
	 * instantiating anything.
	 */
	public function test_tool_ids_match_declaration_order(): void {
		// Seeded with nothing: tool_ids() must not touch the container at all.
		$registry = new ToolRegistry( new Container( new RuntimeContainer( array() ) ) );

		$expected = array();

		foreach ( ToolRegistry::TOOLS as $tool_class ) {
			$expected[] = $tool_class::TOOL_ID;
		}

		$this->assertSame( $expected, $registry->tool_ids() );
	}

	/**
	 * all() resolves one instance per declared tool, through the container.
	 */
	public function test_all_resolves_every_tool_through_the_container(): void {
		$stubs = $this->tool_stubs();

		$registry = new ToolRegistry( new Container( new RuntimeContainer( $stubs ) ) );

		$this->assertSame( array_values( $stubs ), $registry->all() );
	}

	/**
	 * A stub instance per declared tool, keyed by class name.
	 *
	 * @return array<string, object>
	 */
	private function tool_stubs(): array {
		$stubs = array();

		foreach ( ToolRegistry::TOOLS as $tool_class ) {
			$stubs[ $tool_class ] = \Mockery::mock( $tool_class );
		}

		return $stubs;
	}

	/**
	 * all() memoises, so repeated calls do not re-resolve.
	 *
	 * register_tools() may fire on more than one Abilities API hook variant.
	 */
	public function test_all_is_memoised(): void {
		$registry = new ToolRegistry( new Container( new RuntimeContainer( $this->tool_stubs() ) ) );

		$this->assertSame( $registry->all(), $registry->all() );
	}
}
