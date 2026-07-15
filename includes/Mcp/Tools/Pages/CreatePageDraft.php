<?php
/**
 * CreatePageDraft MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;

/**
 * Registers and executes the create-page-draft MCP ability.
 */
class CreatePageDraft extends BaseTool {

	public const TOOL_ID = 'airo-wp/create-page-draft';

	/**
	 * Draft page service instance.
	 *
	 * @var DraftPageService
	 */
	private DraftPageService $draft_service;

	/**
	 * Constructor.
	 *
	 * @param DraftPageService $draft_service Draft page service.
	 */
	public function __construct( DraftPageService $draft_service ) {
		$this->draft_service = $draft_service;
	}

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_pages' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Create Page Draft', 'airo-wp' ),
				'description'         => __( 'Creates a draft copy of a published page for safe editing without affecting the live site.', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'content-management',
			)
		);
	}

	/**
	 * Execute the tool.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed> Execution result.
	 */
	public function execute( array $input ): array {
		$post_id = isset( $input['post_id'] ) ? (int) $input['post_id'] : 0;

		if ( $post_id <= 0 ) {
			return array(
				'success' => false,
				'message' => __( 'A valid post_id is required.', 'airo-wp' ),
			);
		}

		$post = get_post( $post_id );

		if ( ! $post ) {
			return array(
				'success' => false,
				'message' => __( 'Post not found.', 'airo-wp' ),
			);
		}

		if ( 'page' !== $post->post_type ) {
			return array(
				'success' => false,
				'message' => __( 'Drafts can only be created for pages (post_type=page).', 'airo-wp' ),
			);
		}

		if ( 'publish' !== $post->post_status ) {
			return array(
				'success' => false,
				'message' => __( 'A draft can only be created from a published page.', 'airo-wp' ),
			);
		}

		if ( $this->draft_service->has_draft( $post_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'A draft version already exists for this page.', 'airo-wp' ),
			);
		}

		$draft_data = array(
			'post_title'            => sanitize_text_field( isset( $input['title'] ) ? $input['title'] : $post->post_title ),
			// All MCP callers are admins who have unfiltered_html; wp_insert_post() skips kses for them.
			'post_content'          => wp_kses_post( isset( $input['content'] ) ? $input['content'] : $post->post_content ),
			'post_excerpt'          => sanitize_textarea_field( isset( $input['excerpt'] ) ? $input['excerpt'] : $post->post_excerpt ),
			'post_status'           => 'draft',
			'post_type'             => 'page',
			'post_author'           => get_current_user_id(),
			'post_parent'           => $post_id,
			'menu_order'            => $post->menu_order,
			'post_password'         => $post->post_password,
			'comment_status'        => $post->comment_status,
			'ping_status'           => $post->ping_status,
			'post_content_filtered' => $post->post_content_filtered,
		);

		$draft_id = wp_insert_post( wp_slash( $draft_data ), true );

		if ( is_wp_error( $draft_id ) ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Failed to create draft: %s', 'airo-wp' ),
					$draft_id->get_error_message()
				),
			);
		}

		// Generate unique slug.
		$base_slug = sanitize_title( $post->post_name . '-draft' );
		$slug      = wp_unique_post_slug( $base_slug, $draft_id, 'draft', 'page', $post_id );

		global $wpdb;

		if ( empty( $slug ) ) {
			$slug    = $base_slug;
			$counter = 2;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			while ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND ID != %d LIMIT 1", $slug, $draft_id ) ) ) {
				$slug = $base_slug . '-' . $counter;
				++$counter;
			}
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$updated = $wpdb->update(
			$wpdb->posts,
			array( 'post_name' => $slug ),
			array( 'ID' => $draft_id ),
			array( '%s' ),
			array( '%d' )
		);

		if ( false === $updated ) {
			wp_delete_post( $draft_id, true );

			return array(
				'success' => false,
				'message' => __( 'Failed to set draft slug.', 'airo-wp' ),
			);
		}

		clean_post_cache( $draft_id );

		$this->draft_service->copy_post_meta( $post_id, $draft_id );

		// Copy thumbnail if exists.
		$thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );

		if ( ! empty( $thumbnail_id ) ) {
			update_post_meta( $draft_id, '_thumbnail_id', $thumbnail_id );
		}

		// Set draft meta relationships.
		update_post_meta( $draft_id, DraftPageService::META_DRAFT_OF, $post_id );
		update_post_meta( $draft_id, DraftPageService::META_DRAFT_CREATED, time() );
		update_post_meta( $post_id, DraftPageService::META_HAS_DRAFT, $draft_id );

		/**
		 * Fires after a page draft has been created.
		 *
		 * @param int $draft_id The new draft post ID.
		 * @param int $post_id  The original published page ID.
		 */
		do_action( 'airowp_draft_created', $draft_id, $post_id );

		return array(
			'success'     => true,
			'message'     => __( 'Draft created successfully.', 'airo-wp' ),
			'draft_id'    => $draft_id,
			'edit_url'    => get_edit_post_link( $draft_id, 'raw' ),
			'original_id' => $post_id,
		);
	}

	/**
	 * Get input schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'post_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'The ID of the published page to create a draft of.', 'airo-wp' ),
				),
				'title'   => array(
					'type'        => 'string',
					'description' => __( 'Optional title override for the draft.', 'airo-wp' ),
				),
				'content' => array(
					'type'        => 'string',
					'description' => __( 'Optional content override for the draft.', 'airo-wp' ),
				),
				'excerpt' => array(
					'type'        => 'string',
					'description' => __( 'Optional excerpt override for the draft.', 'airo-wp' ),
				),
			),
			'required'   => array( 'post_id' ),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Page draft creation result', 'airo-wp' ),
			array(
				'draft_id'    => array(
					'type'        => 'integer',
					'description' => __( 'The new draft post ID', 'airo-wp' ),
				),
				'edit_url'    => array(
					'type'        => 'string',
					'description' => __( 'URL to edit the draft', 'airo-wp' ),
				),
				'original_id' => array(
					'type'        => 'integer',
					'description' => __( 'The original published page ID', 'airo-wp' ),
				),
			)
		);
	}
}
