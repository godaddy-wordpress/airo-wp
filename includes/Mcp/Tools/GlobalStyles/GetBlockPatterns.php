<?php
/**
 * GetBlockPatterns MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-block-patterns MCP ability.
 */
class GetBlockPatterns extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-block-patterns';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the get block patterns ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Block Patterns', 'airo-wp' ),
				'description'         => __( 'Retrieves all registered block patterns on the site', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the get block patterns tool.
	 *
	 * @param array $_input Input parameters (none required).
	 * @return array Block patterns result or error.
	 */
	public function execute( array $_input ): array {
		if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
			return array(
				'success' => false,
				'message' => __( 'Block patterns registry is not available', 'airo-wp' ),
			);
		}

		$registry            = \WP_Block_Patterns_Registry::get_instance();
		$registered_patterns = $registry->get_all_registered();

		$block_patterns = array();

		foreach ( $registered_patterns as $pattern_data ) {
			$block_patterns[] = array(
				'name'        => $pattern_data['name'] ?? '',
				'title'       => $pattern_data['title'] ?? '',
				'description' => $pattern_data['description'] ?? '',
				'content'     => $pattern_data['content'] ?? '',
				'categories'  => $pattern_data['categories'] ?? array(),
				'keywords'    => $pattern_data['keywords'] ?? array(),
				'blockTypes'  => $pattern_data['blockTypes'] ?? array(),
			);
		}

		return array(
			'success'        => true,
			'block_patterns' => $block_patterns,
			// translators: %d is the number of block patterns found.
			'message'        => sprintf( __( 'Retrieved %d registered block patterns', 'airo-wp' ), count( $block_patterns ) ),
		);
	}

	/**
	 * Get input schema for the tool.
	 *
	 * @return array
	 */
	private function get_input_schema(): array {
		// Zero-argument tool. Omit `properties`/`required` rather than passing
		// empty PHP arrays: json_encode() renders those as `[]`, but MCP requires
		// inputSchema.properties to be an object, and clients reject the array.
		return array(
			'type' => 'object',
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Block patterns retrieval result', 'airo-wp' ),
			array(
				'block_patterns' => array(
					'type'        => 'array',
					'description' => __( 'Array of registered block patterns', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'        => array(
								'type'        => 'string',
								'description' => __( 'The pattern name', 'airo-wp' ),
							),
							'title'       => array(
								'type'        => 'string',
								'description' => __( 'The pattern title', 'airo-wp' ),
							),
							'description' => array(
								'type'        => 'string',
								'description' => __( 'The pattern description', 'airo-wp' ),
							),
							'content'     => array(
								'type'        => 'string',
								'description' => __( 'The pattern HTML content', 'airo-wp' ),
							),
							'categories'  => array(
								'type'        => 'array',
								'description' => __( 'Array of pattern categories', 'airo-wp' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'keywords'    => array(
								'type'        => 'array',
								'description' => __( 'Array of pattern keywords', 'airo-wp' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'blockTypes'  => array(
								'type'        => 'array',
								'description' => __( 'Array of supported block types', 'airo-wp' ),
								'items'       => array(
									'type' => 'string',
								),
							),
						),
					),
				),
			)
		);
	}
}
