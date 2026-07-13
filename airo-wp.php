<?php
/**
 * Plugin Name:       Airo WP AI Builder
 * Plugin URI:        https://github.com/godaddy-wordpress/airo-wp
 * Description:       MCP server and block pattern library for AI-powered site building.
 * Version:           0.2.4
 * Requires at least: 6.9
 * Requires PHP:      7.4
 * Author:            GoDaddy
 * Author URI:        https://www.godaddy.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       airo-wp
 *
 * @package airo-wp
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'AIRO_WP_VERSION' ) ) {
	define( 'AIRO_WP_VERSION', '0.2.4' );
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
