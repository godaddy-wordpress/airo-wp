<?php
/**
 * UploadImage MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the upload-image MCP ability.
 *
 * Downloads an image from a URL and uploads it to the WordPress media library,
 * optionally attaching it to a post.
 */
class UploadImage extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/upload-image';

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
				'label'               => __( 'Upload Image', 'airo-wp' ),
				'description'         => __( 'Downloads an image from a URL and uploads it to the WordPress media library, optionally attaching it to a post', 'airo-wp' ),
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
	 * @return array<string, mixed> Upload result or error.
	 */
	public function execute( array $input ): array {
		if ( empty( $input['url'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Image URL is required', 'airo-wp' ),
			);
		}

		// Sanitize and validate URL.
		$url = esc_url_raw( $input['url'] );

		if ( ! $url || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid URL provided', 'airo-wp' ),
			);
		}

		// Get post ID if provided, otherwise null.
		$post_id = isset( $input['post_id'] ) ? absint( $input['post_id'] ) : null;

		// Validate post ID only if one was provided.
		if ( $post_id && ! get_post( $post_id ) ) {
			return array(
				'success' => false,
				/* translators: %d: Post ID */
				'message' => sprintf( __( 'Post with ID %d does not exist', 'airo-wp' ), $post_id ),
			);
		}

		// Get optional title.
		$title = isset( $input['title'] ) ? sanitize_text_field( $input['title'] ) : null;

		// Stage metadata to apply after upload (avoid multiple DB writes).
		$metadata_updates = array();

		if ( isset( $input['alt_text'] ) ) {
			$metadata_updates['alt_text'] = sanitize_text_field( $input['alt_text'] );
		}

		if ( isset( $input['description'] ) ) {
			$metadata_updates['post_content'] = wp_kses_post( $input['description'] );
		}

		if ( isset( $input['caption'] ) ) {
			$metadata_updates['caption'] = wp_kses_post( $input['caption'] );
		}

		// Include media handling functions.
		if ( ! function_exists( 'media_sideload_image' ) ) {
			$this->load_admin_file( 'media.php' );
			$this->load_admin_file( 'file.php' );
			$this->load_admin_file( 'image.php' );
		}

		// Upload the image and get the attachment ID.
		$attachment_id = media_sideload_image( $url, $post_id, $title, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			return array(
				'success' => false,
				/* translators: %s: Error message */
				'message' => sprintf( __( 'Failed to upload image: %s', 'airo-wp' ), $attachment_id->get_error_message() ),
			);
		}

		// Apply staged metadata updates in a single pass.
		if ( ! empty( $metadata_updates ) ) {
			$post_update = array( 'ID' => $attachment_id );

			// Update caption (post_excerpt) if provided.
			if ( isset( $metadata_updates['caption'] ) ) {
				$post_update['post_excerpt'] = $metadata_updates['caption'];
			}

			// Update description (post_content) if provided.
			if ( isset( $metadata_updates['post_content'] ) ) {
				$post_update['post_content'] = $metadata_updates['post_content'];
			}

			// Update post data if we have changes.
			if ( count( $post_update ) > 1 ) {
				wp_update_post( $post_update, true );
			}

			// Update alt text (post meta) if provided.
			if ( isset( $metadata_updates['alt_text'] ) ) {
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', $metadata_updates['alt_text'] );
			}
		}

		// Get the attachment URL.
		$attachment_url = wp_get_attachment_url( $attachment_id );

		return array(
			'success'       => true,
			'attachment_id' => $attachment_id,
			'url'           => $attachment_url,
			'message'       => __( 'Image uploaded successfully', 'airo-wp' ),
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
				'url'         => array(
					'type'        => 'string',
					'description' => __( 'The URL of the image to download and upload', 'airo-wp' ),
				),
				'post_id'     => array(
					'type'        => 'integer',
					'description' => __( 'Optional. The post ID to attach the image to', 'airo-wp' ),
				),
				'title'       => array(
					'type'        => 'string',
					'description' => __( 'Optional. The title for the image', 'airo-wp' ),
				),
				'description' => array(
					'type'        => 'string',
					'description' => __( 'Optional. The description for the image', 'airo-wp' ),
				),
				'alt_text'    => array(
					'type'        => 'string',
					'description' => __( 'Optional. The alt text for the image', 'airo-wp' ),
				),
				'caption'     => array(
					'type'        => 'string',
					'description' => __( 'Optional. The caption for the image', 'airo-wp' ),
				),
			),
			'required'   => array( 'url' ),
		);
	}

	/**
	 * Output JSON Schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Image upload result', 'airo-wp' ),
			array(
				'attachment_id' => array(
					'type'        => 'integer',
					'description' => __( 'The attachment ID of the uploaded image', 'airo-wp' ),
				),
				'url'           => array(
					'type'        => 'string',
					'description' => __( 'The URL of the uploaded image', 'airo-wp' ),
				),
			)
		);
	}
}
