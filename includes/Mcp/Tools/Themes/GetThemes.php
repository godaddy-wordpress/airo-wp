<?php
/**
 * GetThemes MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-themes MCP ability.
 *
 * Retrieves theme information — either the active theme or all installed themes.
 */
class GetThemes extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/get-themes';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'switch_themes' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Themes', 'airo-wp' ),
				'description'         => __( 'Retrieves theme information — either the active theme or all installed themes', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Get the input schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'active' => array(
					'type'        => 'boolean',
					'description' => __( 'If true, return only the active theme; if false, return all themes', 'airo-wp' ),
				),
			),
			'required'   => array( 'active' ),
		);
	}

	/**
	 * Get the output schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Theme information', 'airo-wp' ),
			array(
				'themes' => array(
					'type'        => 'array',
					'description' => __( 'List of themes', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'name'             => array( 'type' => 'string' ),
							'title'            => array( 'type' => 'string' ),
							'description'      => array( 'type' => 'string' ),
							'version'          => array( 'type' => 'string' ),
							'author'           => array( 'type' => 'string' ),
							'author_uri'       => array( 'type' => 'string' ),
							'theme_uri'        => array( 'type' => 'string' ),
							'stylesheet'       => array( 'type' => 'string' ),
							'template'         => array( 'type' => 'string' ),
							'status'           => array( 'type' => 'string' ),
							'tags'             => array( 'type' => 'array' ),
							'is_block_theme'   => array( 'type' => 'boolean' ),
							'global_styles_id' => array( 'type' => 'integer' ),
						),
					),
				),
			)
		);
	}
	/**
	 * Execute the tool.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed> Execution result.
	 */
	public function execute( array $input ): array {
		if ( ! isset( $input['active'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'The active parameter is required.', 'airo-wp' ),
			);
		}

		$active           = (bool) $input['active'];
		$current_theme    = get_stylesheet();
		$formatted_themes = array();

		if ( $active ) {
			$theme              = wp_get_theme();
			$formatted_themes[] = $this->format_theme_data( $theme, true );

			$this->ensure_global_styles_post_exists( $theme );
		} else {
			$themes = wp_get_themes();

			foreach ( $themes as $stylesheet => $theme ) {
				$is_active          = ( $stylesheet === $current_theme );
				$formatted_themes[] = $this->format_theme_data( $theme, $is_active );
			}
		}

		return array(
			'success' => true,
			'themes'  => $formatted_themes,
			'message' => sprintf(
				/* translators: %d: number of themes */
				__( 'Found %d theme(s).', 'airo-wp' ),
				count( $formatted_themes )
			),
		);
	}

	/**
	 * Format theme data into a consistent array structure.
	 *
	 * @param \WP_Theme $theme     Theme object.
	 * @param bool      $is_active Whether the theme is currently active.
	 * @return array<string, mixed> Formatted theme data.
	 */
	private function format_theme_data( $theme, bool $is_active ): array {
		$is_block_theme = is_callable( array( $theme, 'is_block_theme' ) ) ? $theme->is_block_theme() : false;

		$data = array(
			'name'           => $theme->get( 'Name' ),
			'title'          => $theme->get( 'Name' ),
			'description'    => $theme->get( 'Description' ),
			'version'        => $theme->get( 'Version' ),
			'author'         => $theme->get( 'Author' ),
			'author_uri'     => $theme->get( 'AuthorURI' ),
			'theme_uri'      => $theme->get( 'ThemeURI' ),
			'stylesheet'     => $theme->get_stylesheet(),
			'template'       => $theme->get_template(),
			'status'         => $is_active ? 'active' : 'inactive',
			'tags'           => $theme->get( 'Tags' ) ? $theme->get( 'Tags' ) : array(),
			'is_block_theme' => $is_block_theme,
		);

		if ( $is_active ) {
			$data['global_styles_id'] = $this->get_global_styles_id( $theme );
		}

		return $data;
	}

	/**
	 * Ensure global styles post exists for a block theme.
	 *
	 * @param \WP_Theme $theme Theme object.
	 */
	private function ensure_global_styles_post_exists( $theme ): void {
		$is_block_theme = is_callable( array( $theme, 'is_block_theme' ) ) ? $theme->is_block_theme() : false;

		if ( $is_block_theme && class_exists( 'WP_Theme_JSON_Resolver' ) ) {
			\WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
		}
	}

	/**
	 * Get the global styles post ID for a theme.
	 *
	 * @param \WP_Theme $theme Theme object.
	 * @return int Global styles post ID or 0.
	 */
	private function get_global_styles_id( $theme ): int {
		$is_block_theme = is_callable( array( $theme, 'is_block_theme' ) ) ? $theme->is_block_theme() : false;

		if ( $is_block_theme && class_exists( 'WP_Theme_JSON_Resolver' ) ) {
			return (int) \WP_Theme_JSON_Resolver::get_user_global_styles_post_id();
		}

		return 0;
	}
}
