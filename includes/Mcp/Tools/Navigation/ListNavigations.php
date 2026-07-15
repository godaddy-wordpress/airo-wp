<?php
/**
 * ListNavigations MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-navigations MCP ability.
 */
class ListNavigations extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-navigations';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Navigations', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of navigation posts with filtering and pagination options', 'airo-wp' ),
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
	 * @return array<string, mixed> List of navigations with pagination info.
	 */
	public function execute( array $input ): array {
		$args = $this->build_query_args( $input );

		$query = new \WP_Query( $args );

		$navigations = array();
		$context     = isset( $input['context'] ) ? $input['context'] : 'view';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post = get_post();

				$navigations[] = $this->build_navigation_data( $post, $context );
			}
			wp_reset_postdata();
		}

		return array(
			'navigations' => $navigations,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => (int) ( isset( $input['page'] ) ? $input['page'] : 1 ),
			'per_page'    => (int) ( isset( $input['per_page'] ) ? $input['per_page'] : 10 ),
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
			'post_type'      => 'wp_navigation',
			'post_status'    => isset( $input['status'] ) ? $input['status'] : 'publish',
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'paged'          => isset( $input['page'] ) ? (int) $input['page'] : 1,
			'orderby'        => isset( $input['orderby'] ) ? $input['orderby'] : 'date',
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
		);

		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
		}

		if ( ! empty( $input['search_columns'] ) && is_array( $input['search_columns'] ) ) {
			$args['search_columns'] = array_map( 'sanitize_text_field', $input['search_columns'] );
		}

		if ( ! empty( $input['include'] ) && is_array( $input['include'] ) ) {
			$args['post__in'] = array_map( 'intval', $input['include'] );
		}

		if ( ! empty( $input['exclude'] ) && is_array( $input['exclude'] ) ) {
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- caller-supplied filter on bounded result sets.
			$args['post__not_in'] = array_map( 'intval', $input['exclude'] );
		}

		if ( isset( $input['offset'] ) ) {
			$args['offset'] = (int) $input['offset'];
		}

		if ( ! empty( $input['slug'] ) ) {
			if ( is_array( $input['slug'] ) ) {
				$args['post_name__in'] = array_map( 'sanitize_title', $input['slug'] );
			} else {
				$args['post_name__in'] = array( sanitize_title( $input['slug'] ) );
			}
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
	 * Build navigation data based on context.
	 *
	 * @param \WP_Post $post    Post object.
	 * @param string   $context Response context.
	 * @return array<string, mixed> Navigation data.
	 */
	private function build_navigation_data( \WP_Post $post, string $context ): array {
		$data = array(
			'id'           => $post->ID,
			'date'         => $post->post_date,
			'date_gmt'     => $post->post_date_gmt,
			'guid'         => array(
				'rendered' => $post->guid,
				'raw'      => $post->guid,
			),
			'modified'     => $post->post_modified,
			'modified_gmt' => $post->post_modified_gmt,
			'slug'         => $post->post_name,
			'status'       => $post->post_status,
			'type'         => $post->post_type,
			'link'         => get_permalink( $post->ID ),
			'title'        => array(
				'rendered' => get_the_title( $post->ID ),
				'raw'      => $post->post_title,
			),
		);

		if ( 'edit' === $context ) {
			$data['content']  = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			);
			$data['template'] = get_page_template_slug( $post->ID );
		} elseif ( 'view' === $context ) {
			$data['content']  = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
			$data['template'] = get_page_template_slug( $post->ID );
		} elseif ( 'embed' === $context ) {
			$data['content'] = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		return $data;
	}

	/**
	 * Get input schema for the tool.
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
					'description' => __( 'Limit response to posts published after a given ISO8601 compliant date', 'airo-wp' ),
				),
				'modified_after'  => array(
					'type'        => 'string',
					'description' => __( 'Limit response to posts modified after a given ISO8601 compliant date', 'airo-wp' ),
				),
				'before'          => array(
					'type'        => 'string',
					'description' => __( 'Limit response to posts published before a given ISO8601 compliant date', 'airo-wp' ),
				),
				'modified_before' => array(
					'type'        => 'string',
					'description' => __( 'Limit response to posts modified before a given ISO8601 compliant date', 'airo-wp' ),
				),
				// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- schema definition, not a query call.
				'exclude'         => array(
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
					'description' => __( 'Sort collection by post attribute', 'airo-wp' ),
					'enum'        => array( 'author', 'date', 'id', 'include', 'modified', 'parent', 'relevance', 'slug', 'include_slugs', 'title' ),
					'default'     => 'date',
				),
				'search_columns'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'description' => __( 'Array of column names to be searched', 'airo-wp' ),
				),
				'slug'            => array(
					'description' => __( 'Limit result set to posts with one or more specific slugs', 'airo-wp' ),
				),
				'status'          => array(
					'type'        => 'string',
					'description' => __( 'Limit result set to posts assigned one or more statuses', 'airo-wp' ),
					'default'     => 'publish',
				),
			),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'navigations' => array(
					'type'        => 'array',
					'description' => __( 'Array of navigation objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'           => array(
								'type'        => 'integer',
								'description' => __( 'Unique identifier for the post', 'airo-wp' ),
							),
							'date'         => array(
								'type'        => 'string',
								'description' => __( 'The date the post was published, in the site\'s timezone', 'airo-wp' ),
							),
							'date_gmt'     => array(
								'type'        => 'string',
								'description' => __( 'The date the post was published, as GMT', 'airo-wp' ),
							),
							'guid'         => array(
								'type'        => 'object',
								'description' => __( 'The globally unique identifier for the post', 'airo-wp' ),
							),
							'modified'     => array(
								'type'        => 'string',
								'description' => __( 'The date the post was last modified, in the site\'s timezone', 'airo-wp' ),
							),
							'modified_gmt' => array(
								'type'        => 'string',
								'description' => __( 'The date the post was last modified, as GMT', 'airo-wp' ),
							),
							'slug'         => array(
								'type'        => 'string',
								'description' => __( 'An alphanumeric identifier for the post unique to its type', 'airo-wp' ),
							),
							'status'       => array(
								'type'        => 'string',
								'description' => __( 'A named status for the post', 'airo-wp' ),
							),
							'type'         => array(
								'type'        => 'string',
								'description' => __( 'Type of post', 'airo-wp' ),
							),
							'link'         => array(
								'type'        => 'string',
								'description' => __( 'URL to the post', 'airo-wp' ),
							),
							'title'        => array(
								'type'        => 'object',
								'description' => __( 'The title for the post', 'airo-wp' ),
							),
							'content'      => array(
								'type'        => 'object',
								'description' => __( 'The content for the post', 'airo-wp' ),
							),
							'template'     => array(
								'type'        => 'string',
								'description' => __( 'The theme file to use to display the post', 'airo-wp' ),
							),
						),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of navigations', 'airo-wp' ),
				),
				'total_pages' => array(
					'type'        => 'integer',
					'description' => __( 'Total number of pages', 'airo-wp' ),
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
