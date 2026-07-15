<?php
/**
 * UpdateTemplate MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the update-template MCP ability.
 */
class UpdateTemplate extends BaseTool {

	public const TOOL_ID = 'airo-wp/update-template';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the update template ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Template', 'airo-wp' ),
				'description'         => __( 'Updates a template in the database with new HTML content. Creates the template if it does not exist.', 'airo-wp' ),
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
	 * Uses WordPress core REST API controller for template operations.
	 * Permissions are handled automatically via the permission_callback.
	 *
	 * @param array $input Tool input parameters.
	 * @return array
	 */
	public function execute( array $input ): array {
		try {
			// Validate required parameters.
			$theme         = isset( $input['theme'] ) ? sanitize_text_field( $input['theme'] ) : '';
			$template_id   = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';
			$template_name = isset( $input['template_name'] ) ? sanitize_text_field( $input['template_name'] ) : '';
			$html_content  = isset( $input['html'] ) ? $input['html'] : '';

			if ( empty( $theme ) ) {
				return array(
					'success' => false,
					'message' => __( 'Theme parameter is required', 'airo-wp' ),
				);
			}

			if ( empty( $template_id ) && empty( $template_name ) ) {
				return array(
					'success' => false,
					'message' => __( 'Either template ID or template_name is required', 'airo-wp' ),
				);
			}

			if ( empty( $html_content ) ) {
				return array(
					'success' => false,
					'message' => __( 'HTML content is required', 'airo-wp' ),
				);
			}

			if ( ! empty( $template_id ) ) {
				$wp_template_id = (string) $template_id;
				// Extract slug from template ID.
				$parts = explode( '//', $wp_template_id );
				$slug  = end( $parts );
			} else {
				// Build template ID in WordPress format: theme//slug.
				$wp_template_id = $theme . '//' . $template_name;
				$slug           = $template_name;
			}

			/*
			 * Use WordPress core function to check if template exists.
			 * This handles both database and file system lookups automatically.
			 */
			$existing_template = get_block_template( $wp_template_id, 'wp_template' );

			$was_already_db_backed = $existing_template && ! empty( $existing_template->wp_id );

			// Prepare request object for WordPress REST API.
			$request = new \WP_REST_Request( 'POST', '/wp/v2/templates' );
			$request->set_param( 'id', $wp_template_id );
			$request->set_param( 'theme', $theme );
			$request->set_param( 'slug', $slug );
			$request->set_param( 'content', $html_content );

			/*
			 * Use WordPress REST controller for create/update operations.
			 * Permission checks pass automatically because we're running as admin
			 * via the permission_callback.
			 */
			$controller = new \WP_REST_Templates_Controller( 'wp_template' );

			if ( $existing_template ) {
				// Update existing template using WordPress core.
				$response = $controller->update_item( $request );
			} else {
				// Create new template using WordPress core.
				$response = $controller->create_item( $request );
			}

			// Handle WordPress REST API errors.
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$data  = $response->get_data();
			$wp_id = isset( $data['wp_id'] ) ? (int) $data['wp_id'] : 0;

			if ( ! $was_already_db_backed && $wp_id > 0 ) {
				wp_save_post_revision( $wp_id );
			}

			return array(
				'success' => true,
				'data'    => array(
					'id'      => $data['id'],
					'slug'    => $data['slug'],
					'theme'   => $data['theme'],
					'wp_id'   => $wp_id > 0 ? $wp_id : null,
					'content' => $data['content']['raw'],
				),
				'message' => __( 'Template updated successfully', 'airo-wp' ),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error updating template: %s', 'airo-wp' ),
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
				'theme'         => array(
					'type'        => 'string',
					'description' => __( 'The theme slug where the template belongs', 'airo-wp' ),
				),
				'id'            => array(
					'type'        => 'string',
					'description' => __( 'The template ID (optional if template_name is provided)', 'airo-wp' ),
				),
				'template_name' => array(
					'type'        => 'string',
					'description' => __( 'The template name/slug (optional if id is provided, e.g., "page", "single")', 'airo-wp' ),
				),
				'html'          => array(
					'type'        => 'string',
					'description' => __( 'The new HTML content for the template', 'airo-wp' ),
				),
			),
			'required'   => array( 'theme', 'html' ),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Template update result', 'airo-wp' ),
			array(
				'data' => array(
					'type'        => 'object',
					'description' => __( 'Template data', 'airo-wp' ),
					'properties'  => array(
						'id'      => array(
							'type'        => 'string',
							'description' => __( 'The template ID', 'airo-wp' ),
						),
						'slug'    => array(
							'type'        => 'string',
							'description' => __( 'The template slug', 'airo-wp' ),
						),
						'theme'   => array(
							'type'        => 'string',
							'description' => __( 'The theme slug', 'airo-wp' ),
						),
						'wp_id'   => array(
							'type'        => array( 'integer', 'null' ),
							'description' => __( 'Numeric post ID in wp_posts table; used for revision operations. Null when not yet persisted as a DB override.', 'airo-wp' ),
						),
						'content' => array(
							'type'        => 'string',
							'description' => __( 'The updated HTML content', 'airo-wp' ),
						),
					),
				),
			)
		);
	}
}
