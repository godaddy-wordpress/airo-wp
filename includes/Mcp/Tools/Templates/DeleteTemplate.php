<?php
/**
 * DeleteTemplate MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the delete-template MCP ability.
 */
class DeleteTemplate extends BaseTool {

	public const TOOL_ID = 'airo-wp/delete-template';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the delete template ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Template', 'airo-wp' ),
				'description'         => __( 'Deletes a block template (resets to theme default by removing DB override)', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the delete template tool.
	 *
	 * Uses WordPress core REST API controller for template operations.
	 * Permissions are handled automatically via the permission_callback.
	 *
	 * @param array $input Input parameters.
	 * @return array Deletion result or error.
	 */
	public function execute( array $input ): array {
		try {
			$template_id = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';
			$force       = isset( $input['force'] ) ? (bool) $input['force'] : false;

			if ( empty( $template_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Template ID is required', 'airo-wp' ),
				);
			}

			// Check if template exists using WordPress core function.
			$existing_template = get_block_template( $template_id, 'wp_template' );

			if ( ! $existing_template ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: template ID */
						__( 'Template "%s" not found', 'airo-wp' ),
						$template_id
					),
				);
			}

			// Store previous data before deletion.
			$previous = array(
				'id'          => $existing_template->id,
				'slug'        => $existing_template->slug,
				'theme'       => $existing_template->theme,
				'source'      => $existing_template->source,
				'title'       => $existing_template->title,
				'description' => $existing_template->description,
				'wp_id'       => isset( $existing_template->wp_id ) ? (int) $existing_template->wp_id : null,
			);

			// Check if this is a theme-only template (no DB override).
			// If source is 'theme' and there's no wp_id, it's theme-only.
			if ( 'theme' === $existing_template->source && empty( $existing_template->wp_id ) ) {
				return array(
					'success' => true,
					'message' => sprintf(
						/* translators: %s: template ID */
						__( 'Template "%s" is theme-only (no customizations to reset)', 'airo-wp' ),
						$template_id
					),
					'deleted' => array(
						'id'     => $template_id,
						'status' => 'theme-only',
					),
				);
			}

			// Prepare request object for WordPress REST API.
			$request = new \WP_REST_Request( 'DELETE', '/wp/v2/templates/' . $template_id );
			$request->set_param( 'id', $template_id );
			$request->set_param( 'force', $force );

			// Use WordPress REST controller.
			$controller = new \WP_REST_Templates_Controller( 'wp_template' );
			$response   = $controller->delete_item( $request );

			// Handle WordPress REST API errors.
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			return array(
				'success'  => true,
				'message'  => sprintf(
					/* translators: %s: template ID */
					__( 'Template "%s" reset to theme default', 'airo-wp' ),
					$template_id
				),
				'deleted'  => array(
					'id'     => $template_id,
					'status' => 'reset',
				),
				'previous' => $previous,
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error deleting template: %s', 'airo-wp' ),
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
				'id'    => array(
					'type'        => 'string',
					'description' => __( 'Template ID in format theme//slug (e.g., "twentytwentyfive//page")', 'airo-wp' ),
				),
				'force' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to force deletion (bypass trash)', 'airo-wp' ),
					'default'     => false,
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
			__( 'Template deletion result', 'airo-wp' ),
			array(
				'deleted'  => array(
					'type'        => 'object',
					'description' => __( 'Information about the deleted template', 'airo-wp' ),
					'properties'  => array(
						'id'     => array(
							'type'        => 'string',
							'description' => __( 'The deleted template ID', 'airo-wp' ),
						),
						'status' => array(
							'type'        => 'string',
							'description' => __( 'The previous status of the template', 'airo-wp' ),
						),
					),
				),
				'previous' => array(
					'type'        => 'object',
					'description' => __( 'The template data before deletion', 'airo-wp' ),
				),
			)
		);
	}
}
