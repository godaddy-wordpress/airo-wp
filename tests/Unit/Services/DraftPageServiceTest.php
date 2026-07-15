<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Services;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

// Minimal WP_Error stub for unit tests when WordPress is not loaded.
if ( ! class_exists( 'WP_Error' ) ) {
	// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
	class WP_Error_Stub {

		private string $code;
		private string $message;

		public function __construct( string $code = '', string $message = '' ) {
			$this->code    = $code;
			$this->message = $message;
		}

		public function get_error_code(): string {
			return $this->code;
		}

		public function get_error_message(): string {
			return $this->message;
		}
	}

	class_alias( __NAMESPACE__ . '\\WP_Error_Stub', 'WP_Error' );
}

final class DraftPageServiceTest extends TestCase {

	private DraftPageService $service;

	protected function setUp(): void {
		parent::setUp();

		Functions\when( '__' )->returnArg();
		Functions\when( 'apply_filters' )->returnArg();

		$this->service = new DraftPageService();
	}

	public function test_has_draft_returns_false_when_no_meta(): void {
		Functions\expect( 'get_post_meta' )
			->once()
			->with( 42, DraftPageService::META_HAS_DRAFT, true )
			->andReturn( '' );

		$this->assertFalse( $this->service->has_draft( 42 ) );
	}

	public function test_has_draft_returns_false_when_draft_not_actually_draft_status(): void {
		$draft              = new \stdClass();
		$draft->ID          = 100;
		$draft->post_status = 'publish';

		Functions\expect( 'get_post_meta' )
			->once()
			->with( 42, DraftPageService::META_HAS_DRAFT, true )
			->andReturn( '100' );

		Functions\expect( 'get_post' )
			->once()
			->with( 100 )
			->andReturn( $draft );

		$this->assertFalse( $this->service->has_draft( 42 ) );
	}

	public function test_has_draft_returns_true_when_valid_draft_exists(): void {
		$draft              = new \stdClass();
		$draft->ID          = 100;
		$draft->post_status = 'draft';

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key, $single ) {
				if ( 42 === $post_id && DraftPageService::META_HAS_DRAFT === $key ) {
					return '100';
				}
				if ( 100 === $post_id && DraftPageService::META_DRAFT_OF === $key ) {
					return '42';
				}
				return '';
			}
		);

		Functions\expect( 'get_post' )
			->with( 100 )
			->andReturn( $draft );

		$this->assertTrue( $this->service->has_draft( 42 ) );
	}

	public function test_get_draft_returns_null_when_no_draft(): void {
		Functions\expect( 'get_post_meta' )
			->once()
			->with( 42, DraftPageService::META_HAS_DRAFT, true )
			->andReturn( '' );

		$this->assertNull( $this->service->get_draft( 42 ) );
	}

	public function test_publish_draft_fails_when_post_not_found(): void {
		Functions\expect( 'get_post' )
			->once()
			->with( 99 )
			->andReturn( null );

		$result = $this->service->publish_draft( 99 );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'draft_not_found', $result->get_error_code() );
	}

	public function test_publish_draft_fails_when_not_page_type(): void {
		$draft              = new \stdClass();
		$draft->ID          = 50;
		$draft->post_type   = 'post';
		$draft->post_status = 'draft';

		Functions\expect( 'get_post' )
			->once()
			->with( 50 )
			->andReturn( $draft );

		$result = $this->service->publish_draft( 50 );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'not_a_page', $result->get_error_code() );
	}

	public function test_discard_draft_fails_when_post_not_found(): void {
		Functions\expect( 'get_post' )
			->once()
			->with( 99 )
			->andReturn( null );

		$result = $this->service->discard_draft( 99 );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'draft_not_found', $result->get_error_code() );
	}

	public function test_discard_draft_fails_when_not_draft_status(): void {
		$page              = new \stdClass();
		$page->ID          = 60;
		$page->post_type   = 'page';
		$page->post_status = 'publish';

		Functions\expect( 'get_post' )
			->once()
			->with( 60 )
			->andReturn( $page );

		$result = $this->service->discard_draft( 60 );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'not_a_draft', $result->get_error_code() );
	}

	public function test_publish_draft_trashes_draft_when_force_delete_false(): void {
		$draft_id   = 200;
		$original_id = 100;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';
		$draft->post_content          = 'content';
		$draft->post_title            = 'title';
		$draft->post_excerpt          = '';
		$draft->menu_order            = 0;
		$draft->post_password         = '';
		$draft->comment_status        = 'open';
		$draft->ping_status           = 'open';
		$draft->post_content_filtered = '';

		$original              = new \stdClass();
		$original->ID          = $original_id;
		$original->post_status = 'publish';

		Functions\expect( 'get_post' )
			->twice()
			->andReturnValues( array( $draft, $original ) );

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key = null, $single = false ) use ( $draft_id, $original_id ) {
				if ( $post_id === $draft_id && $key === DraftPageService::META_DRAFT_OF ) {
					return (string) $original_id;
				}
				if ( $key === '_thumbnail_id' ) {
					return '';
				}
				// replace_post_meta: get_post_meta( $source_id ) with no key returns array.
				if ( null === $key ) {
					return array();
				}
				return '';
			}
		);

		Functions\expect( 'wp_update_post' )
			->once()
			->andReturn( $original_id );

		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'delete_post_meta' )->justReturn( true );
		Functions\when( 'update_post_meta' )->justReturn( true );
		Functions\when( 'do_action' )->justReturn( null );
		Functions\when( 'apply_filters' )->returnArg( 2 );

		Functions\expect( 'wp_trash_post' )
			->once()
			->with( $draft_id )
			->andReturn( new \WP_Post() );

		Functions\expect( 'wp_delete_post' )->never();

		$result = $this->service->publish_draft( $draft_id );

		$this->assertSame( true, $result );
	}

	public function test_publish_draft_permanently_deletes_when_force_delete_true(): void {
		$draft_id    = 200;
		$original_id = 100;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';
		$draft->post_content          = 'content';
		$draft->post_title            = 'title';
		$draft->post_excerpt          = '';
		$draft->menu_order            = 0;
		$draft->post_password         = '';
		$draft->comment_status        = 'open';
		$draft->ping_status           = 'open';
		$draft->post_content_filtered = '';

		$original              = new \stdClass();
		$original->ID          = $original_id;
		$original->post_status = 'publish';

		Functions\expect( 'get_post' )
			->twice()
			->andReturnValues( array( $draft, $original ) );

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key = null, $single = false ) use ( $draft_id, $original_id ) {
				if ( $post_id === $draft_id && $key === DraftPageService::META_DRAFT_OF ) {
					return (string) $original_id;
				}
				if ( $key === '_thumbnail_id' ) {
					return '';
				}
				if ( null === $key ) {
					return array();
				}
				return '';
			}
		);

		Functions\expect( 'wp_update_post' )
			->once()
			->andReturn( $original_id );

		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'delete_post_meta' )->justReturn( true );
		Functions\when( 'update_post_meta' )->justReturn( true );
		Functions\when( 'do_action' )->justReturn( null );
		Functions\when( 'apply_filters' )->returnArg( 2 );

		Functions\expect( 'wp_delete_post' )
			->once()
			->with( $draft_id, true );

		Functions\expect( 'wp_trash_post' )->never();

		$result = $this->service->publish_draft( $draft_id, true );

		$this->assertSame( true, $result );
	}

	public function test_publish_draft_returns_error_when_trash_fails(): void {
		$draft_id    = 200;
		$original_id = 100;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';
		$draft->post_content          = 'content';
		$draft->post_title            = 'title';
		$draft->post_excerpt          = '';
		$draft->menu_order            = 0;
		$draft->post_password         = '';
		$draft->comment_status        = 'open';
		$draft->ping_status           = 'open';
		$draft->post_content_filtered = '';

		$original              = new \stdClass();
		$original->ID          = $original_id;
		$original->post_status = 'publish';

		Functions\expect( 'get_post' )
			->twice()
			->andReturnValues( array( $draft, $original ) );

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key = null, $single = false ) use ( $draft_id, $original_id ) {
				if ( $post_id === $draft_id && $key === DraftPageService::META_DRAFT_OF ) {
					return (string) $original_id;
				}
				if ( $key === '_thumbnail_id' ) {
					return '';
				}
				if ( null === $key ) {
					return array();
				}
				return '';
			}
		);

		Functions\expect( 'wp_update_post' )
			->once()
			->andReturn( $original_id );

		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'delete_post_meta' )->justReturn( true );
		Functions\when( 'update_post_meta' )->justReturn( true );
		Functions\when( 'apply_filters' )->returnArg( 2 );

		Functions\expect( 'wp_trash_post' )
			->once()
			->andReturn( null );

		$result = $this->service->publish_draft( $draft_id );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'trash_failed', $result->get_error_code() );
	}

	public function test_discard_draft_trashes_draft_when_force_delete_false(): void {
		$draft_id    = 201;
		$original_id = 101;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';

		Functions\expect( 'get_post' )
			->once()
			->with( $draft_id )
			->andReturn( $draft );

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $draft_id, DraftPageService::META_DRAFT_OF, true )
			->andReturn( (string) $original_id );

		Functions\when( 'delete_post_meta' )->justReturn( true );

		Functions\expect( 'wp_trash_post' )
			->once()
			->with( $draft_id )
			->andReturn( new \WP_Post() );

		Functions\expect( 'wp_delete_post' )->never();

		Functions\when( 'do_action' )->justReturn( null );

		$result = $this->service->discard_draft( $draft_id );

		$this->assertSame( true, $result );
	}

	public function test_discard_draft_permanently_deletes_when_force_delete_true(): void {
		$draft_id    = 201;
		$original_id = 101;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';

		Functions\expect( 'get_post' )
			->once()
			->with( $draft_id )
			->andReturn( $draft );

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $draft_id, DraftPageService::META_DRAFT_OF, true )
			->andReturn( (string) $original_id );

		Functions\when( 'delete_post_meta' )->justReturn( true );

		Functions\expect( 'wp_delete_post' )
			->once()
			->with( $draft_id, true );

		Functions\expect( 'wp_trash_post' )->never();

		Functions\when( 'do_action' )->justReturn( null );

		$result = $this->service->discard_draft( $draft_id, true );

		$this->assertSame( true, $result );
	}

	public function test_discard_draft_returns_error_when_trash_fails(): void {
		$draft_id    = 201;
		$original_id = 101;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';

		Functions\expect( 'get_post' )
			->once()
			->with( $draft_id )
			->andReturn( $draft );

		Functions\expect( 'get_post_meta' )
			->once()
			->with( $draft_id, DraftPageService::META_DRAFT_OF, true )
			->andReturn( (string) $original_id );

		Functions\when( 'delete_post_meta' )->justReturn( true );

		Functions\expect( 'wp_trash_post' )
			->once()
			->andReturn( null );

		$result = $this->service->discard_draft( $draft_id );

		$this->assertInstanceOf( \WP_Error::class, $result );
		$this->assertSame( 'trash_failed', $result->get_error_code() );
	}
}
