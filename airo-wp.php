<?php
/**
 * Plugin Name:       Airo WP AI Builder
 * Plugin URI:        https://github.com/godaddy-wordpress/airo-wp
 * Description:       MCP server and block pattern library for AI-powered site building.
 * Version:           0.4.0
 * Requires at least: 6.9
 * Requires PHP:      7.4
 * Author:            GoDaddy
 * Author URI:        https://www.godaddy.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       airo-wp
 * GitHub Plugin URI:  godaddy-wordpress/airo-wp
 * Primary Branch:     main
 * Release Asset:      true
 *
 * @package airo-wp
 */

/*
 * Notes on the three Git Updater headers above.
 *
 * They are read by the Git Updater plugin, which offers updates straight from this
 * repository's GitHub Releases, and are inert without it. They are kept in the
 * header block proper -- and the explanation down here -- because WordPress scans
 * that whole block for "Name:" patterns, so prose quoting a header name next to the
 * real one invites a parser surprise.
 *
 * Primary Branch is required because Git Updater defaults to "master".
 *
 * Release Asset makes Git Updater install the built zip attached to each Release
 * instead of GitHub's source zipball. That is not an optimisation. The repository
 * ships no vendor/ or dist/, so a zipball install would hit the autoload bail-out
 * below and register no blocks. With this header set, Git Updater refuses to update
 * when a Release carries no asset -- failing loudly beats installing a dead plugin.
 *
 * One header is deliberately absent: Update URI. It is a WordPress 5.8+ core header,
 * and when it points anywhere other than wordpress.org, core stops applying
 * WordPress.org updates for the plugin. Setting it to GitHub would disable
 * auto-updates for everyone who installed from the plugin directory. Git Updater
 * needs only the three above, so both channels keep working.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'AIRO_WP_VERSION' ) ) {
	define( 'AIRO_WP_VERSION', '0.4.0' );
}
if ( ! defined( 'AIRO_WP_PLUGIN_FILE' ) ) {
	define( 'AIRO_WP_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'AIRO_WP_PLUGIN_DIR' ) ) {
	define( 'AIRO_WP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'AIRO_WP_PLUGIN_URL' ) ) {
	define( 'AIRO_WP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

$airo_wp_autoload = __DIR__ . '/vendor/autoload.php';

if ( ! file_exists( $airo_wp_autoload ) ) {
	return;
}

// phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
require $airo_wp_autoload;

use GoDaddy\WordPress\Plugins\AiroWp\Plugin;

Plugin::init();
