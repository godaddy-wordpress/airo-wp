<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Navigation;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\CreateNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class CreateNavigationTest extends TestCase {

	private CreateNavigation $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'sanitize_title' )->returnArg();

		$this->tool = new CreateNavigation();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/create-navigation', CreateNavigation::TOOL_ID );
	}

	public function test_check_permissions_returns_false_without_edit_theme_options(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'edit_theme_options' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	public function test_successful_creation_returns_navigation_data(): void {
		Functions\when( 'current_user_can' )->justReturn( true );

		$post                = \Mockery::mock( 'WP_Post' );
		$post->ID            = 10;
		$post->post_type     = 'wp_navigation';
		$post->post_date     = '2026-01-01 00:00:00';
		$post->post_date_gmt = '2026-01-01 00:00:00';
		$post->guid          = 'http://example.com/?p=10';
		$post->post_modified     = '2026-01-01 00:00:00';
		$post->post_modified_gmt = '2026-01-01 00:00:00';
		$post->post_name     = 'test-nav';
		$post->post_status   = 'publish';
		$post->post_title    = 'Test Nav';
		$post->post_content  = '<!-- wp:navigation-link -->';

		Functions\when( 'wp_insert_post' )->justReturn( 10 );
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'wp_save_post_revision' )->justReturn( 11 );
		Functions\when( 'get_post' )->justReturn( $post );
		Functions\when( 'get_permalink' )->justReturn( 'http://example.com/?p=10' );
		Functions\when( 'get_the_title' )->justReturn( 'Test Nav' );
		Functions\when( 'apply_filters' )->returnArg( 2 );
		Functions\when( 'get_page_template_slug' )->justReturn( '' );

		$result = $this->tool->execute( array( 'title' => 'Test Nav', 'content' => '<!-- wp:navigation-link -->' ) );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'navigation', $result );
		$this->assertSame( 10, $result['navigation']['id'] );
	}
}
