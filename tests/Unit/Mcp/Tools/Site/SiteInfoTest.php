<?php
/**
 * SiteInfo tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Site;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site\SiteInfo;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for SiteInfo MCP tool.
 */
final class SiteInfoTest extends TestCase {

	/**
	 * SiteInfo tool under test.
	 *
	 * @var SiteInfo
	 */
	private SiteInfo $tool;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->tool = new SiteInfo();
	}

	/**
	 * Execute() returns all core fields.
	 */
	public function test_execute_returns_core_fields(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->alias(
			function ( $key, $default = false ) {
				$map = array(
					'blogname'           => 'Test Site',
					'blogdescription'    => 'Test Tagline',
					'siteurl'            => 'https://example.com',
					'home'               => 'https://example.com',
					'admin_email'        => 'admin@example.com',
					'gdl_site_published' => false,
					'date_format'        => 'F j, Y',
					'time_format'        => 'g:i a',
					'posts_per_page'     => 10,
					'blog_public'        => true,
					'site_logo'          => 0,
					'site_icon'          => 0,
				);
				return isset( $map[ $key ] ) ? $map[ $key ] : $default;
			}
		);
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'America/New_York' );

		$result = $this->tool->execute( array() );

		$this->assertArrayHasKey( 'site_name', $result );
		$this->assertArrayHasKey( 'site_url', $result );
		$this->assertArrayHasKey( 'description', $result );
		$this->assertArrayHasKey( 'wordpress_version', $result );
		$this->assertArrayHasKey( 'is_published', $result );
		$this->assertArrayHasKey( 'site_locale', $result );
		$this->assertIsString( $result['site_locale'] );
		$this->assertArrayHasKey( 'timezone', $result );
		$this->assertArrayHasKey( 'date_format', $result );
		$this->assertArrayHasKey( 'time_format', $result );
		$this->assertArrayHasKey( 'posts_per_page', $result );
		$this->assertIsInt( $result['posts_per_page'] );
		$this->assertArrayHasKey( 'blog_public', $result );
		$this->assertIsBool( $result['blog_public'] );
		$this->assertArrayHasKey( 'site_logo', $result );
		$this->assertArrayHasKey( 'site_icon', $result );
	}

	/**
	 * Optional fields absent when flags not set.
	 */
	public function test_execute_omits_optional_fields_by_default(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->justReturn( false );
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'UTC' );

		$result = $this->tool->execute( array() );

		$this->assertArrayNotHasKey( 'stats', $result );
		$this->assertArrayNotHasKey( 'theme_info', $result );
		$this->assertArrayNotHasKey( 'plugin_count', $result );
		$this->assertArrayNotHasKey( 'reading_settings', $result );
	}

	/**
	 * Stats included when include_stats flag set.
	 */
	public function test_execute_includes_stats_when_flag_set(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->justReturn( false );
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'UTC' );

		$counts          = new \stdClass();
		$counts->publish = 5;
		Functions\when( 'wp_count_posts' )->justReturn( $counts );

		$result = $this->tool->execute( array( 'include_stats' => true ) );

		$this->assertArrayHasKey( 'stats', $result );
		$this->assertSame( 5, $result['stats']['post_count'] );
		$this->assertSame( 5, $result['stats']['page_count'] );
	}

	/**
	 * Theme info included when include_theme_info flag set.
	 */
	public function test_execute_includes_theme_info_when_flag_set(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->justReturn( false );
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'UTC' );

		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'get' )->with( 'Name' )->andReturn( 'Twenty Twenty-Four' );
		$theme->shouldReceive( 'get' )->with( 'Version' )->andReturn( '1.2' );
		$theme->shouldReceive( 'get' )->with( 'Author' )->andReturn( 'WordPress' );
		Functions\when( 'wp_get_theme' )->justReturn( $theme );

		$result = $this->tool->execute( array( 'include_theme_info' => true ) );

		$this->assertArrayHasKey( 'theme_info', $result );
		$this->assertSame( 'Twenty Twenty-Four', $result['theme_info']['name'] );
	}

	/**
	 * Is_published reflects gdl_site_published option.
	 */
	public function test_execute_is_published_reflects_option(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->alias(
			function ( $key, $fallback = false ) {
				if ( 'gdl_site_published' === $key ) {
					return '1';
				}
				return $fallback;
			}
		);
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'UTC' );

		$result = $this->tool->execute( array() );

		$this->assertTrue( $result['is_published'] );
	}

	/**
	 * site_logo is null when option is 0.
	 */
	public function test_execute_site_logo_null_when_not_set(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->alias(
			function ( $key, $default = false ) {
				if ( 'site_logo' === $key ) {
					return 0;
				}
				return $default;
			}
		);
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'UTC' );

		$result = $this->tool->execute( array() );

		$this->assertNull( $result['site_logo'] );
	}

	/**
	 * site_icon is null when option is 0.
	 */
	public function test_execute_site_icon_null_when_not_set(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->alias(
			function ( $key, $default = false ) {
				if ( 'site_icon' === $key ) {
					return 0;
				}
				return $default;
			}
		);
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'UTC' );

		$result = $this->tool->execute( array() );

		$this->assertNull( $result['site_icon'] );
	}

	/**
	 * site_logo returns attachment ID when option is set.
	 */
	public function test_execute_site_logo_returns_id_when_set(): void {
		Functions\when( 'get_bloginfo' )->justReturn( 'test' );
		Functions\when( 'get_site_url' )->justReturn( 'https://example.com' );
		Functions\when( 'get_option' )->alias(
			function ( $key, $default = false ) {
				if ( 'site_logo' === $key ) {
					return 42;
				}
				return $default;
			}
		);
		Functions\when( 'get_locale' )->justReturn( 'en_US' );
		Functions\when( 'wp_timezone_string' )->justReturn( 'UTC' );

		$result = $this->tool->execute( array() );

		$this->assertSame( 42, $result['site_logo'] );
	}

	/**
	 * Execute() is callable.
	 */
	public function test_execute_is_callable(): void {
		$this->assertTrue( is_callable( array( $this->tool, 'execute' ) ) );
	}

	/**
	 * Register() is callable.
	 */
	public function test_register_is_callable(): void {
		$this->assertTrue( is_callable( array( $this->tool, 'register' ) ) );
	}
}
