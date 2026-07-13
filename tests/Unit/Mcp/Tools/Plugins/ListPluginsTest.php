<?php
/**
 * ListPlugins tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Plugins;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services\PluginHelper;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\ListPlugins;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for ListPlugins MCP tool.
 */
final class ListPluginsTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var ListPlugins
	 */
	private ListPlugins $tool;

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

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->plugin_helper = $this->createMock( PluginHelper::class );
		$this->plugin_helper->method( 'refresh_updates' );
		$this->plugin_helper->method( 'get_update_info' )->willReturn(
			array( 'update_available' => false, 'new_version' => null )
		);
		$this->tool = new ListPlugins( $this->plugin_helper );
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertStringStartsWith( 'airo-wp/', ListPlugins::TOOL_ID );
		$this->assertSame( 'airo-wp/list-plugins', ListPlugins::TOOL_ID );
	}

	/**
	 * Returns all plugins when no filters.
	 */
	public function test_returns_all_plugins_when_no_filters(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array(
				'hello-dolly/hello-dolly.php'   => array(
					'Name'    => 'Hello Dolly',
					'Version' => '1.7.2',
					'Author'  => 'Matt Mullenweg',
				),
				'akismet/akismet.php'           => array(
					'Name'    => 'Akismet',
					'Version' => '5.3',
					'Author'  => 'Automattic',
				),
			)
		);
		Functions\expect( 'is_plugin_active' )->andReturn( true );

		$result = $this->tool->execute( array() );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 2, $result['plugins'] );
		$this->assertSame( 2, $result['total'] );
	}

	/**
	 * Filters by active status.
	 */
	public function test_filters_by_active_status(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array(
				'hello-dolly/hello-dolly.php' => array(
					'Name'    => 'Hello Dolly',
					'Version' => '1.7.2',
				),
				'akismet/akismet.php'         => array(
					'Name'    => 'Akismet',
					'Version' => '5.3',
				),
			)
		);
		Functions\expect( 'is_plugin_active' )->andReturnUsing(
			function ( $file ) {
				return 'hello-dolly/hello-dolly.php' === $file;
			}
		);

		$result = $this->tool->execute( array( 'status' => 'active' ) );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 1, $result['plugins'] );
		$this->assertSame( 'hello-dolly', $result['plugins'][0]['slug'] );
	}

	/**
	 * Filters by inactive status.
	 */
	public function test_filters_by_inactive_status(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array(
				'hello-dolly/hello-dolly.php' => array(
					'Name'    => 'Hello Dolly',
					'Version' => '1.7.2',
				),
				'akismet/akismet.php'         => array(
					'Name'    => 'Akismet',
					'Version' => '5.3',
				),
			)
		);
		Functions\expect( 'is_plugin_active' )->andReturnUsing(
			function ( $file ) {
				return 'hello-dolly/hello-dolly.php' === $file;
			}
		);

		$result = $this->tool->execute( array( 'status' => 'inactive' ) );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 1, $result['plugins'] );
		$this->assertSame( 'akismet', $result['plugins'][0]['slug'] );
	}

	/**
	 * Filters by search term.
	 */
	public function test_filters_by_search_term(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array(
				'hello-dolly/hello-dolly.php' => array(
					'Name'    => 'Hello Dolly',
					'Version' => '1.7.2',
				),
				'akismet/akismet.php'         => array(
					'Name'    => 'Akismet',
					'Version' => '5.3',
				),
			)
		);
		Functions\expect( 'is_plugin_active' )->andReturn( false );

		$result = $this->tool->execute( array( 'search' => 'Hello' ) );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 1, $result['plugins'] );
		$this->assertSame( 'Hello Dolly', $result['plugins'][0]['name'] );
	}

	/**
	 * Embed context returns minimal fields.
	 */
	public function test_embed_context_returns_minimal_fields(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array(
				'hello-dolly/hello-dolly.php' => array(
					'Name'        => 'Hello Dolly',
					'Version'     => '1.7.2',
					'Author'      => 'Matt Mullenweg',
					'Description' => 'A lyrics plugin.',
				),
			)
		);
		Functions\expect( 'is_plugin_active' )->andReturn( true );

		$result = $this->tool->execute( array( 'context' => 'embed' ) );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 1, $result['plugins'] );
		$this->assertArrayHasKey( 'slug', $result['plugins'][0] );
		$this->assertArrayHasKey( 'name', $result['plugins'][0] );
		$this->assertArrayHasKey( 'version', $result['plugins'][0] );
		$this->assertArrayHasKey( 'status', $result['plugins'][0] );
		$this->assertArrayNotHasKey( 'author', $result['plugins'][0] );
		$this->assertArrayNotHasKey( 'description', $result['plugins'][0] );
		$this->assertArrayNotHasKey( 'file', $result['plugins'][0] );
	}

	/**
	 * Empty plugin list returns empty results.
	 */
	public function test_empty_plugin_list_returns_empty_results(): void {
		Functions\expect( 'get_plugins' )->andReturn( array() );

		$result = $this->tool->execute( array() );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 0, $result['plugins'] );
		$this->assertSame( 0, $result['total'] );
	}

	/**
	 * force_check=true passes true to refresh_updates.
	 */
	public function test_force_check_true_calls_refresh_updates(): void {
		$this->plugin_helper->expects( $this->once() )
			->method( 'refresh_updates' )
			->with( true );

		Functions\when( 'get_plugins' )->justReturn( array() );

		$this->tool->execute( array( 'force_check' => true ) );
	}

	/**
	 * View context includes update_available and new_version.
	 */
	public function test_view_context_includes_update_info(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array( 'hello-dolly/hello-dolly.php' => array( 'Name' => 'Hello Dolly', 'Version' => '1.7.2' ) )
		);
		Functions\expect( 'is_plugin_active' )->andReturn( false );

		$plugin_helper = $this->createMock( PluginHelper::class );
		$plugin_helper->method( 'refresh_updates' );
		$plugin_helper->method( 'get_update_info' )
			->willReturn( array( 'update_available' => true, 'new_version' => '1.8.0' ) );
		$tool = new ListPlugins( $plugin_helper );

		$result = $tool->execute( array() );

		$this->assertTrue( $result['plugins'][0]['update_available'] );
		$this->assertSame( '1.8.0', $result['plugins'][0]['new_version'] );
	}

	/**
	 * Search term matches plugin description.
	 */
	public function test_search_matches_plugin_description(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array(
				'hello-dolly/hello-dolly.php' => array(
					'Name'        => 'Hello Dolly',
					'Version'     => '1.7.2',
					'Description' => 'A classic lyrics plugin for WordPress.',
				),
				'akismet/akismet.php'         => array(
					'Name'        => 'Akismet',
					'Version'     => '5.3',
					'Description' => 'Spam protection.',
				),
			)
		);
		Functions\expect( 'is_plugin_active' )->andReturn( false );

		$result = $this->tool->execute( array( 'search' => 'lyrics' ) );

		$this->assertCount( 1, $result['plugins'] );
		$this->assertSame( 'Hello Dolly', $result['plugins'][0]['name'] );
	}

	/**
	 * View context includes new metadata fields.
	 */
	public function test_view_context_includes_new_fields(): void {
		Functions\expect( 'get_plugins' )->andReturn(
			array(
				'hello-dolly/hello-dolly.php' => array(
					'Name'        => 'Hello Dolly',
					'Version'     => '1.7.2',
					'PluginURI'   => 'https://wordpress.org/plugins/hello-dolly/',
					'AuthorURI'   => 'https://ma.tt/',
					'TextDomain'  => 'hello-dolly',
					'Network'     => true,
					'RequiresWP'  => '6.0',
					'RequiresPHP' => '7.4',
				),
			)
		);
		Functions\expect( 'is_plugin_active' )->andReturn( true );

		$result = $this->tool->execute( array() );

		$plugin = $result['plugins'][0];
		$this->assertSame( 'https://wordpress.org/plugins/hello-dolly/', $plugin['plugin_uri'] );
		$this->assertSame( 'https://ma.tt/', $plugin['author_uri'] );
		$this->assertSame( 'hello-dolly', $plugin['text_domain'] );
		$this->assertTrue( $plugin['network_only'] );
		$this->assertSame( '6.0', $plugin['requires_wp'] );
		$this->assertSame( '7.4', $plugin['requires_php'] );
	}
}
