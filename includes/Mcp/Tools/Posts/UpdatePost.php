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

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to edit this post', 'airo-wp' ),
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
			// Callers may be any edit_posts user; always run kses (wp_update_post() only skips it for unfiltered_html users).
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
				// Like WP_REST_Posts_Controller::handle_status_param(), moving into a
				// published-like status needs the post type's own publish cap. As in core
				// (prepare_item_for_database(), since 5.6 / #40399), the check is skipped
				// when the status is unchanged: re-saving the current status grants nothing
				// new, and edit_post was already checked.
				$publishing = in_array( $input['status'], array( 'publish', 'private', 'future' ), true )
					&& $input['status'] !== $existing_post->post_status;
				if ( $publishing ) {
					if ( ! $this->can_publish( $existing_post ) ) {
						return array(
							'success' => false,
							'message' => __( 'You do not have permission to publish this post', 'airo-wp' ),
						);
					}
				}
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
			$scheduled_date_raw = sanitize_text_field( $input['date'] );

			if ( false === strtotime( $scheduled_date_raw ) ) {
				return array(
					'success' => false,
					'message' => __( 'Invalid date format provided for scheduling', 'airo-wp' ),
				);
			}

			// As WP_REST_Posts_Controller does, set both dates plus edit_date: wp_insert_post()
			// chooses `future` vs `publish` from post_date_gmt, and the publish cron is keyed
			// to it, so writing post_date alone leaves the two out of step.
			$post_date_gmt       = get_gmt_from_date( $scheduled_date_raw );
			$scheduled_timestamp = (int) strtotime( $post_date_gmt . ' UTC' );

			if ( isset( $input['status'] ) && 'future' === $input['status'] && $scheduled_timestamp <= time() ) {
				return array(
					'success' => false,
					'message' => __( 'Scheduled date must be in the future', 'airo-wp' ),
				);
			}

			// wp_insert_post() publishes a `future` post dated less than a minute ahead, so
			// moving a scheduled post's date to now or earlier is a publish transition.
			$resulting_status = $update_data['post_status'] ?? $existing_post->post_status;
			if ( 'future' === $resulting_status
				&& $scheduled_timestamp - time() < MINUTE_IN_SECONDS
				&& ! $this->can_publish( $existing_post )
			) {
				return array(
					'success' => false,
					'message' => __( 'You do not have permission to publish this post', 'airo-wp' ),
				);
			}

			$update_data['post_date']     = get_date_from_gmt( $post_date_gmt );
			$update_data['post_date_gmt'] = $post_date_gmt;
			$update_data['edit_date']     = true;
			$updated_fields[]             = 'date';
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
				'skipped_meta'   => array(),
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
				// (`_`-prefixed) keys and honours register_meta() auth callbacks, and falls
				// back to edit_post for an unprotected, unregistered key.
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

		$message = __( 'Post updated successfully', 'airo-wp' );
		if ( ! empty( $skipped_meta ) ) {
			// The caller is an LLM that reads `message`, so a refused write must not look like a completed one.
			$message = sprintf(
				/* translators: %s: comma-separated meta keys */
				__( 'Post updated successfully, but these meta keys were skipped because you lack permission to edit them: %s', 'airo-wp' ),
				implode( ', ', $skipped_meta )
			);
		}

		return array(
			'success'        => true,
			'post_id'        => $post_id,
			'message'        => $message,
			'updated_fields' => $updated_fields,
			'updated_meta'   => $updated_meta,
			'skipped_meta'   => $skipped_meta,
		);
	}

	/**
	 * Whether the current user holds the post type's own publish cap.
	 *
	 * @param \WP_Post $post Post being updated.
	 * @return bool
	 */
	private function can_publish( $post ): bool {
		$post_type_object = get_post_type_object( $post->post_type );
		$publish_cap      = $post_type_object ? $post_type_object->cap->publish_posts : 'publish_posts';

		return current_user_can( $publish_cap );
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
				'skipped_meta'   => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'List of meta keys not written because the user may not edit them (e.g. protected keys)', 'airo-wp' ),
				),
			)
		);
	}
}
