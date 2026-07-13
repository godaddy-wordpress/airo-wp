<?php
/**
 * GetMediaById MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-media-by-id MCP ability.
 */
class GetMediaById extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/get-media-by-id';

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
				'label'               => __( 'Get Media By ID', 'airo-wp' ),
				'description'         => __( 'Retrieves a WordPress media attachment by its ID', 'airo-wp' ),
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
	 * @return array<string, mixed> Media information or error.
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

		$attachment_metadata = wp_get_attachment_metadata( $media_id );
		$file_path           = get_attached_file( $media_id );
		$file_size           = $file_path && file_exists( $file_path ) ? filesize( $file_path ) : 0;

		$result = array(
			'id'            => $post->ID,
			'title'         => $post->post_title,
			'description'   => $post->post_content,
			'caption'       => $post->post_excerpt,
			'alt_text'      => get_post_meta( $media_id, '_wp_attachment_image_alt', true ),
			'status'        => $post->post_status,
			'author_id'     => (int) $post->post_author,
			'date_created'  => $post->post_date,
			'date_modified' => $post->post_modified,
			'slug'          => $post->post_name,
			'url'           => wp_get_attachment_url( $media_id ),
			'mime_type'     => $post->post_mime_type,
			'file_size'     => $file_size,
			'dimensions'    => array(),
			'meta'          => array(),
		);

		if ( isset( $attachment_metadata['width'] ) && isset( $attachment_metadata['height'] ) ) {
			$result['dimensions'] = array(
				'width'  => (int) $attachment_metadata['width'],
				'height' => (int) $attachment_metadata['height'],
			);
		}

		if ( ! empty( $input['include_meta'] ) ) {
			$result['meta'] = get_post_meta( $media_id );
		}

		return $result;
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
				'media_id'     => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to retrieve', 'airo-wp' ),
					'minimum'     => 1,
				),
				'include_meta' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include attachment meta data', 'airo-wp' ),
					'default'     => true,
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
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'type'        => 'integer',
					'description' => __( 'The media attachment ID', 'airo-wp' ),
				),
				'title'         => array(
					'type'        => 'string',
					'description' => __( 'The media title', 'airo-wp' ),
				),
				'description'   => array(
					'type'        => 'string',
					'description' => __( 'The media description', 'airo-wp' ),
				),
				'caption'       => array(
					'type'        => 'string',
					'description' => __( 'The media caption', 'airo-wp' ),
				),
				'alt_text'      => array(
					'type'        => 'string',
					'description' => __( 'The media alt text', 'airo-wp' ),
				),
				'status'        => array(
					'type'        => 'string',
					'description' => __( 'The attachment status', 'airo-wp' ),
				),
				'author_id'     => array(
					'type'        => 'integer',
					'description' => __( 'The media author ID', 'airo-wp' ),
				),
				'date_created'  => array(
					'type'        => 'string',
					'description' => __( 'The media creation date', 'airo-wp' ),
				),
				'date_modified' => array(
					'type'        => 'string',
					'description' => __( 'The media modification date', 'airo-wp' ),
				),
				'slug'          => array(
					'type'        => 'string',
					'description' => __( 'The media slug', 'airo-wp' ),
				),
				'url'           => array(
					'type'        => 'string',
					'description' => __( 'The media file URL', 'airo-wp' ),
				),
				'mime_type'     => array(
					'type'        => 'string',
					'description' => __( 'The media MIME type', 'airo-wp' ),
				),
				'file_size'     => array(
					'type'        => 'integer',
					'description' => __( 'The file size in bytes', 'airo-wp' ),
				),
				'dimensions'    => array(
					'type'        => 'object',
					'properties'  => array(
						'width'  => array(
							'type'        => 'integer',
							'description' => __( 'Image width in pixels', 'airo-wp' ),
						),
						'height' => array(
							'type'        => 'integer',
							'description' => __( 'Image height in pixels', 'airo-wp' ),
						),
					),
					'description' => __( 'Image dimensions (for images)', 'airo-wp' ),
				),
				'meta'          => array(
					'type'        => 'object',
					'description' => __( 'Attachment meta data (if requested)', 'airo-wp' ),
				),
			),
		);
	}
}
