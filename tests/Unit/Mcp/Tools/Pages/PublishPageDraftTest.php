<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Pages;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\PublishPageDraft;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class PublishPageDraftTest extends TestCase {

	private PublishPageDraft $tool;

	private DraftPageService $service;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'absint' )->alias(
			function ( $value ) {
				return abs( (int) $value );
			}
		);

		$this->service = new DraftPageService();
		$this->tool    = new PublishPageDraft( $this->service );
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/publish-page-draft', PublishPageDraft::TOOL_ID );
	}

	public function test_missing_draft_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'A valid draft page ID is required', $result['message'] );
	}

	public function test_nonexistent_draft_returns_error(): void {
		Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

		$result = $this->tool->execute( array( 'draft_id' => 99 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'The requested draft page does not exist', $result['message'] );
	}

	public function test_non_page_type_returns_error(): void {
		$post              = new \stdClass();
		$post->ID          = 10;
		$post->post_type   = 'post';
		$post->post_status = 'draft';

		Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );

		$result = $this->tool->execute( array( 'draft_id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Only page drafts can be published', $result['message'] );
	}

	public function test_non_draft_status_returns_error(): void {
		$post              = new \stdClass();
		$post->ID          = 10;
		$post->post_type   = 'page';
		$post->post_status = 'publish';

		Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );

		$result = $this->tool->execute( array( 'draft_id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Only posts in draft status can be published as a Site Designer draft', $result['message'] );
	}

	public function test_force_delete_false_passes_to_service(): void {
		$draft_id    = 50;
		$original_id = 5;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';
		$draft->post_content          = 'content';
		$draft->post_title            = 'title';
		$draft->post_excerpt          = '';
		$draft->menu_order            = 0;
		$draft->post_password         = '';
		$draft->comment_status        = 'open';
		$draft->ping_status           = 'open';
		$draft->post_content_filtered = '';

		$original              = new \stdClass();
		$original->ID          = $original_id;
		$original->post_status = 'publish';

		// get_post is called 3 times: tool($draft_id), service($draft_id), service($original_id).
		Functions\when( 'get_post' )->alias(
			function ( $id ) use ( $draft_id, $draft, $original_id, $original ) {
				if ( (int) $id === $draft_id ) {
					return $draft;
				}
				if ( (int) $id === $original_id ) {
					return $original;
				}
				return null;
			}
		);

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key = null, $single = false ) use ( $draft_id, $original_id ) {
				if ( $post_id === $draft_id && DraftPageService::META_DRAFT_OF === $key ) {
					return (string) $original_id;
				}
				if ( '_thumbnail_id' === $key ) {
					return '';
				}
				if ( null === $key ) {
					return array();
				}
				return '';
			}
		);

		Functions\expect( 'wp_update_post' )
			->once()
			->andReturn( $original_id );

		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'delete_post_meta' )->justReturn( true );
		Functions\when( 'update_post_meta' )->justReturn( true );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'set_post_thumbnail' )->justReturn( true );
		Functions\when( 'delete_post_thumbnail' )->justReturn( true );
		Functions\when( 'do_action' )->justReturn( null );
		Functions\when( 'apply_filters' )->returnArg( 2 );

		Functions\expect( 'wp_trash_post' )
			->once()
			->with( $draft_id )
			->andReturn( (object) array( 'ID' => $draft_id ) );

		Functions\expect( 'wp_delete_post' )->never();

		$result = $this->tool->execute( array( 'draft_id' => $draft_id, 'force_delete' => false ) );

		$this->assertTrue( $result['success'] );
	}

	public function test_force_delete_true_passes_to_service(): void {
		$draft_id    = 50;
		$original_id = 5;

		$draft              = new \stdClass();
		$draft->ID          = $draft_id;
		$draft->post_type   = 'page';
		$draft->post_status = 'draft';
		$draft->post_content          = 'content';
		$draft->post_title            = 'title';
		$draft->post_excerpt          = '';
		$draft->menu_order            = 0;
		$draft->post_password         = '';
		$draft->comment_status        = 'open';
		$draft->ping_status           = 'open';
		$draft->post_content_filtered = '';

		$original              = new \stdClass();
		$original->ID          = $original_id;
		$original->post_status = 'publish';

		// get_post is called 3 times: tool($draft_id), service($draft_id), service($original_id).
		Functions\when( 'get_post' )->alias(
			function ( $id ) use ( $draft_id, $draft, $original_id, $original ) {
				if ( (int) $id === $draft_id ) {
					return $draft;
				}
				if ( (int) $id === $original_id ) {
					return $original;
				}
				return null;
			}
		);

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key = null, $single = false ) use ( $draft_id, $original_id ) {
				if ( $post_id === $draft_id && DraftPageService::META_DRAFT_OF === $key ) {
					return (string) $original_id;
				}
				if ( '_thumbnail_id' === $key ) {
					return '';
				}
				if ( null === $key ) {
					return array();
				}
				return '';
			}
		);

		Functions\expect( 'wp_update_post' )
			->once()
			->andReturn( $original_id );

		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'delete_post_meta' )->justReturn( true );
		Functions\when( 'update_post_meta' )->justReturn( true );
		Functions\when( 'get_post_thumbnail_id' )->justReturn( 0 );
		Functions\when( 'set_post_thumbnail' )->justReturn( true );
		Functions\when( 'delete_post_thumbnail' )->justReturn( true );
		Functions\when( 'do_action' )->justReturn( null );
		Functions\when( 'apply_filters' )->returnArg( 2 );

		Functions\expect( 'wp_delete_post' )
			->once()
			->with( $draft_id, true );

		Functions\expect( 'wp_trash_post' )->never();

		$result = $this->tool->execute( array( 'draft_id' => $draft_id, 'force_delete' => true ) );

		$this->assertTrue( $result['success'] );
	}
}
