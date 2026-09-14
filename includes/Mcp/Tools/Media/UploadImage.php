<?php
/**
 * UploadImage MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the upload-image MCP ability.
 *
 * Downloads an image from a URL and uploads it to the WordPress media library,
 * optionally attaching it to a post.
 */
class UploadImage extends BaseTool {

	/**
	 * MCP ability identifier.
	 *
	 * @var string
	 */
	public const TOOL_ID = 'airo-wp/upload-image';

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'upload_files' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Upload Image', 'airo-wp' ),
				'description'         => __( 'Uploads an image to the WordPress media library from a URL or base64-encoded file data, optionally attaching it to a post', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_output_schema(),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'category'            => 'media-management',
			)
		);
	}

	/**
	 * Execute the tool.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed> Upload result or error.
	 */
	public function execute( array $input ): array {
		$has_url       = ! empty( $input['url'] );
		$has_file_data = ! empty( $input['file_data'] );

		if ( $has_url && $has_file_data ) {
			return array(
				'success' => false,
				'message' => __( 'Provide either url or file_data, not both', 'airo-wp' ),
			);
		}

		if ( ! $has_url && ! $has_file_data ) {
			return array(
				'success' => false,
				'message' => __( 'Either url or file_data is required', 'airo-wp' ),
			);
		}

		$result = $has_url
			? $this->handle_url_upload( $input )
			: $this->handle_base64_upload( $input );

		if ( empty( $result['success'] ) ) {
			return $result;
		}

		$this->apply_metadata( (int) $result['attachment_id'], $input );

		return $result;
	}

	/**
	 * Handle upload from a remote URL via media_sideload_image().
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed>
	 */
	private function handle_url_upload( array $input ): array {
		$url = esc_url_raw( $input['url'] );

		if ( ! $url || ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid URL provided', 'airo-wp' ),
			);
		}

		$post_id            = isset( $input['post_id'] ) ? absint( $input['post_id'] ) : null;
		$precondition_error = $this->check_upload_preconditions( $post_id );

		if ( null !== $precondition_error ) {
			return $precondition_error;
		}

		$title = isset( $input['title'] ) ? sanitize_text_field( $input['title'] ) : null;

		$this->ensure_media_admin_files();

		$attachment_id = media_sideload_image( $url, $post_id, $title, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			return array(
				'success' => false,
				/* translators: %s: Error message */
				'message' => sprintf( __( 'Failed to upload image: %s', 'airo-wp' ), $attachment_id->get_error_message() ),
			);
		}

		return array(
			'success'       => true,
			'attachment_id' => $attachment_id,
			'url'           => wp_get_attachment_url( $attachment_id ),
			'message'       => __( 'Image uploaded successfully', 'airo-wp' ),
		);
	}

	/**
	 * Handle upload from base64-encoded file data.
	 *
	 * Writes the decoded bytes to a temp file, hands it to media_handle_sideload(),
	 * and removes the temp file in a finally block.
	 *
	 * Validation order is deliberate: filename -> post precondition -> decode ->
	 * sanitize -> MIME -> I/O, so the potentially expensive decode runs only after
	 * the cheap checks have passed.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed>
	 */
	private function handle_base64_upload( array $input ): array {
		if ( empty( $input['filename'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'filename is required when file_data is provided', 'airo-wp' ),
			);
		}

		$post_id            = isset( $input['post_id'] ) ? absint( $input['post_id'] ) : null;
		$precondition_error = $this->check_upload_preconditions( $post_id );

		if ( null !== $precondition_error ) {
			return $precondition_error;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$file_bytes = base64_decode( $input['file_data'], true );

		if ( false === $file_bytes ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid base64 data', 'airo-wp' ),
			);
		}

		$filename = sanitize_file_name( $input['filename'] );

		if ( empty( $filename ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid filename provided', 'airo-wp' ),
			);
		}

		$mime_type = isset( $input['mime_type'] ) ? sanitize_text_field( $input['mime_type'] ) : 'image/jpeg';
		$title     = isset( $input['title'] ) ? sanitize_text_field( $input['title'] ) : null;

		if ( ! in_array( $mime_type, get_allowed_mime_types(), true ) ) {
			return array(
				'success' => false,
				'message' => __( 'Unsupported MIME type. Provide a type allowed by this WordPress installation.', 'airo-wp' ),
			);
		}

		$this->ensure_media_admin_files();

		add_filter(
			'filesystem_method',
			static function () {
				return 'direct';
			}
		);

		if ( ! WP_Filesystem() ) {
			return array(
				'success' => false,
				'message' => __( 'Could not initialize filesystem', 'airo-wp' ),
			);
		}

		global $wp_filesystem;

		$tmp_path = '';

		try {
			$tmp_path = wp_tempnam( 'airo-wp-upload' );

			if ( ! $tmp_path ) {
				return array(
					'success' => false,
					'message' => __( 'Failed to create temporary file', 'airo-wp' ),
				);
			}

			if ( ! $wp_filesystem->put_contents( $tmp_path, $file_bytes, FS_CHMOD_FILE ) ) {
				return array(
					'success' => false,
					'message' => __( 'Failed to write temporary file', 'airo-wp' ),
				);
			}

			$file_array = array(
				'name'     => $filename,
				'type'     => $mime_type,
				'tmp_name' => $tmp_path,
				'error'    => 0,
				'size'     => strlen( $file_bytes ),
			);

			$attachment_id = media_handle_sideload( $file_array, $post_id, $title );

			if ( is_wp_error( $attachment_id ) ) {
				return array(
					'success' => false,
					/* translators: %s: Error message */
					'message' => sprintf( __( 'Failed to upload image: %s', 'airo-wp' ), $attachment_id->get_error_message() ),
				);
			}

			return array(
				'success'       => true,
				'attachment_id' => $attachment_id,
				'url'           => wp_get_attachment_url( $attachment_id ),
				'message'       => __( 'Image uploaded successfully', 'airo-wp' ),
			);
		} finally {
			if ( $tmp_path && $wp_filesystem->exists( $tmp_path ) ) {
				$wp_filesystem->delete( $tmp_path );
			}
		}
	}

	/**
	 * Check preconditions shared by both upload paths.
	 *
	 * The upload_files capability is enforced by check_permissions(), so it is not
	 * re-checked here. Only the optional attachment target is validated.
	 *
	 * @param int|null $post_id Resolved post ID, or null when not provided.
	 * @return array<string, mixed>|null Error array on failure, null when all checks pass.
	 */
	private function check_upload_preconditions( ?int $post_id ): ?array {
		if ( $post_id && ! get_post( $post_id ) ) {
			return array(
				'success' => false,
				/* translators: %d: Post ID */
				'message' => sprintf( __( 'Post with ID %d does not exist', 'airo-wp' ), $post_id ),
			);
		}

		return null;
	}

	/**
	 * Ensure the WordPress admin media files are loaded.
	 *
	 * Both upload paths need the same three files; centralising the guard keeps them
	 * from diverging if the list ever changes.
	 *
	 * @return void
	 */
	private function ensure_media_admin_files(): void {
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			$this->load_admin_file( 'media.php' );
			$this->load_admin_file( 'file.php' );
			$this->load_admin_file( 'image.php' );
		}
	}

	/**
	 * Apply optional metadata to an attachment after upload.
	 *
	 * Post fields are written in a single wp_update_post() call; alt text is post meta
	 * and is written separately.
	 *
	 * @param int                  $attachment_id Attachment post ID.
	 * @param array<string, mixed> $input         Original input parameters.
	 * @return void
	 */
	private function apply_metadata( int $attachment_id, array $input ): void {
		$post_update = array( 'ID' => $attachment_id );

		if ( isset( $input['caption'] ) ) {
			$post_update['post_excerpt'] = wp_kses_post( $input['caption'] );
		}

		if ( isset( $input['description'] ) ) {
			$post_update['post_content'] = wp_kses_post( $input['description'] );
		}

		if ( count( $post_update ) > 1 ) {
			wp_update_post( $post_update, true );
		}

		if ( isset( $input['alt_text'] ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $input['alt_text'] ) );
		}
	}

	/**
	 * Get the input schema for the tool.
	 *
	 * @return array<string, mixed>
	 */
	private function get_input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'url'         => array(
					'type'        => 'string',
					'description' => __( 'The URL of the image to download and upload. Provide either url or file_data, not both.', 'airo-wp' ),
				),
				'file_data'   => array(
					'type'        => 'string',
					'description' => __( 'Base64-encoded file bytes. Provide either url or file_data, not both.', 'airo-wp' ),
				),
				'filename'    => array(
					'type'        => 'string',
					'description' => __( 'Filename including extension (e.g. logo.png). Required when file_data is provided.', 'airo-wp' ),
				),
				'mime_type'   => array(
					'type'        => 'string',
					'description' => __( 'MIME type of the file (e.g. image/jpeg, image/png). Defaults to image/jpeg. Only used with file_data.', 'airo-wp' ),
				),
				'post_id'     => array(
					'type'        => 'integer',
					'description' => __( 'Optional. The post ID to attach the image to', 'airo-wp' ),
				),
				'title'       => array(
					'type'        => 'string',
					'description' => __( 'Optional. The title for the image', 'airo-wp' ),
				),
				'description' => array(
					'type'        => 'string',
					'description' => __( 'Optional. The description for the image', 'airo-wp' ),
				),
				'alt_text'    => array(
					'type'        => 'string',
					'description' => __( 'Optional. The alt text for the image', 'airo-wp' ),
				),
				'caption'     => array(
					'type'        => 'string',
					'description' => __( 'Optional. The caption for the image', 'airo-wp' ),
				),
			),
			'oneOf'      => array(
				array( 'required' => array( 'url' ) ),
				array( 'required' => array( 'file_data', 'filename' ) ),
			),
		);
	}

	/**
	 * Output JSON Schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Image upload result', 'airo-wp' ),
			array(
				'attachment_id' => array(
					'type'        => 'integer',
					'description' => __( 'The attachment ID of the uploaded image', 'airo-wp' ),
				),
				'url'           => array(
					'type'        => 'string',
					'description' => __( 'The URL of the uploaded image', 'airo-wp' ),
				),
			)
		);
	}
}
