<?php
/**
 * CreatePost MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the create-post MCP ability.
 */
class CreatePost extends BaseTool {

	public const TOOL_ID = 'airo-wp/create-post';

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
				'label'               => __( 'Create Post', 'airo-wp' ),
				'description'         => __( 'Creates a new WordPress post, page, or custom post type', 'airo-wp' ),
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
	 * @return array<string, mixed> Creation result.
	 */
	public function execute( array $input ): array {
		if ( empty( $input['title'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Title is required', 'airo-wp' ),
			);
		}

		$post_type = isset( $input['post_type'] ) && '' !== $input['post_type'] ? sanitize_key( $input['post_type'] ) : 'post';
		if ( ! post_type_exists( $post_type ) ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: post type slug */
					__( 'Post type %s does not exist', 'airo-wp' ),
					$post_type
				),
			);
		}

		$post_type_object = get_post_type_object( $post_type );
		$create_cap       = $post_type_object ? $post_type_object->cap->create_posts : 'edit_posts';
		$publish_cap      = $post_type_object ? $post_type_object->cap->publish_posts : 'publish_posts';
		if ( ! current_user_can( $create_cap ) || ! current_user_can( $publish_cap ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to create posts', 'airo-wp' ),
			);
		}

		$post_data = array(
			'post_type'    => $post_type,
			'post_title'   => sanitize_text_field( $input['title'] ),
			// Callers may be any edit_posts user; always run kses (wp_insert_post() only skips it for unfiltered_html users).
			'post_content' => isset( $input['content'] ) ? wp_kses_post( $input['content'] ) : '',
			'post_excerpt' => isset( $input['excerpt'] ) ? sanitize_textarea_field( $input['excerpt'] ) : '',
			'post_status'  => isset( $input['status'] ) ? $input['status'] : 'draft',
		);

		if ( isset( $input['slug'] ) && '' !== $input['slug'] ) {
			$sanitized_slug = sanitize_title( $input['slug'] );
			if ( '' === $sanitized_slug ) {
				return array(
					'success' => false,
					'message' => __( 'Invalid slug provided. The slug cannot be empty after sanitization.', 'airo-wp' ),
				);
			}
			$post_data['post_name'] = $sanitized_slug;
		}

		if ( isset( $input['status'] ) && ! empty( $input['status'] ) ) {
			$allowed_statuses = array( 'publish', 'draft', 'private', 'pending', 'future' );
			if ( ! in_array( $input['status'], $allowed_statuses, true ) ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: post status */
						__( 'Invalid post status: %s', 'airo-wp' ),
						$input['status']
					),
				);
			}
		}

		if ( isset( $input['status'] ) && 'future' === $input['status'] && empty( $input['date'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Date is required when scheduling a post (status=future)', 'airo-wp' ),
			);
		}

		if ( ! empty( $input['date'] ) ) {
			$scheduled_date_raw = sanitize_text_field( $input['date'] );

			if ( false === strtotime( $scheduled_date_raw ) ) {
				return array(
					'success' => false,
					'message' => __( 'Invalid date format provided for scheduling', 'airo-wp' ),
				);
			}

			// The input is site-local (see the schema); PHP's default timezone is UTC under
			// WordPress, so compare via GMT as UpdatePost does.
			$post_date_gmt       = get_gmt_from_date( $scheduled_date_raw );
			$scheduled_timestamp = (int) strtotime( $post_date_gmt . ' UTC' );

			if ( isset( $input['status'] ) && 'future' === $input['status'] && $scheduled_timestamp <= time() ) {
				return array(
					'success' => false,
					'message' => __( 'Scheduled date must be in the future', 'airo-wp' ),
				);
			}

			// Round-trip through GMT so a relative input ("tomorrow 10:00") reaches
			// wp_insert_post() as Y-m-d H:i:s; its wp_checkdate() rejects anything else.
			// post_date_gmt stays unset: core derives it, and keeps it zeroed for a draft.
			$post_data['post_date'] = get_date_from_gmt( $post_date_gmt );
		}

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Failed to create post: %s', 'airo-wp' ),
					$post_id->get_error_message()
				),
			);
		}

		wp_save_post_revision( $post_id );

		$media_id = (int) ( $input['featured_media'] ?? 0 );
		if ( $media_id > 0 ) {
			set_post_thumbnail( $post_id, $media_id );
		}

		$skipped_meta = array();

		if ( ! empty( $input['meta'] ) && is_array( $input['meta'] ) ) {
			foreach ( $input['meta'] as $meta_item ) {
				if ( ! is_array( $meta_item ) || empty( $meta_item['key'] ) ) {
					continue;
				}

				$meta_key = sanitize_text_field( $meta_item['key'] );
				if ( empty( $meta_key ) ) {
					continue;
				}

				// edit_post_meta is what core REST checks: map_meta_cap() denies protected
				// (`_`-prefixed) keys and honours register_meta() auth callbacks.
				if ( ! current_user_can( 'edit_post_meta', $post_id, $meta_key ) ) {
					$skipped_meta[] = $meta_key;
					continue;
				}

				$meta_value = isset( $meta_item['value'] ) ? $meta_item['value'] : '';
				if ( is_array( $meta_value ) ) {
					$meta_value = array_map( 'sanitize_text_field', $meta_value );
				} else {
					$meta_value = sanitize_text_field( $meta_value );
				}

				update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		$message = __( 'Post created successfully', 'airo-wp' );
		if ( ! empty( $skipped_meta ) ) {
			// The caller is an LLM that reads `message`, so a refused write must not look like a completed one.
			$message = sprintf(
				/* translators: %s: comma-separated meta keys */
				__( 'Post created successfully, but these meta keys were skipped because you lack permission to edit them: %s', 'airo-wp' ),
				implode( ', ', $skipped_meta )
			);
		}

		return array(
			'success'      => true,
			'post_id'      => $post_id,
			'message'      => $message,
			'skipped_meta' => $skipped_meta,
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
				'post_type'      => array(
					'type'        => 'string',
					'description' => __( 'The post type (post, page, or custom post type). Defaults to post.', 'airo-wp' ),
				),
				'title'          => array(
					'type'        => 'string',
					'description' => __( 'The title for the new post', 'airo-wp' ),
				),
				'slug'           => array(
					'type'        => 'string',
					'description' => __( 'The slug (URL-safe name) for the new post', 'airo-wp' ),
				),
				'content'        => array(
					'type'        => 'string',
					'description' => __( 'The content for the new post', 'airo-wp' ),
				),
				'excerpt'        => array(
					'type'        => 'string',
					'description' => __( 'The excerpt for the new post', 'airo-wp' ),
				),
				'status'         => array(
					'type'        => 'string',
					'description' => __( 'The post status (publish, draft, private, etc.). Use "future" with date to schedule a post.', 'airo-wp' ),
					'enum'        => array( 'publish', 'draft', 'private', 'pending', 'future' ),
				),
				'date'           => array(
					'type'        => 'string',
					'description' => __( 'The date the post should be published, in the site\'s timezone. Format: YYYY-MM-DD HH:MM:SS. Required for scheduling (status=future).', 'airo-wp' ),
				),
				'featured_media' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to use as featured image', 'airo-wp' ),
				),
				'meta'           => array(
					'type'        => 'array',
					'description' => __( 'Array of meta fields to set', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'key'   => array(
								'type'        => 'string',
								'description' => __( 'Meta key', 'airo-wp' ),
							),
							'value' => array(
								'type'        => array( 'string', 'array' ),
								'description' => __( 'Meta value (string or array)', 'airo-wp' ),
							),
						),
						'required'   => array( 'key' ),
					),
				),
			),
			'required'   => array( 'title' ),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Post creation result', 'airo-wp' ),
			array(
				'post_id'      => array(
					'type'        => 'integer',
					'description' => __( 'The new post ID', 'airo-wp' ),
				),
				'skipped_meta' => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'List of meta keys not written because the user may not edit them (e.g. protected keys)', 'airo-wp' ),
				),
			)
		);
	}
}
