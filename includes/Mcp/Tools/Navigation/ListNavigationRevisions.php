<?php
/**
 * ListNavigationRevisions MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-navigation-revisions MCP ability.
 */
class ListNavigationRevisions extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-navigation-revisions';

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
				'label'               => __( 'List Navigation Revisions', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of revisions for a specific navigation post with filtering and pagination options', 'airo-wp' ),
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
	 * @return array<string, mixed>
	 */
	public function execute( array $input ): array {
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent navigation ID is required', 'airo-wp' ),
			);
		}

		$parent_id = (int) $input['parent'];

		$parent_post = get_post( $parent_id );
		if ( ! $parent_post || 'wp_navigation' !== $parent_post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: Navigation post ID */
					__( 'Navigation with ID %d not found', 'airo-wp' ),
					$parent_id
				),
			);
		}

		if ( ! current_user_can( 'edit_post', $parent_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to view revisions for this navigation', 'airo-wp' ),
			);
		}

		$args = $this->build_query_args( $input, $parent_id );

		$revisions_query = new \WP_Query( $args );

		$revisions = array();
		$context   = isset( $input['context'] ) ? $input['context'] : 'view';

		if ( $revisions_query->have_posts() ) {
			while ( $revisions_query->have_posts() ) {
				$revisions_query->the_post();
				$revision = get_post();

				$revisions[] = $this->build_revision_data( $revision, $context );
			}
			wp_reset_postdata();
		}

		return array(
			'success'     => true,
			'revisions'   => $revisions,
			'total'       => (int) $revisions_query->found_posts,
			'total_pages' => (int) $revisions_query->max_num_pages,
			'page'        => (int) ( isset( $input['page'] ) ? $input['page'] : 1 ),
			'per_page'    => (int) ( isset( $input['per_page'] ) ? $input['per_page'] : 10 ),
		);
	}

	/**
	 * Build WP_Query arguments from input parameters.
	 *
	 * @param array<string, mixed> $input     Input parameters.
	 * @param int                  $parent_id Parent navigation ID.
	 * @return array<string, mixed> WP_Query arguments.
	 */
	private function build_query_args( array $input, int $parent_id ): array {
		$args = array(
			'post_type'      => 'revision',
			'post_parent'    => $parent_id,
			'post_status'    => 'inherit',
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'paged'          => isset( $input['page'] ) ? (int) $input['page'] : 1,
			'orderby'        => isset( $input['orderby'] ) ? $input['orderby'] : 'date',
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
		);

		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
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

		return $args;
	}

	/**
	 * Build revision data based on context.
	 *
	 * @param \WP_Post $revision Revision post object.
	 * @param string   $context  Response context.
	 * @return array<string, mixed> Revision data.
	 */
	private function build_revision_data( \WP_Post $revision, string $context ): array {
		$data = array(
			'id'           => $revision->ID,
			'author'       => (int) $revision->post_author,
			'date'         => $revision->post_date,
			'date_gmt'     => $revision->post_date_gmt,
			'guid'         => array(
				'rendered' => $revision->guid,
			),
			'modified'     => $revision->post_modified,
			'modified_gmt' => $revision->post_modified_gmt,
			'parent'       => (int) $revision->post_parent,
			'slug'         => $revision->post_name,
			'title'        => array(
				'rendered' => get_the_title( $revision->ID ),
			),
		);

		if ( 'edit' === $context ) {
			$data['title']['raw'] = $revision->post_title;
			$data['content']      = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $revision->post_content ),
				'raw'      => $revision->post_content,
			);
		} elseif ( 'view' === $context ) {
			$data['content'] = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $revision->post_content ),
			);
		} elseif ( 'embed' === $context ) {
			$data['content'] = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $revision->post_content ),
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
				'parent'   => array(
					'type'        => 'integer',
					'description' => __( 'The ID for the parent navigation post', 'airo-wp' ),
					'minimum'     => 1,
				),
				'context'  => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'airo-wp' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'page'     => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'airo-wp' ),
					'default'     => 1,
					'minimum'     => 1,
				),
				'per_page' => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to be returned in result set', 'airo-wp' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'search'   => array(
					'type'        => 'string',
					'description' => __( 'Limit results to those matching a string', 'airo-wp' ),
				),
				// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- schema definition, not a query call.
				'exclude'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Ensure result set excludes specific revision IDs', 'airo-wp' ),
				),
				'include'  => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'integer' ),
					'description' => __( 'Limit result set to specific revision IDs', 'airo-wp' ),
				),
				'offset'   => array(
					'type'        => 'integer',
					'description' => __( 'Offset the result set by a specific number of items', 'airo-wp' ),
					'minimum'     => 0,
				),
				'order'    => array(
					'type'        => 'string',
					'description' => __( 'Order sort attribute ascending or descending', 'airo-wp' ),
					'enum'        => array( 'asc', 'desc' ),
					'default'     => 'desc',
				),
				'orderby'  => array(
					'type'        => 'string',
					'description' => __( 'Sort collection by object attribute', 'airo-wp' ),
					'enum'        => array( 'date', 'id', 'include', 'relevance', 'slug', 'include_slugs', 'title' ),
					'default'     => 'date',
				),
			),
			'required'   => array( 'parent' ),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Navigation revisions list result', 'airo-wp' ),
			array(
				'revisions'   => array(
					'type'        => 'array',
					'description' => __( 'Array of revision objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'           => array(
								'type'        => 'integer',
								'description' => __( 'Unique identifier for the revision', 'airo-wp' ),
							),
							'author'       => array(
								'type'        => 'integer',
								'description' => __( 'The ID for the author of the revision', 'airo-wp' ),
							),
							'date'         => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was published, in the site\'s timezone', 'airo-wp' ),
							),
							'date_gmt'     => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was published, as GMT', 'airo-wp' ),
							),
							'guid'         => array(
								'type'        => 'object',
								'description' => __( 'The globally unique identifier for the post', 'airo-wp' ),
							),
							'modified'     => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was last modified, in the site\'s timezone', 'airo-wp' ),
							),
							'modified_gmt' => array(
								'type'        => 'string',
								'description' => __( 'The date the revision was last modified, as GMT', 'airo-wp' ),
							),
							'parent'       => array(
								'type'        => 'integer',
								'description' => __( 'The ID for the parent of the revision', 'airo-wp' ),
							),
							'slug'         => array(
								'type'        => 'string',
								'description' => __( 'An alphanumeric identifier for the revision unique to its type', 'airo-wp' ),
							),
							'title'        => array(
								'type'        => 'object',
								'description' => __( 'The title for the post', 'airo-wp' ),
							),
							'content'      => array(
								'type'        => 'object',
								'description' => __( 'The content for the post', 'airo-wp' ),
							),
						),
					),
				),
				'total'       => array(
					'type'        => 'integer',
					'description' => __( 'Total number of revisions', 'airo-wp' ),
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
			)
		);
	}
}
