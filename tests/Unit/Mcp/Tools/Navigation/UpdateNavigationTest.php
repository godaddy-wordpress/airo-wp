<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Navigation;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\UpdateNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UpdateNavigationTest extends TestCase {

	private UpdateNavigation $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'sanitize_title' )->returnArg();

		$this->tool = new UpdateNavigation();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/update-navigation', UpdateNavigation::TOOL_ID );
	}

	public function test_missing_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'required', $result['message'] );
	}

	public function test_non_existent_post_returns_error(): void {
		Functions\when( 'get_post' )->justReturn( null );

		$result = $this->tool->execute( array( 'id' => 99999 ) );

		$this->assertFalse( $result['success'] );
	}

	public function test_successful_update_returns_navigation_data(): void {
		$post                = \Mockery::mock( 'WP_Post' );
		$post->ID            = 42;
		$post->post_type     = 'wp_navigation';
		$post->post_date     = '2026-01-01 00:00:00';
		$post->post_date_gmt = '2026-01-01 00:00:00';
		$post->guid          = 'http://example.com/?p=42';
		$post->post_modified     = '2026-01-02 00:00:00';
		$post->post_modified_gmt = '2026-01-02 00:00:00';
		$post->post_name     = 'updated-nav';
		$post->post_status   = 'publish';
		$post->post_title    = 'Updated Nav';
		$post->post_content  = '<!-- wp:navigation-link -->';

		Functions\when( 'get_post' )->justReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'wp_update_post' )->justReturn( 42 );
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'get_permalink' )->justReturn( 'http://example.com/?p=42' );
		Functions\when( 'get_the_title' )->justReturn( 'Updated Nav' );
		Functions\when( 'apply_filters' )->returnArg( 2 );
		Functions\when( 'get_page_template_slug' )->justReturn( '' );

		$result = $this->tool->execute( array( 'id' => 42, 'title' => 'Updated Nav' ) );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'navigation', $result );
		$this->assertSame( 42, $result['navigation']['id'] );
	}
}
