<?php
/**
 * GetGlobalStyles MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-global-styles MCP ability.
 */
class GetGlobalStyles extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-global-styles';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the get global styles ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Global Styles', 'airo-wp' ),
				'description'         => __( 'Retrieves global style configuration by ID', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the tool.
	 *
	 * @param array $input Tool input parameters.
	 * @return array
	 */
	public function execute( array $input ): array {
		try {
			$style_id = isset( $input['id'] ) ? absint( $input['id'] ) : 0;
			$context  = isset( $input['context'] ) ? sanitize_text_field( $input['context'] ) : 'view';

			if ( empty( $style_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Style ID is required', 'airo-wp' ),
				);
			}

			$global_style = get_post( $style_id );

			if ( ! $global_style || 'wp_global_styles' !== $global_style->post_type ) {
				return array(
					'success' => false,
					'message' => __( 'Global style not found or invalid ID', 'airo-wp' ),
				);
			}

			$style_content = ! empty( $global_style->post_content ) ? json_decode( $global_style->post_content, true ) : array();

			$response_data = array(
				'id'    => (int) $global_style->ID,
				'title' => array(
					'rendered' => $global_style->post_title,
				),
			);

			if ( 'edit' === $context ) {
				$response_data['title']['raw'] = $global_style->post_title;
				$response_data['status']       = $global_style->post_status;
				$response_data['date']         = $global_style->post_date;
				$response_data['date_gmt']     = $global_style->post_date_gmt;
				$response_data['modified']     = $global_style->post_modified;
				$response_data['modified_gmt'] = $global_style->post_modified_gmt;
				$response_data['slug']         = $global_style->post_name;
				$response_data['settings']     = isset( $style_content['settings'] ) ? $style_content['settings'] : new \stdClass();
				$response_data['styles']       = isset( $style_content['styles'] ) ? $style_content['styles'] : new \stdClass();
			} elseif ( 'embed' !== $context ) {
				// View context: standard fields (embed returns only id and title).
				$response_data['status']   = $global_style->post_status;
				$response_data['date']     = $global_style->post_date;
				$response_data['modified'] = $global_style->post_modified;
				$response_data['settings'] = isset( $style_content['settings'] ) ? $style_content['settings'] : new \stdClass();
				$response_data['styles']   = isset( $style_content['styles'] ) ? $style_content['styles'] : new \stdClass();
			}

			return array(
				'success' => true,
				'data'    => $response_data,
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				/* translators: %s: Error message */
				'message' => sprintf( __( 'Error retrieving global style: %s', 'airo-wp' ), $e->getMessage() ),
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
				'id'      => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'The ID of the global style to retrieve', 'airo-wp' ),
				),
				'context' => array(
					'type'        => 'string',
					'enum'        => array( 'view', 'edit', 'embed' ),
					'default'     => 'view',
					'description' => __( 'The context in which the request is made', 'airo-wp' ),
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
			__( 'Global styles retrieval result', 'airo-wp' ),
			array(
				'data' => array(
					'type'        => 'object',
					'description' => __( 'Global style data', 'airo-wp' ),
					'properties'  => array(
						'id'           => array(
							'type'        => 'integer',
							'description' => __( 'The global style ID', 'airo-wp' ),
						),
						'title'        => array(
							'type'        => 'object',
							'description' => __( 'The global style title object', 'airo-wp' ),
							'properties'  => array(
								'rendered' => array(
									'type'        => 'string',
									'description' => __( 'Rendered title', 'airo-wp' ),
								),
								'raw'      => array(
									'type'        => 'string',
									'description' => __( 'Raw title (edit context only)', 'airo-wp' ),
								),
							),
						),
						'status'       => array(
							'type'        => 'string',
							'description' => __( 'The global style post status', 'airo-wp' ),
						),
						'date'         => array(
							'type'        => 'string',
							'description' => __( 'The global style creation date', 'airo-wp' ),
						),
						'date_gmt'     => array(
							'type'        => 'string',
							'description' => __( 'The global style creation date in GMT (edit context only)', 'airo-wp' ),
						),
						'modified'     => array(
							'type'        => 'string',
							'description' => __( 'The global style modification date', 'airo-wp' ),
						),
						'modified_gmt' => array(
							'type'        => 'string',
							'description' => __( 'The global style modification date in GMT (edit context only)', 'airo-wp' ),
						),
						'slug'         => array(
							'type'        => 'string',
							'description' => __( 'The global style slug (edit context only)', 'airo-wp' ),
						),
						'settings'     => array(
							'type'        => 'object',
							'description' => __( 'The global style settings object', 'airo-wp' ),
						),
						'styles'       => array(
							'type'        => 'object',
							'description' => __( 'The global style styles object', 'airo-wp' ),
						),
					),
				),
			)
		);
	}
}
