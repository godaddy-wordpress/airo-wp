<?php
/**
 * ListGlobalStylesRevisions MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-global-styles-revisions MCP ability.
 *
 * Uses WP_REST_Global_Styles_Revisions_Controller so caps and JSON
 * decoding match the rest of the global-styles REST surface.
 */
class ListGlobalStylesRevisions extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-global-styles-revisions';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the list global styles revisions ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Global Styles Revisions', 'airo-wp' ),
				'description'         => __( 'Retrieves revisions for a wp_global_styles post', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the list global styles revisions tool.
	 *
	 * @param array $input Input parameters.
	 * @return array List of revisions or error.
	 */
	public function execute( array $input ): array {
		try {
			$global_styles_id = isset( $input['id'] ) ? (int) $input['id'] : 0;

			if ( $global_styles_id <= 0 ) {
				return array(
					'success' => false,
					'message' => __( 'Global styles post ID is required', 'airo-wp' ),
				);
			}

			$parent_post = get_post( $global_styles_id );

			if ( ! $parent_post ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %d: post ID */
						__( 'Post "%d" not found', 'airo-wp' ),
						$global_styles_id
					),
				);
			}

			if ( 'wp_global_styles' !== $parent_post->post_type ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: 1: post ID, 2: post type */
						__( 'Post "%1$d" is not a wp_global_styles post (got "%2$s")', 'airo-wp' ),
						$global_styles_id,
						$parent_post->post_type
					),
				);
			}

			$request = new \WP_REST_Request( 'GET', '/wp/v2/global-styles/' . $global_styles_id . '/revisions' );
			$request->set_param( 'parent', $global_styles_id );
			$request->set_param( 'per_page', isset( $input['per_page'] ) ? (int) $input['per_page'] : 10 );
			$request->set_param( 'page', isset( $input['page'] ) ? (int) $input['page'] : 1 );

			if ( ! class_exists( '\WP_REST_Global_Styles_Revisions_Controller' ) ) {
				return array(
					'success' => false,
					'message' => __( 'Global styles revisions are not supported on this WordPress version', 'airo-wp' ),
				);
			}

			$controller = new \WP_REST_Global_Styles_Revisions_Controller( 'wp_global_styles' );
			$response   = $controller->get_items( $request );

			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$revisions   = $response->get_data();
			$headers     = $response->get_headers();
			$total       = isset( $headers['X-WP-Total'] ) ? (int) $headers['X-WP-Total'] : count( $revisions );
			$total_pages = isset( $headers['X-WP-TotalPages'] ) ? (int) $headers['X-WP-TotalPages'] : 1;

			return array(
				'success'     => true,
				'revisions'   => $revisions,
				'total'       => $total,
				'total_pages' => $total_pages,
				'message'     => sprintf(
					/* translators: 1: number of revisions, 2: post ID */
					__( 'Retrieved %1$d revision(s) for global styles post "%2$d"', 'airo-wp' ),
					count( $revisions ),
					$global_styles_id
				),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error listing global styles revisions: %s', 'airo-wp' ),
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
				'id'       => array(
					'type'        => 'integer',
					'description' => __( 'The ID of a wp_global_styles post (obtained from list-global-styles or get-global-styles)', 'airo-wp' ),
					'minimum'     => 1,
				),
				'per_page' => array(
					'type'        => 'integer',
					'description' => __( 'Maximum number of items to return', 'airo-wp' ),
					'default'     => 10,
					'minimum'     => 1,
					'maximum'     => 100,
				),
				'page'     => array(
					'type'        => 'integer',
					'description' => __( 'Current page of the collection', 'airo-wp' ),
					'default'     => 1,
					'minimum'     => 1,
				),
			),
			'required'   => array( 'id' ),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Global styles revisions list result', 'airo-wp' ),
			array(
				'revisions'   => array(
					'type'        => 'array',
					'description' => __( 'Array of revision objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'       => array(
								'type'        => 'integer',
								'description' => __( 'Revision ID', 'airo-wp' ),
							),
							'parent'   => array(
								'type'        => 'integer',
								'description' => __( 'Parent wp_global_styles post ID', 'airo-wp' ),
							),
							'author'   => array(
								'type'        => 'integer',
								'description' => __( 'Author ID', 'airo-wp' ),
							),
							'date'     => array(
								'type'        => 'string',
								'description' => __( 'Revision date', 'airo-wp' ),
							),
							'styles'   => array(
								'type'        => 'object',
								'description' => __( 'Decoded styles object', 'airo-wp' ),
							),
							'settings' => array(
								'type'        => 'object',
								'description' => __( 'Decoded settings object', 'airo-wp' ),
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
			)
		);
	}
}
