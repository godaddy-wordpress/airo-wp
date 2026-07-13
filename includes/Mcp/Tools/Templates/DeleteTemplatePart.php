<?php
/**
 * DeleteTemplatePart MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the delete-template-part MCP ability.
 */
class DeleteTemplatePart extends BaseTool {

	public const TOOL_ID = 'airo-wp/delete-template-part';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the delete template part ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Template Part', 'airo-wp' ),
				'description'         => __( 'Deletes a block template part (resets to theme default by removing DB override)', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the delete template part tool.
	 *
	 * Uses WordPress core REST API controller for template part operations.
	 * Permissions are handled automatically via the permission_callback.
	 *
	 * @param array $input Input parameters.
	 * @return array Deletion result or error.
	 */
	public function execute( array $input ): array {
		try {
			$template_part_id = isset( $input['id'] ) ? sanitize_text_field( $input['id'] ) : '';
			$force            = isset( $input['force'] ) ? (bool) $input['force'] : false;

			if ( empty( $template_part_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Template part ID is required', 'airo-wp' ),
				);
			}

			// Check if template part exists using WordPress core function.
			$existing_template_part = get_block_template( $template_part_id, 'wp_template_part' );

			if ( ! $existing_template_part ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: template part ID */
						__( 'Template part "%s" not found', 'airo-wp' ),
						$template_part_id
					),
				);
			}

			// Store previous data before deletion.
			$previous = array(
				'id'          => $existing_template_part->id,
				'slug'        => $existing_template_part->slug,
				'theme'       => $existing_template_part->theme,
				'source'      => $existing_template_part->source,
				'title'       => $existing_template_part->title,
				'description' => $existing_template_part->description,
				'area'        => $existing_template_part->area ?? 'uncategorized',
				'wp_id'       => isset( $existing_template_part->wp_id ) ? (int) $existing_template_part->wp_id : null,
			);

			// Check if this is a theme-only template part (no DB override).
			// If source is 'theme' and there's no wp_id, it's theme-only.
			if ( 'theme' === $existing_template_part->source && empty( $existing_template_part->wp_id ) ) {
				return array(
					'success' => true,
					'message' => sprintf(
						/* translators: %s: template part ID */
						__( 'Template part "%s" is theme-only (no customizations to reset)', 'airo-wp' ),
						$template_part_id
					),
					'deleted' => array(
						'id'     => $template_part_id,
						'status' => 'theme-only',
					),
				);
			}

			// Prepare request object for WordPress REST API.
			$request = new \WP_REST_Request( 'DELETE', '/wp/v2/template-parts/' . $template_part_id );
			$request->set_param( 'id', $template_part_id );
			$request->set_param( 'force', $force );

			// Use WordPress REST controller.
			$controller = new \WP_REST_Templates_Controller( 'wp_template_part' );
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
					/* translators: %s: template part ID */
					__( 'Template part "%s" reset to theme default', 'airo-wp' ),
					$template_part_id
				),
				'deleted'  => array(
					'id'     => $template_part_id,
					'status' => 'reset',
				),
				'previous' => $previous,
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error deleting template part: %s', 'airo-wp' ),
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
					'description' => __( 'Template part ID in format theme//slug (e.g., "twentytwentyfive//header")', 'airo-wp' ),
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
			__( 'Template part deletion result', 'airo-wp' ),
			array(
				'deleted'  => array(
					'type'        => 'object',
					'description' => __( 'Information about the deleted template part', 'airo-wp' ),
					'properties'  => array(
						'id'     => array(
							'type'        => 'string',
							'description' => __( 'The deleted template part ID', 'airo-wp' ),
						),
						'status' => array(
							'type'        => 'string',
							'description' => __( 'The previous status of the template part', 'airo-wp' ),
						),
					),
				),
				'previous' => array(
					'type'        => 'object',
					'description' => __( 'The template part data before deletion', 'airo-wp' ),
				),
			)
		);
	}
}
