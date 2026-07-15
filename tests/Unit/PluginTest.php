<?php
/**
 * Plugin orchestrator tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Blocks\Package as BlocksPackage;
use GoDaddy\WordPress\Plugins\AiroWp\Plugin;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for Plugin static orchestrator.
 */
final class PluginTest extends TestCase {

	/**
	 * init() registers the plugins_loaded hook.
	 */
	public function test_init_registers_plugins_loaded_hook(): void {
		Actions\expectAdded( 'plugins_loaded' )
			->once()
			->with( array( Plugin::class, 'boot' ), 10 );

		Plugin::init();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * boot() calls init on each registered package.
	 */
	public function test_boot_invokes_package_init(): void {
		// init() must be called first to create the container.
		Plugin::init();

		Functions\when( 'is_plugin_active' )->justReturn( false );

		// McpPackage::init() adds these hooks via AbilitiesApiProxy::setup().
		Actions\expectAdded( 'wp_abilities_api_init' )->atLeast()->once();
		Actions\expectAdded( 'abilities_api_init' )->atLeast()->once();
		Actions\expectAdded( 'mcp_adapter_init' )->atLeast()->once();

		Plugin::boot();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Blocks package init is callable.
	 */
	public function test_blocks_package_init_is_callable(): void {
		$this->assertTrue( is_callable( array( BlocksPackage::class, 'init' ) ) );
	}
}
