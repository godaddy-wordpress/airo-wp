<?php
/**
 * ActivatePlugin MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the activate-plugin MCP ability.
 *
 * Installs a plugin from WordPress.org if not present, then activates it.
 */
class ActivatePlugin extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/activate-plugin';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'activate_plugins' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Activate Plugin', 'airo-wp' ),
				'description'         => __( 'Installs a plugin from WordPress.org if not present, then activates it', 'airo-wp' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'plugin_slug' => array(
							'type'        => 'string',
							'description' => __( 'The plugin slug (e.g. "hello-dolly")', 'airo-wp' ),
						),
					),
					'required'   => array( 'plugin_slug' ),
				),
				'output_schema'       => $this->build_output_schema(
					__( 'Plugin activation result', 'airo-wp' ),
					array(
						'plugin'  => array(
							'type'        => 'string',
							'description' => __( 'The plugin slug', 'airo-wp' ),
						),
						'version' => array(
							'type'        => 'string',
							'description' => __( 'The activated plugin version', 'airo-wp' ),
						),
					)
				),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'plugin-management',
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
		$slug = isset( $input['plugin_slug'] ) ? sanitize_text_field( $input['plugin_slug'] ) : '';

		if ( empty( $slug ) ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin slug is required.', 'airo-wp' ),
			);
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to install plugins.', 'airo-wp' ),
			);
		}

		$this->load_admin_file( 'plugin.php' );

		$plugin_file = $this->find_plugin_file( $slug );

		if ( ! $plugin_file ) {
			$install_result = $this->install_from_org( $slug );

			if ( is_wp_error( $install_result ) ) {
				return array(
					'success' => false,
					'message' => $install_result->get_error_message(),
					'plugin'  => $slug,
				);
			}

			$plugin_file = $this->find_plugin_file( $slug );

			if ( ! $plugin_file ) {
				return array(
					'success' => false,
					'message' => __( 'Plugin installed but could not locate plugin file.', 'airo-wp' ),
					'plugin'  => $slug,
				);
			}
		}

		if ( is_plugin_active( $plugin_file ) ) {
			return array(
				'success' => true,
				'message' => __( 'Plugin is already active.', 'airo-wp' ),
				'plugin'  => $slug,
				'version' => $this->get_plugin_version( $plugin_file ),
			);
		}

		$result = activate_plugin( $plugin_file );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => $result->get_error_message(),
				'plugin'  => $slug,
			);
		}

		return array(
			'success' => true,
			'message' => __( 'Plugin activated successfully.', 'airo-wp' ),
			'plugin'  => $slug,
			'version' => $this->get_plugin_version( $plugin_file ),
		);
	}

	/**
	 * Install a plugin from WordPress.org.
	 *
	 * @param string $slug Plugin slug.
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	private function install_from_org( string $slug ) {
		$this->load_admin_file( 'plugin-install.php' );
		$this->load_admin_file( 'file.php' );
		$this->load_admin_file( 'class-wp-upgrader.php' );

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $slug,
				'fields' => array( 'sections' => false ),
			)
		);

		if ( is_wp_error( $api ) ) {
			return $api;
		}

		$upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $api->download_link );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( ! $result ) {
			return new \WP_Error( 'install_failed', __( 'Plugin installation failed.', 'airo-wp' ) );
		}

		return true;
	}

	/**
	 * Find the main plugin file for a given slug.
	 *
	 * @param string $slug Plugin slug.
	 * @return string|false Plugin file relative path or false if not found.
	 */
	private function find_plugin_file( string $slug ) {
		$plugins = get_plugins();

		// Try the conventional path first.
		$conventional = $slug . '/' . $slug . '.php';
		if ( isset( $plugins[ $conventional ] ) ) {
			return $conventional;
		}

		// Search by directory name.
		foreach ( $plugins as $file => $data ) {
			if ( strpos( $file, $slug . '/' ) === 0 ) {
				return $file;
			}
		}

		return false;
	}

	/**
	 * Get the version of a plugin by its file path.
	 *
	 * @param string $file Plugin file relative path.
	 * @return string Plugin version or empty string.
	 */
	private function get_plugin_version( string $file ): string {
		$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $file, false, false );

		return isset( $data['Version'] ) ? $data['Version'] : '';
	}
}
