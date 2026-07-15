<?php
/**
 * Blocks domain package.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Blocks;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\PackageInterface;

/**
 * Boots the Blocks domain package.
 *
 * Registers custom blocks and patterns sourced from DesignSetGo.
 * Silently defers to the DesignSetGo plugin if it is already active.
 */
final class Package implements PackageInterface {

	/**
	 * Prevent direct instantiation.
	 */
	private function __construct() {}

	/**
	 * Initialise the Blocks package.
	 *
	 * @param Container $container Plugin container.
	 */
	public static function init( Container $container ): void {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_plugin_active( 'designsetgo/designsetgo.php' ) ) {
			return;
		}

		$instance = new self();
		add_action( 'init', array( $instance, 'register_blocks' ) );
		add_action( 'init', array( $instance, 'register_patterns' ) );
	}

	/**
	 * Register blocks from dist/blocks/.
	 */
	public function register_blocks(): void {
		$blocks_dir = AIRO_WP_PLUGIN_DIR . 'dist/blocks/';
		foreach ( (array) glob( $blocks_dir . '*', GLOB_ONLYDIR ) as $block_dir ) {
			register_block_type( $block_dir );
		}
	}

	/**
	 * Register patterns from patterns/.
	 */
	public function register_patterns(): void {
		$patterns_dir = AIRO_WP_PLUGIN_DIR . 'patterns/';

		foreach ( (array) glob( $patterns_dir . '*', GLOB_ONLYDIR ) as $cat_dir ) {
			$category = 'airo-wp-' . basename( $cat_dir );
			register_block_pattern_category( $category, array( 'label' => ucfirst( basename( $cat_dir ) ) ) );

			foreach ( (array) glob( $cat_dir . '/*.php' ) as $pattern_file ) {
                // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
				$pattern = require $pattern_file;
				$name    = 'airo-wp/' . basename( $cat_dir ) . '/' . basename( $pattern_file, '.php' );
				register_block_pattern( $name, $pattern );
			}
		}
	}
}
