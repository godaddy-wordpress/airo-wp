<?php
/**
 * SwitchTheme tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Themes;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes\SwitchTheme;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for SwitchTheme MCP tool.
 */
final class SwitchThemeTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var SwitchTheme
	 */
	private SwitchTheme $tool;

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

		$this->tool = new SwitchTheme();
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertStringStartsWith( 'airo-wp/', SwitchTheme::TOOL_ID );
		$this->assertSame( 'airo-wp/switch-theme', SwitchTheme::TOOL_ID );
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
	 * check_permissions returns false without switch_themes capability.
	 */
	public function test_check_permissions_returns_false_without_switch_themes(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'switch_themes' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	/**
	 * Already active theme returns success.
	 */
	public function test_already_active_theme_returns_success(): void {
		Functions\expect( 'current_user_can' )->with( 'switch_themes' )->andReturn( true );
		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );

		$result = $this->tool->execute( array( 'theme_slug' => 'twentytwentyfour' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'twentytwentyfour', $result['theme'] );
		$this->assertStringContainsString( 'already active', $result['message'] );
	}

	/**
	 * Theme not installed returns error with hint.
	 */
	public function test_theme_not_installed_returns_error_with_hint(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( false );

		Functions\expect( 'current_user_can' )->with( 'switch_themes' )->andReturn( true );
		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentythree' );
		Functions\expect( 'wp_get_theme' )->with( 'nonexistent-theme' )->andReturn( $theme );

		$result = $this->tool->execute( array( 'theme_slug' => 'nonexistent-theme' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not installed', $result['message'] );
		$this->assertStringContainsString( 'activate-theme', $result['message'] );
	}

	/**
	 * Successful theme switch.
	 */
	public function test_successful_theme_switch(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( true );
		$theme->shouldReceive( 'get' )->with( 'Name' )->andReturn( 'Twenty Twenty-Four' );

		$previous_theme = \Mockery::mock( 'WP_Theme' );
		$previous_theme->shouldReceive( 'get' )->with( 'Name' )->andReturn( 'Twenty Twenty-Three' );

		Functions\expect( 'current_user_can' )->with( 'switch_themes' )->andReturn( true );
		Functions\expect( 'get_stylesheet' )->once()->andReturn( 'twentytwentythree' );
		Functions\expect( 'wp_get_theme' )->with( 'twentytwentyfour' )->andReturn( $theme );
		Functions\expect( 'wp_get_theme' )->with( 'twentytwentythree' )->andReturn( $previous_theme );
		Functions\expect( 'switch_theme' )->with( 'twentytwentyfour' )->once();
		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );

		$result = $this->tool->execute( array( 'theme_slug' => 'twentytwentyfour' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'twentytwentyfour', $result['theme'] );
		$this->assertSame( 'twentytwentythree', $result['previous_theme'] );
	}

	/**
	 * Theme switch failure returns error.
	 */
	public function test_theme_switch_failure_returns_error(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'exists' )->andReturn( true );

		Functions\expect( 'current_user_can' )->with( 'switch_themes' )->andReturn( true );
		Functions\expect( 'get_stylesheet' )->once()->andReturn( 'twentytwentythree' );
		Functions\expect( 'wp_get_theme' )->with( 'twentytwentyfour' )->andReturn( $theme );
		Functions\expect( 'switch_theme' )->with( 'twentytwentyfour' )->once();

		// After switch, get_stylesheet still returns old theme (failure).
		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentythree' );

		$result = $this->tool->execute( array( 'theme_slug' => 'twentytwentyfour' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'failed', $result['message'] );
	}
}
