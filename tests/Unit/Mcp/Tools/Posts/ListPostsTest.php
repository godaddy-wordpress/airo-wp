<?php
/**
 * ListPosts unit tests.
 *
 * The WP_Query stub (with $seed / $last_args support) is defined in tests/bootstrap.php.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\ListPosts;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class ListPostsTest extends TestCase {

	private ListPosts $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		\WP_Query::$seed      = array();
		\WP_Query::$last_args = null;
		unset( $GLOBALS['__lp_current_post'] );

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'sanitize_key' )->returnArg();
		Functions\when( 'sanitize_title' )->returnArg();
		Functions\when( 'get_post' )->alias( fn() => $GLOBALS['__lp_current_post'] ?? null );
		Functions\when( 'wp_reset_postdata' )->justReturn( null );
		Functions\when( 'apply_filters' )->alias( fn( $tag, $value = null ) => $value );
		Functions\when( 'get_post_meta' )->justReturn( array() );
		Functions\when( 'wp_trim_words' )->returnArg();

		$this->tool = new ListPosts();
	}

	/**
	 * Build a minimal WP_Post stub with the fields build_post_data() reads.
	 *
	 * @param int    $id    Post ID.
	 * @param string $title Post title.
	 * @return \WP_Post
	 */
	private function make_post( int $id, string $title ): \WP_Post {
		return new \WP_Post(
			array(
				'ID'            => $id,
				'post_title'    => $title,
				'post_name'     => 'post-' . $id,
				'post_status'   => 'publish',
				'post_parent'   => 0,
				'menu_order'    => 0,
				'post_content'  => 'content',
				'post_excerpt'  => '',
				'post_author'   => '1',
				'post_date'     => '2025-01-01 00:00:00',
				'post_modified' => '2025-02-01 00:00:00',
			)
		);
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/list-posts', ListPosts::TOOL_ID );
	}

	public function test_empty_result_returns_pagination_structure(): void {
		$result = $this->tool->execute( array() );

		$this->assertArrayHasKey( 'posts', $result );
		$this->assertArrayHasKey( 'total', $result );
		$this->assertArrayHasKey( 'total_pages', $result );
		$this->assertSame( array(), $result['posts'] );
		$this->assertSame( 0, $result['total'] );
		$this->assertSame( 1, $result['total_pages'] );
	}

	public function test_build_query_args_adds_meta_query_for_featured_media_id(): void {
		$reflection = new \ReflectionClass( $this->tool );
		$method     = $reflection->getMethod( 'build_query_args' );
		$method->setAccessible( true );

		$args = $method->invoke( $this->tool, array( 'featured_media_id' => 42 ) );

		$this->assertArrayHasKey( 'meta_query', $args );
		$this->assertCount( 1, $args['meta_query'] );

		$clause = $args['meta_query'][0];
		$this->assertSame( '_thumbnail_id', $clause['key'] );
		$this->assertSame( 42, $clause['value'] );
		$this->assertSame( '=', $clause['compare'] );
		$this->assertSame( 'NUMERIC', $clause['type'] );
	}

	public function test_build_query_args_skips_meta_query_when_featured_media_id_is_zero(): void {
		$reflection = new \ReflectionClass( $this->tool );
		$method     = $reflection->getMethod( 'build_query_args' );
		$method->setAccessible( true );

		$args = $method->invoke( $this->tool, array( 'featured_media_id' => 0 ) );

		$this->assertArrayNotHasKey( 'meta_query', $args );
	}

	public function test_featured_media_id_output_with_and_without_thumbnail(): void {
		\WP_Query::$seed = array(
			$this->make_post( 10, 'Has thumb' ),
			$this->make_post( 11, 'No thumb' ),
		);

		$thumb_map = array( 10 => 99, 11 => 0 );
		Functions\when( 'get_post_thumbnail_id' )->alias( fn( $id ) => $thumb_map[ $id ] ?? 0 );

		$result = $this->tool->execute( array( 'post_type' => 'page', '_fields' => array( 'id', 'featured_media_id' ) ) );

		$this->assertSame( 99, $result['posts'][0]['featured_media_id'] );
		$this->assertSame( 0, $result['posts'][1]['featured_media_id'] );
	}

	public function test_featured_media_id_lookup_skipped_when_field_not_requested(): void {
		\WP_Query::$seed = array(
			$this->make_post( 1, 'A' ),
			$this->make_post( 2, 'B' ),
		);

		Functions\expect( 'get_post_thumbnail_id' )->never();

		$result = $this->tool->execute( array( 'post_type' => 'page', '_fields' => array( 'id', 'title' ) ) );

		$this->assertArrayNotHasKey( 'featured_media_id', $result['posts'][0] );
		$this->assertArrayNotHasKey( 'featured_media_id', $result['posts'][1] );
	}

	public function test_featured_media_id_filter_with_nonexistent_attachment_returns_no_matches(): void {
		\WP_Query::$seed = array();

		$result = $this->tool->execute( array( 'post_type' => 'post', 'featured_media_id' => 999, '_fields' => array( 'id' ) ) );

		$this->assertSame( array(), $result['posts'] );
		$this->assertSame( 0, $result['total'] );
		$this->assertSame( 999, \WP_Query::$last_args['meta_query'][0]['value'] );
	}
}
