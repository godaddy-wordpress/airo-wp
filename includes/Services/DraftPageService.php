<?php
/**
 * DraftPageService — shared page draft business logic.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Encapsulates page draft operations shared by MCP tools and REST endpoints.
 */
class DraftPageService {

	/**
	 * Post meta key linking a draft to its published original.
	 */
	public const META_DRAFT_OF = '_sd_draft_of';

	/**
	 * Post meta key on the original pointing to its draft.
	 */
	public const META_HAS_DRAFT = '_sd_has_draft';

	/**
	 * Post meta key storing draft creation timestamp.
	 */
	public const META_DRAFT_CREATED = '_sd_draft_created';

	/**
	 * Get the draft post for a published page.
	 *
	 * @param int $post_id The published page ID.
	 * @return \WP_Post|null The draft post, or null if none exists.
	 */
	public function get_draft( int $post_id ) {
		$draft_id = get_post_meta( $post_id, self::META_HAS_DRAFT, true );

		if ( empty( $draft_id ) ) {
			return null;
		}

		$draft = get_post( (int) $draft_id );

		if ( ! $draft ) {
			return null;
		}

		if ( 'draft' !== $draft->post_status ) {
			return null;
		}

		$draft_of = get_post_meta( $draft->ID, self::META_DRAFT_OF, true );

		if ( (int) $draft_of !== $post_id ) {
			return null;
		}

		return $draft;
	}

	/**
	 * Check whether a published page has a draft.
	 *
	 * @param int $post_id The published page ID.
	 * @return bool True if a valid draft exists.
	 */
	public function has_draft( int $post_id ): bool {
		return null !== $this->get_draft( $post_id );
	}

	/**
	 * Publish a draft by copying its content to the original and cleaning up.
	 *
	 * @param int  $draft_id     The draft post ID.
	 * @param bool $force_delete Whether to permanently delete (true) or move to trash (false).
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function publish_draft( int $draft_id, bool $force_delete = false ) {
		$draft = get_post( $draft_id );

		if ( ! $draft ) {
			return new \WP_Error(
				'draft_not_found',
				__( 'Draft post not found.', 'airo-wp' ),
				array( 'status' => 404 )
			);
		}

		if ( 'page' !== $draft->post_type ) {
			return new \WP_Error(
				'not_a_page',
				__( 'Post is not a page.', 'airo-wp' ),
				array( 'status' => 400 )
			);
		}

		if ( 'draft' !== $draft->post_status ) {
			return new \WP_Error(
				'not_a_draft',
				__( 'Post is not a draft.', 'airo-wp' ),
				array( 'status' => 400 )
			);
		}

		$original_id = get_post_meta( $draft_id, self::META_DRAFT_OF, true );

		if ( empty( $original_id ) ) {
			return new \WP_Error(
				'missing_original_link',
				__( 'Draft is not linked to an original page.', 'airo-wp' ),
				array( 'status' => 400 )
			);
		}

		$original = get_post( (int) $original_id );

		if ( ! $original ) {
			return new \WP_Error(
				'original_not_found',
				__( 'Original page not found.', 'airo-wp' ),
				array( 'status' => 404 )
			);
		}

		if ( 'publish' !== $original->post_status ) {
			return new \WP_Error(
				'original_not_published',
				__( 'Original page is not published.', 'airo-wp' ),
				array( 'status' => 400 )
			);
		}

		$update_result = wp_update_post(
			array(
				'ID'                    => $original->ID,
				'post_content'          => $draft->post_content,
				'post_title'            => $draft->post_title,
				'post_excerpt'          => $draft->post_excerpt,
				'menu_order'            => $draft->menu_order,
				'post_password'         => $draft->post_password,
				'comment_status'        => $draft->comment_status,
				'ping_status'           => $draft->ping_status,
				'post_content_filtered' => $draft->post_content_filtered,
			),
			true
		);

		if ( is_wp_error( $update_result ) ) {
			return $update_result;
		}

		// Handle the featured image: copy from the draft, or clear it on the original
		// when the draft has none. Uses the thumbnail API rather than writing
		// _thumbnail_id directly — set_post_thumbnail() clears the meta if the
		// attachment no longer resolves to an image, so publishing a draft whose
		// featured image was deleted in the meantime cannot stamp a dangling
		// attachment ID onto the live page.
		$draft_thumbnail = get_post_thumbnail_id( $draft_id );

		if ( $draft_thumbnail ) {
			set_post_thumbnail( $original->ID, (int) $draft_thumbnail );
		} else {
			delete_post_thumbnail( $original->ID );
		}

		$this->replace_post_meta( $draft_id, $original->ID );

		// Clean up meta links — always remove the "has draft" pointer from the original.
		delete_post_meta( $original->ID, self::META_HAS_DRAFT );

		if ( $force_delete ) {
			delete_post_meta( $draft_id, self::META_DRAFT_OF );
			delete_post_meta( $draft_id, self::META_DRAFT_CREATED );
			wp_delete_post( $draft_id, true );
		} else {
			// Preserve META_DRAFT_OF and META_DRAFT_CREATED so a trashed draft can be restored.
			$trashed = wp_trash_post( $draft_id );

			if ( ! $trashed ) {
				return new \WP_Error(
					'trash_failed',
					__( 'Failed to move draft to trash.', 'airo-wp' ),
					array( 'status' => 500 )
				);
			}
		}

		/**
		 * Fires after a page draft has been published.
		 *
		 * @param int $original_id The original page ID.
		 * @param int $draft_id    The draft post ID that was published.
		 */
		do_action( 'airowp_draft_published', $original->ID, $draft_id );

		return true;
	}

	/**
	 * Discard a draft by deleting it and cleaning up meta.
	 *
	 * @param int  $draft_id     The draft post ID.
	 * @param bool $force_delete Whether to permanently delete (true) or move to trash (false).
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function discard_draft( int $draft_id, bool $force_delete = false ) {
		$draft = get_post( $draft_id );

		if ( ! $draft ) {
			return new \WP_Error(
				'draft_not_found',
				__( 'Draft post not found.', 'airo-wp' ),
				array( 'status' => 404 )
			);
		}

		if ( 'page' !== $draft->post_type ) {
			return new \WP_Error(
				'not_a_page',
				__( 'Post is not a page.', 'airo-wp' ),
				array( 'status' => 400 )
			);
		}

		if ( 'draft' !== $draft->post_status ) {
			return new \WP_Error(
				'not_a_draft',
				__( 'Post is not a draft.', 'airo-wp' ),
				array( 'status' => 400 )
			);
		}

		$original_id = get_post_meta( $draft_id, self::META_DRAFT_OF, true );

		if ( ! empty( $original_id ) ) {
			delete_post_meta( (int) $original_id, self::META_HAS_DRAFT );
		}

		if ( $force_delete ) {
			delete_post_meta( $draft_id, self::META_DRAFT_OF );
			delete_post_meta( $draft_id, self::META_DRAFT_CREATED );
			wp_delete_post( $draft_id, true );
		} else {
			// Preserve META_DRAFT_OF and META_DRAFT_CREATED so a trashed draft can be restored.
			$trashed = wp_trash_post( $draft_id );

			if ( ! $trashed ) {
				return new \WP_Error(
					'trash_failed',
					__( 'Failed to move draft to trash.', 'airo-wp' ),
					array( 'status' => 500 )
				);
			}
		}

		/**
		 * Fires after a page draft has been discarded.
		 *
		 * @param int $draft_id    The draft post ID that was discarded.
		 * @param int $original_id The original page ID (0 if no link existed).
		 */
		do_action( 'airowp_draft_discarded', $draft_id, ! empty( $original_id ) ? (int) $original_id : 0 );

		return true;
	}

	/**
	 * Copy post meta from one post to another, excluding internal keys.
	 *
	 * @param int $source_id Source post ID.
	 * @param int $target_id Target post ID.
	 */
	public function copy_post_meta( int $source_id, int $target_id ): void {
		$meta     = get_post_meta( $source_id );
		$excluded = $this->get_draft_excluded_meta_keys( $source_id );

		if ( ! is_array( $meta ) ) {
			return;
		}

		foreach ( $meta as $key => $values ) {
			if ( in_array( $key, $excluded, true ) ) {
				continue;
			}

			foreach ( $values as $value ) {
				// Skip object values that cannot be reliably serialized.
				if ( is_object( $value ) ) {
					continue;
				}

				add_post_meta( $target_id, $key, maybe_unserialize( $value ) );
			}
		}
	}

	/**
	 * Hook for `before_delete_post`. Cleans up draft meta relationships.
	 *
	 * @param int $post_id The post being deleted.
	 */
	public function cleanup_draft_meta( int $post_id ): void {
		// If this post is a draft, clean parent's META_HAS_DRAFT.
		$original_id = get_post_meta( $post_id, self::META_DRAFT_OF, true );

		if ( ! empty( $original_id ) ) {
			delete_post_meta( (int) $original_id, self::META_HAS_DRAFT );
		}

		// If this post has a draft linked to it, delete that draft.
		$draft_id = get_post_meta( $post_id, self::META_HAS_DRAFT, true );

		if ( ! empty( $draft_id ) ) {
			wp_delete_post( (int) $draft_id, true );
		}
	}

	/**
	 * Replace all non-excluded meta on the target with meta from the source.
	 *
	 * @param int $source_id Source post ID.
	 * @param int $target_id Target post ID.
	 */
	private function replace_post_meta( int $source_id, int $target_id ): void {
		$source_meta = get_post_meta( $source_id );
		$excluded    = $this->get_draft_excluded_meta_keys( $source_id );

		if ( ! is_array( $source_meta ) ) {
			return;
		}

		// Delete target meta that exists on the source (non-excluded).
		foreach ( array_keys( $source_meta ) as $key ) {
			if ( in_array( $key, $excluded, true ) ) {
				continue;
			}

			delete_post_meta( $target_id, $key );
		}

		$this->copy_post_meta( $source_id, $target_id );
	}

	/**
	 * Get meta keys excluded from draft copy operations.
	 *
	 * @param int $source_id Source post ID for filter context.
	 * @return array<string> Excluded meta keys.
	 */
	private function get_draft_excluded_meta_keys( int $source_id ): array {
		$excluded_keys = array(
			'_edit_lock',
			'_edit_last',
			self::META_DRAFT_OF,
			self::META_HAS_DRAFT,
			self::META_DRAFT_CREATED,
			'_wp_old_slug',
			'_wp_old_date',
		);

		/**
		 * Filters the meta keys excluded from draft copy operations.
		 *
		 * @param array<string> $excluded_keys Default excluded meta keys.
		 * @param int           $source_id     Source post ID.
		 */
		return apply_filters( 'airowp_draft_excluded_meta_keys', $excluded_keys, $source_id );
	}
}
