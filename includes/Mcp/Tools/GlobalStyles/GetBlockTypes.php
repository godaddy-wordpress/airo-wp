<?php
/**
 * GetBlockTypes MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-block-types MCP ability.
 */
class GetBlockTypes extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-block-types';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the get block types ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Block Types', 'airo-wp' ),
				'description'         => __( 'Retrieves all registered block types on the site', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the get block types tool.
	 *
	 * @param array $_input Input parameters (none required).
	 * @return array Block types result or error.
	 */
	public function execute( array $_input ): array {
		if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
			return array(
				'success' => false,
				'message' => __( 'Block type registry is not available', 'airo-wp' ),
			);
		}

		$block_registry    = \WP_Block_Type_Registry::get_instance();
		$registered_blocks = $block_registry->get_all_registered();

		if ( ! is_array( $registered_blocks ) ) {
			return array(
				'success' => false,
				'message' => __( 'Unable to retrieve registered block types', 'airo-wp' ),
			);
		}

		$block_types = array();

		foreach ( $registered_blocks as $block_name => $block_type ) {
			$block_types[] = array(
				'name'           => $block_name,
				'title'          => isset( $block_type->title ) ? $block_type->title : '',
				'description'    => isset( $block_type->description ) ? $block_type->description : '',
				'category'       => isset( $block_type->category ) ? $block_type->category : '',
				'icon'           => isset( $block_type->icon ) ? $block_type->icon : '',
				'keywords'       => isset( $block_type->keywords ) ? $block_type->keywords : array(),
				'api_version'    => isset( $block_type->api_version ) ? (int) $block_type->api_version : 1,
				'is_dynamic'     => $block_type->is_dynamic(),
				'parent'         => isset( $block_type->parent ) ? $block_type->parent : array(),
				'ancestor'       => isset( $block_type->ancestor ) ? $block_type->ancestor : array(),
				'allowed_blocks' => isset( $block_type->allowed_blocks ) ? $block_type->allowed_blocks : array(),
				'attributes'     => $block_type->get_attributes(),
				'supports'       => isset( $block_type->supports ) ? $block_type->supports : array(),
				'styles'         => isset( $block_type->styles ) ? $block_type->styles : array(),
				'variations'     => isset( $block_type->variations ) ? $block_type->variations : array(),
			);
		}

		return array(
			'success'     => true,
			'block_types' => $block_types,
			// translators: %d is the number of block types found.
			'message'     => sprintf( __( 'Retrieved %d registered block types', 'airo-wp' ), count( $block_types ) ),
		);
	}

	/**
	 * Get input schema for the tool.
	 *
	 * @return array
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(),
			'required'   => array(),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Block types retrieval result', 'airo-wp' ),
			array(
				'block_types' => array(
					'type'        => 'array',
					'description' => __( 'Array of registered block types', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'           => array(
								'type'        => 'string',
								'description' => __( 'The block type name (e.g. core/paragraph)', 'airo-wp' ),
							),
							'title'          => array(
								'type'        => 'string',
								'description' => __( 'The block type title', 'airo-wp' ),
							),
							'description'    => array(
								'type'        => 'string',
								'description' => __( 'The block type description', 'airo-wp' ),
							),
							'category'       => array(
								'type'        => 'string',
								'description' => __( 'The block category', 'airo-wp' ),
							),
							'icon'           => array(
								'type'        => array( 'string', 'object' ),
								'description' => __( 'The block icon (dashicon slug or SVG object)', 'airo-wp' ),
							),
							'keywords'       => array(
								'type'        => 'array',
								'description' => __( 'Search keywords for block discovery', 'airo-wp' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'api_version'    => array(
								'type'        => 'integer',
								'description' => __( 'Block API version', 'airo-wp' ),
							),
							'is_dynamic'     => array(
								'type'        => 'boolean',
								'description' => __( 'Whether the block has a server-side render callback', 'airo-wp' ),
							),
							'parent'         => array(
								'type'        => 'array',
								'description' => __( 'Parent block types that can contain this block', 'airo-wp' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'ancestor'       => array(
								'type'        => 'array',
								'description' => __( 'Ancestor block types (any level) that must contain this block', 'airo-wp' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'allowed_blocks' => array(
								'type'        => 'array',
								'description' => __( 'Block types allowed as direct children', 'airo-wp' ),
								'items'       => array(
									'type' => 'string',
								),
							),
							'attributes'     => array(
								'type'        => 'object',
								'description' => __( 'Block attribute definitions (name to schema)', 'airo-wp' ),
							),
							'supports'       => array(
								'type'        => 'object',
								'description' => __( 'Block support settings (color, spacing, typography, etc.)', 'airo-wp' ),
							),
							'styles'         => array(
								'type'        => 'array',
								'description' => __( 'Registered block style variations', 'airo-wp' ),
								'items'       => array(
									'type' => 'object',
								),
							),
							'variations'     => array(
								'type'        => 'array',
								'description' => __( 'Block variations with preset configurations', 'airo-wp' ),
								'items'       => array(
									'type' => 'object',
								),
							),
						),
					),
				),
			)
		);
	}
}
