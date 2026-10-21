<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\GetPost;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetPostTest extends TestCase {

	private GetPost $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'get_post_type_object' )->justReturn( $this->type_object( 'edit_posts' ) );
		Functions\when( 'is_post_type_viewable' )->justReturn( true );

		$this->tool = new GetPost();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/get-post', GetPost::TOOL_ID );
	}

	public function test_nonexistent_post_returns_not_found(): void {
		Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

		$result = $this->tool->execute( array( 'post_id' => 99 ) );

		$this->assertSame( 'not_found', $result['status'] );
		$this->assertSame( 'Post not found', $result['title'] );
		$this->assertSame( 0, $result['featured_media_id'] );
	}

	public function test_returns_post_data_without_meta(): void {
		$post                = new \stdClass();
		$post->ID            = 5;
		$post->post_title    = 'Hello World';
		$post->post_content  = 'Some content';
		$post->post_excerpt  = 'Excerpt';
		$post->post_status   = 'publish';
		$post->post_type     = 'post';
		$post->post_author   = '1';
		$post->post_date     = '2025-01-01 00:00:00';
		$post->post_modified = '2025-01-02 00:00:00';
		$post->post_name     = 'hello-world';
		$post->post_password = '';

		Functions\expect( 'get_post' )->with( 5 )->andReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'get_post_thumbnail_id' )->with( 5 )->andReturn( 0 );

		$result = $this->tool->execute( array( 'post_id' => 5, 'include_meta' => false ) );

		$this->assertSame( 5, $result['id'] );
		$this->assertSame( 'Hello World', $result['title'] );
		$this->assertSame( 'publish', $result['status'] );
		$this->assertSame( array(), $result['meta'] );
		$this->assertSame( 0, $result['featured_media_id'] );
	}

	public function test_returns_featured_media_id_when_thumbnail_set(): void {
		$post                = new \stdClass();
		$post->ID            = 42;
		$post->post_title    = 'Thumbnail Post';
		$post->post_content  = 'Content';
		$post->post_excerpt  = '';
		$post->post_status   = 'publish';
		$post->post_type     = 'post';
		$post->post_author   = '1';
		$post->post_date     = '2025-05-01 00:00:00';
		$post->post_modified = '2025-05-02 00:00:00';
		$post->post_name     = 'thumbnail-post';
		$post->post_password = '';

		Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'get_post_thumbnail_id' )->with( 42 )->andReturn( 99 );

		$result = $this->tool->execute( array( 'post_id' => 42, 'include_meta' => false ) );

		$this->assertArrayHasKey( 'featured_media_id', $result );
		$this->assertSame( 99, $result['featured_media_id'] );
	}

	public function test_returns_meta_when_requested(): void {
		$post                = new \stdClass();
		$post->ID            = 7;
		$post->post_title    = 'Meta Post';
		$post->post_content  = '';
		$post->post_excerpt  = '';
		$post->post_status   = 'draft';
		$post->post_type     = 'post';
		$post->post_author   = '2';
		$post->post_date     = '2025-03-01 00:00:00';
		$post->post_modified = '2025-03-02 00:00:00';
		$post->post_name     = 'meta-post';
		$post->post_password = '';

		$meta_data = array( '_custom_key' => array( 'custom_value' ) );

		Functions\expect( 'get_post' )->with( 7 )->andReturn( $post );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'get_post_thumbnail_id' )->with( 7 )->andReturn( 0 );
		Functions\expect( 'get_post_meta' )->with( 7 )->andReturn( $meta_data );

		$result = $this->tool->execute( array( 'post_id' => 7, 'include_meta' => true ) );

		$this->assertSame( $meta_data, $result['meta'] );
		$this->assertSame( 0, $result['featured_media_id'] );
	}

	public function test_unreadable_post_returns_not_found_shape(): void {
		// ->once() on both get_post expectations: Mockery reuses the first matching
		// expectation until it is exhausted, so without it the second call returns null too.
		Functions\expect( 'get_post' )->once()->with( 99 )->andReturn( null );
		$missing = $this->tool->execute( array( 'post_id' => 99 ) );

		$post                = new \stdClass();
		$post->ID            = 99;
		$post->post_title    = 'Secret';
		$post->post_content  = 'Secret body';
		$post->post_password = '';
		Functions\expect( 'get_post' )->once()->with( 99 )->andReturn( $post );
		Functions\expect( 'current_user_can' )->with( 'read_post', 99 )->andReturn( false );
		Functions\expect( 'get_post_meta' )->never();

		$hidden = $this->tool->execute( array( 'post_id' => 99 ) );

		$this->assertSame( $missing, $hidden );
	}

	public function test_meta_omitted_without_edit_post(): void {
		$post = $this->make_post( 7 );
		Functions\expect( 'get_post' )->with( 7 )->andReturn( $post );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'read_post' === $cap;
			}
		);
		Functions\expect( 'get_post_meta' )->never();

		$result = $this->tool->execute( array( 'post_id' => 7, 'include_meta' => true ) );

		$this->assertSame( array(), $result['meta'] );
		$this->assertSame( 'Body', $result['content'] );
	}

	public function test_password_protected_content_hidden_without_edit_post(): void {
		$post                = $this->make_post( 8 );
		$post->post_password = 'hunter2';
		Functions\expect( 'get_post' )->with( 8 )->andReturn( $post );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'read_post' === $cap;
			}
		);

		$result = $this->tool->execute( array( 'post_id' => 8 ) );

		$this->assertSame( '', $result['content'] );
		$this->assertSame( '', $result['excerpt'] );
		$this->assertSame( 'Title', $result['title'] );
	}

	public function test_revision_without_edit_post_returns_not_found_shape(): void {
		Functions\expect( 'get_post' )->once()->with( 99 )->andReturn( null );
		$missing = $this->tool->execute( array( 'post_id' => 99 ) );

		$revision            = $this->make_post( 99 );
		$revision->post_type = 'revision';
		Functions\expect( 'get_post' )->once()->with( 99 )->andReturn( $revision );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'read_post' === $cap;
			}
		);
		Functions\expect( 'get_post_meta' )->never();

		$this->assertSame( $missing, $this->tool->execute( array( 'post_id' => 99 ) ) );
	}

	public function test_revision_with_edit_post_returns_content(): void {
		$revision            = $this->make_post( 12 );
		$revision->post_type = 'revision';
		Functions\expect( 'get_post' )->with( 12 )->andReturn( $revision );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'current_user_can' )->justReturn( true );

		$result = $this->tool->execute( array( 'post_id' => 12, 'include_meta' => false ) );

		$this->assertSame( 'Body', $result['content'] );
	}

	public function test_published_public_custom_post_type_is_readable_without_type_edit_cap(): void {
		$post            = $this->make_post( 12 );
		$post->post_type = 'product';
		Functions\expect( 'get_post' )->with( 12 )->andReturn( $post );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'get_post_type_object' )->justReturn( $this->type_object( 'edit_products' ) );
		Functions\when( 'is_post_type_viewable' )->justReturn( true );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'read_post' === $cap;
			}
		);

		$result = $this->tool->execute( array( 'post_id' => 12 ) );

		$this->assertSame( 'product', $result['post_type'] );
		$this->assertSame( 'Body', $result['content'] );
	}

	public function test_non_public_post_type_without_edit_cap_returns_not_found_shape(): void {
		Functions\expect( 'get_post' )->once()->with( 99 )->andReturn( null );
		$missing = $this->tool->execute( array( 'post_id' => 99 ) );

		$template            = $this->make_post( 99 );
		$template->post_type = 'wp_template';
		Functions\expect( 'get_post' )->once()->with( 99 )->andReturn( $template );
		Functions\when( 'get_post_type_object' )->justReturn( $this->type_object( 'edit_theme_options' ) );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'edit_theme_options' !== $cap;
			}
		);

		$this->assertSame( $missing, $this->tool->execute( array( 'post_id' => 99 ) ) );
	}

	public function test_non_public_post_type_with_edit_cap_returns_content(): void {
		$template            = $this->make_post( 13 );
		$template->post_type = 'wp_template';
		Functions\expect( 'get_post' )->with( 13 )->andReturn( $template );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'get_post_type_object' )->justReturn( $this->type_object( 'edit_theme_options' ) );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'current_user_can' )->justReturn( true );

		$result = $this->tool->execute( array( 'post_id' => 13, 'include_meta' => false ) );

		$this->assertSame( 'Body', $result['content'] );
	}

	private function type_object( string $edit_posts_cap ): \stdClass {
		$type                  = new \stdClass();
		$type->cap             = new \stdClass();
		$type->cap->edit_posts = $edit_posts_cap;
		return $type;
	}

	private function make_post( int $id ): \stdClass {
		$post                = new \stdClass();
		$post->ID            = $id;
		$post->post_title    = 'Title';
		$post->post_content  = 'Body';
		$post->post_excerpt  = 'Excerpt';
		$post->post_status   = 'publish';
		$post->post_type     = 'post';
		$post->post_author   = 1;
		$post->post_date     = '2026-01-01 00:00:00';
		$post->post_modified = '2026-01-01 00:00:00';
		$post->post_name     = 'title';
		$post->post_password = '';
		return $post;
	}
}
