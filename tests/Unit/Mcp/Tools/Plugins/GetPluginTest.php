<?php
/**
 * GetPlugin tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Plugins;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services\PluginHelper;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\GetPlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for GetPlugin MCP tool.
 */
final class GetPluginTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var GetPlugin
	 */
	private GetPlugin $tool;

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

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->plugin_helper = $this->createMock( PluginHelper::class );
		$this->tool          = new GetPlugin( $this->plugin_helper );
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertStringStartsWith( 'airo-wp/', GetPlugin::TOOL_ID );
		$this->assertSame( 'airo-wp/get-plugin', GetPlugin::TOOL_ID );
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
	 * Plugin not found returns error.
	 */
	public function test_plugin_not_found_returns_error(): void {
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'find_file' )->willReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'nonexistent' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found', $result['message'] );
	}

	/**
	 * View context returns full plugin data.
	 */
	public function test_view_context_returns_full_data(): void {
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn( array( 'update_available' => false, 'new_version' => null ) );

		Functions\expect( 'get_plugin_data' )->andReturn(
			array(
				'Name'        => 'Hello Dolly',
				'Version'     => '1.7.2',
				'Author'      => 'Matt Mullenweg',
				'Description' => 'A plugin that adds lyrics.',
				'PluginURI'   => 'https://wordpress.org/plugins/hello-dolly/',
				'AuthorURI'   => 'https://ma.tt/',
				'TextDomain'  => 'hello-dolly',
			)
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( true );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'hello-dolly', $result['plugin']['slug'] );
		$this->assertSame( 'Hello Dolly', $result['plugin']['name'] );
		$this->assertSame( '1.7.2', $result['plugin']['version'] );
		$this->assertSame( 'Matt Mullenweg', $result['plugin']['author'] );
		$this->assertSame( 'active', $result['plugin']['status'] );
		$this->assertArrayHasKey( 'description', $result['plugin'] );
		$this->assertArrayHasKey( 'file', $result['plugin'] );
		$this->assertFalse( $result['plugin']['update_available'] );
		$this->assertNull( $result['plugin']['new_version'] );
	}

	/**
	 * Embed context returns minimal fields.
	 */
	public function test_embed_context_returns_minimal_fields(): void {
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );

		Functions\expect( 'get_plugin_data' )->andReturn(
			array(
				'Name'    => 'Hello Dolly',
				'Version' => '1.7.2',
			)
		);
		Functions\expect( 'is_plugin_active' )->with( 'hello-dolly/hello-dolly.php' )->andReturn( false );

		$result = $this->tool->execute(
			array(
				'plugin_slug' => 'hello-dolly',
				'context'     => 'embed',
			)
		);

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'hello-dolly', $result['plugin']['slug'] );
		$this->assertSame( 'Hello Dolly', $result['plugin']['name'] );
		$this->assertSame( 'inactive', $result['plugin']['status'] );
		$this->assertArrayNotHasKey( 'author', $result['plugin'] );
		$this->assertArrayNotHasKey( 'description', $result['plugin'] );
		$this->assertArrayNotHasKey( 'file', $result['plugin'] );
	}

	/**
	 * Finds plugin by directory name when conventional path does not match.
	 */
	public function test_finds_plugin_by_directory_name(): void {
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'find_file' )->willReturn( 'woocommerce/woocommerce.php' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn( array( 'update_available' => false, 'new_version' => null ) );

		Functions\expect( 'get_plugin_data' )->andReturn(
			array(
				'Name'    => 'WooCommerce',
				'Version' => '8.0.0',
			)
		);
		Functions\expect( 'is_plugin_active' )->with( 'woocommerce/woocommerce.php' )->andReturn( true );

		$result = $this->tool->execute( array( 'plugin_slug' => 'woocommerce' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'woocommerce', $result['plugin']['slug'] );
	}

	/**
	 * force_check=true passes true to refresh_updates.
	 */
	public function test_force_check_true_calls_refresh_updates(): void {
		$this->plugin_helper->expects( $this->once() )
			->method( 'refresh_updates' )
			->with( true );
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn( array( 'update_available' => false, 'new_version' => null ) );

		Functions\when( 'get_plugin_data' )->justReturn( array( 'Name' => 'Hello Dolly', 'Version' => '1.7.2' ) );
		Functions\when( 'is_plugin_active' )->justReturn( false );

		$this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'force_check' => true ) );
	}

	/**
	 * View context includes update_available and new_version fields.
	 */
	public function test_view_context_includes_update_info(): void {
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'get_update_info' )
			->willReturn( array( 'update_available' => true, 'new_version' => '1.8.0' ) );

		Functions\when( 'get_plugin_data' )->justReturn( array( 'Name' => 'Hello Dolly', 'Version' => '1.7.2' ) );
		Functions\when( 'is_plugin_active' )->justReturn( true );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['plugin']['update_available'] );
		$this->assertSame( '1.8.0', $result['plugin']['new_version'] );
	}

	/**
	 * Embed context does not include update_available or new_version.
	 */
	public function test_embed_context_excludes_update_info(): void {
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );

		Functions\when( 'get_plugin_data' )->justReturn( array( 'Name' => 'Hello Dolly', 'Version' => '1.7.2' ) );
		Functions\when( 'is_plugin_active' )->justReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly', 'context' => 'embed' ) );

		$this->assertArrayNotHasKey( 'update_available', $result['plugin'] );
		$this->assertArrayNotHasKey( 'new_version', $result['plugin'] );
	}

	/**
	 * View context includes network_only, requires_wp, requires_php fields.
	 */
	public function test_view_context_includes_network_only_requires_wp_requires_php(): void {
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'find_file' )->willReturn( 'hello-dolly/hello-dolly.php' );
		$this->plugin_helper->method( 'get_update_info' )
			->willReturn( array( 'update_available' => false, 'new_version' => null ) );

		Functions\when( 'get_plugin_data' )->justReturn(
			array(
				'Name'        => 'Hello Dolly',
				'Version'     => '1.7.2',
				'Network'     => true,
				'RequiresWP'  => '6.0',
				'RequiresPHP' => '7.4',
			)
		);
		Functions\when( 'is_plugin_active' )->justReturn( false );

		$result = $this->tool->execute( array( 'plugin_slug' => 'hello-dolly' ) );

		$this->assertTrue( $result['plugin']['network_only'] );
		$this->assertSame( '6.0', $result['plugin']['requires_wp'] );
		$this->assertSame( '7.4', $result['plugin']['requires_php'] );
	}
}
