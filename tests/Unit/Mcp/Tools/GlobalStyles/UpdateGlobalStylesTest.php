<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\GlobalStyles;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\UpdateGlobalStyles;
use GoDaddy\WordPress\Plugins\AiroWp\Services\FontDownloader;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UpdateGlobalStylesTest extends TestCase {

	private UpdateGlobalStyles $tool;

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
		Functions\when( 'wp_slash' )->returnArg();
		Functions\when( 'wp_json_encode' )->alias( function ( $data ) {
			return json_encode( $data );
		} );
		Functions\when( 'current_time' )->justReturn( '2024-01-03 00:00:00' );
		Functions\when( 'is_wp_error' )->justReturn( false );

		$font_downloader = \Mockery::mock( FontDownloader::class );
		$font_downloader->shouldReceive( 'process_font_families' )->andReturnUsing( function ( $arg ) {
			return $arg;
		} );

		$this->tool = new UpdateGlobalStyles( $font_downloader );
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/update-global-styles', UpdateGlobalStyles::TOOL_ID );
	}

	public function test_missing_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global styles ID is required', $result['message'] );
	}

	public function test_zero_id_returns_error(): void {
		$result = $this->tool->execute( array( 'id' => 0 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global styles ID is required', $result['message'] );
	}

	public function test_post_not_found_returns_error(): void {
		Functions\when( 'get_post' )->justReturn( null );

		$result = $this->tool->execute( array( 'id' => 999 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global styles post not found', $result['message'] );
	}

	public function test_wrong_post_type_returns_error(): void {
		$post            = new \stdClass();
		$post->ID        = 42;
		$post->post_type = 'page';

		Functions\when( 'get_post' )->justReturn( $post );

		$result = $this->tool->execute( array( 'id' => 42 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Global styles post not found or invalid ID', $result['message'] );
	}

	public function test_merge_mode_merges_styles(): void {
		$existing_post                = new \stdClass();
		$existing_post->ID            = 1;
		$existing_post->post_type     = 'wp_global_styles';
		$existing_post->post_title    = 'Styles';
		$existing_post->post_status   = 'publish';
		$existing_post->post_modified = '2024-01-02 00:00:00';
		$existing_post->post_content  = json_encode( array(
			'styles' => array(
				'color' => array( 'background' => '#fff' ),
			),
		) );

		Functions\when( 'get_post' )->justReturn( $existing_post );

		Functions\expect( 'wp_update_post' )
			->once()
			->with( \Mockery::on( function ( $args ) {
				$content = json_decode( $args['post_content'], true );
				// Should have merged: existing background + new text.
				return isset( $content['styles']['color']['background'] )
					&& isset( $content['styles']['color']['text'] );
			} ), true )
			->andReturn( 1 );

		$result = $this->tool->execute( array(
			'id'     => 1,
			'styles' => array(
				'color' => array( 'text' => '#000' ),
			),
		) );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'data', $result );
	}

	public function test_overwrite_mode_replaces_styles(): void {
		$existing_post                = new \stdClass();
		$existing_post->ID            = 1;
		$existing_post->post_type     = 'wp_global_styles';
		$existing_post->post_title    = 'Styles';
		$existing_post->post_status   = 'publish';
		$existing_post->post_modified = '2024-01-02 00:00:00';
		$existing_post->post_content  = json_encode( array(
			'styles' => array(
				'color' => array( 'background' => '#fff', 'text' => '#000' ),
			),
		) );

		$new_styles = array(
			'color' => array( 'background' => '#333' ),
		);

		Functions\when( 'get_post' )->justReturn( $existing_post );

		Functions\expect( 'wp_update_post' )
			->once()
			->with( \Mockery::on( function ( $args ) use ( $new_styles ) {
				$content = json_decode( $args['post_content'], true );
				// Should have replaced: only new styles, no old text key.
				return $content['styles'] === $new_styles;
			} ), true )
			->andReturn( 1 );

		$result = $this->tool->execute( array(
			'id'        => 1,
			'styles'    => $new_styles,
			'overwrite' => true,
		) );

		$this->assertTrue( $result['success'] );
	}

	public function test_updates_title(): void {
		$existing_post                = new \stdClass();
		$existing_post->ID            = 1;
		$existing_post->post_type     = 'wp_global_styles';
		$existing_post->post_title    = 'Old Title';
		$existing_post->post_status   = 'publish';
		$existing_post->post_modified = '2024-01-02 00:00:00';
		$existing_post->post_content  = json_encode( array( 'styles' => array() ) );

		Functions\when( 'get_post' )->justReturn( $existing_post );

		Functions\expect( 'wp_update_post' )
			->once()
			->with( \Mockery::on( function ( $args ) {
				return isset( $args['post_title'] ) && 'New Title' === $args['post_title'];
			} ), true )
			->andReturn( 1 );

		$result = $this->tool->execute( array(
			'id'    => 1,
			'title' => 'New Title',
		) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 'New Title', $result['data']['title'] );
	}

	public function test_successful_update_returns_data(): void {
		$existing_post                = new \stdClass();
		$existing_post->ID            = 5;
		$existing_post->post_type     = 'wp_global_styles';
		$existing_post->post_title    = 'My Styles';
		$existing_post->post_status   = 'publish';
		$existing_post->post_modified = '2024-01-02 00:00:00';
		$existing_post->post_content  = json_encode( array( 'styles' => array() ) );

		Functions\when( 'get_post' )->justReturn( $existing_post );

		Functions\when( 'wp_update_post' )->justReturn( 5 );

		$result = $this->tool->execute( array(
			'id'     => 5,
			'styles' => array( 'spacing' => array( 'padding' => '20px' ) ),
		) );

		$this->assertTrue( $result['success'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertSame( 5, $result['data']['id'] );
		$this->assertSame( 'My Styles', $result['data']['title'] );
		$this->assertSame( 'publish', $result['data']['status'] );
		$this->assertArrayHasKey( 'content', $result['data'] );
		$this->assertArrayHasKey( 'date', $result['data'] );
		$this->assertStringContainsString( 'updated successfully', $result['message'] );
	}
}
