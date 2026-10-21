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

	/**
	 * @return array<string, array{array, bool}>
	 */
	public function provide_non_array_font_families_input(): array {
		return array(
			'string, merge'     => array( array( 'typography' => array( 'fontFamilies' => 'Roboto' ) ), false ),
			'number, merge'     => array( array( 'typography' => array( 'fontFamilies' => 42 ) ), false ),
			'string, overwrite' => array( array( 'typography' => array( 'fontFamilies' => 'Roboto' ) ), true ),
		);
	}

	/**
	 * @dataProvider provide_non_array_font_families_input
	 */
	public function test_non_array_font_families_input_does_not_fatal( array $settings, bool $overwrite ): void {
		$font_downloader = \Mockery::mock( FontDownloader::class );
		$font_downloader->shouldNotReceive( 'process_font_families' );
		$tool = new UpdateGlobalStyles( $font_downloader );

		Functions\when( 'get_post' )->justReturn( $this->global_styles_post( array() ) );
		Functions\expect( 'wp_update_post' )->once()->andReturn( 1 );

		$result = $tool->execute(
			array(
				'id'        => 1,
				'settings'  => $settings,
				'overwrite' => $overwrite,
			)
		);

		$this->assertTrue( $result['success'] );
	}

	public function test_type_error_from_font_downloader_returns_error_instead_of_fataling(): void {
		// What FontDownloader lets through when an FTP/SSH WP_Filesystem has lost its connection.
		$font_downloader = \Mockery::mock( FontDownloader::class );
		$font_downloader->shouldReceive( 'process_font_families' )->once()
			->andThrow( new \TypeError( 'ftp_rename(): Argument #1 ($ftp) must be of type FTP\Connection, null given' ) );
		$tool = new UpdateGlobalStyles( $font_downloader );

		Functions\when( 'get_post' )->justReturn( $this->global_styles_post( array() ) );
		Functions\expect( 'wp_update_post' )->never();

		$log_file = (string) tempnam( sys_get_temp_dir(), 'airo-wp-log-' );
		ini_set( 'error_log', $log_file ); // phpcs:ignore WordPress.PHP.IniSet.Risky

		try {
			$result = $tool->execute(
				array(
					'id'       => 1,
					'settings' => array( 'typography' => array( 'fontFamilies' => array( 'theme' => array() ) ) ),
				)
			);
			$log    = (string) file_get_contents( $log_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		} finally {
			ini_restore( 'error_log' );
			unlink( $log_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
		}

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Error updating global styles', $result['message'] );
		$this->assertStringContainsString( '[Airo WP] update-global-styles: TypeError: ftp_rename()', $log );
		$this->assertMatchesRegularExpression( '/ in \S+:\d+/', $log );
	}

	public function test_logged_throwable_message_cannot_forge_log_lines(): void {
		$font_downloader = \Mockery::mock( FontDownloader::class );
		$font_downloader->shouldReceive( 'process_font_families' )->once()
			->andThrow( new \RuntimeException( "boom\n[Airo WP] forged line" ) );
		$tool = new UpdateGlobalStyles( $font_downloader );

		Functions\when( 'get_post' )->justReturn( $this->global_styles_post( array() ) );
		Functions\expect( 'wp_update_post' )->never();

		$log_file = (string) tempnam( sys_get_temp_dir(), 'airo-wp-log-' );
		ini_set( 'error_log', $log_file ); // phpcs:ignore WordPress.PHP.IniSet.Risky

		try {
			$tool->execute(
				array(
					'id'       => 1,
					'settings' => array( 'typography' => array( 'fontFamilies' => array( 'theme' => array() ) ) ),
				)
			);
			$log = (string) file_get_contents( $log_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		} finally {
			ini_restore( 'error_log' );
			unlink( $log_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
		}

		$lines = array_values( array_filter( explode( "\n", $log ) ) );
		$this->assertCount( 1, $lines );
		$this->assertStringContainsString( 'RuntimeException: boom [Airo WP] forged line', $lines[0] );
	}

	public function test_stored_non_array_font_families_does_not_fatal_on_settings_update(): void {
		$font_downloader = \Mockery::mock( FontDownloader::class );
		$font_downloader->shouldNotReceive( 'process_font_families' );
		$tool = new UpdateGlobalStyles( $font_downloader );

		Functions\when( 'get_post' )->justReturn(
			$this->global_styles_post( array( 'settings' => array( 'typography' => array( 'fontFamilies' => 'Roboto' ) ) ) )
		);
		Functions\expect( 'wp_update_post' )->once()->andReturn( 1 );

		$result = $tool->execute(
			array(
				'id'       => 1,
				'settings' => array( 'color' => array( 'custom' => true ) ),
			)
		);

		$this->assertTrue( $result['success'] );
	}

	/**
	 * @return array<string, array{string}>
	 */
	public function provide_mergeable_sections(): array {
		return array(
			'settings' => array( 'settings' ),
			'styles'   => array( 'styles' ),
		);
	}

	/**
	 * @dataProvider provide_mergeable_sections
	 */
	public function test_stored_non_array_section_is_replaced_in_merge_mode( string $section ): void {
		$incoming = array( 'color' => array( 'text' => '#000' ) );

		Functions\when( 'get_post' )->justReturn( $this->global_styles_post( array( $section => 'corrupt' ) ) );
		Functions\expect( 'wp_update_post' )
			->once()
			->with(
				\Mockery::on(
					static function ( $args ) use ( $section, $incoming ) {
						return json_decode( $args['post_content'], true )[ $section ] === $incoming;
					}
				),
				true
			)
			->andReturn( 1 );

		$result = $this->tool->execute(
			array(
				'id'     => 1,
				$section => $incoming,
			)
		);

		$this->assertTrue( $result['success'] );
	}

	/**
	 * @param array $content Decoded global-styles content.
	 * @return \stdClass
	 */
	private function global_styles_post( array $content ): \stdClass {
		$post               = new \stdClass();
		$post->ID           = 1;
		$post->post_type    = 'wp_global_styles';
		$post->post_title   = 'Styles';
		$post->post_status  = 'publish';
		$post->post_content = json_encode( $content );

		return $post;
	}
}
