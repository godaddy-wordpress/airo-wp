<?php
/**
 * GetPostByOptionName MCP tool.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\BaseTool;

/**
 * Registers and executes the get-post-by-option-name MCP ability.
 *
 * Reads a site option that stores a post ID, then delegates to GetPost
 * to retrieve the post.
 */
class GetPostByOptionName extends BaseTool {

	public const TOOL_ID = 'airo-wp/get-post-by-option-name';

	/**
	 * GetPost tool used to retrieve the post after resolving the option.
	 *
	 * @var GetPost
	 */
	private GetPost $get_post;

	/**
	 * Constructor.
	 *
	 * @param GetPost $get_post The GetPost tool instance.
	 */
	public function __construct( GetPost $get_post ) {
		$this->get_post = $get_post;
	}

	/**
	 * Permission callback for wp_register_ability.
	 *
	 * @return bool Whether the current user has the required capability.
	 */
	public function check_permissions(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register this tool as a WordPress ability.
	 */
	public function register(): void {
		wp_register_ability(
			self::TOOL_ID,
			array(
				'label'               => __( 'Get post by option name', 'airo-wp' ),
				'description'         => __( 'Reads a site option that stores a post ID, then returns that post.', 'airo-wp' ),
				'input_schema'        => $this->get_input_schema(),
				'output_schema'       => $this->get_post->get_output_schema(),
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
		$option_name = isset( $input['option_name'] ) ? sanitize_key( (string) $input['option_name'] ) : '';
		if ( '' === $option_name ) {
			return $this->get_post->execute( array( 'post_id' => 0 ) );
		}

		$raw     = get_option( $option_name, null );
		$post_id = 0;
		if ( null !== $raw && '' !== $raw ) {
			$n       = is_numeric( $raw ) ? (int) $raw : 0;
			$post_id = $n > 0 ? $n : 0;
		}

		return $this->get_post->execute(
			array(
				'post_id'      => $post_id,
				'include_meta' => ! empty( $input['include_meta'] ),
			)
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
				'option_name'  => array(
					'type'        => 'string',
					'description' => __( 'Option name whose value is a post ID', 'airo-wp' ),
				),
				'include_meta' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether to include post meta.', 'airo-wp' ),
					'default'     => false,
				),
			),
			'required'   => array( 'option_name' ),
		);
	}
}
