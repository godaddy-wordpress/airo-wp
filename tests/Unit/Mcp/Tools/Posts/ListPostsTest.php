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

	/**
	 * Resolve every post type as registered and viewable, so can_read_post() passes it.
	 */
	private function stub_viewable_post_types(): void {
		$type      = new \stdClass();
		$type->cap = (object) array( 'edit_posts' => 'edit_posts' );
		Functions\when( 'get_post_type_object' )->justReturn( $type );
		Functions\when( 'is_post_type_viewable' )->justReturn( true );
	}

	public function test_non_viewable_items_without_type_edit_cap_are_skipped(): void {
		$visible         = $this->make_post( 1, 'Visible' );
		$nav             = $this->make_post( 2, 'Nav' );
		$nav->post_type  = 'wp_navigation';
		\WP_Query::$seed = array( $visible, $nav );

		Functions\when( 'get_post_type_object' )->alias(
			function ( $post_type ) {
				$type      = new \stdClass();
				$type->cap = (object) array( 'edit_posts' => 'wp_navigation' === $post_type ? 'edit_theme_options' : 'edit_posts' );
				return $type;
			}
		);
		Functions\when( 'is_post_type_viewable' )->alias(
			fn( $type ) => 'edit_theme_options' !== $type->cap->edit_posts
		);
		Functions\when( 'current_user_can' )->alias(
			fn( $cap ) => 'edit_theme_options' !== $cap
		);

		$result = $this->tool->execute( array( 'post_type' => 'page', '_fields' => array( 'id' ) ) );

		$this->assertCount( 1, $result['posts'] );
		$this->assertSame( 1, $result['posts'][0]['id'] );
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/list-posts', ListPosts::TOOL_ID );
	}

	public function test_empty_result_returns_pagination_structure(): void {
		$this->stub_viewable_post_types();
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
		$this->stub_viewable_post_types();
		\WP_Query::$seed = array(
			$this->make_post( 10, 'Has thumb' ),
			$this->make_post( 11, 'No thumb' ),
		);

		$thumb_map = array( 10 => 99, 11 => 0 );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_post_thumbnail_id' )->alias( fn( $id ) => $thumb_map[ $id ] ?? 0 );

		$result = $this->tool->execute( array( 'post_type' => 'page', '_fields' => array( 'id', 'featured_media_id' ) ) );

		$this->assertSame( 99, $result['posts'][0]['featured_media_id'] );
		$this->assertSame( 0, $result['posts'][1]['featured_media_id'] );
	}

	public function test_featured_media_id_lookup_skipped_when_field_not_requested(): void {
		$this->stub_viewable_post_types();
		\WP_Query::$seed = array(
			$this->make_post( 1, 'A' ),
			$this->make_post( 2, 'B' ),
		);

		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'get_post_thumbnail_id' )->never();

		$result = $this->tool->execute( array( 'post_type' => 'page', '_fields' => array( 'id', 'title' ) ) );

		$this->assertArrayNotHasKey( 'featured_media_id', $result['posts'][0] );
		$this->assertArrayNotHasKey( 'featured_media_id', $result['posts'][1] );
	}

	public function test_featured_media_id_filter_with_nonexistent_attachment_returns_no_matches(): void {
		$this->stub_viewable_post_types();
		\WP_Query::$seed = array();

		$result = $this->tool->execute( array( 'post_type' => 'post', 'featured_media_id' => 999, '_fields' => array( 'id' ) ) );

		$this->assertSame( array(), $result['posts'] );
		$this->assertSame( 0, $result['total'] );
		$this->assertSame( 999, \WP_Query::$last_args['meta_query'][0]['value'] );
	}

	public function test_non_public_status_denied_without_edit_others_posts(): void {
		$type_obj                         = new \stdClass();
		$type_obj->cap                    = new \stdClass();
		$type_obj->cap->edit_others_posts = 'edit_others_posts';

		Functions\expect( 'get_post_type_object' )->with( 'page' )->andReturn( $type_obj );
		Functions\when( 'is_post_type_viewable' )->justReturn( true );
		Functions\expect( 'current_user_can' )->with( 'edit_others_posts' )->andReturn( false );

		$result = $this->tool->execute( array( 'status' => 'private' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission', $result['message'] );
	}

	public function test_invalid_custom_post_type_returns_error(): void {
		Functions\expect( 'get_post_type_object' )->with( 'nonexistent_type' )->andReturn( null );

		$result = $this->tool->execute( array( 'post_type' => 'nonexistent_type' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Invalid post type', $result['message'] );
	}

	public function test_custom_post_type_without_permission_returns_error(): void {
		$type_obj              = new \stdClass();
		$type_obj->cap         = new \stdClass();
		$type_obj->cap->edit_posts = 'edit_products';

		Functions\expect( 'get_post_type_object' )->with( 'product' )->andReturn( $type_obj );
		Functions\expect( 'current_user_can' )->with( 'edit_products' )->andReturn( false );

		$result = $this->tool->execute( array( 'post_type' => 'product' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission', $result['message'] );
	}

	public function test_unreadable_posts_are_skipped(): void {
		$this->stub_viewable_post_types();
		\WP_Query::$seed = array(
			$this->make_post( 1, 'Visible' ),
			$this->make_post( 2, 'Hidden' ),
		);

		Functions\when( 'current_user_can' )->alias(
			fn( $cap, $id = null ) => ! ( 'read_post' === $cap && 2 === $id )
		);

		$result = $this->tool->execute( array( 'post_type' => 'page', '_fields' => array( 'id' ) ) );

		$this->assertCount( 1, $result['posts'] );
		$this->assertSame( 1, $result['posts'][0]['id'] );
		$this->assertSame( 2, $result['total'] );
	}

	public function test_meta_requires_edit_post(): void {
		$this->stub_viewable_post_types();
		\WP_Query::$seed = array( $this->make_post( 1, 'A' ) );

		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'current_user_can' )->alias( fn( $cap ) => 'edit_post' !== $cap );
		Functions\when( 'get_post_meta' )->alias(
			function () {
				throw new \RuntimeException( 'get_post_meta must not be called' );
			}
		);

		$result = $this->tool->execute( array( 'post_type' => 'page', 'include_meta' => true ) );

		$this->assertCount( 1, $result['posts'] );
		$this->assertArrayNotHasKey( 'meta', $result['posts'][0] );
	}

	public function test_password_protected_content_hidden_without_edit_post(): void {
		$this->stub_viewable_post_types();
		$post                = $this->make_post( 1, 'A' );
		$post->post_password = 'hunter2';
		$post->post_excerpt  = 'secret excerpt';
		\WP_Query::$seed     = array( $post );

		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'current_user_can' )->alias( fn( $cap ) => 'edit_post' !== $cap );

		$result = $this->tool->execute( array( 'post_type' => 'page' ) );

		$this->assertSame( '', $result['posts'][0]['content'] );
		$this->assertSame( '', $result['posts'][0]['excerpt'] );
	}

	public function test_editor_sees_meta_and_content(): void {
		$this->stub_viewable_post_types();
		$post                = $this->make_post( 1, 'A' );
		$post->post_password = 'hunter2';
		\WP_Query::$seed     = array( $post );

		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_post_meta' )->justReturn( array( 'k' => array( 'v' ) ) );

		$result = $this->tool->execute( array( 'post_type' => 'page', 'include_meta' => true ) );

		$this->assertSame( 'content', $result['posts'][0]['content'] );
		$this->assertSame( array( 'k' => array( 'v' ) ), $result['posts'][0]['meta'] );
	}

	public function test_revisions_without_edit_post_are_skipped(): void {
		$first            = $this->make_post( 1, 'Rev A' );
		$first->post_type = 'revision';
		$second            = $this->make_post( 2, 'Rev B' );
		$second->post_type = 'revision';
		\WP_Query::$seed   = array( $first, $second );

		$type      = new \stdClass();
		$type->cap = (object) array( 'edit_posts' => 'edit_posts' );
		Functions\when( 'get_post_type_object' )->justReturn( $type );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'current_user_can' )->alias(
			fn( $cap, $id = null ) => ! ( 'edit_post' === $cap && 2 === $id )
		);

		$result = $this->tool->execute( array( 'post_type' => 'revision', '_fields' => array( 'id' ) ) );

		$this->assertCount( 1, $result['posts'] );
		$this->assertSame( 1, $result['posts'][0]['id'] );
	}

	public function test_non_viewable_builtin_type_without_type_edit_cap_returns_error(): void {
		$type      = new \stdClass();
		$type->cap = (object) array( 'edit_posts' => 'edit_theme_options' );

		Functions\expect( 'get_post_type_object' )->with( 'nav_menu_item' )->andReturn( $type );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'current_user_can' )->alias( fn( $cap ) => 'edit_theme_options' !== $cap );

		$result = $this->tool->execute( array( 'post_type' => 'nav_menu_item' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission to query this post type', $result['message'] );
		$this->assertNull( \WP_Query::$last_args );
	}

	public function test_builtin_type_filtered_to_non_viewable_requires_type_edit_cap(): void {
		$type      = new \stdClass();
		$type->cap = (object) array( 'edit_posts' => 'edit_restricted_pages' );

		Functions\when( 'get_post_type_object' )->justReturn( $type );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'current_user_can' )->alias( fn( $cap ) => 'edit_restricted_pages' !== $cap );

		$result = $this->tool->execute( array( 'post_type' => 'page' ) );

		$this->assertFalse( $result['success'] );
		$this->assertNull( \WP_Query::$last_args );
	}

	public function test_non_viewable_builtin_type_with_type_edit_cap_is_queried(): void {
		$type      = new \stdClass();
		$type->cap = (object) array( 'edit_posts' => 'edit_theme_options' );

		Functions\when( 'get_post_type_object' )->justReturn( $type );
		Functions\when( 'is_post_type_viewable' )->justReturn( false );
		Functions\when( 'current_user_can' )->justReturn( true );

		$result = $this->tool->execute( array( 'post_type' => 'nav_menu_item' ) );

		$this->assertArrayHasKey( 'posts', $result );
		$this->assertSame( 'nav_menu_item', \WP_Query::$last_args['post_type'] );
	}

	public function test_parents_are_primed_before_per_item_checks(): void {
		$this->stub_viewable_post_types();
		$first              = $this->make_post( 1, 'A' );
		$first->post_parent = 10;
		$second              = $this->make_post( 2, 'B' );
		$second->post_parent = 11;
		\WP_Query::$seed    = array( $first, $second );

		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( '_prime_post_caches' )->once()->with( array( 10, 11 ), false, false );

		$result = $this->tool->execute( array( 'post_type' => 'attachment', '_fields' => array( 'id' ) ) );

		$this->assertCount( 2, $result['posts'] );
	}

	public function test_parents_are_not_primed_for_types_that_do_not_read_them(): void {
		$this->stub_viewable_post_types();
		$child              = $this->make_post( 1, 'Child page' );
		$child->post_parent = 10;
		\WP_Query::$seed    = array( $child );

		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( '_prime_post_caches' )->never();

		$result = $this->tool->execute( array( 'post_type' => 'page', '_fields' => array( 'id', 'parent_id' ) ) );

		$this->assertSame( 10, $result['posts'][0]['parent_id'] );
	}
}
