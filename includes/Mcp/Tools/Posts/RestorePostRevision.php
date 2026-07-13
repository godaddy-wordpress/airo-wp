<?php
/**
 * RestorePostRevision MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the restore-post-revision MCP ability.
 *
 * Restores a post to a previous revision using wp_restore_post_revision().
 * Restoring creates a new revision — history is forward-only.
 */
class RestorePostRevision extends BaseTool {

	public const TOOL_ID = 'airo-wp/restore-post-revision';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Restore Post Revision', 'airo-wp' ),
				'description'         => __( 'Restores a post to a previous revision. Creates a new revision with the restored content — history is forward-only.', 'airo-wp' ),
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
	 * @return array<string, mixed> Restore result.
	 */
	public function execute( array $input ): array {
		// Load revision functions.
		$this->load_admin_file( 'post.php' );

		// Validate required parameters.
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent post ID is required', 'airo-wp' ),
			);
		}

		if ( empty( $input['id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Revision ID is required', 'airo-wp' ),
			);
		}

		$parent_id   = (int) $input['parent'];
		$revision_id = (int) $input['id'];

		// Check if parent post exists.
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: post ID */
					__( 'Post with ID %d not found', 'airo-wp' ),
					$parent_id
				),
			);
		}

		// Check if the post type supports revisions.
		if ( ! post_type_supports( $parent_post->post_type, 'revisions' ) ) {
			return array(
				'success' => false,
				'message' => __( 'This post type does not support revisions', 'airo-wp' ),
			);
		}

		// Get the revision.
		$revision = wp_get_post_revision( $revision_id );

		if ( ! $revision ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: revision ID */
					__( 'Revision with ID %d not found', 'airo-wp' ),
					$revision_id
				),
			);
		}

		// Verify the revision belongs to the parent post.
		if ( (int) $revision->post_parent !== $parent_id ) {
			return array(
				'success' => false,
				'message' => __( 'Revision does not belong to the specified parent post', 'airo-wp' ),
			);
		}

		// Check if user has permission to edit the post.
		if ( ! current_user_can( 'edit_post', $parent_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to edit this post', 'airo-wp' ),
			);
		}

		// Restore the revision — this creates a new revision with the restored content.
		$restored_post_id = wp_restore_post_revision( $revision_id );

		if ( ! $restored_post_id || is_wp_error( $restored_post_id ) ) {
			$error_message = is_wp_error( $restored_post_id )
				? $restored_post_id->get_error_message()
				: __( 'Failed to restore revision', 'airo-wp' );
			return array(
				'success' => false,
				'message' => $error_message,
			);
		}

		// Get the latest revision after restore to return the new revision ID.
		$latest_revisions    = wp_get_post_revisions(
			$parent_id,
			array(
				'numberposts' => 1,
				'orderby'     => 'ID',
				'order'       => 'DESC',
			)
		);
		$current_revision_id = (int) array_key_first( $latest_revisions );

		return array(
			'success'              => true,
			'message'              => sprintf(
				/* translators: 1: post ID, 2: revision ID */
				__( 'Post %1$d restored to revision %2$d', 'airo-wp' ),
				$parent_id,
				$revision_id
			),
			'post_id'              => $parent_id,
			'restored_revision_id' => $revision_id,
			'current_revision_id'  => $current_revision_id,
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
				'parent' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the parent post', 'airo-wp' ),
					'minimum'     => 1,
				),
				'id'     => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the revision to restore', 'airo-wp' ),
					'minimum'     => 1,
				),
			),
			'required'   => array( 'parent', 'id' ),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Post revision restore result', 'airo-wp' ),
			array(
				'post_id'              => array(
					'type'        => 'integer',
					'description' => __( 'The post ID that was restored', 'airo-wp' ),
				),
				'restored_revision_id' => array(
					'type'        => 'integer',
					'description' => __( 'The revision ID that was restored from', 'airo-wp' ),
				),
				'current_revision_id'  => array(
					'type'        => 'integer',
					'description' => __( 'The new revision ID created by the restore', 'airo-wp' ),
				),
			)
		);
	}
}
