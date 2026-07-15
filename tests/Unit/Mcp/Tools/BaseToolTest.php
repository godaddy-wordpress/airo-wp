<?php
/**
 * BaseTool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for BaseTool abstract class.
 */
final class BaseToolTest extends TestCase {

	/**
	 * BaseTool test double.
	 *
	 * @var BaseTool
	 */
	private BaseTool $tool;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();

		Functions\when( '__' )->returnArg();

		$this->tool = new class() extends BaseTool {
			public function execute( array $input ): array {
				return array( 'executed' => true );
			}

			public function register(): void {}

			public function check_permissions(): bool {
				return current_user_can( 'manage_options' );
			}

			public function exposed_build_output_schema( string $description = '', array $properties = array(), array $required = array() ): array {
				return $this->build_output_schema( $description, $properties, $required );
			}

			public function exposed_load_admin_file( string $filename ): void {
				$this->load_admin_file( $filename );
			}
		};
	}

	public function test_execute_delegates_to_implementation(): void {
		$result = $this->tool->execute( array() );

		$this->assertSame( array( 'executed' => true ), $result );
	}

	public function test_check_permissions_delegates_to_current_user_can(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'manage_options' )->andReturn( true );

		$this->assertTrue( $this->tool->check_permissions() );
	}

	public function test_check_permissions_returns_false_when_user_lacks_capability(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'manage_options' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	public function test_build_output_schema_returns_base_schema(): void {
		$schema = $this->tool->exposed_build_output_schema();

		$this->assertSame( 'object', $schema['type'] );
		$this->assertArrayHasKey( 'success', $schema['properties'] );
		$this->assertArrayHasKey( 'message', $schema['properties'] );
		$this->assertContains( 'success', $schema['required'] );
	}

	public function test_build_output_schema_merges_properties(): void {
		$schema = $this->tool->exposed_build_output_schema(
			'Test description',
			array( 'extra' => array( 'type' => 'string' ) ),
			array( 'extra' )
		);

		$this->assertSame( 'Test description', $schema['description'] );
		$this->assertArrayHasKey( 'extra', $schema['properties'] );
		$this->assertContains( 'extra', $schema['required'] );
	}

	public function test_load_admin_file_skips_in_test_env(): void {
		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}
		$this->tool->exposed_load_admin_file( 'nonexistent.php' );
		$this->assertTrue( true );
	}
}
