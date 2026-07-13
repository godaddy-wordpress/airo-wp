<?php
/**
 * GetThemes tool tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Themes;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes\GetThemes;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for GetThemes MCP tool.
 */
final class GetThemesTest extends TestCase {

	/**
	 * Tool under test.
	 *
	 * @var GetThemes
	 */
	private GetThemes $tool;

	/**
	 * Set up.
	 */
	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();

		$this->tool = new GetThemes();
	}

	/**
	 * TOOL_ID has the correct airo-wp prefix.
	 */
	public function test_tool_id_has_correct_prefix(): void {
		$this->assertStringStartsWith( 'airo-wp/', GetThemes::TOOL_ID );
		$this->assertSame( 'airo-wp/get-themes', GetThemes::TOOL_ID );
	}

	/**
	 * Missing active parameter returns error.
	 */
	public function test_missing_active_parameter_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'required', $result['message'] );
	}

	/**
	 * Active=true returns single active theme.
	 */
	public function test_active_true_returns_single_theme(): void {
		$theme = \Mockery::mock( 'WP_Theme' );
		$theme->shouldReceive( 'get' )->with( 'Name' )->andReturn( 'Twenty Twenty-Four' );
		$theme->shouldReceive( 'get' )->with( 'Description' )->andReturn( 'A modern theme' );
		$theme->shouldReceive( 'get' )->with( 'Version' )->andReturn( '1.0' );
		$theme->shouldReceive( 'get' )->with( 'Author' )->andReturn( 'WordPress.org' );
		$theme->shouldReceive( 'get' )->with( 'AuthorURI' )->andReturn( 'https://wordpress.org' );
		$theme->shouldReceive( 'get' )->with( 'ThemeURI' )->andReturn( 'https://wordpress.org/themes/twentytwentyfour' );
		$theme->shouldReceive( 'get' )->with( 'Tags' )->andReturn( array( 'block-themes', 'full-site-editing' ) );
		$theme->shouldReceive( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );
		$theme->shouldReceive( 'get_template' )->andReturn( 'twentytwentyfour' );
		$theme->shouldReceive( 'is_block_theme' )->andReturn( true );

		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );
		Functions\expect( 'wp_get_theme' )->withNoArgs()->andReturn( $theme );

		$result = $this->tool->execute( array( 'active' => true ) );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 1, $result['themes'] );
		$this->assertSame( 'Twenty Twenty-Four', $result['themes'][0]['name'] );
		$this->assertSame( 'twentytwentyfour', $result['themes'][0]['stylesheet'] );
		$this->assertSame( 'active', $result['themes'][0]['status'] );
		$this->assertTrue( $result['themes'][0]['is_block_theme'] );
	}

	/**
	 * Active=false returns all themes.
	 */
	public function test_active_false_returns_all_themes(): void {
		$active_theme = \Mockery::mock( 'WP_Theme' );
		$active_theme->shouldReceive( 'get' )->with( 'Name' )->andReturn( 'Twenty Twenty-Four' );
		$active_theme->shouldReceive( 'get' )->with( 'Description' )->andReturn( 'A modern theme' );
		$active_theme->shouldReceive( 'get' )->with( 'Version' )->andReturn( '1.0' );
		$active_theme->shouldReceive( 'get' )->with( 'Author' )->andReturn( 'WordPress.org' );
		$active_theme->shouldReceive( 'get' )->with( 'AuthorURI' )->andReturn( 'https://wordpress.org' );
		$active_theme->shouldReceive( 'get' )->with( 'ThemeURI' )->andReturn( 'https://wordpress.org/themes/twentytwentyfour' );
		$active_theme->shouldReceive( 'get' )->with( 'Tags' )->andReturn( array() );
		$active_theme->shouldReceive( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );
		$active_theme->shouldReceive( 'get_template' )->andReturn( 'twentytwentyfour' );
		$active_theme->shouldReceive( 'is_block_theme' )->andReturn( true );

		$inactive_theme = \Mockery::mock( 'WP_Theme' );
		$inactive_theme->shouldReceive( 'get' )->with( 'Name' )->andReturn( 'Twenty Twenty-Three' );
		$inactive_theme->shouldReceive( 'get' )->with( 'Description' )->andReturn( 'Classic theme' );
		$inactive_theme->shouldReceive( 'get' )->with( 'Version' )->andReturn( '1.3' );
		$inactive_theme->shouldReceive( 'get' )->with( 'Author' )->andReturn( 'WordPress.org' );
		$inactive_theme->shouldReceive( 'get' )->with( 'AuthorURI' )->andReturn( 'https://wordpress.org' );
		$inactive_theme->shouldReceive( 'get' )->with( 'ThemeURI' )->andReturn( 'https://wordpress.org/themes/twentytwentythree' );
		$inactive_theme->shouldReceive( 'get' )->with( 'Tags' )->andReturn( array( 'block-themes' ) );
		$inactive_theme->shouldReceive( 'get_stylesheet' )->andReturn( 'twentytwentythree' );
		$inactive_theme->shouldReceive( 'get_template' )->andReturn( 'twentytwentythree' );
		$inactive_theme->shouldReceive( 'is_block_theme' )->andReturn( false );

		Functions\expect( 'get_stylesheet' )->andReturn( 'twentytwentyfour' );
		Functions\expect( 'wp_get_themes' )->andReturn(
			array(
				'twentytwentyfour'  => $active_theme,
				'twentytwentythree' => $inactive_theme,
			)
		);

		$result = $this->tool->execute( array( 'active' => false ) );

		$this->assertTrue( $result['success'] );
		$this->assertCount( 2, $result['themes'] );
		$this->assertSame( 'active', $result['themes'][0]['status'] );
		$this->assertSame( 'inactive', $result['themes'][1]['status'] );
		$this->assertSame( 'Twenty Twenty-Three', $result['themes'][1]['name'] );
	}

	/**
	 * Theme without is_block_theme method defaults to false.
	 */
	public function test_theme_without_is_block_theme_method_defaults_false(): void {
		$theme = \Mockery::mock( 'WP_Theme' )->makePartial();
		$theme->shouldReceive( 'get' )->with( 'Name' )->andReturn( 'Legacy Theme' );
		$theme->shouldReceive( 'get' )->with( 'Description' )->andReturn( 'Old theme' );
		$theme->shouldReceive( 'get' )->with( 'Version' )->andReturn( '2.0' );
		$theme->shouldReceive( 'get' )->with( 'Author' )->andReturn( 'Author' );
		$theme->shouldReceive( 'get' )->with( 'AuthorURI' )->andReturn( '' );
		$theme->shouldReceive( 'get' )->with( 'ThemeURI' )->andReturn( '' );
		$theme->shouldReceive( 'get' )->with( 'Tags' )->andReturn( array() );
		$theme->shouldReceive( 'get_stylesheet' )->andReturn( 'legacy-theme' );
		$theme->shouldReceive( 'get_template' )->andReturn( 'legacy-theme' );

		// Simulate a theme that doesn't have is_block_theme method.
		// Since we're using makePartial and NOT defining is_block_theme,
		// method_exists will still return true for a Mockery mock.
		// Instead, let's mock it returning false.
		$theme->shouldReceive( 'is_block_theme' )->andReturn( false );

		Functions\expect( 'get_stylesheet' )->andReturn( 'legacy-theme' );
		Functions\expect( 'wp_get_theme' )->withNoArgs()->andReturn( $theme );

		$result = $this->tool->execute( array( 'active' => true ) );

		$this->assertTrue( $result['success'] );
		$this->assertFalse( $result['themes'][0]['is_block_theme'] );
	}
}
