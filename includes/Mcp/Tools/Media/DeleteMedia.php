<?php
/**
 * DeleteMedia MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the delete-media MCP ability.
 */
class DeleteMedia extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/delete-media';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'upload_files' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Media', 'airo-wp' ),
				'description'         => __( 'Deletes a WordPress media attachment', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'media-management',
			)
		);
	}

	/**
	 * Execute the tool.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed> Delete result or error.
	 */
	public function execute( array $input ): array {
		$media_id = ! empty( $input['media_id'] ) ? (int) $input['media_id'] : 0;

		if ( empty( $media_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'Media ID is required', 'airo-wp' ),
			);
		}

		$post = get_post( $media_id );

		if ( ! $post ) {
			return array(
				'success' => false,
				/* translators: %d: Media ID */
				'message' => sprintf( __( 'Media with ID %d not found', 'airo-wp' ), $media_id ),
			);
		}

		if ( 'attachment' !== $post->post_type ) {
			return array(
				'success' => false,
				/* translators: %d: Post ID */
				'message' => sprintf( __( 'Post with ID %d is not a media attachment', 'airo-wp' ), $media_id ),
			);
		}

		$media_info = array(
			'id'    => $post->ID,
			'title' => $post->post_title,
			'url'   => wp_get_attachment_url( $media_id ),
		);

		$deleted = wp_delete_attachment( $media_id, true );

		if ( false === $deleted || null === $deleted ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete media attachment', 'airo-wp' ),
			);
		}

		return array(
			'success'       => true,
			/* translators: %d: Media ID */
			'message'       => sprintf( __( 'Successfully deleted media with ID %d', 'airo-wp' ), $media_id ),
			'deleted_media' => $media_info,
		);
	}

	/**
	 * Input JSON Schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'media_id' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to delete', 'airo-wp' ),
					'minimum'     => 1,
				),
			),
			'required'   => array( 'media_id' ),
		);
	}

	/**
	 * Output JSON Schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Media deletion result', 'airo-wp' ),
			array(
				'deleted_media' => array(
					'type'        => 'object',
					'properties'  => array(
						'id'    => array(
							'type'        => 'integer',
							'description' => __( 'The deleted media attachment ID', 'airo-wp' ),
						),
						'title' => array(
							'type'        => 'string',
							'description' => __( 'The title of the deleted media', 'airo-wp' ),
						),
						'url'   => array(
							'type'        => 'string',
							'description' => __( 'The URL of the deleted media', 'airo-wp' ),
						),
					),
					'description' => __( 'Information about the deleted media (only present on success)', 'airo-wp' ),
				),
			)
		);
	}
}
