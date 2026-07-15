<?php
/**
 * ActivateTheme tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Themes;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes\ActivateTheme;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for ActivateTheme MCP tool.
 */
final class ActivateThemeTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var ActivateTheme
	 */
	private ActivateTheme $tool;

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

		$this->tool = new ActivateTheme();
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertStringStartsWith( 'airo-wp/', ActivateTheme::TOOL_ID );
		$this->assertSame( 'airo-wp/activate-theme', ActivateTheme::TOOL_ID );
	}

	/**
	 * Empty input returns error.
	 */
	public function test_empty_input_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'required', $result['message'] );
	}

	/**
	 * Empty string slug returns error.
	 */
	public function test_empty_string_slug_returns_error(): void {
		$result = $this->tool->execute( array( 'theme_slug' => '' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'required', $result['message'] );
	}

	/**
	 * Already active theme returns success.
	 */
	public function test_already_active_theme_returns_success(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( true );
		$theme->shouldReceive( 'get' )->with( 'Version' )->andReturn( '1.0' );

		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );
		Functions\expect( 'wp_get_theme' )->with( 'twentytwentyfour' )->andReturn( $theme );

		$result = $this->tool->execute( array( 'theme_slug' => 'twentytwentyfour' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'twentytwentyfour', $result['theme'] );
		$this->assertSame( '1.0', $result['version'] );
		$this->assertStringContainsString( 'already active', $result['message'] );
	}

	/**
	 * check_permissions returns false without switch_themes capability.
	 */
	public function test_check_permissions_returns_false_without_switch_themes(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'switch_themes' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	/**
	 * check_permissions returns true for an already-installed theme with only switch_themes.
	 */
	public function test_check_permissions_true_for_installed_theme_with_switch_themes_only(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( true );

		Functions\expect( 'current_user_can' )->with( 'switch_themes' )->andReturn( true );
		Functions\expect( 'wp_get_theme' )->with( 'twentytwentyfour' )->andReturn( $theme );

		$this->assertTrue( $this->tool->check_permissions( array( 'theme_slug' => 'twentytwentyfour' ) ) );
	}

	/**
	 * check_permissions returns false for uninstalled theme when install_themes is missing.
	 */
	public function test_check_permissions_false_for_uninstalled_theme_without_install_themes(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( false );

		// andReturnUsing disambiguates two calls to current_user_can with different args.
		Functions\expect( 'current_user_can' )->andReturnUsing(
			fn( string $cap ) => 'switch_themes' === $cap
		);
		Functions\expect( 'wp_get_theme' )->with( 'new-theme' )->andReturn( $theme );

		$this->assertFalse( $this->tool->check_permissions( array( 'theme_slug' => 'new-theme' ) ) );
	}

	/**
	 * check_permissions returns true for uninstalled theme when both caps are present.
	 */
	public function test_check_permissions_true_for_uninstalled_theme_with_install_themes(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( false );

		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'wp_get_theme' )->with( 'new-theme' )->andReturn( $theme );

		$this->assertTrue( $this->tool->check_permissions( array( 'theme_slug' => 'new-theme' ) ) );
	}

	/**
	 * check_permissions returns true with no slug — only switch_themes needed.
	 */
	public function test_check_permissions_true_with_no_slug(): void {
		Functions\expect( 'current_user_can' )->with( 'switch_themes' )->andReturn( true );

		$this->assertTrue( $this->tool->check_permissions( array() ) );
	}

	/**
	 * Successful activation of existing theme.
	 */
	public function test_successful_activation_of_existing_theme(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( true );
		$theme->shouldReceive( 'get' )->with( 'Version' )->andReturn( '1.2' );

		Functions\expect( 'get_stylesheet' )->once()->andReturn( 'twentytwentythree' );
		Functions\expect( 'wp_get_theme' )->with( 'twentytwentyfour' )->andReturn( $theme );
		Functions\expect( 'current_user_can' )->with( 'switch_themes' )->andReturn( true );
		Functions\expect( 'switch_theme' )->with( 'twentytwentyfour' )->once();

		// After switch, get_stylesheet returns the new theme.
		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );

		$result = $this->tool->execute( array( 'theme_slug' => 'twentytwentyfour' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'twentytwentyfour', $result['theme'] );
		$this->assertSame( '1.2', $result['version'] );
		$this->assertSame( 'twentytwentythree', $result['previous_theme'] );
		$this->assertStringContainsString( 'successfully', $result['message'] );
	}

	/**
	 * Permission denied for install returns error.
	 */
	public function test_permission_denied_install_returns_error(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( false );

		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentythree' );
		Functions\expect( 'wp_get_theme' )->with( 'nonexistent-theme' )->andReturn( $theme );
		Functions\expect( 'current_user_can' )->with( 'install_themes' )->andReturn( false );

		$result = $this->tool->execute( array( 'theme_slug' => 'nonexistent-theme' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission', $result['message'] );
	}

	/**
	 * Theme API error returns failure.
	 */
	public function test_themes_api_error_returns_failure(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( false );

		$wp_error = \Mockery::mock( 'WP_Error' );
		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Theme not found on WordPress.org' );

		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentythree' );
		Functions\expect( 'wp_get_theme' )->with( 'nonexistent-theme' )->andReturn( $theme );
		Functions\expect( 'current_user_can' )->with( 'install_themes' )->andReturn( true );
		Functions\expect( 'themes_api' )->andReturn( $wp_error );
		Functions\expect( 'is_wp_error' )->andReturnUsing(
			function ( $thing ) use ( $wp_error ) {
				return $thing === $wp_error;
			}
		);

		$result = $this->tool->execute( array( 'theme_slug' => 'nonexistent-theme' ) );

		$this->assertFalse( $result['success'] );
		$this->assertSame( 'Theme not found on WordPress.org', $result['message'] );
	}
}
