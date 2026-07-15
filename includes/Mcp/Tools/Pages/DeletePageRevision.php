<?php
/**
 * DeletePageRevision MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the delete-page-revision MCP ability.
 *
 * Permanently deletes a specific page revision. Revisions do not support
 * trashing — they are always permanently deleted.
 */
class DeletePageRevision extends BaseTool {

	public const TOOL_ID = 'airo-wp/delete-page-revision';

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
				'label'               => __( 'Delete Page Revision', 'airo-wp' ),
				'description'         => __( 'Deletes a specific page revision by ID. Revisions are permanently deleted and do not support trash.', 'airo-wp' ),
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
	 * @return array<string, mixed> Deletion result.
	 */
	public function execute( array $input ): array {
		// Load revision functions.
		$this->load_admin_file( 'post.php' );

		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent page ID is required', 'airo-wp' ),
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

		// Check if parent post exists and is a page.
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post || 'page' !== $parent_post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: page ID */
					__( 'Page with ID %d not found', 'airo-wp' ),
					$parent_id
				),
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

		// Verify the revision belongs to the parent page.
		if ( (int) $revision->post_parent !== $parent_id ) {
			return array(
				'success' => false,
				'message' => __( 'Revision does not belong to the specified parent page', 'airo-wp' ),
			);
		}

		// Check permissions.
		if ( ! current_user_can( 'delete_post', $parent_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to delete revisions for this page', 'airo-wp' ),
			);
		}

		// Store revision data before deletion.
		$deleted_data = array(
			'id'            => $revision->ID,
			'author_id'     => (int) $revision->post_author,
			'date_created'  => $revision->post_date,
			'date_modified' => $revision->post_modified,
			'parent_id'     => (int) $revision->post_parent,
			'title'         => $revision->post_title,
		);

		// Delete the revision permanently.
		$result = wp_delete_post_revision( $revision_id );

		if ( false === $result || is_wp_error( $result ) ) {
			$error_message = is_wp_error( $result )
				? $result->get_error_message()
				: __( 'Failed to delete revision', 'airo-wp' );
			return array(
				'success' => false,
				'message' => $error_message,
			);
		}

		return array(
			'success'     => true,
			'revision_id' => $revision_id,
			'message'     => __( 'Revision deleted successfully', 'airo-wp' ),
			'deleted'     => $deleted_data,
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
					'description' => __( 'The ID of the parent page for the revision', 'airo-wp' ),
					'minimum'     => 1,
				),
				'id'     => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the revision to delete', 'airo-wp' ),
					'minimum'     => 1,
				),
				'force'  => array(
					'type'        => 'boolean',
					'description' => __( 'Required to be true, as revisions do not support trashing', 'airo-wp' ),
					'default'     => true,
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
			__( 'Page revision deletion result', 'airo-wp' ),
			array(
				'revision_id' => array(
					'type'        => 'integer',
					'description' => __( 'The deleted revision ID', 'airo-wp' ),
				),
				'deleted'     => array(
					'type'        => 'object',
					'description' => __( 'The deleted revision data', 'airo-wp' ),
					'properties'  => array(
						'id'           => array(
							'type'        => 'integer',
							'description' => __( 'The revision ID', 'airo-wp' ),
						),
						'parent_id'    => array(
							'type'        => 'integer',
							'description' => __( 'The parent page ID', 'airo-wp' ),
						),
						'author_id'    => array(
							'type'        => 'integer',
							'description' => __( 'The revision author ID', 'airo-wp' ),
						),
						'date_created' => array(
							'type'        => 'string',
							'description' => __( 'The revision creation date', 'airo-wp' ),
						),
						'title'        => array(
							'type'        => 'string',
							'description' => __( 'The revision title', 'airo-wp' ),
						),
					),
				),
			)
		);
	}
}
