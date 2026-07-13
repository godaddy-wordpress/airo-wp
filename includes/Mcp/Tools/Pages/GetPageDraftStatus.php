<?php
/**
 * GetPageDraftStatus MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;

/**
 * Registers and executes the get-page-draft-status MCP ability.
 */
class GetPageDraftStatus extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-page-draft-status';

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
				'label'               => __( 'Get Page Draft Status', 'airo-wp' ),
				'description'         => __( 'Returns draft relationship information for a page: whether it is a draft, has a draft, or can have one created.', 'airo-wp' ),
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
		$post_id = (int) ( $input['post_id'] ?? 0 );

		$post = get_post( $post_id );

		if ( ! $post ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: page ID */
					__( 'Page with ID %d not found.', 'airo-wp' ),
					$post_id
				),
			);
		}

		if ( 'page' !== $post->post_type ) {
			return array(
				'success' => false,
				'message' => sprintf(
					/* translators: %d: post ID */
					__( 'Post with ID %d is not a page.', 'airo-wp' ),
					$post_id
				),
			);
		}

		// Check if this page IS a draft.
		$draft_of_meta = get_post_meta( $post_id, DraftPageService::META_DRAFT_OF, true );
		$original_id   = absint( $draft_of_meta );

		if ( $original_id > 0 && 'draft' === $post->post_status ) {
			return array(
				'success'     => true,
				'is_draft'    => true,
				'has_draft'   => false,
				'draft_id'    => $post_id,
				'original_id' => $original_id,
				'can_create'  => false,
			);
		}

		// Check if this page HAS a draft.
		$draft = $this->draft_service->get_draft( $post_id );

		if ( null !== $draft ) {
			return array(
				'success'     => true,
				'is_draft'    => false,
				'has_draft'   => true,
				'draft_id'    => $draft->ID,
				'original_id' => $post_id,
				'can_create'  => false,
			);
		}

		// Otherwise: check if a draft can be created.
		$can_create = 'page' === $post->post_type && 'publish' === $post->post_status;

		return array(
			'success'     => true,
			'is_draft'    => false,
			'has_draft'   => false,
			'draft_id'    => null,
			'original_id' => $post_id,
			'can_create'  => $can_create,
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
				'post_id' => array(
					'type'        => 'integer',
					'minimum'     => 1,
					'description' => __( 'The page ID to check draft status for.', 'airo-wp' ),
				),
			),
			'required'   => array( 'post_id' ),
		);
	}

	/**
	 * Get output schema.
	 *
	 * @return array<string, mixed>
	 */
	private function get_output_schema(): array {
		return $this->build_output_schema(
			__( 'Page draft status result', 'airo-wp' ),
			array(
				'is_draft'    => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the page is itself a draft copy.', 'airo-wp' ),
				),
				'has_draft'   => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the page has a draft copy.', 'airo-wp' ),
				),
				'draft_id'    => array(
					'type'        => array( 'integer', 'null' ),
					'description' => __( 'The draft page ID, if applicable.', 'airo-wp' ),
				),
				'original_id' => array(
					'type'        => array( 'integer', 'null' ),
					'description' => __( 'The original page ID, if applicable.', 'airo-wp' ),
				),
				'can_create'  => array(
					'type'        => 'boolean',
					'description' => __( 'Whether a draft can be created for this page.', 'airo-wp' ),
				),
			)
		);
	}
}
