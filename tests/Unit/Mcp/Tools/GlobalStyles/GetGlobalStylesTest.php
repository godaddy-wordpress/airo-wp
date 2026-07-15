<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\GlobalStyles;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\GetGlobalStyles;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetGlobalStylesTest extends TestCase {

	private GetGlobalStyles $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'absint' )->alias( function ( $value ) {
			return abs( (int) $value );
		} );

		$this->tool = new GetGlobalStyles();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/get-global-styles', GetGlobalStyles::TOOL_ID );
	}

	public function test_missing_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Style ID is required', $result['message'] );
	}

	public function test_zero_id_returns_error(): void {
		$result = $this->tool->execute( array( 'id' => 0 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Style ID is required', $result['message'] );
	}

	public function test_post_not_found_returns_error(): void {
		Functions\when( 'get_post' )->justReturn( null );

		$result = $this->tool->execute( array( 'id' => 999 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global style not found', $result['message'] );
	}

	public function test_wrong_post_type_returns_error(): void {
		$post            = new \stdClass();
		$post->ID        = 42;
		$post->post_type = 'post';

		Functions\when( 'get_post' )->justReturn( $post );

		$result = $this->tool->execute( array( 'id' => 42 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global style not found or invalid ID', $result['message'] );
	}

	public function test_view_context_returns_expected_fields(): void {
		$post                 = new \stdClass();
		$post->ID             = 1;
		$post->post_type      = 'wp_global_styles';
		$post->post_title     = 'Test Styles';
		$post->post_status    = 'publish';
		$post->post_date      = '2024-01-01 00:00:00';
		$post->post_modified  = '2024-01-02 00:00:00';
		$post->post_content   = json_encode( array(
			'settings' => array( 'color' => array( 'palette' => array() ) ),
			'styles'   => array( 'color' => array( 'background' => '#fff' ) ),
		) );

		Functions\when( 'get_post' )->justReturn( $post );

		$result = $this->tool->execute( array( 'id' => 1 ) );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'data', $result );

		$data = $result['data'];
		$this->assertSame( 1, $data['id'] );
		$this->assertArrayHasKey( 'title', $data );
		$this->assertSame( 'Test Styles', $data['title']['rendered'] );
		$this->assertArrayHasKey( 'status', $data );
		$this->assertArrayHasKey( 'date', $data );
		$this->assertArrayHasKey( 'modified', $data );
		$this->assertArrayHasKey( 'settings', $data );
		$this->assertArrayHasKey( 'styles', $data );
	}

	public function test_edit_context_returns_all_fields(): void {
		$post                  = new \stdClass();
		$post->ID              = 1;
		$post->post_type       = 'wp_global_styles';
		$post->post_title      = 'Test Styles';
		$post->post_status     = 'publish';
		$post->post_date       = '2024-01-01 00:00:00';
		$post->post_date_gmt   = '2024-01-01 00:00:00';
		$post->post_modified   = '2024-01-02 00:00:00';
		$post->post_modified_gmt = '2024-01-02 00:00:00';
		$post->post_name       = 'test-styles';
		$post->post_content    = json_encode( array(
			'settings' => array( 'color' => array() ),
			'styles'   => array( 'color' => array() ),
		) );

		Functions\when( 'get_post' )->justReturn( $post );

		$result = $this->tool->execute( array(
			'id'      => 1,
			'context' => 'edit',
		) );

		$this->assertTrue( $result['success'] );

		$data = $result['data'];
		$this->assertArrayHasKey( 'title', $data );
		$this->assertSame( 'Test Styles', $data['title']['raw'] );
		$this->assertArrayHasKey( 'date_gmt', $data );
		$this->assertArrayHasKey( 'modified_gmt', $data );
		$this->assertArrayHasKey( 'slug', $data );
		$this->assertSame( 'test-styles', $data['slug'] );
		$this->assertArrayHasKey( 'settings', $data );
		$this->assertArrayHasKey( 'styles', $data );
	}

	public function test_embed_context_returns_minimal_fields(): void {
		$post                 = new \stdClass();
		$post->ID             = 1;
		$post->post_type      = 'wp_global_styles';
		$post->post_title     = 'Test Styles';
		$post->post_status    = 'publish';
		$post->post_date      = '2024-01-01 00:00:00';
		$post->post_modified  = '2024-01-02 00:00:00';
		$post->post_content   = json_encode( array(
			'settings' => array( 'color' => array() ),
			'styles'   => array( 'color' => array() ),
		) );

		Functions\when( 'get_post' )->justReturn( $post );

		$result = $this->tool->execute( array(
			'id'      => 1,
			'context' => 'embed',
		) );

		$this->assertTrue( $result['success'] );

		$data = $result['data'];
		$this->assertSame( 1, $data['id'] );
		$this->assertArrayHasKey( 'title', $data );
		// Embed context should NOT include status, date, settings, styles.
		$this->assertArrayNotHasKey( 'status', $data );
		$this->assertArrayNotHasKey( 'date', $data );
		$this->assertArrayNotHasKey( 'settings', $data );
		$this->assertArrayNotHasKey( 'styles', $data );
	}
}
