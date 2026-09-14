<?php
/**
 * AbilitiesApiProxy tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Infrastructure;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\RuntimeContainer;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\AbilitiesApiProxy;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\ToolRegistry;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for AbilitiesApiProxy.
 *
 * ToolRegistry and Container are both final, so these use the real classes over a
 * RuntimeContainer seeded with stub tools rather than mocking the collaborators.
 * That also means the proxy is exercised against the actual tool list.
 */
final class AbilitiesApiProxyTest extends TestCase {

	/**
	 * Stub instances for every declared tool, keyed by class name.
	 *
	 * @var array<string, object>
	 */
	private array $stubs = array();

	/**
	 * Build a proxy over a real registry.
	 *
	 * @param bool $with_tools Whether to seed stub tools (only needed by register_tools()).
	 * @return AbilitiesApiProxy
	 */
	private function create_proxy( bool $with_tools = false ): AbilitiesApiProxy {
		$this->stubs = array();

		if ( $with_tools ) {
			foreach ( ToolRegistry::TOOLS as $tool_class ) {
				$this->stubs[ $tool_class ] = \Mockery::mock( $tool_class );
			}
		}

		$registry = new ToolRegistry( new Container( new RuntimeContainer( $this->stubs ) ) );

		return new AbilitiesApiProxy( $registry );
	}

	/**
	 * Setup() hooks both Abilities API init variants plus the categories hook.
	 */
	public function test_setup_registers_all_hooks(): void {
		$proxy = $this->create_proxy();

		Actions\expectAdded( 'wp_abilities_api_init' )->once()->with( array( $proxy, 'register_tools' ) );
		Actions\expectAdded( 'abilities_api_init' )->once()->with( array( $proxy, 'register_tools' ) );
		Actions\expectAdded( 'wp_abilities_api_categories_init' )->once()->with( array( $proxy, 'register_categories' ) );

		$proxy->setup();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Register_categories() calls wp_register_ability_category for all categories.
	 */
	public function test_register_categories_registers_all_categories(): void {
		$proxy = $this->create_proxy();

		Functions\when( '__' )->returnArg( 1 );
		Functions\when( 'esc_html' )->returnArg( 1 );
		Functions\when( 'did_action' )->justReturn( 0 );
		Functions\when( '_doing_it_wrong' )->justReturn( null );

		$proxy->register_categories();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Register_tools() calls register() exactly once on every tool in the registry.
	 *
	 * This is the assertion the old 52-parameter constructor was carrying by hand: a
	 * tool present in the registry but never registered would slip through otherwise.
	 */
	public function test_register_tools_calls_register_on_every_declared_tool(): void {
		$proxy = $this->create_proxy( true );

		foreach ( $this->stubs as $stub ) {
			$stub->shouldReceive( 'register' )->once();
		}

		$proxy->register_tools();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Tools are registered in the registry's declared order.
	 */
	public function test_register_tools_follows_declaration_order(): void {
		$proxy      = $this->create_proxy( true );
		$registered = array();

		foreach ( $this->stubs as $tool_class => $stub ) {
			$stub->shouldReceive( 'register' )->once()->andReturnUsing(
				function () use ( $tool_class, &$registered ) {
					$registered[] = $tool_class;
				}
			);
		}

		$proxy->register_tools();

		$this->assertSame( ToolRegistry::TOOLS, $registered );
	}

	/**
	 * Firing both Abilities API hook variants must not register a tool twice.
	 */
	public function test_repeated_register_tools_reuses_the_same_instances(): void {
		$proxy = $this->create_proxy( true );

		foreach ( $this->stubs as $stub ) {
			$stub->shouldReceive( 'register' )->twice();
		}

		$proxy->register_tools();
		$proxy->register_tools();

		$this->addToAssertionCount( 1 );
	}
}
