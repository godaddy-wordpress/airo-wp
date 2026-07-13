<?php
/**
 * PluginHelper service.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Helper — shared plugin lookup and update-info operations.
 */
class PluginHelper {

	/**
	 * Locate an installed plugin's file path by its slug.
	 *
	 * Tries the common `slug/slug.php` layout first, then scans installed
	 * plugin directories for a folder match.
	 *
	 * @param string $plugin_slug Plugin slug (e.g. "akismet").
	 *
	 * @return string|false Plugin file path relative to WP_PLUGIN_DIR, or false when not installed.
	 */
	public function find_file( string $plugin_slug ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			$this->load_admin_file( 'plugin.php' );
		}

		$all_plugins = get_plugins();

		$common_path = $plugin_slug . '/' . $plugin_slug . '.php';
		if ( isset( $all_plugins[ $common_path ] ) ) {
			return $common_path;
		}

		foreach ( array_keys( $all_plugins ) as $plugin_file ) {
			if ( dirname( $plugin_file ) === $plugin_slug ) {
				return $plugin_file;
			}
		}

		return false;
	}

	/**
	 * Optionally refresh the `update_plugins` site transient.
	 *
	 * The transient is normally kept fresh by core's cron schedule. Pass true
	 * when the caller explicitly requested up-to-the-second update data.
	 *
	 * @param bool $force Whether to force a refresh by calling wp_update_plugins().
	 *
	 * @return void
	 */
	public function refresh_updates( bool $force = false ): void {
		if ( ! $force ) {
			return;
		}

		if ( ! function_exists( 'wp_update_plugins' ) ) {
			$this->load_admin_file( 'update.php' );
		}

		wp_update_plugins();
	}

	/**
	 * Get update availability info for an installed plugin.
	 *
	 * Reads the `update_plugins` site transient maintained by core. Callers
	 * that need fresh data should invoke {@see refresh_updates()} first — on
	 * headless installs the transient can sit missing for hours since WP cron
	 * only fires on incoming requests, so "no update" here may just mean nothing
	 * has called `wp_update_plugins()` since the cache last expired.
	 *
	 * @param string $plugin_file Plugin file path relative to WP_PLUGIN_DIR (e.g. "akismet/akismet.php").
	 *
	 * @return array{update_available: bool, new_version: string|null}
	 */
	public function get_update_info( string $plugin_file ): array {
		$no_update = array(
			'update_available' => false,
			'new_version'      => null,
		);

		$updates = get_site_transient( 'update_plugins' );

		if ( ! is_object( $updates ) || ! isset( $updates->response ) || ! is_array( $updates->response ) ) {
			return $no_update;
		}

		if ( ! isset( $updates->response[ $plugin_file ] ) ) {
			return $no_update;
		}

		$response = $updates->response[ $plugin_file ];

		if ( ! is_object( $response ) ) {
			return $no_update;
		}

		$new_version = isset( $response->new_version ) ? $response->new_version : null;

		if ( empty( $new_version ) ) {
			return $no_update;
		}

		return array(
			'update_available' => true,
			'new_version'      => $new_version,
		);
	}

	/**
	 * Load a wp-admin/includes file.
	 *
	 * In tests (PHPUnit), functions are pre-defined in bootstrap.php, so skip loading.
	 *
	 * @param string $filename Admin filename (e.g., 'plugin.php').
	 *
	 * @return void
	 */
	private function load_admin_file( string $filename ): void {
		if ( defined( 'PHPUNIT_RUNNING' ) ) {
			return;
		}

		$full_path = ABSPATH . 'wp-admin/includes/' . $filename;

		if ( file_exists( $full_path ) ) {
			// @codeCoverageIgnoreStart
			require_once $full_path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
			// @codeCoverageIgnoreEnd
		}
	}
}
