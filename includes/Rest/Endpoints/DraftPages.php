<?php
/**
 * DraftPages REST endpoint — publish/discard page drafts.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Rest\Endpoints;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;

/**
 * REST controller for page draft publish and discard operations.
 */
class DraftPages {

	/**
	 * REST namespace.
	 */
	private const NAMESPACE = 'airo-wp/v1';

	/**
	 * Draft page service instance.
	 *
	 * @var DraftPageService
	 */
	private DraftPageService $service;

	/**
	 * Constructor.
	 *
	 * @param DraftPageService $service Draft page service.
	 */
	public function __construct( DraftPageService $service ) {
		$this->service = $service;
	}

	/**
	 * Register REST routes for publish and discard.
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/drafts/(?P<id>\d+)/publish',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'publish_draft' ),
				'permission_callback' => array( $this, 'publish_permissions_check' ),
				'args'                => array(
					'id' => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/drafts/(?P<id>\d+)/discard',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'discard_draft' ),
				'permission_callback' => array( $this, 'discard_permissions_check' ),
				'args'                => array(
					'id' => array(
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);
	}

	/**
	 * Publish a page draft.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error Response on success, WP_Error on failure.
	 */
	public function publish_draft( \WP_REST_Request $request ) {
		$draft_id    = (int) $request->get_param( 'id' );
		$original_id = (int) get_post_meta( $draft_id, DraftPageService::META_DRAFT_OF, true );

		if ( empty( $original_id ) ) {
			return new \WP_Error(
				'missing_original_link',
				__( 'Draft is not linked to an original page.', 'airo-wp' ),
				array( 'status' => 400 )
			);
		}

		$result = $this->service->publish_draft( $draft_id );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_REST_Response(
			array(
				'success'      => true,
				'original_id'  => $original_id,
				'redirect_url' => get_edit_post_link( $original_id, 'raw' ),
			),
			200
		);
	}

	/**
	 * Discard a page draft.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response|\WP_Error Response on success, WP_Error on failure.
	 */
	public function discard_draft( \WP_REST_Request $request ) {
		$draft_id    = (int) $request->get_param( 'id' );
		$original_id = (int) get_post_meta( $draft_id, DraftPageService::META_DRAFT_OF, true );

		$result = $this->service->discard_draft( $draft_id );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$redirect_url = null;

		if ( ! empty( $original_id ) && get_post( $original_id ) ) {
			$redirect_url = get_edit_post_link( $original_id, 'raw' );
		}

		return new \WP_REST_Response(
			array(
				'success'      => true,
				'original_id'  => $original_id,
				'redirect_url' => $redirect_url,
			),
			200
		);
	}

	/**
	 * Check permissions for publishing a draft.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return bool True if the user can publish this draft.
	 */
	public function publish_permissions_check( \WP_REST_Request $request ): bool {
		$draft_id    = (int) $request->get_param( 'id' );
		$original_id = (int) get_post_meta( $draft_id, DraftPageService::META_DRAFT_OF, true );

		if ( empty( $original_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_post', $original_id ) ) {
			return false;
		}

		return current_user_can( 'edit_post', $draft_id );
	}

	/**
	 * Check permissions for discarding a draft.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return bool True if the user can discard this draft.
	 */
	public function discard_permissions_check( \WP_REST_Request $request ): bool {
		$draft_id    = (int) $request->get_param( 'id' );
		$original_id = (int) get_post_meta( $draft_id, DraftPageService::META_DRAFT_OF, true );

		if ( ! empty( $original_id ) && ! current_user_can( 'edit_post', $original_id ) ) {
			return false;
		}

		return current_user_can( 'delete_post', $draft_id );
	}
}
