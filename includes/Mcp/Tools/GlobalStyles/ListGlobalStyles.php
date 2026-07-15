<?php
/**
 * ListGlobalStyles MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-global-styles MCP ability.
 */
class ListGlobalStyles extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-global-styles';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the list global styles ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Global Styles', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of all global styles configurations, optionally filtered by theme', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the list global styles tool.
	 *
	 * @param array $input Input parameters.
	 * @return array Global styles list result or error.
	 */
	public function execute( array $input ): array {
		try {
			$theme  = isset( $input['theme'] ) ? sanitize_text_field( $input['theme'] ) : '';
			$status = isset( $input['status'] ) ? $input['status'] : 'any';

			$post_status = 'any' === $status ? array( 'publish', 'draft' ) : $status;

			$query_args = array(
				'post_type'      => 'wp_global_styles',
				'post_status'    => $post_status,
				'posts_per_page' => -1,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
			$global_styles = get_posts( $query_args );

			if ( empty( $global_styles ) ) {
				$message = ! empty( $theme )
					/* translators: %s: theme slug */
					? sprintf( __( 'No global styles found for theme: %s', 'airo-wp' ), $theme )
					: __( 'No global styles found', 'airo-wp' );

				return array(
					'success' => true,
					'styles'  => array(),
					'total'   => 0,
					'message' => $message,
				);
			}

			// WordPress stores the theme association in post_name
			// (e.g. "wp-global-styles-twentytwentyfive"), not in post meta.
			$styles = array();
			foreach ( $global_styles as $style ) {
				$theme_slug = '';
				if ( preg_match( '/^wp-global-styles-(.+)$/', $style->post_name, $matches ) ) {
					$theme_slug = $matches[1];
				}

				if ( empty( $theme ) || $theme_slug === $theme ) {
					$styles[] = array(
						'id'       => (int) $style->ID,
						'title'    => $style->post_title,
						'theme'    => $theme_slug,
						'status'   => $style->post_status,
						'date'     => $style->post_date,
						'modified' => $style->post_modified,
					);
				}
			}

			$total = count( $styles );

			$message = ! empty( $theme )
				/* translators: 1: number of styles, 2: theme slug */
				? sprintf( __( 'Found %1$d global styles for theme: %2$s', 'airo-wp' ), $total, $theme )
				/* translators: %d: number of styles */
				: sprintf( __( 'Found %d global styles', 'airo-wp' ), $total );

			return array(
				'success' => true,
				'styles'  => $styles,
				'total'   => $total,
				'message' => $message,
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error retrieving global styles: %s', 'airo-wp' ),
					$e->getMessage()
				),
			);
		}
	}

	/**
	 * Get input schema for the tool.
	 *
	 * @return array
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'theme'  => array(
					'type'        => 'string',
					'description' => __( 'Optional theme slug to filter global styles by specific theme', 'airo-wp' ),
				),
				'status' => array(
					'type'        => 'string',
					'description' => __( 'Filter by post status (publish, draft, etc.). Defaults to all statuses', 'airo-wp' ),
					'enum'        => array( 'publish', 'draft', 'any' ),
					'default'     => 'any',
				),
			),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Global styles list result', 'airo-wp' ),
			array(
				'styles' => array(
					'type'        => 'array',
					'description' => __( 'Array of global styles information', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'       => array(
								'type'        => 'integer',
								'description' => __( 'The global styles post ID', 'airo-wp' ),
							),
							'title'    => array(
								'type'        => 'string',
								'description' => __( 'The global styles title', 'airo-wp' ),
							),
							'theme'    => array(
								'type'        => 'string',
								'description' => __( 'The theme slug this style belongs to', 'airo-wp' ),
							),
							'status'   => array(
								'type'        => 'string',
								'description' => __( 'The post status (publish, draft, etc.)', 'airo-wp' ),
							),
							'date'     => array(
								'type'        => 'string',
								'description' => __( 'The creation date', 'airo-wp' ),
							),
							'modified' => array(
								'type'        => 'string',
								'description' => __( 'The last modified date', 'airo-wp' ),
							),
						),
					),
				),
				'total'  => array(
					'type'        => 'integer',
					'description' => __( 'Total number of global styles found', 'airo-wp' ),
				),
			)
		);
	}
}
