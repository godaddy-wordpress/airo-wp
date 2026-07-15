<?php
/**
 * DraftPages REST endpoint tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

// Minimal WP stubs for unit tests when WordPress is not loaded.
// Must be declared in the global namespace before any namespace {} block.
namespace {
	if ( ! class_exists( 'WP_REST_Response' ) ) {
		// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
		class WP_REST_Response {
			/** @var mixed */
			public $data;
			public int $status;

			/** @param mixed $data */
			public function __construct( $data = null, int $status = 200 ) {
				$this->data   = $data;
				$this->status = $status;
			}
		}
	}
}

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Rest\Endpoints {

	use Brain\Monkey\Functions;
	use GoDaddy\WordPress\Plugins\AiroWp\Rest\Endpoints\DraftPages;
	use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;
	use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;
	use Mockery;

	/**
	 * Tests for DraftPages REST endpoint.
	 */
	final class DraftPagesTest extends TestCase {

		protected function setUp(): void {
			parent::setUp();
			Functions\when( '__' )->returnArg();
		}

		/**
		 * Test that constructor accepts a DraftPageService instance.
		 */
		public function test_constructor_accepts_service(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$this->assertInstanceOf( DraftPages::class, $endpoint );
		}

		/**
		 * Test that register_routes calls register_rest_route twice.
		 */
		public function test_register_routes_calls_register_rest_route(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			Functions\expect( 'register_rest_route' )
				->twice();

			$endpoint->register_routes();

			$this->addToAssertionCount( 1 );
		}

		// -------------------------------------------------------------------------
		// publish_draft
		// -------------------------------------------------------------------------

		/**
		 * publish_draft returns WP_Error when draft has no original link.
		 */
		public function test_publish_draft_returns_error_when_no_original_link(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 42 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 42, DraftPageService::META_DRAFT_OF, true )
				->andReturn( '' );

			$result = $endpoint->publish_draft( $request );

			$this->assertInstanceOf( \WP_Error::class, $result );
			$this->assertSame( 'missing_original_link', $result->get_error_code() );
		}

		/**
		 * publish_draft returns service WP_Error on failure.
		 */
		public function test_publish_draft_returns_service_error(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 42 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 42, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 10 );

			$wp_error = new \WP_Error( 'publish_failed', 'Could not publish.' );

			$service->shouldReceive( 'publish_draft' )
				->once()
				->with( 42 )
				->andReturn( $wp_error );

			Functions\expect( 'is_wp_error' )
				->once()
				->andReturn( true );

			$result = $endpoint->publish_draft( $request );

			$this->assertSame( $wp_error, $result );
		}

		/**
		 * publish_draft returns 200 response on success.
		 */
		public function test_publish_draft_returns_success_response(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 42 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 42, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 10 );

			$service->shouldReceive( 'publish_draft' )
				->once()
				->with( 42 )
				->andReturn( true );

			Functions\expect( 'is_wp_error' )
				->once()
				->andReturn( false );

			Functions\expect( 'get_edit_post_link' )
				->once()
				->with( 10, 'raw' )
				->andReturn( 'http://example.org/wp-admin/post.php?post=10&action=edit' );

			$result = $endpoint->publish_draft( $request );

			$this->assertInstanceOf( \WP_REST_Response::class, $result );
			$this->assertSame( 200, $result->status );
			$this->assertTrue( $result->data['success'] );
			$this->assertSame( 10, $result->data['original_id'] );
			$this->assertSame( 'http://example.org/wp-admin/post.php?post=10&action=edit', $result->data['redirect_url'] );
		}

		// -------------------------------------------------------------------------
		// discard_draft
		// -------------------------------------------------------------------------

		/**
		 * discard_draft returns service WP_Error on failure.
		 */
		public function test_discard_draft_returns_service_error(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 55 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 55, DraftPageService::META_DRAFT_OF, true )
				->andReturn( '' );

			$wp_error = new \WP_Error( 'discard_failed', 'Could not discard.' );

			$service->shouldReceive( 'discard_draft' )
				->once()
				->with( 55 )
				->andReturn( $wp_error );

			Functions\expect( 'is_wp_error' )
				->once()
				->andReturn( true );

			$result = $endpoint->discard_draft( $request );

			$this->assertSame( $wp_error, $result );
		}

		/**
		 * discard_draft returns success response with null redirect when no original.
		 */
		public function test_discard_draft_returns_success_with_no_original(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 55 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 55, DraftPageService::META_DRAFT_OF, true )
				->andReturn( '' );

			$service->shouldReceive( 'discard_draft' )
				->once()
				->with( 55 )
				->andReturn( true );

			Functions\expect( 'is_wp_error' )
				->once()
				->andReturn( false );

			$result = $endpoint->discard_draft( $request );

			$this->assertInstanceOf( \WP_REST_Response::class, $result );
			$this->assertSame( 200, $result->status );
			$this->assertTrue( $result->data['success'] );
			$this->assertSame( 0, $result->data['original_id'] );
			$this->assertNull( $result->data['redirect_url'] );
		}

		/**
		 * discard_draft returns success response with redirect when original exists.
		 */
		public function test_discard_draft_returns_success_with_original(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 55 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 55, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 20 );

			$service->shouldReceive( 'discard_draft' )
				->once()
				->with( 55 )
				->andReturn( true );

			Functions\expect( 'is_wp_error' )
				->once()
				->andReturn( false );

			$original     = new \stdClass();
			$original->ID = 20;

			Functions\expect( 'get_post' )
				->once()
				->with( 20 )
				->andReturn( $original );

			Functions\expect( 'get_edit_post_link' )
				->once()
				->with( 20, 'raw' )
				->andReturn( 'http://example.org/wp-admin/post.php?post=20&action=edit' );

			$result = $endpoint->discard_draft( $request );

			$this->assertInstanceOf( \WP_REST_Response::class, $result );
			$this->assertTrue( $result->data['success'] );
			$this->assertSame( 20, $result->data['original_id'] );
			$this->assertSame( 'http://example.org/wp-admin/post.php?post=20&action=edit', $result->data['redirect_url'] );
		}

		// -------------------------------------------------------------------------
		// publish_permissions_check
		// -------------------------------------------------------------------------

		/**
		 * publish_permissions_check returns false when no original link.
		 */
		public function test_publish_permissions_check_returns_false_when_no_original_link(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 42 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 42, DraftPageService::META_DRAFT_OF, true )
				->andReturn( '' );

			$this->assertFalse( $endpoint->publish_permissions_check( $request ) );
		}

		/**
		 * publish_permissions_check returns false when user cannot edit original.
		 */
		public function test_publish_permissions_check_returns_false_when_cannot_edit_original(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 42 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 42, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 10 );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 10 )
				->andReturn( false );

			$this->assertFalse( $endpoint->publish_permissions_check( $request ) );
		}

		/**
		 * publish_permissions_check returns false when user cannot edit draft.
		 */
		public function test_publish_permissions_check_returns_false_when_cannot_edit_draft(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 42 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 42, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 10 );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 10 )
				->andReturn( true );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 42 )
				->andReturn( false );

			$this->assertFalse( $endpoint->publish_permissions_check( $request ) );
		}

		/**
		 * publish_permissions_check returns true when user can edit both.
		 */
		public function test_publish_permissions_check_returns_true_when_authorised(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 42 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 42, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 10 );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 10 )
				->andReturn( true );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 42 )
				->andReturn( true );

			$this->assertTrue( $endpoint->publish_permissions_check( $request ) );
		}

		// -------------------------------------------------------------------------
		// discard_permissions_check
		// -------------------------------------------------------------------------

		/**
		 * discard_permissions_check returns false when cannot edit original.
		 */
		public function test_discard_permissions_check_returns_false_when_cannot_edit_original(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 55 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 55, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 20 );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 20 )
				->andReturn( false );

			$this->assertFalse( $endpoint->discard_permissions_check( $request ) );
		}

		/**
		 * discard_permissions_check returns false when cannot delete draft.
		 */
		public function test_discard_permissions_check_returns_false_when_cannot_delete_draft(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 55 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 55, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 20 );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 20 )
				->andReturn( true );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'delete_post', 55 )
				->andReturn( false );

			$this->assertFalse( $endpoint->discard_permissions_check( $request ) );
		}

		/**
		 * discard_permissions_check returns true when authorised.
		 */
		public function test_discard_permissions_check_returns_true_when_authorised(): void {
			$service  = Mockery::mock( DraftPageService::class );
			$endpoint = new DraftPages( $service );

			$request = Mockery::mock( 'WP_REST_Request' );
			$request->shouldReceive( 'get_param' )->with( 'id' )->andReturn( 55 );

			Functions\expect( 'get_post_meta' )
				->once()
				->with( 55, DraftPageService::META_DRAFT_OF, true )
				->andReturn( 20 );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'edit_post', 20 )
				->andReturn( true );

			Functions\expect( 'current_user_can' )
				->once()
				->with( 'delete_post', 55 )
				->andReturn( true );

			$this->assertTrue( $endpoint->discard_permissions_check( $request ) );
		}
	}
}
