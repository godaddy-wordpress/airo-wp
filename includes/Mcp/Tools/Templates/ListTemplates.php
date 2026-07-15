<?php
/**
 * ListTemplates MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-templates MCP ability.
 */
class ListTemplates extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-templates';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the list templates ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Templates', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of block templates with filtering options', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the list templates tool.
	 *
	 * Uses WordPress core REST API controller for template operations.
	 * Permissions are handled automatically via the permission_callback.
	 *
	 * @param array $input Input parameters.
	 * @return array List of templates or error.
	 */
	public function execute( array $input ): array {
		try {
			$context = isset( $input['context'] ) ? $input['context'] : 'edit';
			$wp_id   = isset( $input['wp_id'] ) ? (int) $input['wp_id'] : null;

			// Prepare request object for WordPress REST API.
			$request = new \WP_REST_Request( 'GET', '/wp/v2/templates' );
			$request->set_param( 'context', $context );
			if ( $wp_id ) {
				$request->set_param( 'wp_id', $wp_id );
			}

			// Use WordPress REST controller.
			$controller = new \WP_REST_Templates_Controller( 'wp_template' );
			$response   = $controller->get_items( $request );

			// Handle WordPress REST API errors.
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$templates = $response->get_data();
			foreach ( $templates as &$template ) {
				if ( array_key_exists( 'origin', $template ) && ! is_string( $template['origin'] ) ) {
					unset( $template['origin'] );
				}
				if ( array_key_exists( 'modified', $template ) && ! is_string( $template['modified'] ) ) {
					unset( $template['modified'] );
				}
			}
			unset( $template );

			return array(
				'success'   => true,
				'templates' => $templates,
				'message'   => sprintf(
					/* translators: %d: number of templates */
					__( 'Retrieved %d template(s)', 'airo-wp' ),
					count( $templates )
				),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error listing templates: %s', 'airo-wp' ),
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
				'context' => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'airo-wp' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'edit',
				),
				'wp_id'   => array(
					'type'        => 'integer',
					'description' => __( 'Limit to the specified post ID (numeric wp_posts.ID)', 'airo-wp' ),
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
			__( 'Templates list result', 'airo-wp' ),
			array(
				'templates' => array(
					'type'        => 'array',
					'description' => __( 'Array of template objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'             => array(
								'type'        => 'string',
								'description' => __( 'Template ID in format theme//slug', 'airo-wp' ),
							),
							'slug'           => array(
								'type'        => 'string',
								'description' => __( 'Template slug', 'airo-wp' ),
							),
							'theme'          => array(
								'type'        => 'string',
								'description' => __( 'Theme slug', 'airo-wp' ),
							),
							'type'           => array(
								'type'        => 'string',
								'description' => __( 'Post type (wp_template)', 'airo-wp' ),
							),
							'source'         => array(
								'type'        => 'string',
								'description' => __( 'Source of the template (theme, custom, plugin)', 'airo-wp' ),
							),
							'origin'         => array(
								'type'        => 'string',
								'description' => __( 'Theme/plugin that originally provided the template', 'airo-wp' ),
							),
							'content'        => array(
								'type'        => 'object',
								'description' => __( 'Template content', 'airo-wp' ),
							),
							'title'          => array(
								'type'        => 'object',
								'description' => __( 'Template title', 'airo-wp' ),
							),
							'description'    => array(
								'type'        => 'string',
								'description' => __( 'Template description', 'airo-wp' ),
							),
							'status'         => array(
								'type'        => 'string',
								'description' => __( 'Template status', 'airo-wp' ),
							),
							'wp_id'          => array(
								'type'        => 'integer',
								'description' => __( 'Numeric post ID in wp_posts table', 'airo-wp' ),
							),
							'has_theme_file' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether a theme file exists for this template', 'airo-wp' ),
							),
							'is_custom'      => array(
								'type'        => 'boolean',
								'description' => __( 'Whether this is a user-customized template', 'airo-wp' ),
							),
							'author'         => array(
								'type'        => 'integer',
								'description' => __( 'Author ID', 'airo-wp' ),
							),
							'modified'       => array(
								'type'        => 'string',
								'description' => __( 'Last modified date', 'airo-wp' ),
							),
						),
					),
				),
			)
		);
	}
}
