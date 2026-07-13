<?php
/**
 * DeleteNavigation MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the delete-navigation MCP ability.
 */
class DeleteNavigation extends BaseTool {

	public const TOOL_ID = 'airo-wp/delete-navigation';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Delete Navigation', 'airo-wp' ),
				'description'         => __( 'Deletes a navigation post (trash or permanently)', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'content-management',
			)
		);
	}

	/**
	 * Execute the tool.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed>
	 */
	public function execute( array $input ): array {
		if ( empty( $input['id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Navigation ID is required', 'airo-wp' ),
			);
		}

		$navigation_id = (int) $input['id'];
		$force_delete  = ! empty( $input['force'] );

		$post = get_post( $navigation_id );
		if ( ! $post || 'wp_navigation' !== $post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: Navigation post ID */
					__( 'Navigation with ID %d not found', 'airo-wp' ),
					$navigation_id
				),
			);
		}

		if ( ! current_user_can( 'delete_post', $navigation_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to delete this navigation', 'airo-wp' ),
			);
		}

		$previous = array(
			'id'      => $post->ID,
			'date'    => $post->post_date,
			'slug'    => $post->post_name,
			'status'  => $post->post_status,
			'type'    => $post->post_type,
			'title'   => array(
				'rendered' => get_the_title( $post->ID ),
				'raw'      => $post->post_title,
			),
			'content' => array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			),
		);

		$result = wp_delete_post( $navigation_id, $force_delete );

		if ( ! $result ) {
			return array(
				'success' => false,
				'message' => __( 'Failed to delete navigation', 'airo-wp' ),
			);
		}

		$message = $force_delete
			? __( 'Navigation permanently deleted', 'airo-wp' )
			: __( 'Navigation moved to trash', 'airo-wp' );

		return array(
			'success'  => true,
			'message'  => $message,
			'deleted'  => array(
				'id'     => $navigation_id,
				'status' => $post->post_status,
			),
			'previous' => $previous,
		);
	}

	/**
	 * Get input schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'    => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the post', 'airo-wp' ),
					'minimum'     => 1,
				),
				'force' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to bypass Trash and force deletion', 'airo-wp' ),
					'default'     => false,
				),
			),
			'required'   => array( 'id' ),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Navigation deletion result', 'airo-wp' ),
			array(
				'deleted'  => array(
					'type'        => 'object',
					'description' => __( 'Information about the deleted navigation', 'airo-wp' ),
				),
				'previous' => array(
					'type'        => 'object',
					'description' => __( 'The navigation data before deletion', 'airo-wp' ),
				),
			)
		);
	}
}
