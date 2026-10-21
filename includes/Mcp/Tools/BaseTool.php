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
	 * Whether the current user may read a post through an MCP tool.
	 *
	 * Shared by get-post and list-posts so an ID hidden from one cannot be read
	 * through the other.
	 *
	 * @param \WP_Post $post Post object.
	 * @return bool
	 */
	protected function can_read_post( $post ): bool {
		$post_id = (int) $post->ID;

		if ( ! current_user_can( 'read_post', $post_id ) ) {
			return false;
		}

		// read_post on a revision resolves to the parent's read cap (plain `read` when published), so also require edit_post, as core does.
		if ( 'revision' === $post->post_type ) {
			return current_user_can( 'edit_post', $post_id );
		}

		$type_object = get_post_type_object( $post->post_type );
		if ( ! $type_object ) {
			return false;
		}

		// Non-viewable types (wp_template, wp_navigation, ...) pass read_post for any user, so require the type's own edit cap.
		if ( ! is_post_type_viewable( $type_object ) ) {
			return current_user_can( $type_object->cap->edit_posts );
		}

		return true;
	}

	/**
	 * Whether a post's content must be withheld from the current user.
	 *
	 * Core REST blanks password-protected content for callers who cannot edit the post.
	 *
	 * @param \WP_Post $post     Post object.
	 * @param bool     $can_edit Whether the current user can edit the post.
	 * @return bool
	 */
	protected function is_content_withheld( $post, bool $can_edit ): bool {
		return '' !== (string) $post->post_password && ! $can_edit;
	}

	/**
	 * Load the parents of the given posts into the object cache in one query.
	 *
	 * Checking read_post resolves the parent for two types only: an attached attachment
	 * (its status comes from the parent, in get_post_status()) and a revision (map_meta_cap
	 * checks the parent instead). For those, a per-item capability loop would otherwise
	 * cost one query per parent; other types never read it, so do not prime for them.
	 *
	 * @param array<int, \WP_Post> $posts Posts whose parents will be read.
	 */
	protected function prime_parent_caches( array $posts ): void {
		$parent_ids = array();
		foreach ( $posts as $post ) {
			$parent_id = (int) $post->post_parent;
			if ( $parent_id > 0 ) {
				$parent_ids[ $parent_id ] = $parent_id;
			}
		}

		if ( $parent_ids ) {
			_prime_post_caches( array_values( $parent_ids ), false, false );
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
