<?php
/**
 * SwitchTheme MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the switch-theme MCP ability.
 *
 * Switches to an already-installed theme.
 */
class SwitchTheme extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/switch-theme';

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
				'label'               => __( 'Switch Theme', 'airo-wp' ),
				'description'         => __( 'Switches to an already-installed theme', 'airo-wp' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'theme_slug' => array(
							'type'        => 'string',
							'description' => __( 'The theme slug (e.g. "twentytwentyfour")', 'airo-wp' ),
							'minLength'   => 1,
						),
					),
					'required'   => array( 'theme_slug' ),
				),
				'output_schema'       => $this->build_output_schema(
					__( 'Theme switch result', 'airo-wp' ),
					array(
						'theme'          => array(
							'type'        => 'string',
							'description' => __( 'The theme slug', 'airo-wp' ),
						),
						'previous_theme' => array(
							'type'        => 'string',
							'description' => __( 'The previously active theme slug', 'airo-wp' ),
						),
					)
				),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
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
		$slug = isset( $input['theme_slug'] ) ? sanitize_text_field( $input['theme_slug'] ) : '';

		if ( empty( $slug ) ) {
			return array(
				'success' => false,
				'message' => __( 'Theme slug is required.', 'airo-wp' ),
			);
		}


		$current_theme = get_stylesheet();

		if ( $current_theme === $slug ) {
			return array(
				'success'        => true,
				'message'        => __( 'Theme is already active.', 'airo-wp' ),
				'theme'          => $slug,
				'previous_theme' => $current_theme,
			);
		}

		$theme = wp_get_theme( $slug );

		if ( ! $theme->exists() ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: theme slug */
					__( 'Theme "%s" is not installed. Use the activate-theme tool to install and activate it.', 'airo-wp' ),
					$slug
				),
			);
		}

		switch_theme( $slug );

		if ( get_stylesheet() !== $slug ) {
			return array(
				'success' => false,
				'message' => __( 'Theme switch failed.', 'airo-wp' ),
				'theme'   => $slug,
			);
		}

		return array(
			'success'        => true,
			'message'        => sprintf(
				/* translators: 1: new theme name, 2: previous theme name */
				__( 'Successfully switched from "%2$s" to "%1$s".', 'airo-wp' ),
				$theme->get( 'Name' ),
				wp_get_theme( $current_theme )->get( 'Name' )
			),
			'theme'          => $slug,
			'previous_theme' => $current_theme,
		);
	}
}
