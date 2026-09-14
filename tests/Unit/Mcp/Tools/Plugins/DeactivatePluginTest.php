<?php
/**
 * DeactivatePlugin tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Plugins;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\DeactivatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for DeactivatePlugin MCP tool.
 */
final class DeactivatePluginTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var DeactivatePlugin
	 */
	private DeactivatePlugin $tool;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new DeactivatePlugin();
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertStringStartsWith( 'airo-wp/', DeactivatePlugin::TOOL_ID );
		$this->assertSame( 'airo-wp/deactivate-plugin', DeactivatePlugin::TOOL_ID );
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
	 * check_permissions returns false without activate_plugins capability.
	 */
	public function test_check_permissions_returns_false_without_activate_plugins(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'activate_plugins' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	/**
	 * Plugin not found returns error.
	 */
	public function test_plugin_not_found_returns_error(): void {
		Functions\expect( 'current_user_can' )->with( 'activate_plugins' )->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn( array() );

		$result = $this->tool->execute( array( 'plugin_slug' => 'nonexistent' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found', $result['message'] );
		$this->assertSame( 'nonexistent', $result['plugin'] );
	}

	/**
	 * Successful deactivation returns success.
	 */
	public function test_successful_deactivation_returns_success(): void {
		Functions\expect( 'current_user_can' )->with( 'activate_plugins' )->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn(
			array( 'hello-dolly/hello-dolly.php' => array( 'Name' => 'Hello Dolly' ) )
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( true );
		Functions\expect( 'deactivate_plugins' )->with( 'hello-dolly/hello-dolly.php' )->once();

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'hello-dolly', $result['plugin'] );
		$this->assertStringContainsString( 'deactivated successfully', $result['message'] );
	}

	/**
	 * Deactivation with uninstall returns success.
	 */
	public function test_deactivation_with_uninstall_returns_success(): void {
		Functions\expect( 'current_user_can' )->withAnyArgs()->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn(
			array( 'hello-dolly/hello-dolly.php' => array( 'Name' => 'Hello Dolly' ) )
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( true );
		Functions\expect( 'deactivate_plugins' )->with( 'hello-dolly/hello-dolly.php' )->once();
		Functions\expect( 'delete_plugins' )->with( array( 'hello-dolly/hello-dolly.php' ) )->andReturn( true );
		Functions\expect( 'is_wp_error' )->andReturn( false );

		$result = $this->tool->execute(
			array(
				'plugin_slug' => 'hello-dolly',
				'uninstall'   => true,
			)
		);

		$this->assertTrue( $result['success'] );
		$this->assertStringContainsString( 'uninstalled', $result['message'] );
	}

	/**
	 * Inactive plugin deactivation still succeeds without calling deactivate_plugins.
	 */
	public function test_inactive_plugin_deactivation_succeeds(): void {
		Functions\expect( 'current_user_can' )->with( 'activate_plugins' )->andReturn( true );
		Functions\expect( 'get_plugins' )->andReturn(
			array( 'hello-dolly/hello-dolly.php' => array( 'Name' => 'Hello Dolly' ) )
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertStringContainsString( 'deactivated successfully', $result['message'] );
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
