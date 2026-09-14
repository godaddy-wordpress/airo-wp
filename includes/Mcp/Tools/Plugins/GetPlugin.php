<?php
/**
 * GetPlugin MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Services\PluginHelper;

/**
 * Registers and executes the get-plugin MCP ability.
 *
 * Retrieves information about a specific plugin, including version and available-update info.
 */
class GetPlugin extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/get-plugin';

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
				'label'               => __( 'Get Plugin', 'airo-wp' ),
				'description'         => __( 'Retrieves information about a specific WordPress plugin by its slug, including current version and available-update info (whether an update exists and the target version)', 'airo-wp' ),
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
				'context'     => array(
					'type'        => 'string',
					'description' => __( 'Response context: view, embed, or edit', 'airo-wp' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'force_check' => array(
					'type'        => 'boolean',
					'description' => __( 'Force a refresh of available plugin updates from WordPress.org before reading. Defaults to false; the cached transient is normally kept fresh by core cron.', 'airo-wp' ),
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
			__( 'Plugin information', 'airo-wp' ),
			array(
				'plugin' => array(
					'type'        => 'object',
					'description' => __( 'Plugin details', 'airo-wp' ),
					'properties'  => array(
						'slug'             => array( 'type' => 'string' ),
						'name'             => array( 'type' => 'string' ),
						'version'          => array( 'type' => 'string' ),
						'author'           => array( 'type' => 'string' ),
						'description'      => array( 'type' => 'string' ),
						'plugin_uri'       => array( 'type' => 'string' ),
						'author_uri'       => array( 'type' => 'string' ),
						'text_domain'      => array( 'type' => 'string' ),
						'status'           => array( 'type' => 'string' ),
						'file'             => array( 'type' => 'string' ),
						'network_only'     => array( 'type' => 'boolean' ),
						'requires_wp'      => array( 'type' => 'string' ),
						'requires_php'     => array( 'type' => 'string' ),
						'update_available' => array( 'type' => 'boolean' ),
						'new_version'      => array( 'type' => array( 'string', 'null' ) ),
					),
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

		$context = isset( $input['context'] ) ? sanitize_text_field( $input['context'] ) : 'view';

		if ( ! in_array( $context, array( 'view', 'embed', 'edit' ), true ) ) {
			$context = 'view';
		}

		$this->load_admin_file( 'plugin.php' );

		$this->plugin_helper->refresh_updates( ! empty( $input['force_check'] ) );

		$plugin_file = $this->plugin_helper->find_file( $slug );

		if ( ! $plugin_file ) {
			return array(
				'success' => false,
				'message' => __( 'Plugin not found.', 'airo-wp' ),
			);
		}

		$data   = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, false, false );
		$active = is_plugin_active( $plugin_file );

		if ( 'embed' === $context ) {
			return array(
				'success' => true,
				'plugin'  => array(
					'slug'    => $slug,
					'name'    => isset( $data['Name'] ) ? $data['Name'] : '',
					'version' => isset( $data['Version'] ) ? $data['Version'] : '',
					'status'  => $active ? 'active' : 'inactive',
				),
			);
		}

		$update_info = $this->plugin_helper->get_update_info( $plugin_file );

		return array(
			'success' => true,
			'plugin'  => array(
				'slug'             => $slug,
				'name'             => isset( $data['Name'] ) ? $data['Name'] : '',
				'version'          => isset( $data['Version'] ) ? $data['Version'] : '',
				'author'           => isset( $data['Author'] ) ? $data['Author'] : '',
				'description'      => isset( $data['Description'] ) ? $data['Description'] : '',
				'plugin_uri'       => isset( $data['PluginURI'] ) ? $data['PluginURI'] : '',
				'author_uri'       => isset( $data['AuthorURI'] ) ? $data['AuthorURI'] : '',
				'text_domain'      => isset( $data['TextDomain'] ) ? $data['TextDomain'] : '',
				'status'           => $active ? 'active' : 'inactive',
				'file'             => $plugin_file,
				'network_only'     => ! empty( $data['Network'] ),
				'requires_wp'      => isset( $data['RequiresWP'] ) ? $data['RequiresWP'] : '',
				'requires_php'     => isset( $data['RequiresPHP'] ) ? $data['RequiresPHP'] : '',
				'update_available' => $update_info['update_available'],
				'new_version'      => $update_info['new_version'],
			),
		);
	}
}
