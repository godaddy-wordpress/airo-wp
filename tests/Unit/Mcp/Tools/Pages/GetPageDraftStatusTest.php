<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Pages;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\GetPageDraftStatus;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetPageDraftStatusTest extends TestCase {

	private GetPageDraftStatus $tool;

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
		$this->tool    = new GetPageDraftStatus( $this->service );
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/get-page-draft-status', GetPageDraftStatus::TOOL_ID );
	}

	public function test_nonexistent_post_returns_error(): void {
		Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

		$result = $this->tool->execute( array( 'post_id' => 99 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Page with ID 99 not found', $result['message'] );
	}

	public function test_non_page_type_returns_error(): void {
		$post              = new \stdClass();
		$post->ID          = 10;
		$post->post_type   = 'post';
		$post->post_status = 'publish';

		Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );

		$result = $this->tool->execute( array( 'post_id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Post with ID 10 is not a page', $result['message'] );
	}

	public function test_published_page_without_draft_shows_can_create(): void {
		$post              = new \stdClass();
		$post->ID          = 10;
		$post->post_type   = 'page';
		$post->post_status = 'publish';

		Functions\expect( 'get_post' )->andReturn( $post );

		Functions\when( 'get_post_meta' )->alias(
			function ( $id, $key, $single ) {
				if ( DraftPageService::META_DRAFT_OF === $key ) {
					return '';
				}
				if ( DraftPageService::META_HAS_DRAFT === $key ) {
					return '';
				}
				return '';
			}
		);

		$result = $this->tool->execute( array( 'post_id' => 10 ) );

		$this->assertTrue( $result['success'] );
		$this->assertFalse( $result['is_draft'] );
		$this->assertFalse( $result['has_draft'] );
		$this->assertNull( $result['draft_id'] );
		$this->assertSame( 10, $result['original_id'] );
		$this->assertTrue( $result['can_create'] );
	}
}
