<?php
/**
 * UpdateSiteOptions MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the update-site-options MCP ability.
 *
 * Supports both single and bulk option updates with validation
 * and protection against modifying sensitive options.
 */
class UpdateSiteOptions extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/update-site-options';

	/**
	 * Protected option names that cannot be modified.
	 *
	 * All values must be lowercase; is_protected_option() compares against strtolower($name).
	 * AI service API keys are included because they grant external service access and must
	 * not be writable via the MCP surface.
	 *
	 * @var array<string>
	 */
	private const PROTECTED_OPTIONS = array(
		'db_version',
		'wp_db_version',
		'secret_key',
		'auth_key',
		'secure_auth_key',
		'logged_in_key',
		'nonce_key',
		'auth_salt',
		'secure_auth_salt',
		'logged_in_salt',
		'nonce_salt',
		// AI service credentials.
		'openai_api_key',
		'anthropic_api_key',
	);

	/**
	 * Protected option name prefixes that cannot be modified.
	 *
	 * Any option whose lowercased name starts with one of these prefixes is treated as
	 * protected. Add vendor-specific credential prefixes here rather than enumerating
	 * every individual key.
	 *
	 * @var array<string>
	 */
	private const PROTECTED_PREFIXES = array(
		'gd_mwcs_', // GoDaddy Managed WooCommerce Connect service credentials.
	);

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
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Site Options', 'airo-wp' ),
				'description'         => __( 'Updates one or more WordPress site options', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->build_output_schema(
					__( 'Site options update result', 'airo-wp' ),
					array(
						'updated_count' => array(
							'type'        => 'integer',
							'description' => __( 'Number of options successfully updated', 'airo-wp' ),
						),
						'failed_count'  => array(
							'type'        => 'integer',
							'description' => __( 'Number of options that failed to update', 'airo-wp' ),
						),
						'results'       => array(
							'type'        => 'array',
							'description' => __( 'Per-option results', 'airo-wp' ),
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'option_name' => array( 'type' => 'string' ),
									'success'     => array( 'type' => 'boolean' ),
									'message'     => array( 'type' => 'string' ),
								),
							),
						),
					),
					array( 'updated_count', 'failed_count', 'results' )
				),
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
	 * @return array<string, mixed> Execution result.
	 */
	public function execute( array $input ): array {
		$has_single = array_key_exists( 'option_name', $input );
		$has_bulk   = ! empty( $input['options'] ) && is_array( $input['options'] );

		// Reject if both single and bulk are provided.
		if ( $has_single && $has_bulk ) {
			return array(
				'success' => false,
				'message' => __( 'Provide either option_name/option_value or options, not both.', 'airo-wp' ),
			);
		}

		// Normalize into array of items.
		$items = array();

		if ( $has_single ) {
			$name = sanitize_text_field( (string) $input['option_name'] );
			if ( empty( $name ) ) {
				return array(
					'success' => false,
					'message' => __( 'Option name cannot be empty.', 'airo-wp' ),
				);
			}
			$items[] = array(
				'option_name'  => $input['option_name'],
				'option_value' => array_key_exists( 'option_value', $input ) ? $input['option_value'] : null,
			);
		} elseif ( $has_bulk ) {
			$items = $input['options'];
		}

		if ( empty( $items ) ) {
			return array(
				'success' => false,
				'message' => __( 'No valid options provided.', 'airo-wp' ),
			);
		}

		// Pre-validate entire batch (fail closed, no partial writes).
		foreach ( $items as $item ) {
			$name = isset( $item['option_name'] ) ? sanitize_text_field( $item['option_name'] ) : '';
			$name = $this->canonical_option_name( $name );

			if ( empty( $name ) ) {
				return array(
					'success' => false,
					'message' => __( 'Option name cannot be empty.', 'airo-wp' ),
				);
			}

			if ( $this->is_protected_option( $name ) ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: option name */
						__( 'Option "%s" is protected and cannot be modified.', 'airo-wp' ),
						$name
					),
				);
			}
		}

		// Process updates.
		$results       = array();
		$updated_count = 0;
		$failed_count  = 0;

		foreach ( $items as $item ) {
			$name  = $this->canonical_option_name( sanitize_text_field( $item['option_name'] ) );
			$value = array_key_exists( 'option_value', $item ) ? $item['option_value'] : null;

			// WPLANG special handling.
			if ( 'WPLANG' === $name ) {
				if ( ! is_string( $value ) && ! is_null( $value ) ) {
					$results[] = array(
						'option_name' => $name,
						'success'     => false,
						'message'     => __( 'WPLANG value must be a string or null.', 'airo-wp' ),
					);
					++$failed_count;
					continue;
				}
				$value = $this->normalize_wplang_value( $value );
				$this->ensure_core_language_pack_for_locale( $value );
				// Language pack failure is non-blocking; we still attempt the update.
			}

			$updated = update_option( $name, $value );

			if ( false === $updated ) {
				// Check if value is already the same (treat as success).
				$current = get_option( $name );
				if ( $this->values_are_equal( $current, $value ) ) {
					$results[] = array(
						'option_name' => $name,
						'success'     => true,
						'message'     => __( 'Option value unchanged (already set to this value).', 'airo-wp' ),
					);
					++$updated_count;
				} else {
					$results[] = array(
						'option_name' => $name,
						'success'     => false,
						'message'     => __( 'Failed to update option.', 'airo-wp' ),
					);
					++$failed_count;
				}
			} else {
				$results[] = array(
					'option_name' => $name,
					'success'     => true,
					'message'     => __( 'Option updated successfully.', 'airo-wp' ),
				);
				++$updated_count;
			}
		}

		$all_success = 0 === $failed_count;

		return array(
			'success'       => $all_success,
			'message'       => $all_success
				? sprintf(
					/* translators: %d: number of options updated */
					__( 'Successfully updated %d option(s).', 'airo-wp' ),
					$updated_count
				)
				: sprintf(
					/* translators: 1: number of failures, 2: total options */
					__( '%1$d of %2$d option(s) failed to update.', 'airo-wp' ),
					$failed_count,
					count( $items )
				),
			'updated_count' => $updated_count,
			'failed_count'  => $failed_count,
			'results'       => $results,
		);
	}

	/**
	 * Input JSON Schema for this tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		$option_value_schema = array(
			'description' => __( 'The option value', 'airo-wp' ),
			'anyOf'       => array(
				array( 'type' => 'string' ),
				array( 'type' => 'number' ),
				array( 'type' => 'boolean' ),
				array( 'type' => 'array' ),
				array( 'type' => 'object' ),
				array( 'type' => 'null' ),
			),
		);

		return array(
			'type'       => 'object',
			'properties' => array(
				'option_name'  => array(
					'type'        => 'string',
					'description' => __( 'Option name for single option updates', 'airo-wp' ),
				),
				'option_value' => $option_value_schema,
				'options'      => array(
					'type'        => 'array',
					'description' => __( 'Array of options for bulk updates', 'airo-wp' ),
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'option_name'  => array(
								'type'        => 'string',
								'description' => __( 'The option name', 'airo-wp' ),
							),
							'option_value' => $option_value_schema,
						),
						'required'   => array( 'option_name', 'option_value' ),
					),
				),
			),
			'anyOf'      => array(
				array( 'required' => array( 'option_name', 'option_value' ) ),
				array( 'required' => array( 'options' ) ),
			),
		);
	}

	/**
	 * Canonicalize option name.
	 *
	 * Maps case-insensitive 'wplang' to 'WPLANG'.
	 *
	 * @param string $name Option name.
	 * @return string Canonicalized option name.
	 */
	private function canonical_option_name( string $name ): string {
		if ( 'wplang' === strtolower( $name ) ) {
			return 'WPLANG';
		}

		return $name;
	}

	/**
	 * Check if an option is protected.
	 *
	 * @param string $name Option name.
	 * @return bool True if the option is protected.
	 */
	private function is_protected_option( string $name ): bool {
		$lower = strtolower( $name );

		if ( in_array( $lower, self::PROTECTED_OPTIONS, true ) ) {
			return true;
		}

		// str_starts_with() requires PHP 8.0+; use strpos() for 7.4 compat.
		foreach ( self::PROTECTED_PREFIXES as $prefix ) {
			if ( 0 === strpos( $lower, $prefix ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Normalize a WPLANG value.
	 *
	 * Converts null to empty string, trims, replaces '-' with '_',
	 * and converts 'en_US' to empty string (WordPress default).
	 *
	 * @param string|null $value WPLANG value.
	 * @return string Normalized locale string.
	 */
	private function normalize_wplang_value( $value ): string {
		if ( null === $value ) {
			return '';
		}

		$value = trim( $value );
		$value = str_replace( '-', '_', $value );

		if ( 'en_US' === $value ) {
			return '';
		}

		return $value;
	}

	/**
	 * Ensure a language pack is available for the given locale.
	 *
	 * @param string $locale Locale string.
	 * @return array{success: bool, message: string} Result of language pack installation.
	 */
	private function ensure_core_language_pack_for_locale( string $locale ): array {
		if ( empty( $locale ) ) {
			return array(
				'success' => true,
				'message' => __( 'Default locale (en_US) does not need a language pack.', 'airo-wp' ),
			);
		}

		$this->load_admin_file( 'file.php' );
		$this->load_admin_file( 'translation-install.php' );

		$result = wp_download_language_pack( $locale );

		if ( false === $result ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: locale */
					__( 'Failed to download language pack for "%s".', 'airo-wp' ),
					$locale
				),
			);
		}

		return array(
			'success' => true,
			'message' => sprintf(
				/* translators: %s: locale */
				__( 'Language pack for "%s" installed successfully.', 'airo-wp' ),
				$locale
			),
		);
	}

	/**
	 * Compare two values for equality, handling boolean normalization.
	 *
	 * WordPress stores booleans as '1'/'0' strings, so we normalize
	 * before comparison.
	 *
	 * @param mixed $current   Current option value.
	 * @param mixed $new_value New option value.
	 * @return bool True if values are considered equal.
	 */
	private function values_are_equal( $current, $new_value ): bool {
		// Normalize booleans for comparison.
		if ( is_bool( $new_value ) ) {
			$new_value = $new_value ? '1' : '0';
		}

		if ( is_bool( $current ) ) {
			$current = $current ? '1' : '0';
		}

		// phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison, Universal.Operators.StrictComparisons.LooseEqual -- Intentional loose comparison for option values.
		return $current == $new_value;
	}
}
