<?php
/**
 * Abilities API proxy.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure;

defined( 'ABSPATH' ) || exit;

/**
 * Abstracts native (WP 6.9+) vs bundled (<6.9) Abilities API hook variants.
 *
 * Hooks register_tools() onto both known Abilities API action names:
 *  - wp_abilities_api_init — WordPress 6.9+ core Abilities API; also fired
 *                            by wordpress/abilities-api v0.4.0 (bundled)
 *  - abilities_api_init    — forward-compat listener; reserved in case a
 *                            future bundled variant adopts this hook name
 */
class AbilitiesApiProxy {

	/**
	 * Registry holding the canonical tool list.
	 *
	 * @var ToolRegistry
	 */
	private ToolRegistry $registry;

	/**
	 * Constructor.
	 *
	 * @param ToolRegistry $registry Tool registry.
	 */
	public function __construct( ToolRegistry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Register hooks for both Abilities API variants.
	 */
	public function setup(): void {
		add_action( 'wp_abilities_api_categories_init', array( $this, 'register_categories' ) );
		add_action( 'wp_abilities_api_init', array( $this, 'register_tools' ) );
		add_action( 'abilities_api_init', array( $this, 'register_tools' ) );
	}

	/**
	 * Register ability categories required by our tools.
	 *
	 * The wp_register_ability_category() function is a WP 6.9+ core function. On WP 6.8 the
	 * Abilities API is loaded as a standalone package that provides the same
	 * function. The function_exists() guard ensures graceful degradation when
	 * neither source is available.
	 */
	public function register_categories(): void {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		wp_register_ability_category(
			'site-management',
			array(
				'label'       => __( 'Site Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing the WordPress site.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'plugin-management',
			array(
				'label'       => __( 'Plugin Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress plugins.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'theme-management',
			array(
				'label'       => __( 'Theme Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress themes.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'content-management',
			array(
				'label'       => __( 'Content Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress content.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'media-management',
			array(
				'label'       => __( 'Media Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress media.', 'airo-wp' ),
			)
		);
	}

	/**
	 * Call register() on each tool.
	 */
	public function register_tools(): void {
		foreach ( $this->registry->all() as $tool ) {
			$tool->register();
		}
	}
}
