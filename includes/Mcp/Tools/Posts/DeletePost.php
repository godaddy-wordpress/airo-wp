<?php
/**
 * DeletePost MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the delete-post MCP ability.
 */
class DeletePost extends BaseTool {

	public const TOOL_ID = 'airo-wp/delete-post';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'delete_posts' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Post', 'airo-wp' ),
				'description'         => __( 'Deletes a WordPress post or page by its ID. Can move to trash or permanently delete.', 'airo-wp' ),
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
		if ( empty( $input['post_id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Post ID is required', 'airo-wp' ),
			);
		}

		$post_id      = (int) $input['post_id'];
		$force_delete = isset( $input['force_delete'] ) ? (bool) $input['force_delete'] : true;

		$existing_post = get_post( $post_id );
		if ( ! $existing_post ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: post ID */
					__( 'Post with ID %d not found', 'airo-wp' ),
					$post_id
				),
			);
		}

		if ( ! $force_delete && 'trash' === $existing_post->post_status ) {
			return array(
				'success' => false,
				'message' => __( 'Post is already in trash. Use force_delete to permanently delete it.', 'airo-wp' ),
			);
		}

		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to delete this post', 'airo-wp' ),
			);
		}

		$result = wp_delete_post( $post_id, $force_delete );

		if ( false === $result ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete post. The post may not exist or there was an error.', 'airo-wp' ),
			);
		}

		$message = $force_delete
			? __( 'Post permanently deleted successfully', 'airo-wp' )
			: __( 'Post moved to trash successfully', 'airo-wp' );

		return array(
			'success'             => true,
			'post_id'             => $post_id,
			'message'             => $message,
			'deleted_permanently' => $force_delete,
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
				'post_id'      => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post to delete', 'airo-wp' ),
					'minimum'     => 1,
				),
				'force_delete' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to permanently delete the post (true) or move it to trash (false). Defaults to true.', 'airo-wp' ),
					'default'     => true,
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
			__( 'Post deletion result', 'airo-wp' ),
			array(
				'post_id'             => array(
					'type'        => 'integer',
					'description' => __( 'The deleted post ID', 'airo-wp' ),
				),
				'deleted_permanently' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the post was permanently deleted (true) or moved to trash (false)', 'airo-wp' ),
				),
			)
		);
	}
}
