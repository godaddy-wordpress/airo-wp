<?php
/**
 * DeactivatePlugin MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the deactivate-plugin MCP ability.
 *
 * Deactivates a plugin by slug, optionally uninstalling it.
 */
class DeactivatePlugin extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/deactivate-plugin';

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
				'label'               => __( 'Deactivate Plugin', 'airo-wp' ),
				'description'         => __( 'Deactivates a plugin by slug, optionally uninstalling it', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'plugin-management',
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
				'plugin_slug' => array(
					'type'        => 'string',
					'description' => __( 'The plugin slug (e.g. "hello-dolly")', 'airo-wp' ),
					'minLength'   => 1,
				),
				'uninstall'   => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to also uninstall (delete) the plugin', 'airo-wp' ),
					'default'     => false,
				),
			),
			'required'   => array( 'plugin_slug' ),
		);
	}

	/**
	 * Get the output schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Plugin deactivation result', 'airo-wp' ),
			array(
				'plugin' => array(
					'type'        => 'string',
					'description' => __( 'The plugin slug', 'airo-wp' ),
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
		$slug = isset( $input['plugin_slug'] ) ? sanitize_text_field( $input['plugin_slug'] ) : '';

		if ( empty( $slug ) ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin slug is required.', 'airo-wp' ),
			);
		}

		$this->load_admin_file( 'plugin.php' );

		$plugin_file = $this->find_plugin_file( $slug );

		if ( ! $plugin_file ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin not found.', 'airo-wp' ),
				'plugin'  => $slug,
			);
		}

		if ( is_plugin_active( $plugin_file ) ) {
			deactivate_plugins( $plugin_file );
		}

		$uninstall = ! empty( $input['uninstall'] );

		if ( $uninstall ) {
			$uninstall_result = $this->uninstall_plugin( $plugin_file, $slug );

			if ( is_wp_error( $uninstall_result ) ) {
				return array(
					'success' => false,
					'message' => $uninstall_result->get_error_message(),
					'plugin'  => $slug,
				);
			}

			return array(
				'success' => true,
				'message' => __( 'Plugin deactivated and uninstalled successfully.', 'airo-wp' ),
				'plugin'  => $slug,
			);
		}

		return array(
			'success' => true,
			'message' => __( 'Plugin deactivated successfully.', 'airo-wp' ),
			'plugin'  => $slug,
		);
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
	 * Uninstall (delete) a plugin.
	 *
	 * @param string $file Plugin file relative path.
	 * @param string $_slug Plugin slug (reserved for future use).
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	private function uninstall_plugin( string $file, string $_slug ) {
		if ( ! current_user_can( 'delete_plugins' ) ) {
			return new \WP_Error( 'permission_denied', __( 'You do not have permission to delete plugins.', 'airo-wp' ) );
		}

		$this->load_admin_file( 'file.php' );

		$result = delete_plugins( array( $file ) );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}
}
