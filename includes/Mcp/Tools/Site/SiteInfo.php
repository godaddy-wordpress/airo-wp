<?php
/**
 * SiteInfo MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and executes the get-site-info MCP ability.
 *
 * Ported from mcp-adapter-initializer's Site_Info_Tool.
 * Simplified: no singleton, no Base_Tool dependency, PHP 7.4 compatible.
 */
class SiteInfo {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/get-site-info';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 *
	 * Must be called inside the wp_abilities_api_init or abilities_api_init action.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Site Information', 'airo-wp' ),
				'description'         => __( 'Retrieves basic information about the current WordPress site', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'site-management',
			)
		);
	}

	/**
	 * Execute the tool.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed> Site information.
	 */
	public function execute( array $input ): array {
		$site_logo = (int) get_option( 'site_logo', 0 );
		$site_icon = (int) get_option( 'site_icon', 0 );

		$result = array(
			'site_name'         => get_bloginfo( 'name' ),
			'site_url'          => get_site_url(),
			'description'       => get_bloginfo( 'description' ),
			'wordpress_version' => get_bloginfo( 'version' ),
			'is_published'      => (bool) get_option( 'gdl_site_published', false ),
			// get_locale() is the canonical accessor since WP 4.0; the WPLANG option
			// is deprecated and often empty even on sites with a configured locale.
			'site_locale'       => get_locale(),
			// wp_timezone_string() (WP 5.3+) returns the named zone if set, else
			// converts gmt_offset to "+HH:MM" form. Reading timezone_string alone
			// returns "" for sites configured with a UTC offset instead of a city.
			// The plugin requires WP 6.8+, so wp_timezone_string() is always available.
			'timezone'          => wp_timezone_string(),
			'date_format'       => get_option( 'date_format', 'F j, Y' ),
			'time_format'       => get_option( 'time_format', 'g:i a' ),
			'posts_per_page'    => (int) get_option( 'posts_per_page', 10 ),
			'blog_public'       => (bool) get_option( 'blog_public', true ),
			'site_logo'         => $site_logo > 0 ? $site_logo : null,
			'site_icon'         => $site_icon > 0 ? $site_icon : null,
		);

		if ( ! empty( $input['include_stats'] ) ) {
			$result['stats'] = array(
				'post_count' => $this->get_published_post_count( 'post' ),
				'page_count' => $this->get_published_post_count( 'page' ),
			);
		}

		if ( ! empty( $input['include_theme_info'] ) ) {
			$result['theme_info'] = $this->get_theme_info();
		}

		if ( ! empty( $input['include_plugin_count'] ) ) {
			$result['plugin_count'] = $this->get_active_plugin_count();
		}

		if ( ! empty( $input['include_reading_settings'] ) ) {
			$result['reading_settings'] = $this->get_reading_settings();
		}

		return $result;
	}

	/**
	 * Input JSON Schema for this tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'include_stats'            => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include post/page statistics', 'airo-wp' ),
					'default'     => false,
				),
				'include_theme_info'       => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include active theme information', 'airo-wp' ),
					'default'     => false,
				),
				'include_plugin_count'     => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include plugin count information', 'airo-wp' ),
					'default'     => false,
				),
				'include_reading_settings' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include reading settings', 'airo-wp' ),
					'default'     => false,
				),
			),
		);
	}

	/**
	 * Output JSON Schema for this tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'site_name'         => array(
					'type'        => 'string',
					'description' => __( 'Site name', 'airo-wp' ),
				),
				'site_url'          => array(
					'type'        => 'string',
					'description' => __( 'Site URL', 'airo-wp' ),
				),
				'description'       => array(
					'type'        => 'string',
					'description' => __( 'Site tagline', 'airo-wp' ),
				),
				'wordpress_version' => array(
					'type'        => 'string',
					'description' => __( 'WordPress version', 'airo-wp' ),
				),
				'is_published'      => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the site has been published', 'airo-wp' ),
				),
				'site_locale'       => array(
					'type'        => 'string',
					'description' => __( 'Active site locale from get_locale() (e.g. "es_ES", "en_US")', 'airo-wp' ),
				),
				'timezone'          => array(
					'type'        => 'string',
					'description' => __( 'Timezone — named zone (e.g. "America/New_York") or "+HH:MM"/"-HH:MM" offset for sites configured with a UTC offset', 'airo-wp' ),
				),
				'date_format'       => array(
					'type'        => 'string',
					'description' => __( 'PHP date format string', 'airo-wp' ),
				),
				'time_format'       => array(
					'type'        => 'string',
					'description' => __( 'PHP time format string', 'airo-wp' ),
				),
				'posts_per_page'    => array(
					'type'        => 'integer',
					'description' => __( 'Number of posts to show per page', 'airo-wp' ),
				),
				'blog_public'       => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the site is visible to search engines', 'airo-wp' ),
				),
				'site_logo'         => array(
					'type'        => array( 'integer', 'null' ),
					'description' => __( 'Media attachment ID for site logo, or null if not set', 'airo-wp' ),
				),
				'site_icon'         => array(
					'type'        => array( 'integer', 'null' ),
					'description' => __( 'Media attachment ID for site icon (favicon), or null if not set', 'airo-wp' ),
				),
				'stats'             => array(
					'type'       => 'object',
					'properties' => array(
						'post_count' => array( 'type' => 'integer' ),
						'page_count' => array( 'type' => 'integer' ),
					),
				),
				'theme_info'        => array(
					'type'       => 'object',
					'properties' => array(
						'name'    => array( 'type' => 'string' ),
						'version' => array( 'type' => 'string' ),
						'author'  => array( 'type' => 'string' ),
					),
				),
				'plugin_count'      => array(
					'type'        => 'integer',
					'description' => __( 'Number of active plugins', 'airo-wp' ),
				),
				'reading_settings'  => array(
					'type'       => 'object',
					'properties' => array(
						'show_on_front'  => array(
							'type' => 'string',
							'enum' => array( 'posts', 'page' ),
						),
						'page_on_front'  => array( 'type' => array( 'integer', 'null' ) ),
						'page_for_posts' => array( 'type' => array( 'integer', 'null' ) ),
					),
				),
			),
		);
	}

	/**
	 * Published post count for a post type.
	 *
	 * @param string $post_type Post type slug.
	 * @return int
	 */
	private function get_published_post_count( string $post_type ): int {
		$counts = wp_count_posts( $post_type );
		return isset( $counts->publish ) ? (int) $counts->publish : 0;
	}

	/**
	 * Active theme name, version, author.
	 *
	 * @return array<string, string>
	 */
	private function get_theme_info(): array {
		$theme = wp_get_theme();
		return array(
			'name'    => $theme->get( 'Name' ),
			'version' => $theme->get( 'Version' ),
			'author'  => $theme->get( 'Author' ),
		);
	}

	/**
	 * Count of active plugins (single-site and network-activated).
	 *
	 * @return int
	 */
	private function get_active_plugin_count(): int {
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$network_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins  = array_merge( $active_plugins, $network_plugins );
		}
		return count( array_unique( $active_plugins ) );
	}

	/**
	 * Reading settings (front page display, blog page).
	 *
	 * @return array<string, mixed>
	 */
	private function get_reading_settings(): array {
		$show_on_front       = get_option( 'show_on_front', 'posts' );
		$shows_page_on_front = 'page' === $show_on_front;
		// IDs only meaningful when a static page is shown; zero-out otherwise so the > 0
		// guard below produces null consistently regardless of any stale option values.
		$page_on_front  = $shows_page_on_front ? (int) get_option( 'page_on_front', 0 ) : 0;
		$page_for_posts = $shows_page_on_front ? (int) get_option( 'page_for_posts', 0 ) : 0;

		return array(
			'show_on_front'  => $show_on_front,
			'page_on_front'  => $page_on_front > 0 ? $page_on_front : null,
			'page_for_posts' => $page_for_posts > 0 ? $page_for_posts : null,
		);
	}
}
