<?php
/**
 * UpdateGlobalStyles MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Services\FontDownloader;

/**
 * Registers and executes the update-global-styles MCP ability.
 */
class UpdateGlobalStyles extends BaseTool {

	public const TOOL_ID = 'airo-wp/update-global-styles';

	/**
	 * Font downloader service.
	 *
	 * @var FontDownloader
	 */
	private $font_downloader;

	/**
	 * Constructor.
	 *
	 * @param FontDownloader $font_downloader Font downloader service.
	 */
	public function __construct( FontDownloader $font_downloader ) {
		$this->font_downloader = $font_downloader;
	}

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register the update global styles ability.
	 *
	 * @return void
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Update Global Styles', 'airo-wp' ),
				'description'         => __( 'Updates global styles configuration including styles, settings, and title', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'theme-management',
			)
		);
	}

	/**
	 * Execute the tool.
	 *
	 * @param array $input Tool input parameters.
	 * @return array
	 */
	public function execute( array $input ): array {
		try {
			$global_styles_id = isset( $input['id'] ) ? absint( $input['id'] ) : 0;

			if ( empty( $global_styles_id ) ) {
				return array(
					'success' => false,
					'message' => __( 'Global styles ID is required', 'airo-wp' ),
				);
			}

			$global_styles_post = get_post( $global_styles_id );

			if ( ! $global_styles_post || 'wp_global_styles' !== $global_styles_post->post_type ) {
				return array(
					'success' => false,
					'message' => __( 'Global styles post not found or invalid ID', 'airo-wp' ),
				);
			}

			$existing_content = array();
			if ( ! empty( $global_styles_post->post_content ) ) {
				$decoded = json_decode( $global_styles_post->post_content, true );
				if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
					$existing_content = $decoded;
				} else {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					error_log(
						sprintf(
							'Global styles post %d has corrupted JSON content. Starting fresh. Error: %s',
							$global_styles_id,
							json_last_error_msg()
						)
					);
				}
			}

			$new_content = $existing_content;
			$overwrite   = isset( $input['overwrite'] ) && true === $input['overwrite'];

			if ( isset( $input['styles'] ) && is_array( $input['styles'] ) ) {
				if ( $overwrite ) {
					$new_content['styles'] = $input['styles'];
				} else {
					if ( ! isset( $new_content['styles'] ) ) {
						$new_content['styles'] = array();
					}
					$new_content['styles'] = $this->deep_merge_arrays( $new_content['styles'], $input['styles'] );
				}
			}

			if ( isset( $input['settings'] ) && is_array( $input['settings'] ) ) {
				if ( $overwrite ) {
					$new_content['settings'] = $input['settings'];
				} else {
					if ( ! isset( $new_content['settings'] ) ) {
						$new_content['settings'] = array();
					}
					$new_content['settings'] = $this->deep_merge_arrays( $new_content['settings'], $input['settings'] );
				}

				if ( isset( $new_content['settings']['typography']['fontFamilies'] ) ) {
					$new_content['settings']['typography']['fontFamilies'] = $this->font_downloader->process_font_families(
						$new_content['settings']['typography']['fontFamilies']
					);
				}
			}

			$encoded_content = wp_json_encode( $new_content );
			if ( false === $encoded_content ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: JSON encoding error message. */
						__( 'Failed to encode global styles: %s', 'airo-wp' ),
						json_last_error_msg()
					),
				);
			}

			$post_data = array(
				'ID'           => $global_styles_id,
				'post_content' => $encoded_content,
			);

			if ( isset( $input['title'] ) && ! empty( $input['title'] ) ) {
				$post_data['post_title'] = sanitize_text_field( $input['title'] );
			}

			$post_data = wp_slash( $post_data );

			$update_result = wp_update_post( $post_data, true );

			if ( is_wp_error( $update_result ) ) {
				return array(
					'success' => false,
					'message' => sprintf(
						/* translators: %s: error message */
						__( 'Failed to update global styles: %s', 'airo-wp' ),
						$update_result->get_error_message()
					),
				);
			}

			if ( ! $update_result ) {
				return array(
					'success' => false,
					'message' => __( 'Failed to update global styles', 'airo-wp' ),
				);
			}

			return array(
				'success' => true,
				'data'    => array(
					'id'      => (int) $update_result,
					'title'   => isset( $input['title'] ) && ! empty( $input['title'] ) ? sanitize_text_field( $input['title'] ) : $global_styles_post->post_title,
					'content' => $new_content,
					'status'  => $global_styles_post->post_status,
					'date'    => current_time( 'mysql' ),
				),
				'message' => __( 'Global styles updated successfully', 'airo-wp' ),
			);

		} catch ( \Exception $e ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %s: error message */
					__( 'Error updating global styles: %s', 'airo-wp' ),
					$e->getMessage()
				),
			);
		}
	}

	/**
	 * Deep merge two arrays recursively with intelligent array handling.
	 *
	 * @param array $existing The existing array.
	 * @param array $new_data The new array to merge.
	 * @return array
	 */
	private function deep_merge_arrays( array $existing, array $new_data ): array {
		foreach ( $new_data as $key => $value ) {
			if ( is_array( $value ) && isset( $existing[ $key ] ) && is_array( $existing[ $key ] ) ) {
				if ( $this->is_list_of_objects( $value ) || $this->is_list_of_objects( $existing[ $key ] ) ) {
					$existing[ $key ] = $value;
				} else {
					$existing[ $key ] = $this->deep_merge_arrays( $existing[ $key ], $value );
				}
			} else {
				$existing[ $key ] = $value;
			}
		}
		return $existing;
	}

	/**
	 * Check if an array is a numeric-indexed list of objects/arrays.
	 *
	 * @param array $value The array to check.
	 * @return bool True if this is a list of objects that should be replaced.
	 */
	private function is_list_of_objects( array $value ): bool {
		if ( empty( $value ) ) {
			return false;
		}

		$keys = array_keys( $value );
		if ( range( 0, count( $value ) - 1 ) !== $keys ) {
			return false;
		}

		return is_array( $value[0] );
	}

	/**
	 * Get input schema for the tool.
	 *
	 * @return array
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'        => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the global styles template to update', 'airo-wp' ),
				),
				'styles'    => array(
					'type'        => 'object',
					'description' => __( 'Global styles configuration object', 'airo-wp' ),
				),
				'settings'  => array(
					'type'        => 'object',
					'description' => __( 'Global settings configuration object', 'airo-wp' ),
				),
				'title'     => array(
					'type'        => 'string',
					'description' => __( 'Title of the global styles variation', 'airo-wp' ),
				),
				'overwrite' => array(
					'type'        => 'boolean',
					'description' => __( 'If true, replace entire styles/settings instead of merging. Defaults to false.', 'airo-wp' ),
				),
			),
			'required'   => array( 'id' ),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Updated global styles data', 'airo-wp' ),
			array(
				'data' => array(
					'type'        => 'object',
					'description' => __( 'Updated global styles data', 'airo-wp' ),
					'properties'  => array(
						'id'      => array(
							'type'        => 'integer',
							'description' => __( 'The global styles post ID', 'airo-wp' ),
						),
						'title'   => array(
							'type'        => 'string',
							'description' => __( 'The global styles title', 'airo-wp' ),
						),
						'content' => array(
							'type'        => 'object',
							'description' => __( 'The updated global styles configuration', 'airo-wp' ),
						),
						'status'  => array(
							'type'        => 'string',
							'description' => __( 'The post status', 'airo-wp' ),
						),
						'date'    => array(
							'type'        => 'string',
							'description' => __( 'The last modified date', 'airo-wp' ),
						),
					),
				),
			)
		);
	}
}
