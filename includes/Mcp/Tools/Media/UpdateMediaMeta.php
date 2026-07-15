<?php
/**
 * UpdateMediaMeta MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the update-media-meta MCP ability.
 */
class UpdateMediaMeta extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/update-media-meta';

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
				'label'               => __( 'Update Media Meta', 'airo-wp' ),
				'description'         => __( 'Updates the title, description, alt text, and caption of a WordPress media attachment', 'airo-wp' ),
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
	 * @return array<string, mixed> Update result or error.
	 */
	public function execute( array $input ): array {
		$media_id = ! empty( $input['media_id'] ) ? (int) $input['media_id'] : 0;

		if ( empty( $media_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'Media ID is required', 'airo-wp' ),
			);
		}

		$updatable_fields = array( 'title', 'description', 'alt_text', 'caption' );
		$has_update_field = false;

		foreach ( $updatable_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$has_update_field = true;
				break;
			}
		}

		if ( ! $has_update_field ) {
			return array(
				'success' => false,
				'message' => __( 'At least one field (title, description, alt_text, or caption) is required to update', 'airo-wp' ),
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

		$update_data    = array( 'ID' => $media_id );
		$updated_fields = array();

		if ( isset( $input['title'] ) ) {
			$update_data['post_title'] = sanitize_text_field( $input['title'] );
			$updated_fields[]          = 'title';
		}

		if ( isset( $input['description'] ) ) {
			$update_data['post_content'] = wp_kses_post( $input['description'] );
			$updated_fields[]            = 'description';
		}

		if ( isset( $input['caption'] ) ) {
			$update_data['post_excerpt'] = wp_kses_post( $input['caption'] );
			$updated_fields[]            = 'caption';
		}

		if ( count( $update_data ) > 1 ) {
			$result = wp_update_post( $update_data, true );
			if ( is_wp_error( $result ) ) {
				return array(
					'success' => false,
					/* translators: %s: Error message */
					'message' => sprintf( __( 'Failed to update media: %s', 'airo-wp' ), $result->get_error_message() ),
				);
			}
		}

		if ( isset( $input['alt_text'] ) ) {
			$alt_text_result = update_post_meta( $media_id, '_wp_attachment_image_alt', sanitize_text_field( $input['alt_text'] ) );

			if ( false === $alt_text_result ) {
				return array(
					'success' => false,
					'message' => __( 'Failed to update alt text', 'airo-wp' ),
				);
			}

			$updated_fields[] = 'alt_text';
		}

		$updated_post = get_post( $media_id );

		$attachment_metadata = wp_get_attachment_metadata( $media_id );
		$file_path           = get_attached_file( $media_id );
		$file_size           = $file_path && file_exists( $file_path ) ? filesize( $file_path ) : 0;

		$dimensions = array();
		if ( ! empty( $attachment_metadata['width'] ) && ! empty( $attachment_metadata['height'] ) ) {
			$dimensions = array(
				'width'  => (int) $attachment_metadata['width'],
				'height' => (int) $attachment_metadata['height'],
			);
		}

		return array(
			'success'       => true,
			'message'       => sprintf(
				/* translators: %1$s: comma-separated list of updated fields, %2$d: Media ID */
				__( 'Successfully updated %1$s for media ID %2$d', 'airo-wp' ),
				implode( ', ', $updated_fields ),
				$media_id
			),
			'updated_media' => array(
				'id'            => $updated_post->ID,
				'title'         => $updated_post->post_title,
				'description'   => $updated_post->post_content,
				'alt_text'      => get_post_meta( $media_id, '_wp_attachment_image_alt', true ),
				'caption'       => $updated_post->post_excerpt,
				'status'        => $updated_post->post_status,
				'author_id'     => (int) $updated_post->post_author,
				'date_created'  => $updated_post->post_date,
				'date_modified' => $updated_post->post_modified,
				'slug'          => $updated_post->post_name,
				'url'           => wp_get_attachment_url( $media_id ),
				'mime_type'     => $updated_post->post_mime_type,
				'file_size'     => $file_size,
				'dimensions'    => $dimensions,
			),
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
				'media_id'    => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to update', 'airo-wp' ),
					'minimum'     => 1,
				),
				'title'       => array(
					'type'        => 'string',
					'description' => __( 'The new title for the media attachment', 'airo-wp' ),
				),
				'description' => array(
					'type'        => 'string',
					'description' => __( 'The new description for the media attachment', 'airo-wp' ),
				),
				'alt_text'    => array(
					'type'        => 'string',
					'description' => __( 'The new alt text for the media attachment', 'airo-wp' ),
				),
				'caption'     => array(
					'type'        => 'string',
					'description' => __( 'The new caption for the media attachment', 'airo-wp' ),
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
			__( 'Media meta update result', 'airo-wp' ),
			array(
				'updated_media' => array(
					'type'        => 'object',
					'properties'  => array(
						'id'            => array(
							'type'        => 'integer',
							'description' => __( 'The media attachment ID', 'airo-wp' ),
						),
						'title'         => array(
							'type'        => 'string',
							'description' => __( 'The updated media title', 'airo-wp' ),
						),
						'description'   => array(
							'type'        => 'string',
							'description' => __( 'The updated media description', 'airo-wp' ),
						),
						'alt_text'      => array(
							'type'        => 'string',
							'description' => __( 'The updated media alt text', 'airo-wp' ),
						),
						'caption'       => array(
							'type'        => 'string',
							'description' => __( 'The updated media caption', 'airo-wp' ),
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
							'description' => __( 'The date the media was created', 'airo-wp' ),
						),
						'date_modified' => array(
							'type'        => 'string',
							'description' => __( 'The date the media was last modified', 'airo-wp' ),
						),
						'slug'          => array(
							'type'        => 'string',
							'description' => __( 'The media slug', 'airo-wp' ),
						),
						'url'           => array(
							'type'        => 'string',
							'description' => __( 'The URL of the media file', 'airo-wp' ),
						),
						'mime_type'     => array(
							'type'        => 'string',
							'description' => __( 'The MIME type of the media file', 'airo-wp' ),
						),
						'file_size'     => array(
							'type'        => 'integer',
							'description' => __( 'The file size in bytes', 'airo-wp' ),
						),
						'dimensions'    => array(
							'type'        => 'object',
							'description' => __( 'The image dimensions (width and height)', 'airo-wp' ),
							'properties'  => array(
								'width'  => array(
									'type'        => 'integer',
									'description' => __( 'The image width in pixels', 'airo-wp' ),
								),
								'height' => array(
									'type'        => 'integer',
									'description' => __( 'The image height in pixels', 'airo-wp' ),
								),
							),
						),
					),
					'description' => __( 'The updated media information (only present on success)', 'airo-wp' ),
				),
			)
		);
	}
}
