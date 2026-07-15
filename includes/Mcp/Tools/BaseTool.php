<?php
/**
 * Base class for MCP tools.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools;

defined( 'ABSPATH' ) || exit;

/**
 * Abstract base for MCP tools providing common helpers.
 */
abstract class BaseTool {

	/**
	 * Execute the tool.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed> Execution result.
	 */
	abstract public function execute( array $input ): array;

	/**
	 * Register this tool as a WordPress ability.
	 */
	abstract public function register(): void;

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	abstract public function check_permissions(): bool;

	/**
	 * Load WordPress admin file.
	 *
	 * @param string $filename Admin filename (e.g., 'plugin.php').
	 */
	protected function load_admin_file( string $filename ): void {
		if ( defined( 'PHPUNIT_RUNNING' ) ) {
			return;
		}

		$full_path = ABSPATH . 'wp-admin/includes/' . $filename;

		if ( file_exists( $full_path ) ) {
			require_once $full_path; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable -- path is constructed from ABSPATH, a trusted constant.
		}
	}

	/**
	 * Build a standard output schema with success/message base fields.
	 *
	 * @param string              $description Additional description.
	 * @param array<string,mixed> $properties  Additional properties.
	 * @param array<string>       $required    Additional required properties.
	 * @return array<string,mixed> Output schema.
	 */
	protected function build_output_schema( string $description = '', array $properties = array(), array $required = array() ): array {
		$schema = array(
			'type'       => 'object',
			'properties' => array(
				'success' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the operation succeeded', 'airo-wp' ),
				),
				'message' => array(
					'type'        => 'string',
					'description' => __( 'Success or error message', 'airo-wp' ),
				),
			),
			'required'   => array( 'success' ),
		);

		if ( ! empty( $description ) ) {
			$schema['description'] = $description;
		}

		if ( ! empty( $properties ) ) {
			$schema['properties'] = array_merge( $schema['properties'], $properties );
		}

		if ( ! empty( $required ) ) {
			$schema['required'] = array_merge( $schema['required'], $required );
		}

		return $schema;
	}
}
