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

		Functions\expect( 'get_post' )->with( 5 )->andReturn( $post );
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

		Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
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

		$meta_data = array( '_custom_key' => array( 'custom_value' ) );

		Functions\expect( 'get_post' )->with( 7 )->andReturn( $post );
		Functions\expect( 'get_post_thumbnail_id' )->with( 7 )->andReturn( 0 );
		Functions\expect( 'get_post_meta' )->with( 7 )->andReturn( $meta_data );

		$result = $this->tool->execute( array( 'post_id' => 7, 'include_meta' => true ) );

		$this->assertSame( $meta_data, $result['meta'] );
		$this->assertSame( 0, $result['featured_media_id'] );
	}
}
