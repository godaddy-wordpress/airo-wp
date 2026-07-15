<?php
/**
 * ActivateTheme MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the activate-theme MCP ability.
 *
 * Installs a theme from WordPress.org if not present, then activates it.
 */
class ActivateTheme extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/activate-theme';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * The abilities API passes input to this callback when an input_schema is defined,
	 * so we can check whether the requested theme is already installed and require
	 * install_themes only when an installation will actually be attempted.
	 *
	 * @param array<string, mixed> $input Input parameters (theme_slug).
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions( array $input = array() ): bool {
		if ( ! current_user_can( 'switch_themes' ) ) {
			return false;
		}

		$slug = isset( $input['theme_slug'] ) ? sanitize_text_field( $input['theme_slug'] ) : '';

		// If the theme is not yet installed, the install path requires install_themes too.
		if ( ! empty( $slug ) && ! wp_get_theme( $slug )->exists() ) {
			return current_user_can( 'install_themes' );
		}

		return true;
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Activate Theme', 'airo-wp' ),
				'description'         => __( 'Installs a theme from WordPress.org if not present, then activates it', 'airo-wp' ),
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
					__( 'Theme activation result', 'airo-wp' ),
					array(
						'theme'          => array(
							'type'        => 'string',
							'description' => __( 'The theme slug', 'airo-wp' ),
						),
						'version'        => array(
							'type'        => 'string',
							'description' => __( 'The activated theme version', 'airo-wp' ),
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
		$theme         = wp_get_theme( $slug );

		if ( $theme->exists() ) {
			if ( $current_theme === $slug ) {
				return array(
					'success'        => true,
					'message'        => __( 'Theme is already active.', 'airo-wp' ),
					'theme'          => $slug,
					'version'        => $theme->get( 'Version' ),
					'previous_theme' => $current_theme,
				);
			}

			switch_theme( $slug );

			if ( get_stylesheet() !== $slug ) {
				return array(
					'success' => false,
					'message' => __( 'Theme activation failed.', 'airo-wp' ),
					'theme'   => $slug,
				);
			}

			return array(
				'success'        => true,
				'message'        => __( 'Theme activated successfully.', 'airo-wp' ),
				'theme'          => $slug,
				'version'        => $theme->get( 'Version' ),
				'previous_theme' => $current_theme,
			);
		}

		// Theme not installed — attempt install.
		if ( ! current_user_can( 'install_themes' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to install themes.', 'airo-wp' ),
			);
		}

		$this->load_admin_file( 'theme.php' );
		$this->load_admin_file( 'file.php' );
		$this->load_admin_file( 'class-wp-upgrader.php' );

		$api = themes_api(
			'theme_information',
			array(
				'slug'   => $slug,
				'fields' => array( 'sections' => false ),
			)
		);

		if ( is_wp_error( $api ) ) {
			return array(
				'success' => false,
				'message' => $api->get_error_message(),
				'theme'   => $slug,
			);
		}

		add_filter( 'filesystem_method', array( $this, 'filter_filesystem_method' ) );
		WP_Filesystem();

		$upgrader = new \Theme_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		remove_filter( 'filesystem_method', array( $this, 'filter_filesystem_method' ) );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => $result->get_error_message(),
				'theme'   => $slug,
			);
		}

		if ( ! $result ) {
			return array(
				'success' => false,
				'message' => __( 'Theme installation failed.', 'airo-wp' ),
				'theme'   => $slug,
			);
		}

		// Verify installation and activate.
		$theme = wp_get_theme( $slug );

		if ( ! $theme->exists() ) {
			return array(
				'success' => false,
				'message' => __( 'Theme installed but could not be located.', 'airo-wp' ),
				'theme'   => $slug,
			);
		}

		switch_theme( $slug );

		if ( get_stylesheet() !== $slug ) {
			return array(
				'success' => false,
				'message' => __( 'Theme installed but activation failed.', 'airo-wp' ),
				'theme'   => $slug,
			);
		}

		return array(
			'success'        => true,
			'message'        => __( 'Theme installed and activated successfully.', 'airo-wp' ),
			'theme'          => $slug,
			'version'        => $theme->get( 'Version' ),
			'previous_theme' => $current_theme,
		);
	}

	/**
	 * Filter filesystem method to use direct access.
	 *
	 * @return string Filesystem method.
	 */
	public function filter_filesystem_method(): string {
		return 'direct';
	}
}
