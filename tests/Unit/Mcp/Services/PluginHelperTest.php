<?php
/**
 * PluginHelper service tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Services;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services\PluginHelper;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for PluginHelper service.
 */
final class PluginHelperTest extends TestCase {

	/**
	 * Service under test.
	 *
	 * @var PluginHelper
	 */
	private PluginHelper $service;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();

		$this->service = new PluginHelper();
	}

	/**
	 * find_file returns conventional path when it exists.
	 */
	public function test_find_file_returns_conventional_path_when_exists(): void {
		Functions\expect( 'get_plugins' )->once()->andReturn(
			array( 'akismet/akismet.php' => array( 'Name' => 'Akismet' ) )
		);
		$result = $this->service->find_file( 'akismet' );
		$this->assertSame( 'akismet/akismet.php', $result );
	}

	/**
	 * find_file returns path by directory scan when conventional path not found.
	 */
	public function test_find_file_returns_path_by_directory_scan_when_conventional_not_found(): void {
		Functions\expect( 'get_plugins' )->once()->andReturn(
			array( 'myplugin/main.php' => array( 'Name' => 'My Plugin' ) )
		);
		$result = $this->service->find_file( 'myplugin' );
		$this->assertSame( 'myplugin/main.php', $result );
	}

	/**
	 * find_file returns false when plugin not installed.
	 */
	public function test_find_file_returns_false_when_plugin_not_installed(): void {
		Functions\expect( 'get_plugins' )->once()->andReturn( array() );
		$result = $this->service->find_file( 'nonexistent' );
		$this->assertFalse( $result );
	}

	/**
	 * refresh_updates does nothing when force is false.
	 */
	public function test_refresh_updates_does_nothing_when_force_is_false(): void {
		Functions\expect( 'wp_update_plugins' )->never();
		$this->service->refresh_updates( false );
		$this->addToAssertionCount( 1 );
	}

	/**
	 * refresh_updates calls wp_update_plugins when force is true.
	 */
	public function test_refresh_updates_calls_wp_update_plugins_when_force_is_true(): void {
		Functions\expect( 'wp_update_plugins' )->once();
		$this->service->refresh_updates( true );
		$this->addToAssertionCount( 1 );
	}

	/**
	 * get_update_info returns no update when transient is missing.
	 */
	public function test_get_update_info_returns_no_update_when_transient_missing(): void {
		Functions\expect( 'get_site_transient' )->with( 'update_plugins' )->andReturn( false );
		$result = $this->service->get_update_info( 'akismet/akismet.php' );
		$this->assertFalse( $result['update_available'] );
		$this->assertNull( $result['new_version'] );
	}

	/**
	 * get_update_info returns no update when plugin not in response.
	 */
	public function test_get_update_info_returns_no_update_when_plugin_not_in_response(): void {
		$updates           = new \stdClass();
		$updates->response = array();
		Functions\expect( 'get_site_transient' )->andReturn( $updates );
		$result = $this->service->get_update_info( 'akismet/akismet.php' );
		$this->assertFalse( $result['update_available'] );
		$this->assertNull( $result['new_version'] );
	}

	/**
	 * get_update_info returns update when new version is available.
	 */
	public function test_get_update_info_returns_update_when_new_version_available(): void {
		$response_entry              = new \stdClass();
		$response_entry->new_version = '5.3.4';
		$updates                     = new \stdClass();
		$updates->response           = array( 'akismet/akismet.php' => $response_entry );
		Functions\expect( 'get_site_transient' )->andReturn( $updates );
		$result = $this->service->get_update_info( 'akismet/akismet.php' );
		$this->assertTrue( $result['update_available'] );
		$this->assertSame( '5.3.4', $result['new_version'] );
	}

	/**
	 * get_update_info returns no update when new version is empty.
	 */
	public function test_get_update_info_returns_no_update_when_new_version_is_empty(): void {
		$response_entry              = new \stdClass();
		$response_entry->new_version = '';
		$updates                     = new \stdClass();
		$updates->response           = array( 'akismet/akismet.php' => $response_entry );
		Functions\expect( 'get_site_transient' )->andReturn( $updates );
		$result = $this->service->get_update_info( 'akismet/akismet.php' );
		$this->assertFalse( $result['update_available'] );
		$this->assertNull( $result['new_version'] );
	}
}
