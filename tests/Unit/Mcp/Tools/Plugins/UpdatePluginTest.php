<?php
/**
 * UpdatePlugin tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Plugins;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services\PluginHelper;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\UpdatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for UpdatePlugin MCP tool.
 */
final class UpdatePluginTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var UpdatePlugin
	 */
	private UpdatePlugin $tool;

	/**
	 * Plugin helper mock.
	 *
	 * @var PluginHelper
	 */
	private PluginHelper $plugin_helper;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
			define( 'WP_PLUGIN_DIR', '/tmp/plugins' );
		}

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		// Reset upgrader stub state before each test.
		\Plugin_Upgrader::$next_install_result = true;
		\Plugin_Upgrader::$last_install_args   = null;

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->plugin_helper = $this->createMock( PluginHelper::class );
		$this->tool          = new UpdatePlugin( $this->plugin_helper );
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/update-plugin', UpdatePlugin::TOOL_ID );
	}

	/**
	 * check_permissions mirrors current_user_can('activate_plugins').
	 */
	public function test_check_permissions_returns_current_user_can(): void {
		Functions\when( 'current_user_can' )->justReturn( true );
		$this->assertTrue( $this->tool->check_permissions() );

		Functions\when( 'current_user_can' )->justReturn( false );
		$this->assertFalse( $this->tool->check_permissions() );
	}

	/**
	 * Returns failure when plugin_slug is missing.
	 */
	public function test_returns_failure_when_plugin_slug_missing(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'required', strtolower( $result['message'] ) );
		$this->assertSame( '', $result['plugin'] );
	}

	/**
	 * Returns failure when plugin is not installed.
	 */
	public function test_returns_failure_when_plugin_is_not_installed(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( false );
		Functions\when( 'is_wp_error' )->justReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'nonexistent' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not installed', $result['message'] );
	}

	/**
	 * Returns already up to date when no update is available.
	 */
	public function test_returns_already_up_to_date_when_no_update_available(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn(
			array( 'update_available' => false, 'new_version' => null )
		);
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );
		Functions\when( 'is_wp_error' )->justReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertStringContainsString( 'already up to date', $result['message'] );
		$this->assertSame( '1.7.2', $result['version'] );
		$this->assertSame( '1.7.2', $result['previous_version'] );
	}

	/**
	 * Resolves latest from transient and installs when version is omitted.
	 */
	public function test_resolves_latest_from_transient_and_installs_when_version_omitted(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn(
			array( 'update_available' => true, 'new_version' => '2.0' )
		);

		$call = 0;
		Functions\when( 'get_plugin_data' )->alias(
			function () use ( &$call ) {
				$call++;
				return array( 'Version' => 1 === $call ? '1.0.0' : '2.0' );
			}
		);
		Functions\when( 'is_plugin_active' )->justReturn( false );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '2.0' => 'https://example.com/hello-dolly-2.0.zip' );
				return $api;
			}
		);
		Functions\when( 'is_wp_error' )->justReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( '2.0', $result['version'] );
	}

	/**
	 * Returns already at version when target matches current.
	 */
	public function test_returns_already_at_version_when_target_matches_current(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );
		Functions\when( 'is_wp_error' )->justReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'version' => '1.7.2' ) );

		$this->assertTrue( $result['success'] );
		$this->assertStringContainsString( 'already at version', $result['message'] );
		$this->assertStringContainsString( '1.7.2', $result['message'] );
	}

	/**
	 * Returns failure when plugins_api returns WP_Error.
	 */
	public function test_returns_failure_when_plugins_api_returns_wp_error(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );

		$wp_error = new \WP_Error( 'not_found', 'Plugin not found' );
		Functions\when( 'plugins_api' )->justReturn( $wp_error );
		Functions\when( 'is_wp_error' )->alias(
			function ( $thing ) {
				return $thing instanceof \WP_Error;
			}
		);

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'version' => '9.9.9' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found on WordPress.org', $result['message'] );
	}

	/**
	 * Returns failure when target version is not in API versions.
	 */
	public function test_returns_failure_when_target_version_not_in_api_versions(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '1.7.2' => 'https://example.com/hello-dolly-1.7.2.zip' );
				return $api;
			}
		);
		Functions\when( 'is_wp_error' )->alias(
			function ( $thing ) {
				return $thing instanceof \WP_Error;
			}
		);

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'version' => '0.0.1' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'is not available on WordPress.org', $result['message'] );
	}

	/**
	 * Successful install upgrade.
	 */
	public function test_successful_install_upgrade(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn(
			array( 'update_available' => true, 'new_version' => '1.8.0' )
		);

		$call = 0;
		Functions\when( 'get_plugin_data' )->alias(
			function () use ( &$call ) {
				$call++;
				return array( 'Version' => 1 === $call ? '1.7.2' : '1.8.0' );
			}
		);
		Functions\when( 'is_plugin_active' )->justReturn( false );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '1.8.0' => 'https://example.com/hello-dolly-1.8.0.zip' );
				return $api;
			}
		);
		Functions\when( 'is_wp_error' )->justReturn( false );
		// Plugin_Upgrader::$next_install_result = true (set in setUp).

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( '1.8.0', $result['version'] );
		$this->assertSame( '1.7.2', $result['previous_version'] );
	}

	/**
	 * Reactivates when install returns WP_Error and plugin was active.
	 */
	public function test_reactivates_when_install_returns_wp_error_and_plugin_was_active(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '1.8.0' => 'https://example.com/hello-dolly-1.8.0.zip' );
				return $api;
			}
		);
		Functions\when( 'is_plugin_active' )->justReturn( true );
		Functions\when( 'deactivate_plugins' )->justReturn( null );
		Functions\when( 'activate_plugin' )->justReturn( null );

		\Plugin_Upgrader::$next_install_result = new \WP_Error( 'install_failed', 'Install failed' );
		Functions\when( 'is_wp_error' )->alias(
			function ( $thing ) {
				return $thing instanceof \WP_Error;
			}
		);

		$activate_called = false;
		Functions\when( 'activate_plugin' )->alias(
			function () use ( &$activate_called ) {
				$activate_called = true;
				return null;
			}
		);

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'version' => '1.8.0' ) );

		$this->assertFalse( $result['success'] );
		$this->assertTrue( $activate_called );
	}

	/**
	 * Install failure surfaces reactivation error in message.
	 */
	public function test_install_failure_surfaces_reactivation_error_in_message(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '1.8.0' => 'https://example.com/hello-dolly-1.8.0.zip' );
				return $api;
			}
		);
		Functions\when( 'is_plugin_active' )->justReturn( true );
		Functions\when( 'deactivate_plugins' )->justReturn( null );

		\Plugin_Upgrader::$next_install_result = new \WP_Error( 'install_failed', 'Install failed' );
		Functions\when( 'is_wp_error' )->alias(
			function ( $thing ) {
				return $thing instanceof \WP_Error;
			}
		);
		Functions\when( 'activate_plugin' )->justReturn( new \WP_Error( 'activation_failed', 'Cannot activate' ) );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'version' => '1.8.0' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Install failed', $result['message'] );
		$this->assertStringContainsString( 'Cannot activate', $result['message'] );
	}

	/**
	 * Reactivates when install returns falsy and plugin was active.
	 */
	public function test_reactivates_when_install_returns_falsy_and_plugin_was_active(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '1.8.0' => 'https://example.com/hello-dolly-1.8.0.zip' );
				return $api;
			}
		);
		Functions\when( 'is_plugin_active' )->justReturn( true );
		Functions\when( 'deactivate_plugins' )->justReturn( null );

		\Plugin_Upgrader::$next_install_result = null;
		Functions\when( 'is_wp_error' )->alias(
			function ( $thing ) {
				return $thing instanceof \WP_Error;
			}
		);

		$activate_called = false;
		Functions\when( 'activate_plugin' )->alias(
			function () use ( &$activate_called ) {
				$activate_called = true;
				return null;
			}
		);

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'version' => '1.8.0' ) );

		$this->assertFalse( $result['success'] );
		$this->assertTrue( $activate_called );
	}

	/**
	 * Does not reactivate when install fails and plugin was inactive.
	 */
	public function test_does_not_reactivate_when_install_fails_and_plugin_was_inactive(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		Functions\when( 'get_plugin_data' )->justReturn( array( 'Version' => '1.7.2' ) );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '1.8.0' => 'https://example.com/hello-dolly-1.8.0.zip' );
				return $api;
			}
		);
		Functions\when( 'is_plugin_active' )->justReturn( false );

		\Plugin_Upgrader::$next_install_result = null;
		Functions\when( 'is_wp_error' )->alias(
			function ( $thing ) {
				return $thing instanceof \WP_Error;
			}
		);

		Functions\expect( 'activate_plugin' )->never();

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'version' => '1.8.0' ) );

		$this->assertFalse( $result['success'] );
	}

	/**
	 * Succeeds with warning when reactivation returns WP_Error.
	 */
	public function test_succeeds_with_warning_when_reactivation_returns_wp_error(): void {
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn(
			array( 'update_available' => true, 'new_version' => '1.8.0' )
		);

		$call = 0;
		Functions\when( 'get_plugin_data' )->alias(
			function () use ( &$call ) {
				$call++;
				return array( 'Version' => 1 === $call ? '1.7.2' : '1.8.0' );
			}
		);
		Functions\when( 'is_plugin_active' )->justReturn( true );
		Functions\when( 'deactivate_plugins' )->justReturn( null );
		Functions\when( 'plugins_api' )->alias(
			function () {
				$api           = new \stdClass();
				$api->versions = array( '1.8.0' => 'https://example.com/hello-dolly-1.8.0.zip' );
				return $api;
			}
		);
		// Install succeeds.
		\Plugin_Upgrader::$next_install_result = true;
		Functions\when( 'is_wp_error' )->alias(
			function ( $thing ) {
				return $thing instanceof \WP_Error;
			}
		);
		// Reactivation fails.
		Functions\when( 'activate_plugin' )->justReturn( new \WP_Error( 'activation_failed', 'failed to reactivate' ) );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertStringContainsString( 'failed to reactivate', $result['message'] );
	}

	/**
	 * plugin_slug carries minLength so an empty string is rejected at the
	 * schema boundary rather than only inside execute().
	 */
	public function test_input_schema_constrains_plugin_slug_to_non_empty(): void {
		$schema = $this->call_private( $this->tool, 'get_input_schema' );

		$this->assertSame(
			1,
			$schema['properties']['plugin_slug']['minLength'] ?? null,
			'plugin_slug lost its minLength constraint'
		);
		$this->assertContains( 'plugin_slug', $schema['required'] );
	}
}
