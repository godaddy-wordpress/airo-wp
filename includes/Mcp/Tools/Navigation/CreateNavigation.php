<?php
/**
 * CreateNavigation MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the create-navigation MCP ability.
 */
class CreateNavigation extends BaseTool {

	public const TOOL_ID = 'airo-wp/create-navigation';

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
				'label'               => __( 'Create Navigation', 'airo-wp' ),
				'description'         => __( 'Creates a new navigation post', 'airo-wp' ),
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
		$post_data = array(
			'post_type'   => 'wp_navigation',
			'post_status' => isset( $input['status'] ) ? $input['status'] : 'publish',
		);

		if ( ! empty( $input['title'] ) ) {
			$post_data['post_title'] = sanitize_text_field( $input['title'] );
		}

		if ( ! empty( $input['content'] ) ) {
			$post_data['post_content'] = $input['content'];
		}

		if ( ! empty( $input['slug'] ) ) {
			$post_data['post_name'] = sanitize_title( $input['slug'] );
		}

		if ( ! empty( $input['password'] ) ) {
			$post_data['post_password'] = $input['password'];
		}

		if ( ! empty( $input['date'] ) ) {
			$post_data['post_date'] = $input['date'];
		}

		if ( ! empty( $input['date_gmt'] ) ) {
			$post_data['post_date_gmt'] = $input['date_gmt'];
		}

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return array(
				'success' => false,
				'message' => $post_id->get_error_message(),
			);
		}

		wp_save_post_revision( $post_id );

		if ( ! empty( $input['template'] ) ) {
			update_post_meta( $post_id, '_wp_page_template', sanitize_text_field( $input['template'] ) );
		}

		$post = get_post( $post_id );

		return array(
			'success'    => true,
			'navigation' => $this->build_navigation_data( $post ),
			'message'    => __( 'Navigation created successfully', 'airo-wp' ),
		);
	}

	/**
	 * Build navigation data for response.
	 *
	 * @param \WP_Post $post Post object.
	 * @return array<string, mixed> Navigation data.
	 */
	private function build_navigation_data( \WP_Post $post ): array {
		return array(
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
				'raw'      => $post->post_title,
			),
			'content'      => array(
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				'rendered' => apply_filters( 'the_content', $post->post_content ),
				'raw'      => $post->post_content,
			),
			'template'     => get_page_template_slug( $post->ID ),
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
				'date'     => array(
					'type'        => 'string',
					'description' => __( 'The date the post was published, in the site\'s timezone', 'airo-wp' ),
				),
				'date_gmt' => array(
					'type'        => 'string',
					'description' => __( 'The date the post was published, as GMT', 'airo-wp' ),
				),
				'slug'     => array(
					'type'        => 'string',
					'description' => __( 'An alphanumeric identifier for the post unique to its type', 'airo-wp' ),
				),
				'status'   => array(
					'type'        => 'string',
					'description' => __( 'A named status for the post', 'airo-wp' ),
					'enum'        => array( 'publish', 'future', 'draft', 'pending', 'private' ),
					'default'     => 'publish',
				),
				'password' => array(
					'type'        => 'string',
					'description' => __( 'A password to protect access to the content and excerpt', 'airo-wp' ),
				),
				'title'    => array(
					'type'        => 'string',
					'description' => __( 'The title for the post', 'airo-wp' ),
				),
				'content'  => array(
					'type'        => 'string',
					'description' => __( 'The content for the post', 'airo-wp' ),
				),
				'template' => array(
					'type'        => 'string',
					'description' => __( 'The theme file to use to display the post', 'airo-wp' ),
				),
			),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Navigation creation result', 'airo-wp' ),
			array(
				'navigation' => array(
					'type'        => 'object',
					'description' => __( 'The created navigation data', 'airo-wp' ),
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
