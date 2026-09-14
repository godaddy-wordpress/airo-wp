<?php
/**
 * ActivatePlugin tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Plugins;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\ActivatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for ActivatePlugin MCP tool.
 */
final class ActivatePluginTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var ActivatePlugin
	 */
	private ActivatePlugin $tool;

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

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new ActivatePlugin();
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertStringStartsWith( 'airo-wp/', ActivatePlugin::TOOL_ID );
		$this->assertSame( 'airo-wp/activate-plugin', ActivatePlugin::TOOL_ID );
	}

	/**
	 * Empty plugin_slug returns error.
	 */
	public function test_empty_slug_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'required', $result['message'] );
	}

	/**
	 * Empty string slug returns error.
	 */
	public function test_empty_string_slug_returns_error(): void {
		$result = $this->tool->execute( array( 'plugin_slug' => '' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'required', $result['message'] );
	}

	/**
	 * Permission denied returns error.
	 */
	public function test_permission_denied_returns_error(): void {
		Functions\expect( 'current_user_can' )->with( 'install_plugins' )->andReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission', $result['message'] );
	}

	/**
	 * Already active plugin returns success.
	 */
	public function test_already_active_plugin_returns_success(): void {
		Functions\expect( 'current_user_can' )->with( 'install_plugins' )->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn(
			array( 'hello-dolly/hello-dolly.php' => array( 'Name' => 'Hello Dolly' ) )
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( true );
		Functions\expect( 'get_plugin_data' )->andReturn( array( 'Version' => '1.7.2' ) );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'hello-dolly', $result['plugin'] );
		$this->assertSame( '1.7.2', $result['version'] );
		$this->assertStringContainsString( 'already active', $result['message'] );
	}

	/**
	 * Successful activation returns success with version.
	 */
	public function test_successful_activation_returns_success(): void {
		Functions\expect( 'current_user_can' )->with( 'install_plugins' )->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn(
			array( 'hello-dolly/hello-dolly.php' => array( 'Name' => 'Hello Dolly' ) )
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( false );
		Functions\expect( 'activate_plugin' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( null );
		Functions\expect( 'get_plugin_data' )->andReturn( array( 'Version' => '1.7.2' ) );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'hello-dolly', $result['plugin'] );
		$this->assertSame( '1.7.2', $result['version'] );
		$this->assertStringContainsString( 'successfully', $result['message'] );
	}

	/**
	 * Activation WP_Error returns failure.
	 */
	public function test_activation_wp_error_returns_failure(): void {
		Functions\expect( 'current_user_can' )->with( 'install_plugins' )->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn(
			array( 'hello-dolly/hello-dolly.php' => array( 'Name' => 'Hello Dolly' ) )
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( false );

		$wp_error = \Mockery::mock( 'WP_Error' );
		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Activation failed' );
		Functions\expect( 'activate_plugin' )->andReturn( $wp_error );
		Functions\expect( 'is_wp_error' )->andReturnUsing(
			function ( $thing ) use ( $wp_error ) {
				return $thing === $wp_error;
			}
		);

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertFalse( $result['success'] );
		$this->assertSame( 'Activation failed', $result['message'] );
	}

	/**
	 * Plugin not found triggers install attempt.
	 */
	public function test_plugin_not_found_attempts_install(): void {
		Functions\expect( 'current_user_can' )->with( 'install_plugins' )->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn( array() );

		$wp_error = \Mockery::mock( 'WP_Error' );
		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Plugin not found on WordPress.org' );
		Functions\expect( 'plugins_api' )->andReturn( $wp_error );
		Functions\expect( 'is_wp_error' )->andReturnUsing(
			function ( $thing ) use ( $wp_error ) {
				return $thing === $wp_error;
			}
		);

		$result = $this->tool->execute( array( 'plugin_slug' => 'nonexistent-plugin' ) );

		$this->assertFalse( $result['success'] );
		$this->assertSame( 'nonexistent-plugin', $result['plugin'] );
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
