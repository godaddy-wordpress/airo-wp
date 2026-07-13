<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\GlobalStyles;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\ListGlobalStylesRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class ListGlobalStylesRevisionsTest extends TestCase {

	private ListGlobalStylesRevisions $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new ListGlobalStylesRevisions();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/list-global-styles-revisions', ListGlobalStylesRevisions::TOOL_ID );
	}

	public function test_missing_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global styles post ID is required', $result['message'] );
	}

	public function test_zero_id_returns_error(): void {
		$result = $this->tool->execute( array( 'id' => 0 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global styles post ID is required', $result['message'] );
	}

	public function test_post_not_found_returns_error(): void {
		Functions\when( 'get_post' )->justReturn( null );

		$result = $this->tool->execute( array( 'id' => 999 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found', $result['message'] );
	}

	public function test_wrong_post_type_returns_error(): void {
		$post            = new \stdClass();
		$post->ID        = 42;
		$post->post_type = 'post';

		Functions\when( 'get_post' )->justReturn( $post );

		$result = $this->tool->execute( array( 'id' => 42 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'is not a wp_global_styles post', $result['message'] );
	}
}
