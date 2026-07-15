<?php
/**
 * ListPosts MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-posts MCP ability.
 */
class ListPosts extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-posts';

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
				'label'               => __( 'List Posts', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of WordPress posts, pages, or custom post types with filtering and pagination options', 'airo-wp' ),
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
	 * @return array<string, mixed> List of posts with pagination info.
	 */
	public function execute( array $input ): array {
		$query_args = $this->build_query_args( $input );
		$post_type  = $query_args['post_type'];
		$status     = $query_args['post_status'];

		// check_permissions() only validates edit_posts for built-in types; custom post
		// types may register different edit caps, so verify against the actual type cap.
		$builtin_types = array( 'post', 'page', 'attachment', 'revision', 'nav_menu_item' );
		if ( ! in_array( $post_type, $builtin_types, true ) ) {
			$post_type_obj = get_post_type_object( $post_type );
			if ( ! $post_type_obj ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: post type slug */
						__( 'Invalid post type: %s', 'airo-wp' ),
						$post_type
					),
				);
			}
			if ( ! current_user_can( $post_type_obj->cap->edit_posts ) ) {
				return array(
					'success' => false,
					'message' => __( 'You do not have permission to query this post type.', 'airo-wp' ),
				);
			}
		}

		// Non-public statuses expose unpublished content across all authors, which
		// requires edit_others_posts (or the type-specific equivalent).
		$non_public = array( 'private', 'draft', 'pending', 'future', 'trash', 'any' );
		if ( in_array( $status, $non_public, true ) ) {
			$post_type_obj   = get_post_type_object( $post_type );
			$edit_others_cap = $post_type_obj ? $post_type_obj->cap->edit_others_posts : 'edit_others_posts';
			if ( ! current_user_can( $edit_others_cap ) ) {
				return array(
					'success' => false,
					'message' => __( 'You do not have permission to query posts with this status.', 'airo-wp' ),
				);
			}
		}

		$query = new \WP_Query( $query_args );

		$posts            = array();
		$include_meta     = ! empty( $input['include_meta'] );
		$fields_filter    = ! empty( $input['_fields'] ) && is_array( $input['_fields'] ) ? $input['_fields'] : array();
		$context          = isset( $input['context'] ) ? $input['context'] : 'view';
		$include_featured = empty( $fields_filter )
			|| in_array( 'featured_media_id', array_map( 'sanitize_key', $fields_filter ), true );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				$complete_post_data = $this->build_post_data( $post, $context, $include_meta, $include_featured );

				if ( ! empty( $fields_filter ) ) {
					$post_data = $this->filter_fields( $complete_post_data, $fields_filter );
				} else {
					$post_data = $complete_post_data;
				}

				$posts[] = $post_data;
			}
			wp_reset_postdata();
		}

		$page        = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$per_page    = isset( $input['per_page'] ) ? (int) $input['per_page'] : 10;
		$total       = $query->found_posts;
		$total_pages = max( 1, (int) ceil( $total / $per_page ) );

		return array(
			'posts'       => $posts,
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
		$post_type = isset( $input['post_type'] ) && '' !== $input['post_type'] ? sanitize_key( $input['post_type'] ) : 'page';

		$args = array(
			'post_type'      => $post_type,
			'post_status'    => isset( $input['status'] ) ? $input['status'] : 'publish',
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

		if ( ! empty( $input['exclude'] ) && is_array( $input['exclude'] ) ) {
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- caller-supplied filter on bounded result sets.
			$args['post__not_in'] = array_map( 'intval', $input['exclude'] );
		}

		if ( ! empty( $input['include'] ) && is_array( $input['include'] ) ) {
			$args['post__in'] = array_map( 'intval', $input['include'] );
			if ( 'include' === $args['orderby'] ) {
				$args['orderby'] = 'post__in';
			}
		}

		if ( isset( $input['parent'] ) ) {
			$args['post_parent'] = (int) $input['parent'];
		}

		if ( ! empty( $input['parent_exclude'] ) && is_array( $input['parent_exclude'] ) ) {
			$args['post_parent__not_in'] = array_map( 'intval', $input['parent_exclude'] );
		}

		if ( isset( $input['featured_media_id'] ) && (int) $input['featured_media_id'] > 0 ) {
			$args['meta_query']   = isset( $args['meta_query'] ) && is_array( $args['meta_query'] ) ? $args['meta_query'] : array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			$args['meta_query'][] = array(
				'key'     => '_thumbnail_id',
				'value'   => (int) $input['featured_media_id'],
				'compare' => '=',
				'type'    => 'NUMERIC',
			);
		}

		if ( ! empty( $input['slug'] ) ) {
			if ( is_array( $input['slug'] ) ) {
				$args['post_name__in'] = array_map( 'sanitize_title', $input['slug'] );
			} else {
				$args['post_name__in'] = array( sanitize_title( $input['slug'] ) );
			}
		}

		return $args;
	}

	/**
	 * Build post data based on context.
	 *
	 * @param \WP_Post $post             Post object.
	 * @param string   $context          Context (view, embed, edit).
	 * @param bool     $include_meta     Whether to include meta data.
	 * @param bool     $include_featured Whether to include featured_media_id.
	 * @return array<string, mixed> Post data.
	 */
	private function build_post_data( \WP_Post $post, string $context, bool $include_meta, bool $include_featured = true ): array {
		$post_data = array(
			'id'         => $post->ID,
			'title'      => $post->post_title,
			'slug'       => $post->post_name,
			'status'     => $post->post_status,
			'parent_id'  => (int) $post->post_parent,
			'menu_order' => (int) $post->menu_order,
		);

		if ( $include_featured ) {
			$post_data['featured_media_id'] = (int) get_post_thumbnail_id( $post->ID );
		}

		switch ( $context ) {
			case 'embed':
				$post_data['excerpt']       = wp_trim_words( $post->post_excerpt ? $post->post_excerpt : $post->post_content, 55 );
				$post_data['date_created']  = $post->post_date;
				$post_data['date_modified'] = $post->post_modified;
				break;

			case 'edit':
				$post_data['content']       = $post->post_content;
				$post_data['excerpt']       = $post->post_excerpt;
				$post_data['author_id']     = (int) $post->post_author;
				$post_data['date_created']  = $post->post_date;
				$post_data['date_modified'] = $post->post_modified;

				if ( $include_meta ) {
					$post_data['meta'] = get_post_meta( $post->ID );
				}
				break;

			case 'view':
			default:
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				$post_data['content']       = apply_filters( 'the_content', $post->post_content );
				$post_data['excerpt']       = $post->post_excerpt;
				$post_data['author_id']     = (int) $post->post_author;
				$post_data['date_created']  = $post->post_date;
				$post_data['date_modified'] = $post->post_modified;

				if ( $include_meta ) {
					$post_data['meta'] = get_post_meta( $post->ID );
				}
				break;
		}

		return $post_data;
	}

	/**
	 * Filter post data to only include specified fields.
	 *
	 * @param array<string, mixed> $post_data Complete post data.
	 * @param array<int, string>   $fields    Array of field names to include.
	 * @return array<string, mixed> Filtered post data.
	 */
	private function filter_fields( array $post_data, array $fields ): array {
		$filtered = array();

		if ( isset( $post_data['id'] ) ) {
			$filtered['id'] = $post_data['id'];
		}

		foreach ( $fields as $field ) {
			$field = sanitize_key( $field );
			if ( isset( $post_data[ $field ] ) && 'id' !== $field ) {
				$filtered[ $field ] = $post_data[ $field ];
			}
		}

		return $filtered;
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
				'post_type'         => array(
					'type'        => 'string',
					'description' => __( 'The post type (post, page, or custom post type). Defaults to page.', 'airo-wp' ),
					'default'     => 'page',
				),
				'page'              => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'airo-wp' ),
					'default'     => 1,
					'minimum'     => 1,
				),
				'per_page'          => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to be returned in result set', 'airo-wp' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'search'            => array(
					'type'        => 'string',
					'description' => __( 'Limit results to those matching a string', 'airo-wp' ),
				),
				'author'            => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to posts assigned to specific author ID', 'airo-wp' ),
				),
				// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- schema definition, not a query call.
				'exclude'           => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes specific IDs', 'airo-wp' ),
				),
				'include'           => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to specific IDs', 'airo-wp' ),
				),
				'order'             => array(
					'type'        => 'string',
					'description' => __( 'Order sort attribute ascending or descending', 'airo-wp' ),
					'enum'        => array( 'asc', 'desc' ),
					'default'     => 'desc',
				),
				'orderby'           => array(
					'type'        => 'string',
					'description' => __( 'Sort collection by post attribute', 'airo-wp' ),
					'enum'        => array( 'author', 'date', 'id', 'include', 'modified', 'parent', 'title', 'menu_order' ),
					'default'     => 'date',
				),
				'parent'            => array(
					'type'        => 'integer',
					'description' => __( 'Limit result set to items with particular parent ID', 'airo-wp' ),
				),
				'parent_exclude'    => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to all items except those of a particular parent ID', 'airo-wp' ),
				),
				'slug'              => array(
					'type'        => array( 'string', 'array' ),
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Limit result set to posts with one or more specific slugs. Can be a single slug string or an array of slugs', 'airo-wp' ),
				),
				'status'            => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to posts assigned one or more statuses', 'airo-wp' ),
					'enum'        => array( 'publish', 'future', 'draft', 'pending', 'private', 'trash', 'any' ),
					'default'     => 'publish',
				),
				'featured_media_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'Limit result set to items whose featured image is the given attachment ID', 'airo-wp' ),
				),
				'include_meta'      => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include post meta data', 'airo-wp' ),
					'default'     => false,
				),
				'_fields'           => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Limit response to specific fields. Available fields: id, title, content, excerpt, status, author_id, date_created, date_modified, slug, parent_id, menu_order, featured_media_id, meta', 'airo-wp' ),
				),
				'context'           => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'airo-wp' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
			),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'posts'       => array(
					'type'        => 'array',
					'description' => __( 'Array of post objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'                => array(
								'type'        => 'integer',
								'description' => __( 'The post ID', 'airo-wp' ),
							),
							'title'             => array(
								'type'        => 'string',
								'description' => __( 'The post title', 'airo-wp' ),
							),
							'content'           => array(
								'type'        => 'string',
								'description' => __( 'The post content', 'airo-wp' ),
							),
							'excerpt'           => array(
								'type'        => 'string',
								'description' => __( 'The post excerpt', 'airo-wp' ),
							),
							'status'            => array(
								'type'        => 'string',
								'description' => __( 'The post status', 'airo-wp' ),
							),
							'author_id'         => array(
								'type'        => 'integer',
								'description' => __( 'The post author ID', 'airo-wp' ),
							),
							'date_created'      => array(
								'type'        => 'string',
								'description' => __( 'The post creation date', 'airo-wp' ),
							),
							'date_modified'     => array(
								'type'        => 'string',
								'description' => __( 'The post modification date', 'airo-wp' ),
							),
							'slug'              => array(
								'type'        => 'string',
								'description' => __( 'The post slug', 'airo-wp' ),
							),
							'parent_id'         => array(
								'type'        => 'integer',
								'description' => __( 'The parent post ID', 'airo-wp' ),
							),
							'menu_order'        => array(
								'type'        => 'integer',
								'description' => __( 'The post menu order', 'airo-wp' ),
							),
							'featured_media_id' => array(
								'type'        => 'integer',
								'description' => __( 'The featured image attachment ID (0 if none)', 'airo-wp' ),
							),
							'meta'              => array(
								'type'        => 'object',
								'description' => __( 'Post meta data (if requested)', 'airo-wp' ),
							),
						),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of posts matching the query', 'airo-wp' ),
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
