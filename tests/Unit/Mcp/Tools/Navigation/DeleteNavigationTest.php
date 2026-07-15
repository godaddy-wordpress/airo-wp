<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Navigation;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\DeleteNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class DeleteNavigationTest extends TestCase {

	private DeleteNavigation $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();

		$this->tool = new DeleteNavigation();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/delete-navigation', DeleteNavigation::TOOL_ID );
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

	public function test_successful_trash_returns_previous_data(): void {
		$post                = \Mockery::mock( 'WP_Post' );
		$post->ID            = 42;
		$post->post_type     = 'wp_navigation';
		$post->post_date     = '2026-01-01 00:00:00';
		$post->post_name     = 'nav-to-delete';
		$post->post_status   = 'publish';
		$post->post_title    = 'Nav to Delete';
		$post->post_content  = '<!-- wp:navigation-link -->';

		Functions\when( 'get_post' )->justReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_the_title' )->justReturn( 'Nav to Delete' );
		Functions\when( 'apply_filters' )->returnArg( 2 );
		Functions\when( 'wp_delete_post' )->justReturn( $post );

		$result = $this->tool->execute( array( 'id' => 42 ) );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'deleted', $result );
		$this->assertArrayHasKey( 'previous', $result );
		$this->assertSame( 42, $result['deleted']['id'] );
	}
}
