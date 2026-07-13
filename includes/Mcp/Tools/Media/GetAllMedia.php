<?php
/**
 * GetAllMedia MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-all-media MCP ability.
 */
class GetAllMedia extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/get-all-media';

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
				'label'               => __( 'Get All Media', 'airo-wp' ),
				'description'         => __( 'Retrieves all WordPress media attachments', 'airo-wp' ),
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
	 * @return array<string, mixed> Media list.
	 */
	public function execute( array $input ): array {
		$include_meta = isset( $input['include_meta'] ) ? (bool) $input['include_meta'] : false;

		$query = new \WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( ! $query->have_posts() ) {
			return array(
				'media' => array(),
				'total' => 0,
			);
		}

		$media_items = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post = get_post();

			$attachment_metadata = wp_get_attachment_metadata( $post->ID );
			$file_path           = get_attached_file( $post->ID );
			$file_size           = $file_path && file_exists( $file_path ) ? filesize( $file_path ) : 0;

			$media_item = array(
				'id'            => $post->ID,
				'title'         => $post->post_title,
				'description'   => $post->post_content,
				'caption'       => $post->post_excerpt,
				'alt_text'      => get_post_meta( $post->ID, '_wp_attachment_image_alt', true ),
				'status'        => $post->post_status,
				'author_id'     => (int) $post->post_author,
				'date_created'  => $post->post_date,
				'date_modified' => $post->post_modified,
				'slug'          => $post->post_name,
				'url'           => wp_get_attachment_url( $post->ID ),
				'mime_type'     => $post->post_mime_type,
				'file_size'     => $file_size,
				'dimensions'    => array(),
			);

			if ( isset( $attachment_metadata['width'] ) && isset( $attachment_metadata['height'] ) ) {
				$media_item['dimensions'] = array(
					'width'  => (int) $attachment_metadata['width'],
					'height' => (int) $attachment_metadata['height'],
				);
			}

			if ( $include_meta ) {
				$media_item['meta'] = $this->get_processed_meta( $post->ID, $attachment_metadata );
			}

			$media_items[] = $media_item;
		}

		wp_reset_postdata();

		return array(
			'media' => $media_items,
			'total' => count( $media_items ),
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
				'include_meta' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include attachment meta data', 'airo-wp' ),
					'default'     => false,
				),
			),
			'required'   => array(),
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
				'media' => array(
					'type'        => 'array',
					'description' => __( 'Array of all media attachments', 'airo-wp' ),
					'items'       => array(
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
					),
				),
				'total' => array(
					'type'        => 'integer',
					'description' => __( 'Total number of media attachments', 'airo-wp' ),
				),
			),
		);
	}

	/**
	 * Get processed meta data for an attachment.
	 *
	 * @param int        $media_id            The media attachment ID.
	 * @param array|bool $attachment_metadata The attachment metadata.
	 * @return array<string, mixed> Processed meta data.
	 */
	private function get_processed_meta( int $media_id, $attachment_metadata ): array {
		$meta = array();

		if ( ! is_array( $attachment_metadata ) ) {
			return $meta;
		}

		$attached_file = get_post_meta( $media_id, '_wp_attached_file', true );
		if ( $attached_file ) {
			$meta['attached_file'] = $attached_file;
		}

		if ( isset( $attachment_metadata['sizes'] ) && is_array( $attachment_metadata['sizes'] ) ) {
			$meta['image_sizes'] = array();
			foreach ( $attachment_metadata['sizes'] as $size_name => $size_data ) {
				$meta['image_sizes'][ $size_name ] = array(
					'file'      => $size_data['file'],
					'width'     => $size_data['width'],
					'height'    => $size_data['height'],
					'mime_type' => $size_data['mime-type'],
					'filesize'  => isset( $size_data['filesize'] ) ? $size_data['filesize'] : 0,
				);
			}
		}

		if ( isset( $attachment_metadata['image_meta'] ) && is_array( $attachment_metadata['image_meta'] ) ) {
			$image_meta = $attachment_metadata['image_meta'];

			$meta['image_meta'] = array(
				'aperture'          => $image_meta['aperture'] ?? '',
				'credit'            => $image_meta['credit'] ?? '',
				'camera'            => $image_meta['camera'] ?? '',
				'caption'           => $image_meta['caption'] ?? '',
				'created_timestamp' => $image_meta['created_timestamp'] ?? '',
				'copyright'         => $image_meta['copyright'] ?? '',
				'focal_length'      => $image_meta['focal_length'] ?? '',
				'iso'               => $image_meta['iso'] ?? '',
				'shutter_speed'     => $image_meta['shutter_speed'] ?? '',
				'title'             => $image_meta['title'] ?? '',
				'orientation'       => $image_meta['orientation'] ?? '',
				'keywords'          => $image_meta['keywords'] ?? array(),
			);
		}

		return $meta;
	}
}
