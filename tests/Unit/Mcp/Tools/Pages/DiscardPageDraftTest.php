<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Pages;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\DiscardPageDraft;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class DiscardPageDraftTest extends TestCase {

	private DiscardPageDraft $tool;

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
		$this->tool    = new DiscardPageDraft( $this->service );
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/discard-page-draft', DiscardPageDraft::TOOL_ID );
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

	public function test_force_delete_false_passes_to_service(): void {
		$draft_id    = 50;
		$original_id = 5;

		$post              = new \stdClass();
		$post->ID          = $draft_id;
		$post->post_type   = 'page';
		$post->post_status = 'draft';

		// get_post is called twice: once by the tool and once by the service.
		Functions\expect( 'get_post' )
			->twice()
			->with( $draft_id )
			->andReturn( $post );

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key, $single ) use ( $draft_id, $original_id ) {
				if ( $post_id === $draft_id && DraftPageService::META_DRAFT_OF === $key ) {
					return (string) $original_id;
				}
				return '';
			}
		);

		Functions\when( 'delete_post_meta' )->justReturn( true );
		Functions\when( 'do_action' )->justReturn( null );

		Functions\expect( 'wp_trash_post' )
			->once()
			->with( $draft_id )
			->andReturn( (object) array( 'ID' => $draft_id ) );

		Functions\expect( 'wp_delete_post' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );

		$result = $this->tool->execute( array( 'draft_id' => $draft_id, 'force_delete' => false ) );

		$this->assertTrue( $result['success'] );
	}

	public function test_force_delete_true_passes_to_service(): void {
		$draft_id    = 50;
		$original_id = 5;

		$post              = new \stdClass();
		$post->ID          = $draft_id;
		$post->post_type   = 'page';
		$post->post_status = 'draft';

		// get_post is called twice: once by the tool and once by the service.
		Functions\expect( 'get_post' )
			->twice()
			->with( $draft_id )
			->andReturn( $post );

		Functions\when( 'get_post_meta' )->alias(
			function ( $post_id, $key, $single ) use ( $draft_id, $original_id ) {
				if ( $post_id === $draft_id && DraftPageService::META_DRAFT_OF === $key ) {
					return (string) $original_id;
				}
				return '';
			}
		);

		Functions\when( 'delete_post_meta' )->justReturn( true );
		Functions\when( 'do_action' )->justReturn( null );

		Functions\expect( 'wp_delete_post' )
			->once()
			->with( $draft_id, true );

		Functions\expect( 'wp_trash_post' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );

		$result = $this->tool->execute( array( 'draft_id' => $draft_id, 'force_delete' => true ) );

		$this->assertTrue( $result['success'] );
	}
}
