<?php
/**
 * ListMedia MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-media MCP ability.
 *
 * Provides filtering and pagination similar to the WP REST API /wp/v2/media endpoint.
 */
class ListMedia extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/list-media';

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
				'label'               => __( 'List Media', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of WordPress media attachments with filtering and pagination options', 'airo-wp' ),
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
	 * @return array<string, mixed> List of media with pagination info.
	 */
	public function execute( array $input ): array {
		$query_args = $this->build_query_args( $input );

		// WP_Query's `s` only searches post_title/excerpt/content. For attachments,
		// post_title is the original filename (with spaces) while users typically
		// know the sanitized filename (with dashes), so title-only search misses.
		// _filter_query_attachment_filenames extends the LIKE clause to the
		// _wp_attached_file postmeta — same approach core REST /wp/v2/media uses.
		// The filter self-removes on first invocation.
		$has_search = ! empty( $input['search'] );
		if ( $has_search ) {
			add_filter( 'posts_clauses', '_filter_query_attachment_filenames' );
		}

		$query = new \WP_Query( $query_args );

		$media         = array();
		$include_meta  = ! empty( $input['include_meta'] );
		$fields_filter = ! empty( $input['_fields'] ) && is_array( $input['_fields'] ) ? $input['_fields'] : array();
		$context       = isset( $input['context'] ) ? $input['context'] : 'view';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				$complete_media_data = $this->build_media_data( $post, $context, $include_meta );

				if ( ! empty( $fields_filter ) ) {
					$media_data = $this->filter_fields( $complete_media_data, $fields_filter );
				} else {
					$media_data = $complete_media_data;
				}

				$media[] = $media_data;
			}
			wp_reset_postdata();
		}

		$page        = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$per_page    = isset( $input['per_page'] ) ? (int) $input['per_page'] : 10;
		$total       = $query->found_posts;
		$total_pages = max( 1, (int) ceil( $total / $per_page ) );

		return array(
			'media'       => $media,
			'total'       => $total,
			'total_pages' => $total_pages,
			'page'        => $page,
			'per_page'    => $per_page,
		);
	}

	/**
	 * Build WP_Query arguments from input parameters.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed> WP_Query arguments.
	 */
	private function build_query_args( array $input ): array {
		$args = array(
			'post_type'      => 'attachment',
			'post_status'    => isset( $input['status'] ) ? $input['status'] : 'inherit',
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'paged'          => isset( $input['page'] ) ? (int) $input['page'] : 1,
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
			'orderby'        => isset( $input['orderby'] ) ? $input['orderby'] : 'date',
		);

		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
		}

		if ( isset( $input['author'] ) ) {
			$args['author'] = (int) $input['author'];
		}

		if ( ! empty( $input['author_exclude'] ) && is_array( $input['author_exclude'] ) ) {
			$args['author__not_in'] = array_map( 'intval', $input['author_exclude'] );
		}

		if ( ! empty( $input['exclude'] ) && is_array( $input['exclude'] ) ) {
			$args['post__not_in'] = array_map( 'intval', $input['exclude'] ); // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- bounded input from MCP schema.
		}

		if ( ! empty( $input['include'] ) && is_array( $input['include'] ) ) {
			$args['post__in'] = array_map( 'intval', $input['include'] );
			if ( 'include' === $args['orderby'] ) {
				$args['orderby'] = 'post__in';
			}
		}

		if ( isset( $input['offset'] ) ) {
			$args['offset'] = (int) $input['offset'];
		}

		if ( isset( $input['parent'] ) ) {
			$args['post_parent'] = (int) $input['parent'];
		}

		if ( ! empty( $input['parent_exclude'] ) && is_array( $input['parent_exclude'] ) ) {
			$args['post_parent__not_in'] = array_map( 'intval', $input['parent_exclude'] );
		}

		if ( ! empty( $input['slug'] ) ) {
			if ( is_array( $input['slug'] ) ) {
				$args['post_name__in'] = array_map( 'sanitize_title', $input['slug'] );
			} else {
				$args['post_name__in'] = array( sanitize_title( $input['slug'] ) );
			}
		}

		if ( ! empty( $input['media_type'] ) ) {
			$args['post_mime_type'] = sanitize_text_field( $input['media_type'] );
		}

		if ( ! empty( $input['mime_type'] ) ) {
			$args['post_mime_type'] = sanitize_mime_type( $input['mime_type'] );
		}

		$date_query = array();

		if ( ! empty( $input['after'] ) ) {
			$date_query[] = array(
				'after'     => $input['after'],
				'inclusive' => true,
				'column'    => 'post_date',
			);
		}

		if ( ! empty( $input['before'] ) ) {
			$date_query[] = array(
				'before'    => $input['before'],
				'inclusive' => true,
				'column'    => 'post_date',
			);
		}

		if ( ! empty( $input['modified_after'] ) ) {
			$date_query[] = array(
				'after'     => $input['modified_after'],
				'inclusive' => true,
				'column'    => 'post_modified',
			);
		}

		if ( ! empty( $input['modified_before'] ) ) {
			$date_query[] = array(
				'before'    => $input['modified_before'],
				'inclusive' => true,
				'column'    => 'post_modified',
			);
		}

		if ( ! empty( $date_query ) ) {
			$args['date_query'] = $date_query;
		}

		return $args;
	}

	/**
	 * Build media data for a single post.
	 *
	 * @param \WP_Post $post         Post object.
	 * @param string   $context      Context (view, embed, edit).
	 * @param bool     $include_meta Whether to include meta data.
	 * @return array<string, mixed> Media data.
	 */
	private function build_media_data( \WP_Post $post, string $context, bool $include_meta ): array {
		$attachment_metadata = wp_get_attachment_metadata( $post->ID );
		$file_path           = get_attached_file( $post->ID );
		$file_size           = $file_path && file_exists( $file_path ) ? filesize( $file_path ) : 0;

		$media_data = array(
			'id'            => $post->ID,
			'title'         => $post->post_title,
			'description'   => $post->post_content,
			'caption'       => $post->post_excerpt,
			'alt_text'      => get_post_meta( $post->ID, '_wp_attachment_image_alt', true ),
			'slug'          => $post->post_name,
			'status'        => $post->post_status,
			'author_id'     => (int) $post->post_author,
			'date_created'  => $post->post_date,
			'date_modified' => $post->post_modified,
			'url'           => wp_get_attachment_url( $post->ID ),
			'mime_type'     => $post->post_mime_type,
			'file_size'     => $file_size,
			'dimensions'    => array(),
		);

		if ( isset( $attachment_metadata['width'] ) && isset( $attachment_metadata['height'] ) ) {
			$media_data['dimensions'] = array(
				'width'  => (int) $attachment_metadata['width'],
				'height' => (int) $attachment_metadata['height'],
			);
		}

		if ( $include_meta ) {
			$media_data['meta'] = $this->get_processed_meta( $post->ID, $attachment_metadata );
		}

		return $media_data;
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

	/**
	 * Filter media data to only include specified fields.
	 *
	 * @param array<string, mixed> $media_data Complete media data.
	 * @param array<string>        $fields     Array of field names to include.
	 * @return array<string, mixed> Filtered media data.
	 */
	private function filter_fields( array $media_data, array $fields ): array {
		$filtered = array();

		if ( isset( $media_data['id'] ) ) {
			$filtered['id'] = $media_data['id'];
		}

		foreach ( $fields as $field ) {
			$field = sanitize_key( $field );
			if ( isset( $media_data[ $field ] ) && 'id' !== $field ) {
				$filtered[ $field ] = $media_data[ $field ];
			}
		}

		return $filtered;
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
				'context'         => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'airo-wp' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'page'            => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'airo-wp' ),
					'default'     => 1,
					'minimum'     => 1,
				),
				'per_page'        => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to be returned in result set', 'airo-wp' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'search'          => array(
					'type'        => 'string',
					'description' => __( 'Limit results to those matching a string', 'airo-wp' ),
				),
				'after'           => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media uploaded after a given ISO8601 compliant date', 'airo-wp' ),
				),
				'modified_after'  => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media modified after a given ISO8601 compliant date', 'airo-wp' ),
				),
				'author'          => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to media assigned to specific author ID', 'airo-wp' ),
				),
				'author_exclude'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes media assigned to specific author IDs', 'airo-wp' ),
				),
				'before'          => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media uploaded before a given ISO8601 compliant date', 'airo-wp' ),
				),
				'modified_before' => array(
					'type'        => 'string',
					'description' => __( 'Limit response to media modified before a given ISO8601 compliant date', 'airo-wp' ),
				),
				'exclude'         => array( // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- mirrors WP REST API exclude param; bounded by schema.
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes specific IDs', 'airo-wp' ),
				),
				'include'         => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to specific IDs', 'airo-wp' ),
				),
				'offset'          => array(
					'type'        => 'integer',
					'description' => __( 'Offset the result set by a specific number of items', 'airo-wp' ),
					'minimum'     => 0,
				),
				'order'           => array(
					'type'        => 'string',
					'description' => __( 'Order sort attribute ascending or descending', 'airo-wp' ),
					'enum'        => array( 'asc', 'desc' ),
					'default'     => 'desc',
				),
				'orderby'         => array(
					'type'        => 'string',
					'description' => __( 'Sort collection by media attribute', 'airo-wp' ),
					'enum'        => array( 'author', 'date', 'id', 'include', 'modified', 'parent', 'relevance', 'slug', 'title' ),
					'default'     => 'date',
				),
				'parent'          => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to media attached to a particular parent ID', 'airo-wp' ),
				),
				'parent_exclude'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to all media except those of a particular parent ID', 'airo-wp' ),
				),
				'slug'            => array(
					'description' => __( 'Limit result set to media with one or more specific slugs', 'airo-wp' ),
				),
				'status'          => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to media assigned one or more statuses', 'airo-wp' ),
					'enum'        => array( 'inherit', 'private', 'trash' ),
					'default'     => 'inherit',
				),
				'media_type'      => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to media of a particular media type', 'airo-wp' ),
					'enum'        => array( 'image', 'video', 'audio', 'application' ),
				),
				'mime_type'       => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to media of a particular MIME type', 'airo-wp' ),
				),
				'include_meta'    => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include attachment meta data', 'airo-wp' ),
					'default'     => false,
				),
				'_fields'         => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Limit response to specific fields', 'airo-wp' ),
				),
			),
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
				'media'       => array(
					'type'        => 'array',
					'description' => __( 'Array of media objects', 'airo-wp' ),
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
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of media items matching the query', 'airo-wp' ),
				),
				'total_pages' => array(
					'type'        => 'integer',
					'description' => __( 'Total number of pages available', 'airo-wp' ),
				),
				'page'        => array(
					'type'        => 'integer',
					'description' => __( 'Current page number', 'airo-wp' ),
				),
				'per_page'    => array(
					'type'        => 'integer',
					'description' => __( 'Number of items per page', 'airo-wp' ),
				),
			),
		);
	}
}
