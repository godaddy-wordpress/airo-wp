<?php
/**
 * UpdatePost MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the update-post MCP ability.
 */
class UpdatePost extends BaseTool {

	public const TOOL_ID = 'airo-wp/update-post';

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
				'label'               => __( 'Update Post', 'airo-wp' ),
				'description'         => __( 'Updates a WordPress post by its ID with new title, content, excerpt, status, and/or post meta fields', 'airo-wp' ),
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
	 * @return array<string, mixed> Update result.
	 */
	public function execute( array $input ): array {
		if ( empty( $input['post_id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Post ID is required', 'airo-wp' ),
			);
		}

		$post_id = (int) $input['post_id'];

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

		$update_data = array(
			'ID' => $post_id,
		);

		$updated_fields = array();

		if ( isset( $input['title'] ) && ! empty( $input['title'] ) ) {
			$update_data['post_title'] = sanitize_text_field( $input['title'] );
			$updated_fields[]          = 'title';
		}

		if ( isset( $input['slug'] ) && '' !== $input['slug'] ) {
			$sanitized_slug = sanitize_title( $input['slug'] );
			if ( '' === $sanitized_slug ) {
				return array(
					'success' => false,
					'message' => __( 'Invalid slug provided. The slug cannot be empty after sanitization.', 'airo-wp' ),
				);
			}
			$update_data['post_name'] = $sanitized_slug;
			$updated_fields[]         = 'slug';
		}

		if ( isset( $input['content'] ) ) {
			// All MCP callers are admins who have unfiltered_html; wp_update_post() skips kses for them.
			$update_data['post_content'] = wp_kses_post( $input['content'] );
			$updated_fields[]            = 'content';
		}

		if ( isset( $input['excerpt'] ) ) {
			$update_data['post_excerpt'] = sanitize_textarea_field( $input['excerpt'] );
			$updated_fields[]            = 'excerpt';
		}

		if ( isset( $input['status'] ) && ! empty( $input['status'] ) ) {
			$allowed_statuses = array( 'publish', 'draft', 'private', 'pending', 'future' );
			if ( in_array( $input['status'], $allowed_statuses, true ) ) {
				$update_data['post_status'] = $input['status'];
				$updated_fields[]           = 'status';
			}
		}

		if ( isset( $input['status'] ) && 'future' === $input['status'] && empty( $input['date'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Date is required when scheduling a post (status=future)', 'airo-wp' ),
			);
		}

		if ( ! empty( $input['date'] ) ) {
			$scheduled_date_raw  = sanitize_text_field( $input['date'] );
			$scheduled_timestamp = strtotime( $scheduled_date_raw );

			if ( false === $scheduled_timestamp ) {
				return array(
					'success' => false,
					'message' => __( 'Invalid date format provided for scheduling', 'airo-wp' ),
				);
			}

			if ( isset( $input['status'] ) && 'future' === $input['status'] && $scheduled_timestamp <= time() ) {
				return array(
					'success' => false,
					'message' => __( 'Scheduled date must be in the future', 'airo-wp' ),
				);
			}

			$update_data['post_date'] = $scheduled_date_raw;
			$updated_fields[]         = 'date';
		}

		$has_update_data = ! empty( $updated_fields )
			|| ( isset( $input['meta'] ) && is_array( $input['meta'] ) && ! empty( $input['meta'] ) )
			|| ! empty( $input['featured_media'] );

		if ( ! $has_update_data ) {
			return array(
				'success'        => true,
				'post_id'        => $post_id,
				'message'        => __( 'No updates requested', 'airo-wp' ),
				'updated_fields' => array(),
				'updated_meta'   => array(),
			);
		}

		$result = wp_update_post( $update_data, true );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Failed to update post: %s', 'airo-wp' ),
					$result->get_error_message()
				),
			);
		}

		$updated_meta = array();

		if ( ! empty( $input['meta'] ) && is_array( $input['meta'] ) ) {
			foreach ( $input['meta'] as $meta_item ) {
				if ( ! is_array( $meta_item ) || empty( $meta_item['key'] ) ) {
					continue;
				}

				$meta_key = sanitize_text_field( $meta_item['key'] );

				if ( empty( $meta_key ) ) {
					continue;
				}

				$meta_value = isset( $meta_item['value'] ) ? $meta_item['value'] : '';

				if ( is_array( $meta_value ) ) {
					$meta_value = array_map( 'sanitize_text_field', $meta_value );
				} else {
					$meta_value = sanitize_text_field( $meta_value );
				}

				$meta_result = update_post_meta( $post_id, $meta_key, $meta_value );

				if ( false !== $meta_result ) {
					$updated_meta[] = $meta_key;
				}
			}
		}

		$media_id = (int) ( $input['featured_media'] ?? 0 );
		if ( $media_id > 0 ) {
			set_post_thumbnail( $post_id, $media_id );
			$updated_fields[] = 'featured_media';
		}

		return array(
			'success'        => true,
			'post_id'        => $post_id,
			'message'        => __( 'Post updated successfully', 'airo-wp' ),
			'updated_fields' => $updated_fields,
			'updated_meta'   => $updated_meta,
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
				'post_id'        => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post to update', 'airo-wp' ),
					'minimum'     => 1,
				),
				'title'          => array(
					'type'        => 'string',
					'description' => __( 'The new title for the post', 'airo-wp' ),
				),
				'slug'           => array(
					'type'        => 'string',
					'description' => __( 'The slug (URL-safe name) for the post', 'airo-wp' ),
				),
				'content'        => array(
					'type'        => 'string',
					'description' => __( 'The new content for the post', 'airo-wp' ),
				),
				'excerpt'        => array(
					'type'        => 'string',
					'description' => __( 'The new excerpt for the post', 'airo-wp' ),
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
				'meta'           => array(
					'type'        => 'array',
					'description' => __( 'Array of meta fields to update', 'airo-wp' ),
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
				'featured_media' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the media attachment to use as featured image', 'airo-wp' ),
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
			__( 'Post update result', 'airo-wp' ),
			array(
				'post_id'        => array(
					'type'        => 'integer',
					'description' => __( 'The updated post ID', 'airo-wp' ),
				),
				'updated_fields' => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'List of fields that were updated', 'airo-wp' ),
				),
				'updated_meta'   => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'List of meta keys that were updated', 'airo-wp' ),
				),
			)
		);
	}
}
