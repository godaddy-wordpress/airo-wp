<?php
/**
 * Bootstrap smoke tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests plugin bootstrap file loads.
 */
final class BootstrapTest extends TestCase {

	/**
	 * Plugin file loads without fatal error.
	 */
	public function test_plugin_file_loads_without_fatal(): void {
		Functions\when( 'plugin_dir_path' )->returnArg();
		Functions\when( 'plugin_dir_url' )->justReturn( 'http://example.org/wp-content/plugins/airo-wp/' );

		$this->expectNotToPerformAssertions();
		require dirname( __DIR__, 2 ) . '/airo-wp.php';
	}
}
