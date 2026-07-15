<?php
/**
 * ListPlugins MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services\PluginHelper;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the list-plugins MCP ability.
 *
 * Lists installed plugins with optional filtering by status and search term.
 */
class ListPlugins extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/list-plugins';

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
				'label'               => __( 'List Plugins', 'airo-wp' ),
				'description'         => __( 'Retrieves a list of installed WordPress plugins with their status, metadata, and available-update info (current version plus the new version when an update is available)', 'airo-wp' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'context'     => array(
							'type'        => 'string',
							'description' => __( 'Response context: view, embed, or edit', 'airo-wp' ),
							'enum'        => array( 'view', 'embed', 'edit' ),
							'default'     => 'view',
						),
						'search'      => array(
							'type'        => 'string',
							'description' => __( 'Search term to filter plugins by name or description', 'airo-wp' ),
						),
						'status'      => array(
							'type'        => 'string',
							'description' => __( 'Filter by plugin status', 'airo-wp' ),
							'enum'        => array( 'active', 'inactive' ),
						),
						'force_check' => array(
							'type'        => 'boolean',
							'description' => __( 'Force a refresh of available plugin updates from WordPress.org before reading. Defaults to false; the cached transient is normally kept fresh by core cron.', 'airo-wp' ),
							'default'     => false,
						),
					),
				),
				'output_schema'       => $this->build_output_schema(
					__( 'Plugin list result', 'airo-wp' ),
					array(
						'plugins' => array(
							'type'        => 'array',
							'description' => __( 'List of plugins', 'airo-wp' ),
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'slug'             => array( 'type' => 'string' ),
									'name'             => array( 'type' => 'string' ),
									'version'          => array( 'type' => 'string' ),
									'author'           => array( 'type' => 'string' ),
									'description'      => array( 'type' => 'string' ),
									'status'           => array( 'type' => 'string' ),
									'file'             => array( 'type' => 'string' ),
									'plugin_uri'       => array( 'type' => 'string' ),
									'author_uri'       => array( 'type' => 'string' ),
									'text_domain'      => array( 'type' => 'string' ),
									'network_only'     => array( 'type' => 'boolean' ),
									'requires_wp'      => array( 'type' => 'string' ),
									'requires_php'     => array( 'type' => 'string' ),
									'update_available' => array( 'type' => 'boolean' ),
									'new_version'      => array( 'type' => array( 'string', 'null' ) ),
								),
							),
						),
						'total'   => array(
							'type'        => 'integer',
							'description' => __( 'Total number of plugins returned', 'airo-wp' ),
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
		$this->load_admin_file( 'plugin.php' );

		$this->plugin_helper->refresh_updates( ! empty( $input['force_check'] ) );

		$context = isset( $input['context'] ) ? sanitize_text_field( $input['context'] ) : 'view';
		$search  = isset( $input['search'] ) ? sanitize_text_field( $input['search'] ) : '';
		$status  = isset( $input['status'] ) ? sanitize_text_field( $input['status'] ) : '';

		if ( ! in_array( $context, array( 'view', 'embed', 'edit' ), true ) ) {
			$context = 'view';
		}

		$all_plugins = get_plugins();
		$results     = array();

		foreach ( $all_plugins as $file => $data ) {
			$active = is_plugin_active( $file );

			// Filter by status.
			if ( 'active' === $status && ! $active ) {
				continue;
			}
			if ( 'inactive' === $status && $active ) {
				continue;
			}

			// Filter by search term.
			if ( ! empty( $search ) ) {
				$name = isset( $data['Name'] ) ? $data['Name'] : '';
				$desc = isset( $data['Description'] ) ? $data['Description'] : '';
				if ( stripos( $name, $search ) === false && stripos( $desc, $search ) === false ) {
					continue;
				}
			}

			// Derive slug from file path.
			$slug = $this->get_slug_from_file( $file );

			if ( 'embed' === $context ) {
				$results[] = array(
					'slug'    => $slug,
					'name'    => isset( $data['Name'] ) ? $data['Name'] : '',
					'version' => isset( $data['Version'] ) ? $data['Version'] : '',
					'status'  => $active ? 'active' : 'inactive',
				);
			} else {
				$update_info = $this->plugin_helper->get_update_info( $file );
				$results[]   = array(
					'slug'             => $slug,
					'name'             => isset( $data['Name'] ) ? $data['Name'] : '',
					'version'          => isset( $data['Version'] ) ? $data['Version'] : '',
					'author'           => isset( $data['Author'] ) ? $data['Author'] : '',
					'description'      => isset( $data['Description'] ) ? $data['Description'] : '',
					'status'           => $active ? 'active' : 'inactive',
					'file'             => $file,
					'plugin_uri'       => isset( $data['PluginURI'] ) ? $data['PluginURI'] : '',
					'author_uri'       => isset( $data['AuthorURI'] ) ? $data['AuthorURI'] : '',
					'text_domain'      => isset( $data['TextDomain'] ) ? $data['TextDomain'] : '',
					'network_only'     => ! empty( $data['Network'] ),
					'requires_wp'      => isset( $data['RequiresWP'] ) ? $data['RequiresWP'] : '',
					'requires_php'     => isset( $data['RequiresPHP'] ) ? $data['RequiresPHP'] : '',
					'update_available' => $update_info['update_available'],
					'new_version'      => $update_info['new_version'],
				);
			}
		}

		return array(
			'success' => true,
			'plugins' => $results,
			'total'   => count( $results ),
		);
	}

	/**
	 * Derive a plugin slug from its file path.
	 *
	 * @param string $file Plugin file relative path.
	 * @return string Plugin slug.
	 */
	private function get_slug_from_file( string $file ): string {
		$parts = explode( '/', $file );

		return $parts[0];
	}
}
