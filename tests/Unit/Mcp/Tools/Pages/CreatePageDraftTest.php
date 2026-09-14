<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Pages;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\CreatePageDraft;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class CreatePageDraftTest extends TestCase {

	private CreatePageDraft $tool;

	private DraftPageService $service;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'sanitize_textarea_field' )->returnArg();
		Functions\when( 'wp_kses_post' )->returnArg();

		$this->service = new DraftPageService();
		$this->tool    = new CreatePageDraft( $this->service );
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/create-page-draft', CreatePageDraft::TOOL_ID );
	}

	public function test_missing_post_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'valid post_id is required', $result['message'] );
	}

	public function test_nonexistent_post_returns_error(): void {
		Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

		$result = $this->tool->execute( array( 'post_id' => 99 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Post not found', $result['message'] );
	}

	public function test_non_page_type_returns_error(): void {
		$post              = new \stdClass();
		$post->ID          = 10;
		$post->post_type   = 'post';
		$post->post_status = 'publish';

		Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );

		$result = $this->tool->execute( array( 'post_id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Drafts can only be created for pages (post_type=page)', $result['message'] );
	}

	public function test_non_published_page_returns_error(): void {
		$post              = new \stdClass();
		$post->ID          = 10;
		$post->post_type   = 'page';
		$post->post_status = 'draft';

		Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );

		$result = $this->tool->execute( array( 'post_id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'A draft can only be created from a published page', $result['message'] );
	}

	public function test_existing_draft_returns_error(): void {
		$post              = new \stdClass();
		$post->ID          = 10;
		$post->post_type   = 'page';
		$post->post_status = 'publish';

		$draft              = new \stdClass();
		$draft->ID          = 20;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';

		Functions\when( 'get_post' )->alias(
			function ( $id ) use ( $post, $draft ) {
				if ( 10 === (int) $id ) {
					return $post;
				}
				if ( 20 === (int) $id ) {
					return $draft;
				}
				return null;
			}
		);

		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'set_post_thumbnail' )->justReturn( true );
		Functions\when( 'get_post_meta' )->alias(
			function ( $id, $key, $single ) {
				if ( 10 === (int) $id && DraftPageService::META_HAS_DRAFT === $key ) {
					return 20;
				}
				if ( 20 === (int) $id && DraftPageService::META_DRAFT_OF === $key ) {
					return 10;
				}
				return '';
			}
		);

		$result = $this->tool->execute( array( 'post_id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'A draft version already exists for this page', $result['message'] );
	}

	public function test_content_is_kses_sanitized(): void {
		// wp_kses_post MUST be called when content is provided.
		// Track calls via alias since setUp already registers a when() stub.
		$kses_called = 0;
		Functions\when( 'wp_kses_post' )->alias(
			function ( $content ) use ( &$kses_called ) {
				$kses_called++;
				return $content;
			}
		);

		$post                          = new \stdClass();
		$post->ID                      = 10;
		$post->post_type               = 'page';
		$post->post_status             = 'publish';
		$post->post_title              = 'Test Page';
		$post->post_content            = 'existing content';
		$post->post_excerpt            = '';
		$post->post_name               = 'test-page';
		$post->menu_order              = 0;
		$post->post_password           = '';
		$post->comment_status          = 'open';
		$post->ping_status             = 'open';
		$post->post_content_filtered   = '';
		$post->post_author             = 1;
		$post->post_parent             = 0;
		$post->post_date               = '';
		$post->post_modified           = '';

		Functions\when( 'get_post' )->justReturn( $post );
		Functions\when( 'get_post_meta' )->justReturn( '' );  // no draft exists
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'set_post_thumbnail' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );
		Functions\when( 'wp_slash' )->returnArg();
		Functions\when( 'wp_insert_post' )->justReturn( new \WP_Error( 'test', 'stop here' ) );
		Functions\when( 'is_wp_error' )->alias( function ( $thing ) { return $thing instanceof \WP_Error; } );

		$this->tool->execute( array(
			'post_id' => 10,
			'content' => '<div class="custom">raw content</div>',
		) );

		$this->assertSame( 1, $kses_called, 'wp_kses_post must be called exactly once for post_content' );
	}
}
