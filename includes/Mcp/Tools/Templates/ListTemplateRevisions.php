<?php
/**
 * ListTemplateRevisions MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-template-revisions MCP ability.
 */
class ListTemplateRevisions extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-template-revisions';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the list template revisions ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Template Revisions', 'airo-wp' ),
				'description'         => __( 'Retrieves revisions for a block template', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the list template revisions tool.
	 *
	 * Uses WordPress core REST API controller for template revision operations.
	 * Permissions are handled automatically via the permission_callback.
	 *
	 * @param array $input Input parameters.
	 * @return array List of revisions or error.
	 */
	public function execute( array $input ): array {
		try {
			$template_id = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';

			if ( empty( $template_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Template ID is required', 'airo-wp' ),
				);
			}

			// Get the template to find its wp_id.
			$template = get_block_template( $template_id, 'wp_template' );

			if ( ! $template ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: template ID */
						__( 'Template "%s" not found', 'airo-wp' ),
						$template_id
					),
				);
			}

			if ( empty( $template->wp_id ) ) {
				return array(
					'success'   => true,
					'revisions' => array(),
					'total'     => 0,
					'message'   => sprintf(
						/* translators: %s: template ID */
						__( 'No revisions found for template "%s"', 'airo-wp' ),
						$template_id
					),
				);
			}

			// Prepare request object for WordPress REST API.
			$request = new \WP_REST_Request( 'GET', '/wp/v2/templates/' . $template_id . '/revisions' );
			$request->set_param( 'parent', $template->wp_id );
			$request->set_param( 'per_page', isset( $input['per_page'] ) ? (int) $input['per_page'] : 10 );
			$request->set_param( 'page', isset( $input['page'] ) ? (int) $input['page'] : 1 );

			// Use WordPress REST controller for revisions.
			$controller = new \WP_REST_Revisions_Controller( 'wp_template' );
			$response   = $controller->get_items( $request );

			// Handle WordPress REST API errors.
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$revisions = $response->get_data();

			return array(
				'success'     => true,
				'revisions'   => $revisions,
				'total'       => count( $revisions ),
				'total_pages' => 1,
				'message'     => sprintf(
					/* translators: %1$d: number of revisions, %2$s: template ID */
					__( 'Retrieved %1$d revision(s) for template "%2$s"', 'airo-wp' ),
					count( $revisions ),
					$template_id
				),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error listing template revisions: %s', 'airo-wp' ),
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
					'type'        => 'string',
					'description' => __( 'Template ID in format theme//slug', 'airo-wp' ),
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
			__( 'Template revisions list result', 'airo-wp' ),
			array(
				'revisions'   => array(
					'type'        => 'array',
					'description' => __( 'Array of revision objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'     => array(
								'type'        => 'integer',
								'description' => __( 'Revision ID', 'airo-wp' ),
							),
							'parent' => array(
								'type'        => 'integer',
								'description' => __( 'Parent template wp_id', 'airo-wp' ),
							),
							'author' => array(
								'type'        => 'integer',
								'description' => __( 'Author ID', 'airo-wp' ),
							),
							'date'   => array(
								'type'        => 'string',
								'description' => __( 'Revision date', 'airo-wp' ),
							),
							'slug'   => array(
								'type'        => 'string',
								'description' => __( 'Revision slug', 'airo-wp' ),
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
