<?php
/**
 * GetPageRevision MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-page-revision MCP ability.
 *
 * Retrieves a specific page revision by ID, enforcing that the parent
 * post must be of type `page`.
 */
class GetPageRevision extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-page-revision';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'edit_pages' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get Page Revision', 'airo-wp' ),
				'description'         => __( 'Retrieves a specific page revision by ID', 'airo-wp' ),
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
	 * @return array<string, mixed> Revision data or error.
	 */
	public function execute( array $input ): array {
		if ( empty( $input['parent'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Parent page ID is required', 'airo-wp' ),
			);
		}

		if ( empty( $input['id'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Revision ID is required', 'airo-wp' ),
			);
		}

		$parent_id   = (int) $input['parent'];
		$revision_id = (int) $input['id'];

		// Check if parent post exists and is a page.
		$parent_post = get_post( $parent_id );
		if ( ! $parent_post || 'page' !== $parent_post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: page ID */
					__( 'Page with ID %d not found', 'airo-wp' ),
					$parent_id
				),
			);
		}

		// Get the revision.
		$revision = wp_get_post_revision( $revision_id );
		if ( ! $revision ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: revision ID */
					__( 'Revision with ID %d not found', 'airo-wp' ),
					$revision_id
				),
			);
		}

		// Verify the revision belongs to the parent page.
		if ( (int) $revision->post_parent !== $parent_id ) {
			return array(
				'success' => false,
				'message' => __( 'Revision does not belong to the specified parent page', 'airo-wp' ),
			);
		}

		$context = isset( $input['context'] ) ? $input['context'] : 'view';

		return $this->format_revision( $revision, $context );
	}

	/**
	 * Format revision data based on context.
	 *
	 * @param \WP_Post $revision Revision post object.
	 * @param string   $context  Context (view, embed, edit).
	 * @return array<string, mixed> Formatted revision data.
	 */
	private function format_revision( \WP_Post $revision, string $context ): array {
		$data = array(
			'id'            => $revision->ID,
			'author_id'     => (int) $revision->post_author,
			'date_created'  => $revision->post_date,
			'date_modified' => $revision->post_modified,
			'parent_id'     => (int) $revision->post_parent,
			'slug'          => $revision->post_name,
		);

		switch ( $context ) {
			case 'embed':
				$data['title']   = $revision->post_title;
				$data['excerpt'] = wp_trim_words( $revision->post_excerpt ? $revision->post_excerpt : $revision->post_content, 55 );
				break;

			case 'edit':
				$data['title']   = $revision->post_title;
				$data['content'] = $revision->post_content;
				$data['excerpt'] = $revision->post_excerpt;
				break;

			case 'view':
			default:
				$data['title'] = $revision->post_title;
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- applying core WP filter.
				$data['content'] = apply_filters( 'the_content', $revision->post_content );
				$data['excerpt'] = $revision->post_excerpt;
				break;
		}

		return $data;
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
				'parent'  => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the parent page for the revision', 'airo-wp' ),
					'minimum'     => 1,
				),
				'id'      => array(
					'type'        => 'integer',
					'description' => __( 'Unique identifier for the revision', 'airo-wp' ),
					'minimum'     => 1,
				),
				'context' => array(
					'type'        => 'string',
					'description' => __( 'Scope under which the request is made', 'airo-wp' ),
					'enum'        => array( 'view', 'embed', 'edit' ),
					'default'     => 'view',
				),
			),
			'required'   => array( 'parent', 'id' ),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	public function get_output_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'id'            => array(
					'type'        => 'integer',
					'description' => __( 'The revision ID', 'airo-wp' ),
				),
				'author_id'     => array(
					'type'        => 'integer',
					'description' => __( 'The revision author ID', 'airo-wp' ),
				),
				'date_created'  => array(
					'type'        => 'string',
					'description' => __( 'The revision creation date', 'airo-wp' ),
				),
				'date_modified' => array(
					'type'        => 'string',
					'description' => __( 'The revision modification date', 'airo-wp' ),
				),
				'parent_id'     => array(
					'type'        => 'integer',
					'description' => __( 'The parent page ID', 'airo-wp' ),
				),
				'slug'          => array(
					'type'        => 'string',
					'description' => __( 'The revision slug', 'airo-wp' ),
				),
				'title'         => array(
					'type'        => 'string',
					'description' => __( 'The revision title', 'airo-wp' ),
				),
				'content'       => array(
					'type'        => 'string',
					'description' => __( 'The revision content', 'airo-wp' ),
				),
				'excerpt'       => array(
					'type'        => 'string',
					'description' => __( 'The revision excerpt', 'airo-wp' ),
				),
			),
		);
	}
}
