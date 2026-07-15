<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\RestorePostRevision;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class RestorePostRevisionTest extends TestCase {

	private RestorePostRevision $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();

		$this->tool = new RestorePostRevision();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/restore-post-revision', RestorePostRevision::TOOL_ID );
	}

	public function test_missing_parent_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Parent post ID is required', $result['message'] );
	}

	public function test_missing_revision_id_returns_error(): void {
		$result = $this->tool->execute( array( 'parent' => 42 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Revision ID is required', $result['message'] );
	}

	public function test_nonexistent_parent_returns_error(): void {
		Functions\expect( 'get_post' )
			->once()
			->with( 999 )
			->andReturn( null );

		$result = $this->tool->execute( array( 'parent' => 999, 'id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( '999', $result['message'] );
	}

	public function test_post_type_without_revisions_returns_error(): void {
		$post            = new \stdClass();
		$post->ID        = 42;
		$post->post_type = 'custom_type';

		Functions\expect( 'get_post' )
			->once()
			->with( 42 )
			->andReturn( $post );

		Functions\expect( 'post_type_supports' )
			->once()
			->with( 'custom_type', 'revisions' )
			->andReturn( false );

		$result = $this->tool->execute( array( 'parent' => 42, 'id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'does not support revisions', $result['message'] );
	}

	public function test_revision_not_belonging_to_parent_returns_error(): void {
		$post            = new \stdClass();
		$post->ID        = 42;
		$post->post_type = 'post';

		$revision              = new \stdClass();
		$revision->ID          = 10;
		$revision->post_parent = 99; // Different parent.

		Functions\expect( 'get_post' )
			->once()
			->with( 42 )
			->andReturn( $post );

		Functions\expect( 'post_type_supports' )
			->once()
			->with( 'post', 'revisions' )
			->andReturn( true );

		Functions\expect( 'wp_get_post_revision' )
			->once()
			->with( 10 )
			->andReturn( $revision );

		$result = $this->tool->execute( array( 'parent' => 42, 'id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'does not belong to the specified parent post', $result['message'] );
	}

	public function test_successful_restore(): void {
		$post            = new \stdClass();
		$post->ID        = 42;
		$post->post_type = 'post';

		$revision              = new \stdClass();
		$revision->ID          = 10;
		$revision->post_parent = 42;

		$latest_revision     = new \stdClass();
		$latest_revision->ID = 15;

		Functions\expect( 'get_post' )
			->once()
			->with( 42 )
			->andReturn( $post );

		Functions\expect( 'post_type_supports' )
			->once()
			->with( 'post', 'revisions' )
			->andReturn( true );

		Functions\expect( 'wp_get_post_revision' )
			->once()
			->with( 10 )
			->andReturn( $revision );

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'edit_post', 42 )
			->andReturn( true );

		Functions\expect( 'wp_restore_post_revision' )
			->once()
			->with( 10 )
			->andReturn( 42 );

		Functions\when( 'is_wp_error' )->justReturn( false );

		Functions\expect( 'wp_get_post_revisions' )
			->once()
			->andReturn( array( 15 => $latest_revision ) );

		$result = $this->tool->execute( array( 'parent' => 42, 'id' => 10 ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 42, $result['post_id'] );
		$this->assertSame( 10, $result['restored_revision_id'] );
		$this->assertSame( 15, $result['current_revision_id'] );
	}
}
