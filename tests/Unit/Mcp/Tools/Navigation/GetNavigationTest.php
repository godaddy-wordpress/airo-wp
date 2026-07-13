<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Navigation;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\GetNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetNavigationTest extends TestCase {

	private GetNavigation $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'esc_html__' )->returnArg();

		$this->tool = new GetNavigation();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/get-navigation', GetNavigation::TOOL_ID );
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
		$this->assertStringContainsString( '99999', $result['message'] );
	}

	public function test_wrong_post_type_returns_error(): void {
		$post            = \Mockery::mock( 'WP_Post' );
		$post->post_type = 'post';

		Functions\when( 'get_post' )->justReturn( $post );

		$result = $this->tool->execute( array( 'id' => 1 ) );

		$this->assertFalse( $result['success'] );
	}

	public function test_valid_navigation_returns_success(): void {
		$post                = \Mockery::mock( 'WP_Post' );
		$post->ID            = 42;
		$post->post_type     = 'wp_navigation';
		$post->post_date     = '2026-01-01 00:00:00';
		$post->post_date_gmt = '2026-01-01 00:00:00';
		$post->guid          = 'http://example.com/?p=42';
		$post->post_modified     = '2026-01-01 00:00:00';
		$post->post_modified_gmt = '2026-01-01 00:00:00';
		$post->post_name     = 'main-nav';
		$post->post_status   = 'publish';
		$post->post_title    = 'Main Navigation';
		$post->post_content  = '<!-- wp:navigation-link -->';
		$post->post_password = '';

		Functions\when( 'get_post' )->justReturn( $post );
		Functions\when( 'get_permalink' )->justReturn( 'http://example.com/?p=42' );
		Functions\when( 'get_the_title' )->justReturn( 'Main Navigation' );
		Functions\when( 'apply_filters' )->returnArg( 2 );
		Functions\when( 'get_page_template_slug' )->justReturn( '' );

		$result = $this->tool->execute( array( 'id' => 42 ) );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'navigation', $result );
		$this->assertSame( 42, $result['navigation']['id'] );
		$this->assertSame( 'main-nav', $result['navigation']['slug'] );
	}
}
