<?php
/**
 * GetNavigation MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-navigation MCP ability.
 */
class GetNavigation extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-navigation';

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
				'label'               => __( 'Get Navigation', 'airo-wp' ),
				'description'         => __( 'Retrieves a specific navigation post by ID', 'airo-wp' ),
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
		$context       = isset( $input['context'] ) ? $input['context'] : 'view';

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

		if ( ! empty( $post->post_password ) ) {
			$password = isset( $input['password'] ) ? $input['password'] : '';
			if ( $password !== $post->post_password ) {
				return array(
					'success' => false,
					'message' => __( 'Incorrect password for protected navigation', 'airo-wp' ),
				);
			}
		}

		if ( 'edit' === $context && ! current_user_can( 'edit_post', $navigation_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'You do not have permission to edit this navigation', 'airo-wp' ),
			);
		}

		return array(
			'success'    => true,
			'navigation' => $this->build_navigation_data( $post, $context ),
		);
	}

	/**
	 * Build navigation data based on context.
	 *
	 * @param \WP_Post $post    Post object.
	 * @param string   $context Response context.
	 * @return array<string, mixed> Navigation data.
	 */
	private function build_navigation_data( \WP_Post $post, string $context ): array {
		$data = array(
			'id'           => $post->ID,
			'date'         => $post->post_date,
			'date_gmt'     => $post->post_date_gmt,
			'guid'         => array(
				'rendered' => $post->guid,
				'raw'      => $post->guid,
			),
			'modified'     => $post->post_modified,
			'modified_gmt' => $post->post_modified_gmt,
			'slug'         => $post->post_name,
			'status'       => $post->post_status,
			'type'         => $post->post_type,
			'link'         => get_permalink( $post->ID ),
			'title'        => array(
				'rendered' => get_the_title( $post->ID ),
			),
		);

		if ( 'edit' === $context ) {
			$data['title']['raw'] = $post->post_title;
			$data['content']      = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			);
			$data['template']     = get_page_template_slug( $post->ID );
		} elseif ( 'view' === $context ) {
			$data['content']  = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
			$data['template'] = get_page_template_slug( $post->ID );
		} elseif ( 'embed' === $context ) {
			$data['content'] = array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			);
		}

		return $data;
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
				'id'       => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the post', 'airo-wp' ),
					'minimum'     => 1,
				),
				'context'  => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made; determines fields present in response', 'airo-wp' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
				'password' => array(
					'type'        => 'string',
					'description' => __( 'The password for the post if it is password protected', 'airo-wp' ),
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
			__( 'Navigation retrieval result', 'airo-wp' ),
			array(
				'navigation' => array(
					'type'        => 'object',
					'description' => __( 'The navigation data', 'airo-wp' ),
					'properties'  => array(
						'id'           => array(
							'type'        => 'integer',
							'description' => __( 'Unique identifier for the post', 'airo-wp' ),
						),
						'date'         => array(
							'type'        => 'string',
							'description' => __( 'The date the post was published, in the site\'s timezone', 'airo-wp' ),
						),
						'date_gmt'     => array(
							'type'        => 'string',
							'description' => __( 'The date the post was published, as GMT', 'airo-wp' ),
						),
						'guid'         => array(
							'type'        => 'object',
							'description' => __( 'The globally unique identifier for the post', 'airo-wp' ),
						),
						'modified'     => array(
							'type'        => 'string',
							'description' => __( 'The date the post was last modified, in the site\'s timezone', 'airo-wp' ),
						),
						'modified_gmt' => array(
							'type'        => 'string',
							'description' => __( 'The date the post was last modified, as GMT', 'airo-wp' ),
						),
						'slug'         => array(
							'type'        => 'string',
							'description' => __( 'An alphanumeric identifier for the post unique to its type', 'airo-wp' ),
						),
						'status'       => array(
							'type'        => 'string',
							'description' => __( 'A named status for the post', 'airo-wp' ),
						),
						'type'         => array(
							'type'        => 'string',
							'description' => __( 'Type of post', 'airo-wp' ),
						),
						'link'         => array(
							'type'        => 'string',
							'description' => __( 'URL to the post', 'airo-wp' ),
						),
						'title'        => array(
							'type'        => 'object',
							'description' => __( 'The title for the post', 'airo-wp' ),
						),
						'content'      => array(
							'type'        => 'object',
							'description' => __( 'The content for the post', 'airo-wp' ),
						),
						'template'     => array(
							'type'        => 'string',
							'description' => __( 'The theme file to use to display the post', 'airo-wp' ),
						),
					),
				),
			)
		);
	}
}
