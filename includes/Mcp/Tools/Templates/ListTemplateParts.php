<?php
/**
 * ListTemplateParts MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-template-parts MCP ability.
 */
class ListTemplateParts extends BaseTool {

	public const TOOL_ID = 'airo-wp/list-template-parts';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the list template parts ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'List Template Parts', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of block template parts with filtering options', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the list template parts tool.
	 *
	 * Uses WordPress core REST API controller for template part operations.
	 * Permissions are handled automatically via the permission_callback.
	 *
	 * @param array $input Input parameters.
	 * @return array List of template parts or error.
	 */
	public function execute( array $input ): array {
		try {
			$context = isset( $input['context'] ) ? $input['context'] : 'edit';
			$wp_id   = isset( $input['wp_id'] ) ? (int) $input['wp_id'] : null;
			$area    = isset( $input['area'] ) ? sanitize_text_field( $input['area'] ) : null;

			// Prepare request object for WordPress REST API.
			$request = new \WP_REST_Request( 'GET', '/wp/v2/template-parts' );
			$request->set_param( 'context', $context );
			if ( $wp_id ) {
				$request->set_param( 'wp_id', $wp_id );
			}
			if ( $area ) {
				$request->set_param( 'area', $area );
			}

			// Use WordPress REST controller.
			$controller = new \WP_REST_Templates_Controller( 'wp_template_part' );
			$response   = $controller->get_items( $request );

			// Handle WordPress REST API errors.
			if ( is_wp_error( $response ) ) {
				return array(
					'success' => false,
					'message' => $response->get_error_message(),
				);
			}

			$template_parts = $response->get_data();
			foreach ( $template_parts as &$template_part ) {
				if ( array_key_exists( 'origin', $template_part ) && ! is_string( $template_part['origin'] ) ) {
					unset( $template_part['origin'] );
				}
				if ( array_key_exists( 'modified', $template_part ) && ! is_string( $template_part['modified'] ) ) {
					unset( $template_part['modified'] );
				}
			}
			unset( $template_part );

			return array(
				'success'        => true,
				'template_parts' => $template_parts,
				'message'        => sprintf(
					/* translators: %d: number of template parts */
					__( 'Retrieved %d template part(s)', 'airo-wp' ),
					count( $template_parts )
				),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error listing template parts: %s', 'airo-wp' ),
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
				'area'    => array(
					'type'        => 'string',
					'description' => __( 'Limit to the specified template part area (header, footer, sidebar, uncategorized)', 'airo-wp' ),
					'enum'        => array( 'header', 'footer', 'sidebar', 'uncategorized' ),
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
			__( 'Template parts list result', 'airo-wp' ),
			array(
				'template_parts' => array(
					'type'        => 'array',
					'description' => __( 'Array of template part objects', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'             => array(
								'type'        => 'string',
								'description' => __( 'Template part ID in format theme//slug', 'airo-wp' ),
							),
							'slug'           => array(
								'type'        => 'string',
								'description' => __( 'Template part slug', 'airo-wp' ),
							),
							'theme'          => array(
								'type'        => 'string',
								'description' => __( 'Theme slug', 'airo-wp' ),
							),
							'type'           => array(
								'type'        => 'string',
								'description' => __( 'Post type (wp_template_part)', 'airo-wp' ),
							),
							'source'         => array(
								'type'        => 'string',
								'description' => __( 'Source of the template part (theme, custom, plugin)', 'airo-wp' ),
							),
							'origin'         => array(
								'type'        => 'string',
								'description' => __( 'Theme/plugin that originally provided the template part', 'airo-wp' ),
							),
							'content'        => array(
								'type'        => 'object',
								'description' => __( 'Template part content', 'airo-wp' ),
							),
							'title'          => array(
								'type'        => 'object',
								'description' => __( 'Template part title', 'airo-wp' ),
							),
							'description'    => array(
								'type'        => 'string',
								'description' => __( 'Template part description', 'airo-wp' ),
							),
							'status'         => array(
								'type'        => 'string',
								'description' => __( 'Template part status', 'airo-wp' ),
							),
							'wp_id'          => array(
								'type'        => 'integer',
								'description' => __( 'Numeric post ID in wp_posts table', 'airo-wp' ),
							),
							'has_theme_file' => array(
								'type'        => 'boolean',
								'description' => __( 'Whether a theme file exists for this template part', 'airo-wp' ),
							),
							'area'           => array(
								'type'        => 'string',
								'description' => __( 'Template part area (header, footer, sidebar, uncategorized)', 'airo-wp' ),
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
