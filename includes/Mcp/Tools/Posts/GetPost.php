<?php
/**
 * GetPost MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-post MCP ability.
 */
class GetPost extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-post';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Post', 'airo-wp' ),
				'description'         => __( 'Retrieves a WordPress post by its ID', 'airo-wp' ),
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
	 * @return array<string, mixed> Post data or not-found result.
	 */
	public function execute( array $input ): array {
		$post_id = isset( $input['post_id'] ) ? (int) $input['post_id'] : 1;
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return array(
				'id'                => $post_id,
				'title'             => 'Post not found',
				'content'           => '',
				'excerpt'           => '',
				'status'            => 'not_found',
				'post_type'         => 'post',
				'author_id'         => 0,
				'date_created'      => '',
				'date_modified'     => '',
				'slug'              => '',
				'featured_media_id' => 0,
				'meta'              => array(),
			);
		}

		$result = array(
			'id'                => $post->ID,
			'title'             => $post->post_title,
			'content'           => $post->post_content,
			'excerpt'           => $post->post_excerpt,
			'status'            => $post->post_status,
			'post_type'         => $post->post_type,
			'author_id'         => (int) $post->post_author,
			'date_created'      => $post->post_date,
			'date_modified'     => $post->post_modified,
			'slug'              => $post->post_name,
			'featured_media_id' => (int) get_post_thumbnail_id( $post->ID ),
			'meta'              => array(),
		);

		if ( ! empty( $input['include_meta'] ) ) {
			$result['meta'] = get_post_meta( $post_id );
		}

		return $result;
	}

	/**
	 * Get input schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'post_id'      => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post to retrieve', 'airo-wp' ),
					'minimum'     => 1,
				),
				'include_meta' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include post meta data', 'airo-wp' ),
					'default'     => true,
				),
			),
			'required'   => array( 'post_id' ),
		);
	}

	/**
	 * Get output schema for the tool.
	 *
	 * This method is public so that GetPostByOptionName can reuse the same schema.
	 *
	 * @return array<string, mixed>
	 */
	public function get_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'                => array(
					'type'        => 'integer',
					'description' => __( 'The post ID', 'airo-wp' ),
				),
				'title'             => array(
					'type'        => 'string',
					'description' => __( 'The post title', 'airo-wp' ),
				),
				'content'           => array(
					'type'        => 'string',
					'description' => __( 'The post content', 'airo-wp' ),
				),
				'excerpt'           => array(
					'type'        => 'string',
					'description' => __( 'The post excerpt', 'airo-wp' ),
				),
				'status'            => array(
					'type'        => 'string',
					'description' => __( 'The post status (publish, draft, etc.)', 'airo-wp' ),
				),
				'post_type'         => array(
					'type'        => 'string',
					'description' => __( 'The post type', 'airo-wp' ),
				),
				'author_id'         => array(
					'type'        => 'integer',
					'description' => __( 'The post author ID', 'airo-wp' ),
				),
				'date_created'      => array(
					'type'        => 'string',
					'description' => __( 'The post creation date', 'airo-wp' ),
				),
				'date_modified'     => array(
					'type'        => 'string',
					'description' => __( 'The post modification date', 'airo-wp' ),
				),
				'slug'              => array(
					'type'        => 'string',
					'description' => __( 'The post slug', 'airo-wp' ),
				),
				'featured_media_id' => array(
					'type'        => 'integer',
					'description' => __( 'The featured image attachment ID (0 if none)', 'airo-wp' ),
				),
				'meta'              => array(
					'type'        => 'object',
					'description' => __( 'Post meta data (if requested)', 'airo-wp' ),
				),
			),
		);
	}
}
