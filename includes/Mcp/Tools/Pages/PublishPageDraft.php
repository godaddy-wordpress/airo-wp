<?php
/**
 * PublishPageDraft MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;

/**
 * Registers and executes the publish-page-draft MCP ability.
 */
class PublishPageDraft extends BaseTool {

	public const TOOL_ID = 'airo-wp/publish-page-draft';

	/**
	 * Draft page service instance.
	 *
	 * @var DraftPageService
	 */
	private DraftPageService $draft_service;

	/**
	 * Constructor.
	 *
	 * @param DraftPageService $draft_service Draft page service.
	 */
	public function __construct( DraftPageService $draft_service ) {
		$this->draft_service = $draft_service;
	}

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
				'label'               => __( 'Publish Page Draft', 'airo-wp' ),
				'description'         => __( 'Publishes a draft page by merging its content into the original published page and deleting the draft.', 'airo-wp' ),
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
	 * @return array<string, mixed> Execution result.
	 */
	public function execute( array $input ): array {
		$draft_id = absint( $input['draft_id'] ?? 0 );

		if ( $draft_id <= 0 ) {
			return array(
				'success' => false,
				'message' => __( 'A valid draft page ID is required.', 'airo-wp' ),
			);
		}

		$draft = get_post( $draft_id );

		if ( ! $draft ) {
			return array(
				'success' => false,
				'message' => __( 'The requested draft page does not exist.', 'airo-wp' ),
			);
		}

		if ( 'page' !== $draft->post_type ) {
			return array(
				'success' => false,
				'message' => __( 'Only page drafts can be published.', 'airo-wp' ),
			);
		}

		if ( 'draft' !== $draft->post_status ) {
			return array(
				'success' => false,
				'message' => __( 'Only posts in draft status can be published as a Site Designer draft.', 'airo-wp' ),
			);
		}

		$original_meta = get_post_meta( $draft_id, DraftPageService::META_DRAFT_OF, true );

		if ( empty( $original_meta ) ) {
			return array(
				'success' => false,
				'message' => __( 'The page is not a Site Designer draft copy.', 'airo-wp' ),
			);
		}

		$original_id = absint( $original_meta );

		if ( $original_id <= 0 ) {
			return array(
				'success' => false,
				'message' => __( 'The draft is missing a valid original page reference.', 'airo-wp' ),
			);
		}

		$force_delete = array_key_exists( 'force_delete', $input ) ? (bool) $input['force_delete'] : false;
		$result       = $this->draft_service->publish_draft( $draft_id, $force_delete );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => $result->get_error_message(),
			);
		}

		return array(
			'success'     => true,
			'original_id' => $original_id,
			'message'     => __( 'Draft published successfully.', 'airo-wp' ),
		);
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
				'draft_id'     => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'The ID of the draft page to publish.', 'airo-wp' ),
				),
				'force_delete' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to permanently delete the draft (true) or move it to trash so it can be restored (false). Defaults to false.', 'airo-wp' ),
					'default'     => false,
				),
			),
			'required'   => array( 'draft_id' ),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Page draft publish result', 'airo-wp' ),
			array(
				'original_id' => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the original page that was updated.', 'airo-wp' ),
				),
			)
		);
	}
}
