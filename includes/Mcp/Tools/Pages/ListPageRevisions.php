<?php
/**
 * ListPageRevisions MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-page-revisions MCP ability.
 *
 * Uses WP_Query against the `revision` post type filtered by `post_parent`
 * (which must be a page) so pagination, totals, search, and include/exclude
 * are computed at the DB layer in a single round-trip.
 */
class ListPageRevisions extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-page-revisions';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_pages' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Page Revisions', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of revisions for a page with filtering and pagination options', 'airo-wp' ),
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
	 * @return array<string, mixed> List of revisions or error.
	 */
	public function execute( array $input ): array {
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent page ID is required', 'airo-wp' ),
			);
		}

		$parent_id = (int) $input['parent'];

		// Check if parent post exists.
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: post ID */
					__( 'Post with ID %d not found', 'airo-wp' ),
					$parent_id
				),
			);
		}

		// Verify the parent is a page.
		if ( 'page' !== $parent_post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: post ID */
					__( 'Post with ID %d is not a page', 'airo-wp' ),
					$parent_id
				),
			);
		}

		// Build WP_Query args so pagination and filtering happen at the DB layer.
		$per_page   = isset( $input['per_page'] ) ? (int) $input['per_page'] : 10;
		$page       = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$use_offset = isset( $input['offset'] );
		$offset     = $use_offset ? (int) $input['offset'] : 0;
		$has_search = ! empty( $input['search'] );

		$args = array(
			'post_type'      => 'revision',
			'post_parent'    => $parent_id,
			'post_status'    => 'inherit',
			'posts_per_page' => $per_page,
			'orderby'        => $this->map_orderby( isset( $input['orderby'] ) ? $input['orderby'] : 'date', $has_search ),
			'order'          => isset( $input['order'] ) ? strtoupper( $input['order'] ) : 'DESC',
		);

		// `offset` and `paged` are mutually exclusive in WP_Query — combining them
		// silently breaks found_posts / max_num_pages. Honor offset exclusively
		// when provided, otherwise use page-based pagination.
		if ( $use_offset ) {
			$args['offset'] = $offset;
		} else {
			$args['paged'] = $page;
		}

		if ( $has_search ) {
			$args['s'] = sanitize_text_field( $input['search'] );
		}
		if ( ! empty( $input['include'] ) && is_array( $input['include'] ) ) {
			$args['post__in'] = array_map( 'intval', $input['include'] );
		}
		if ( ! empty( $input['exclude'] ) && is_array( $input['exclude'] ) ) {
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- caller-supplied filter on bounded revision sets.
			$args['post__not_in'] = array_map( 'intval', $input['exclude'] );
		}

		$query   = new \WP_Query( $args );
		$context = isset( $input['context'] ) ? $input['context'] : 'view';

		$revision_data = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$revision_data[] = $this->format_revision( get_post(), $context );
			}
			wp_reset_postdata();
		}

		// Report the effective page so the response is internally consistent
		// regardless of which pagination mode the caller used.
		$effective_page = $use_offset
			? ( (int) floor( $offset / max( 1, $per_page ) ) + 1 )
			: $page;

		return array(
			'revisions'   => $revision_data,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => $effective_page,
			'per_page'    => $per_page,
		);
	}

	/**
	 * Translate the schema's public `orderby` enum to a WP_Query-native key.
	 *
	 * @param string $orderby    Raw orderby value from input.
	 * @param bool   $has_search Whether the query carries a search term.
	 * @return string WP_Query-compatible orderby value.
	 */
	private function map_orderby( string $orderby, bool $has_search ): string {
		$map = array(
			'date'          => 'date',
			'id'            => 'ID',
			'slug'          => 'name',
			'title'         => 'title',
			'include'       => 'post__in',
			'include_slugs' => 'post_name__in',
			'relevance'     => 'relevance',
		);

		$mapped = isset( $map[ $orderby ] ) ? $map[ $orderby ] : 'date';

		if ( 'relevance' === $mapped && ! $has_search ) {
			return 'date';
		}

		return $mapped;
	}

	/**
	 * Format revision data based on context.
	 *
	 * @param \WP_Post $revision Revision post object.
	 * @param string   $context  Context (view, embed, edit).
	 * @return array<string, mixed> Formatted revision data.
	 */
	private function format_revision( \WP_Post $revision, string $context ): array {
		$data = array(
			'id'            => $revision->ID,
			'author_id'     => (int) $revision->post_author,
			'date_created'  => $revision->post_date,
			'date_modified' => $revision->post_modified,
			'parent_id'     => (int) $revision->post_parent,
			'slug'          => $revision->post_name,
		);

		switch ( $context ) {
			case 'embed':
				$data['title']   = $revision->post_title;
				$data['excerpt'] = wp_trim_words( $revision->post_excerpt ? $revision->post_excerpt : $revision->post_content, 55 );
				break;

			case 'edit':
				$data['title']   = $revision->post_title;
				$data['content'] = $revision->post_content;
				$data['excerpt'] = $revision->post_excerpt;
				break;

			case 'view':
			default:
				$data['title'] = $revision->post_title;
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				$data['content'] = apply_filters( 'the_content', $revision->post_content );
				$data['excerpt'] = $revision->post_excerpt;
				break;
		}

		return $data;
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
				'parent'   => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the page for the revisions', 'airo-wp' ),
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
					'description' => __( 'Offset the result set by a specific number of items. When provided, takes precedence over `page` — the two are mutually exclusive in WP_Query and combining them breaks pagination totals.', 'airo-wp' ),
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
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	public function get_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'revisions'   => array(
					'type'        => 'array',
					'description' => __( 'Array of revision objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'            => array(
								'type'        => 'integer',
								'description' => __( 'The revision ID', 'airo-wp' ),
							),
							'author_id'     => array(
								'type'        => 'integer',
								'description' => __( 'The revision author ID', 'airo-wp' ),
							),
							'date_created'  => array(
								'type'        => 'string',
								'description' => __( 'The revision creation date', 'airo-wp' ),
							),
							'date_modified' => array(
								'type'        => 'string',
								'description' => __( 'The revision modification date', 'airo-wp' ),
							),
							'parent_id'     => array(
								'type'        => 'integer',
								'description' => __( 'The parent page ID', 'airo-wp' ),
							),
							'slug'          => array(
								'type'        => 'string',
								'description' => __( 'The revision slug', 'airo-wp' ),
							),
							'title'         => array(
								'type'        => 'string',
								'description' => __( 'The revision title', 'airo-wp' ),
							),
							'content'       => array(
								'type'        => 'string',
								'description' => __( 'The revision content', 'airo-wp' ),
							),
							'excerpt'       => array(
								'type'        => 'string',
								'description' => __( 'The revision excerpt', 'airo-wp' ),
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
			),
		);
	}
}
