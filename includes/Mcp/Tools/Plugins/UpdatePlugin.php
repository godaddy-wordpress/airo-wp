<?php
/**
 * UpdatePlugin MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services\PluginHelper;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the update-plugin MCP ability.
 *
 * Updates an installed plugin from the WordPress.org repository. If a specific
 * version is provided, installs that exact version (supports upgrade or downgrade);
 * otherwise updates to the latest available version.
 */
class UpdatePlugin extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/update-plugin';

	/**
	 * Plugin helper service.
	 *
	 * @var PluginHelper
	 */
	private PluginHelper $plugin_helper;

	/**
	 * Constructor.
	 *
	 * @param PluginHelper $plugin_helper Plugin helper service.
	 */
	public function __construct( PluginHelper $plugin_helper ) {
		$this->plugin_helper = $plugin_helper;
	}

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
				'label'               => __( 'Update Plugin', 'airo-wp' ),
				'description'         => __( 'Updates an installed WordPress plugin from the WordPress.org repository. If "version" is provided, installs that exact version (allows upgrade or downgrade); otherwise updates to the latest available version.', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'plugin-management',
			)
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
			'properties' => array(
				'plugin_slug' => array(
					'type'        => 'string',
					'description' => __( 'The plugin slug (e.g., "hello-dolly", "akismet")', 'airo-wp' ),
				),
				'version'     => array(
					'type'        => 'string',
					'description' => __( 'Optional target version (e.g., "1.7.2"). If omitted, updates to the latest available version. Supplying a version supports both upgrade and downgrade.', 'airo-wp' ),
					'minLength'   => 1,
				),
				'force_check' => array(
					'type'        => 'boolean',
					'description' => __( 'When "version" is omitted, force a refresh of available plugin updates from WordPress.org before resolving the latest version. Ignored when "version" is supplied. Defaults to true so that "update to latest" reflects the freshest data available.', 'airo-wp' ),
					'default'     => true,
				),
			),
			'required'   => array( 'plugin_slug' ),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array
	 */
	public function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Plugin update result', 'airo-wp' ),
			array(
				'plugin'           => array(
					'type'        => 'string',
					'description' => __( 'The plugin slug that was processed', 'airo-wp' ),
				),
				'previous_version' => array(
					'type'        => 'string',
					'description' => __( 'The plugin version before the update', 'airo-wp' ),
				),
				'version'          => array(
					'type'        => 'string',
					'description' => __( 'The plugin version after the update', 'airo-wp' ),
				),
			)
		);
	}

	/**
	 * Execute the update plugin tool.
	 *
	 * @param array $input Input parameters.
	 * @return array Plugin update result.
	 */
	public function execute( array $input ): array {
		if ( empty( $input['plugin_slug'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin slug is required', 'airo-wp' ),
				'plugin'  => '',
			);
		}

		$plugin_slug    = sanitize_text_field( $input['plugin_slug'] );
		$target_version = ! empty( $input['version'] ) ? sanitize_text_field( $input['version'] ) : null;

		// Load wp-admin/includes/plugin.php unconditionally: this tool calls
		// is_plugin_active(), deactivate_plugins(), activate_plugin(), and get_plugins(),
		// all of which live in that file. load_admin_file() wraps require_once so the
		// call is idempotent and cheap.
		$this->load_admin_file( 'plugin.php' );

		if ( ! function_exists( 'plugins_api' ) ) {
			$this->load_admin_file( 'plugin-install.php' );
		}

		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			$this->load_admin_file( 'file.php' );
		}

		if ( ! class_exists( 'Plugin_Upgrader' ) ) {
			$this->load_admin_file( 'class-wp-upgrader.php' );
		}

		$plugin_file = $this->plugin_helper->find_file( $plugin_slug );
		if ( ! $plugin_file ) {
			return array(
				'success' => false,
				/* translators: %s: Plugin slug. */
				'message' => sprintf( __( 'Plugin "%s" is not installed', 'airo-wp' ), $plugin_slug ),
				'plugin'  => $plugin_slug,
			);
		}

		$previous_version = $this->get_plugin_version( $plugin_file );

		// Resolve the target version up front. When not supplied, look up the
		// latest available version from the update_plugins transient. Unlike
		// list-plugins / get-plugin, force_check defaults to true here: this
		// is a write tool whose semantics are "install the latest version
		// right now," so a stale transient could install a version that has
		// already been superseded. Callers that want the cached-only path
		// can pass force_check=false explicitly.
		if ( null === $target_version ) {
			$force_check = ! isset( $input['force_check'] ) || ! empty( $input['force_check'] );
			$this->plugin_helper->refresh_updates( $force_check );

			$update_info = $this->plugin_helper->get_update_info( $plugin_file );
			if ( ! $update_info['update_available'] || empty( $update_info['new_version'] ) ) {
				return array(
					'success'          => true,
					'message'          => __( 'Plugin is already up to date', 'airo-wp' ),
					'plugin'           => $plugin_slug,
					'previous_version' => $previous_version,
					'version'          => $previous_version,
				);
			}

			$target_version = $update_info['new_version'];
		}

		return $this->perform_update( $plugin_slug, $plugin_file, $previous_version, $target_version );
	}

	/**
	 * Install a specific version of a plugin from the WordPress.org repository.
	 *
	 * Supports both upgrade and downgrade by overwriting the existing install
	 * with the version-pinned package from the .org repo. Preserves activation
	 * state across the swap — if the install fails after deactivation, the
	 * plugin is reactivated before the error response is returned so it is
	 * never silently left disabled.
	 *
	 * @param string $plugin_slug      Plugin slug.
	 * @param string $plugin_file      Plugin file path relative to WP_PLUGIN_DIR.
	 * @param string $previous_version Plugin version before the update.
	 * @param string $target_version   Concrete target version to install.
	 * @return array
	 */
	private function perform_update( string $plugin_slug, string $plugin_file, string $previous_version, string $target_version ): array {
		if ( $target_version === $previous_version ) {
			return array(
				'success'          => true,
				/* translators: %s: Plugin version number. */
				'message'          => sprintf( __( 'Plugin is already at version %s', 'airo-wp' ), $target_version ),
				'plugin'           => $plugin_slug,
				'previous_version' => $previous_version,
				'version'          => $previous_version,
			);
		}

		$api = plugins_api(
			'plugin_information',
			array(
				'slug'   => $plugin_slug,
				'fields' => array( 'versions' => true ),
			)
		);

		if ( is_wp_error( $api ) ) {
			return array(
				'success'          => false,
				/* translators: 1: Plugin slug, 2: Error message. */
				'message'          => sprintf( __( 'Plugin "%1$s" not found on WordPress.org: %2$s', 'airo-wp' ), $plugin_slug, $api->get_error_message() ),
				'plugin'           => $plugin_slug,
				'previous_version' => $previous_version,
			);
		}

		if ( empty( $api->versions ) || ! isset( $api->versions[ $target_version ] ) ) {
			return array(
				'success'          => false,
				/* translators: 1: Version number, 2: Plugin slug. */
				'message'          => sprintf( __( 'Version "%1$s" is not available on WordPress.org for plugin "%2$s"', 'airo-wp' ), $target_version, $plugin_slug ),
				'plugin'           => $plugin_slug,
				'previous_version' => $previous_version,
			);
		}

		$download_url = $api->versions[ $target_version ];
		$was_active   = is_plugin_active( $plugin_file );

		// Deactivate before swapping files to avoid running mid-upgrade code.
		if ( $was_active ) {
			deactivate_plugins( $plugin_file, true );
		}

		$upgrader = new \Plugin_Upgrader( new \WP_Ajax_Upgrader_Skin() );
		$result   = $upgrader->install( $download_url, array( 'overwrite_package' => true ) );

		if ( is_wp_error( $result ) ) {
			return $this->install_failure_response(
				/* translators: 1: Version number, 2: Error message. */
				sprintf( __( 'Failed to install plugin version %1$s: %2$s', 'airo-wp' ), $target_version, $result->get_error_message() ),
				$plugin_slug,
				$plugin_file,
				$previous_version,
				$was_active
			);
		}

		if ( ! $result ) {
			return $this->install_failure_response(
				/* translators: %s: Plugin version number. */
				sprintf( __( 'Plugin install of version %s failed for unknown reason', 'airo-wp' ), $target_version ),
				$plugin_slug,
				$plugin_file,
				$previous_version,
				$was_active
			);
		}

		// Reactivate if it was active beforehand. Activation failure is reported
		// in the message but does not flip success — the install itself worked.
		$activation_message = '';
		if ( $was_active ) {
			$activation_result = activate_plugin( $plugin_file );
			if ( is_wp_error( $activation_result ) ) {
				/* translators: %s: Error message. */
				$activation_message = sprintf( __( ' Plugin installed but failed to reactivate: %s', 'airo-wp' ), $activation_result->get_error_message() );
			}
		}

		$new_version = $this->get_plugin_version( $plugin_file );

		return array(
			'success'          => true,
			/* translators: %s: Plugin version number. */
			'message'          => sprintf( __( 'Plugin updated to version %s', 'airo-wp' ), $new_version ) . $activation_message,
			'plugin'           => $plugin_slug,
			'previous_version' => $previous_version,
			'version'          => $new_version,
		);
	}

	/**
	 * Build a failure response after an install attempt, restoring activation
	 * state first so a previously-active plugin is never silently left disabled
	 * after a failed install.
	 *
	 * @param string $message          Failure message for the response.
	 * @param string $plugin_slug      Plugin slug.
	 * @param string $plugin_file      Plugin file path relative to WP_PLUGIN_DIR.
	 * @param string $previous_version Plugin version before the update.
	 * @param bool   $was_active       Whether the plugin was active before the deactivate call.
	 * @return array
	 */
	private function install_failure_response( string $message, string $plugin_slug, string $plugin_file, string $previous_version, bool $was_active ): array {
		if ( $was_active ) {
			$activation_result = activate_plugin( $plugin_file );
			if ( is_wp_error( $activation_result ) ) {
				/* translators: %s: Error message. */
				$message .= sprintf( __( ' Additionally, the plugin could not be reactivated and is now disabled: %s', 'airo-wp' ), $activation_result->get_error_message() );
			}
		}

		return array(
			'success'          => false,
			'message'          => $message,
			'plugin'           => $plugin_slug,
			'previous_version' => $previous_version,
		);
	}

	/**
	 * Get plugin version.
	 *
	 * @param string $plugin_file Plugin file path.
	 * @return string Plugin version.
	 */
	private function get_plugin_version( string $plugin_file ): string {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, false, false );
		return isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
	}
}
