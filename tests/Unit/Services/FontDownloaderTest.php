<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Services;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Services\FontDownloader;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class FontDownloaderTest extends TestCase {

	private const ROBOTO_FILE_URL = 'https://fonts.gstatic.com/s/roboto/v1/abc.woff2';

	private const INTER_FILE_URL = 'https://fonts.gstatic.com/s/inter/v1/xyz.woff2';

	private FontDownloader $downloader;

	private string $temp_file;

	private string $font_dir;

	private int $css_status = 200;

	private string $log_file;

	protected function setUp(): void {
		parent::setUp();

		$this->temp_file = (string) tempnam( sys_get_temp_dir(), 'airo-wp-font-' );
		$this->font_dir  = sys_get_temp_dir() . '/airo-wp-fonts-' . uniqid( '', true );
		$this->log_file  = (string) tempnam( sys_get_temp_dir(), 'airo-wp-log-' );
		ini_set( 'error_log', $this->log_file ); // phpcs:ignore WordPress.PHP.IniSet.Risky

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		// Define download_url so that function_exists('download_url') returns true
		// and the require_once ABSPATH path is never hit.
		Functions\when( 'download_url' )->alias( fn() => $this->temp_file );
		Functions\when( 'wp_remote_retrieve_response_code' )->alias( fn() => $this->css_status );
		Functions\when( 'wp_parse_url' )->alias(
			static function ( $url, $component = -1 ) {
				return parse_url( $url, $component ); // phpcs:ignore WordPress.WP.AlternativeFunctions.parse_url_parse_url
			}
		);
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'sanitize_file_name' )->returnArg();
		Functions\when( 'trailingslashit' )->alias(
			static function ( $path ) {
				return rtrim( $path, '/' ) . '/';
			}
		);

		$this->downloader = new FontDownloader();
	}

	protected function tearDown(): void {
		unset( $GLOBALS['wp_filesystem'] );
		ini_restore( 'error_log' );

		foreach ( array( $this->temp_file, $this->log_file ) as $file ) {
			if ( file_exists( $file ) ) {
				unlink( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
			}
		}

		if ( is_dir( $this->font_dir ) ) {
			array_map( 'unlink', (array) glob( $this->font_dir . '/*' ) );
			rmdir( $this->font_dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
		}

		parent::tearDown();
	}

	public function test_process_font_families_returns_unchanged_when_no_theme_fonts(): void {
		$input = array(
			'custom' => array(
				array(
					'slug' => 'my-font',
					'name' => 'My Font',
				),
			),
		);

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( $input, $result );
	}

	public function test_process_font_families_returns_unchanged_when_no_font_face(): void {
		$input = array(
			'theme' => array(
				array(
					'slug' => 'roboto',
					'name' => 'Roboto',
				),
			),
		);

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( $input, $result );
	}

	public function test_process_font_families_skips_non_google_fonts_urls(): void {
		$input = array(
			'theme' => array(
				array(
					'slug'     => 'custom-font',
					'name'     => 'Custom Font',
					'fontFace' => array(
						array(
							'fontWeight' => '400',
							'fontStyle'  => 'normal',
							'src'        => array( 'https://example.com/fonts/custom.woff2' ),
						),
					),
				),
			),
		);

		$result = $this->downloader->process_font_families( $input );

		// The src should remain unchanged since it's not a Google Fonts URL.
		$this->assertSame(
			'https://example.com/fonts/custom.woff2',
			$result['theme'][0]['fontFace'][0]['src'][0]
		);
	}

	/**
	 * @return array<string, array{string}>
	 */
	public function provide_look_alike_google_fonts_urls(): array {
		return array(
			'host in query'                => array( 'https://169.254.169.254/latest/meta-data/?fonts.googleapis.com' ),
			'host in path'                 => array( 'http://127.0.0.1:8080/fonts.googleapis.com/' ),
			'look-alike domain'            => array( 'https://fonts.googleapis.com.attacker.example/css2?family=Roboto' ),
			'userinfo'                     => array( 'https://fonts.googleapis.com@attacker.example/css2?family=Roboto' ),
			'sub-domain prefix'            => array( 'https://evil.fonts.googleapis.com/css2?family=Roboto' ),
			'non-standard port'            => array( 'https://fonts.googleapis.com:8443/css2?family=Roboto' ),
			'host in fragment'             => array( 'https://attacker.example/#fonts.googleapis.com' ),
			'protocol-relative look-alike' => array( '//fonts.googleapis.com.attacker.example/css2?family=Roboto' ),
			'protocol-relative userinfo'   => array( '//fonts.googleapis.com@attacker.example/css2?family=Roboto' ),
			'http non-standard port'       => array( 'http://fonts.googleapis.com:8080/css2?family=Roboto' ),
			'https on port 80'             => array( 'https://fonts.googleapis.com:80/css2?family=Roboto' ),
			'http on port 443'             => array( 'http://fonts.googleapis.com:443/css2?family=Roboto' ),
			'ftp scheme'                   => array( 'ftp://fonts.googleapis.com/css2?family=Roboto' ),
			'scheme-less host'             => array( 'fonts.googleapis.com/css2?family=Roboto' ),
		);
	}

	/**
	 * @dataProvider provide_look_alike_google_fonts_urls
	 */
	public function test_process_font_families_never_fetches_urls_that_only_resemble_google_fonts( string $url ): void {
		Functions\expect( 'wp_safe_remote_get' )->never();
		Functions\expect( 'wp_remote_get' )->never();

		$input = $this->font_families_with_src( $url );

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
	}

	public function test_process_font_families_fetches_google_fonts_css_with_safe_http_api(): void {
		$url = 'https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap';

		Functions\expect( 'wp_remote_get' )->never();
		Functions\expect( 'wp_safe_remote_get' )
			->once()
			->with( $url, \Mockery::type( 'array' ) )
			->andReturn( array( 'body' => '' ) );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( '' );

		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		// Empty CSS body yields no font file, so the src is left as-is.
		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
	}

	/**
	 * @return array<string, array{string}>
	 */
	public function provide_upgradable_google_fonts_urls(): array {
		return array(
			'plain http'           => array( 'http://fonts.googleapis.com/css2?family=Roboto' ),
			'protocol-relative'    => array( '//fonts.googleapis.com/css2?family=Roboto' ),
			'upper-case https'     => array( 'HTTPS://Fonts.GoogleAPIs.com/css2?family=Roboto' ),
			'with fragment'        => array( 'https://fonts.googleapis.com/css2?family=Roboto#x' ),
			'http default port'    => array( 'http://fonts.googleapis.com:80/css2?family=Roboto' ),
			'https default port'   => array( 'https://fonts.googleapis.com:443/css2?family=Roboto' ),
		);
	}

	/**
	 * @dataProvider provide_upgradable_google_fonts_urls
	 */
	public function test_google_fonts_css_sources_are_fetched_over_https( string $url ): void {
		Functions\expect( 'wp_safe_remote_get' )
			->once()
			->with( 'https://fonts.googleapis.com/css2?family=Roboto', \Mockery::type( 'array' ) )
			->andReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( '' );

		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		// Empty CSS body yields no font file, so the src is left as-is.
		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
	}

	/**
	 * @return array<string, array{string}>
	 */
	public function provide_font_file_urls_outside_gstatic(): array {
		return array(
			'other host'        => array( 'https://attacker.example/x.woff2' ),
			'php before query'  => array( 'https://attacker.example/x.php?y=.woff2' ),
			'plain http'        => array( 'http://fonts.gstatic.com/s/roboto/v1/abc.woff2' ),
			'look-alike domain' => array( 'https://fonts.gstatic.com.attacker.example/abc.woff2' ),
			'userinfo'          => array( 'https://fonts.gstatic.com@attacker.example/abc.woff2' ),
			'non-standard port' => array( 'https://fonts.gstatic.com:8443/abc.woff2' ),
		);
	}

	public function test_string_src_is_processed_like_a_single_entry_list(): void {
		$url = 'https://fonts.googleapis.com/css2?family=Roboto';

		Functions\expect( 'wp_safe_remote_get' )
			->once()
			->with( $url, \Mockery::type( 'array' ) )
			->andReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( '' );

		$input                                    = $this->font_families_with_src( $url );
		$input['theme'][0]['fontFace'][0]['src'] = $url;

		$result = $this->downloader->process_font_families( $input );

		// Empty CSS body yields no font file, so the string src is left exactly as it was.
		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'] );
	}

	public function test_non_google_string_src_is_left_unchanged(): void {
		Functions\expect( 'wp_safe_remote_get' )->never();

		$url                                      = 'https://example.com/font.woff2';
		$input                                    = $this->font_families_with_src( $url );
		$input['theme'][0]['fontFace'][0]['src'] = $url;

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'] );
	}

	/**
	 * @return array<string, array{array<mixed>|string}>
	 */
	public function provide_google_fonts_src_shapes(): array {
		$url = 'https://fonts.googleapis.com/css2?family=Roboto';
		return array(
			'list src'   => array( array( $url ) ),
			'string src' => array( $url ),
		);
	}

	/**
	 * @dataProvider provide_font_file_urls_outside_gstatic
	 */
	public function test_css_parser_never_returns_a_font_file_outside_gstatic( string $font_file_url ): void {
		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(' . $font_file_url . ') format("woff2"); }'
		);

		$this->assertNull(
			$this->call_private( $this->downloader, 'parse_google_fonts_css', array( 'https://fonts.googleapis.com/css2?family=Roboto' ) )
		);
	}

	public function test_css_parser_returns_gstatic_font_file(): void {
		$font_file_url = 'https://fonts.gstatic.com/s/roboto/v1/abc.woff2';

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(' . $font_file_url . ') format("woff2"); }'
		);

		$this->assertSame(
			$font_file_url,
			$this->call_private( $this->downloader, 'parse_google_fonts_css', array( 'https://fonts.googleapis.com/css2?family=Roboto' ) )
		);
	}

	/**
	 * @dataProvider provide_font_file_urls_outside_gstatic
	 */
	public function test_process_font_families_refuses_font_files_outside_gstatic( string $font_file_url ): void {
		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(' . $font_file_url . ') format("woff2"); }'
		);
		Functions\expect( 'wp_font_dir' )->never();

		$url    = 'https://fonts.googleapis.com/css2?family=Roboto';
		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
		$this->assertStringContainsString( 'Refusing font file from unexpected host', (string) file_get_contents( $this->log_file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	public function test_non_200_css_response_is_not_parsed(): void {
		$this->css_status = 404;
		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(https://fonts.gstatic.com/s/roboto/v1/abc.woff2) format("woff2"); }'
		);
		Functions\expect( 'wp_font_dir' )->never();

		$url    = 'https://fonts.googleapis.com/css2?family=Roboto';
		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
	}

	/**
	 * @return array<string, array{string}>
	 */
	public function provide_invalid_font_contents(): array {
		return array(
			'php payload'      => array( '<?php echo 1;' . str_repeat( ' ', 2048 ) ),
			'woff (not woff2)' => array( 'wOFF' . str_repeat( "\0", 2048 ) ),
			'ttf'              => array( "\x00\x01\x00\x00" . str_repeat( "\0", 2048 ) ),
			'otf'              => array( 'OTTO' . str_repeat( "\0", 2048 ) ),
			'woff2 too small'  => array( 'wOF2' . str_repeat( "\0", 100 ) ),
		);
	}

	/**
	 * @dataProvider provide_invalid_font_contents
	 */
	public function test_invalid_download_is_deleted_before_reaching_fonts_directory( string $contents ): void {
		file_put_contents( $this->temp_file, $contents ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(https://fonts.gstatic.com/s/roboto/v1/abc.woff2) format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/fonts' ) );
		Functions\expect( 'wp_mkdir_p' )->never();
		Functions\expect( 'WP_Filesystem' )->never();
		Functions\expect( 'wp_delete_file' )
			->once()
			->with( $this->temp_file )
			->andReturnUsing(
				static function ( $file ) {
					unlink( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
				}
			);

		$url    = 'https://fonts.googleapis.com/css2?family=Roboto';
		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
		$this->assertFileDoesNotExist( $this->temp_file );
	}

	/**
	 * @dataProvider provide_google_fonts_src_shapes
	 * @param array<mixed>|string $src_value
	 */
	public function test_valid_download_is_moved_into_fonts_directory_and_src_is_localised( $src_value ): void {
		file_put_contents( $this->temp_file, 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$target = $this->font_dir . '/' . self::roboto_400_normal( self::ROBOTO_FILE_URL );

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(' . self::ROBOTO_FILE_URL . ') format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/fonts' ) );
		Functions\expect( 'wp_mkdir_p' )
			->once()
			->with( $this->font_dir )
			->andReturnUsing( static fn( $dir ) => mkdir( $dir, 0777, true ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		Functions\expect( 'WP_Filesystem' )->once()->with( false, $this->font_dir, true )->andReturn( true );

		$filesystem = \Mockery::mock();
		$filesystem->shouldReceive( 'move' )
			->once()
			->with( $this->temp_file, $target, true )
			->andReturnUsing( static fn( $from, $to ) => rename( $from, $to ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename
		$GLOBALS['wp_filesystem'] = $filesystem;

		$input                                    = $this->font_families_with_src( 'https://fonts.googleapis.com/css2?family=Roboto' );
		$input['theme'][0]['fontFace'][0]['src'] = $src_value;

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( array( 'https://example.test/wp-content/uploads/fonts/' . self::roboto_400_normal( self::ROBOTO_FILE_URL ) ), $result['theme'][0]['fontFace'][0]['src'] );
		$this->assertFileExists( $target );
		$this->assertFileDoesNotExist( $this->temp_file );
	}

	public function test_src_points_at_the_resolved_font_directory_on_a_multisite_subsite(): void {
		file_put_contents( $this->temp_file, 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$target = $this->font_dir . '/' . self::roboto_400_normal( self::ROBOTO_FILE_URL );

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(' . self::ROBOTO_FILE_URL . ') format("woff2"); }'
		);
		// wp_font_dir() follows wp_upload_dir(), so a subsite stores fonts under uploads/sites/<id>/fonts.
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/sites/2/fonts/' ) );
		Functions\when( 'wp_mkdir_p' )->alias( static fn( $dir ) => mkdir( $dir, 0777, true ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		Functions\when( 'WP_Filesystem' )->justReturn( true );

		$filesystem = \Mockery::mock();
		$filesystem->shouldReceive( 'move' )->once()->with( $this->temp_file, $target, true )
			->andReturnUsing( static fn( $from, $to ) => rename( $from, $to ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename
		$GLOBALS['wp_filesystem'] = $filesystem;

		$result = $this->downloader->process_font_families( $this->font_families_with_src( 'https://fonts.googleapis.com/css2?family=Roboto' ) );

		$this->assertSame( array( 'https://example.test/wp-content/uploads/sites/2/fonts/' . self::roboto_400_normal( self::ROBOTO_FILE_URL ) ), $result['theme'][0]['fontFace'][0]['src'] );
	}

	public function test_cached_font_src_points_at_the_resolved_font_directory(): void {
		mkdir( $this->font_dir, 0777, true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		file_put_contents( $this->font_dir . '/' . self::roboto_400_normal( self::ROBOTO_FILE_URL ) . '', 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(' . self::ROBOTO_FILE_URL . ') format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/sites/2/fonts' ) );
		Functions\expect( 'download_url' )->never();

		$result = $this->downloader->process_font_families( $this->font_families_with_src( 'https://fonts.googleapis.com/css2?family=Roboto' ) );

		$this->assertSame( array( 'https://example.test/wp-content/uploads/sites/2/fonts/' . self::roboto_400_normal( self::ROBOTO_FILE_URL ) ), $result['theme'][0]['fontFace'][0]['src'] );
	}

	public function test_reused_slug_with_a_different_family_is_not_served_from_cache(): void {
		// A file stored while the "roboto" slug pointed at Roboto.
		mkdir( $this->font_dir, 0777, true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		$stale = $this->font_dir . '/' . self::roboto_400_normal( self::ROBOTO_FILE_URL );
		file_put_contents( $stale, 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		file_put_contents( $this->temp_file, 'wOF2' . str_repeat( "\1", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$target = $this->font_dir . '/' . self::roboto_400_normal( self::INTER_FILE_URL );

		// The same slug now resolves to a different font file.
		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(' . self::INTER_FILE_URL . ') format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/fonts' ) );
		Functions\when( 'WP_Filesystem' )->justReturn( true );

		$filesystem = \Mockery::mock();
		$filesystem->shouldReceive( 'move' )->once()->with( $this->temp_file, $target, true )
			->andReturnUsing( static fn( $from, $to ) => rename( $from, $to ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename
		$GLOBALS['wp_filesystem'] = $filesystem;

		$result = $this->downloader->process_font_families( $this->font_families_with_src( 'https://fonts.googleapis.com/css2?family=Inter' ) );

		$this->assertSame( array( 'https://example.test/wp-content/uploads/fonts/' . self::roboto_400_normal( self::INTER_FILE_URL ) ), $result['theme'][0]['fontFace'][0]['src'] );
		$this->assertStringEqualsFile( $target, 'wOF2' . str_repeat( "\1", 2048 ) );
	}

	public function test_font_directory_without_a_url_is_not_used(): void {
		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(https://fonts.gstatic.com/s/roboto/v1/abc.woff2) format("woff2"); }'
		);
		// A font_dir filter that drops the url key.
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir ) );
		Functions\expect( 'download_url' )->never();

		$url    = 'https://fonts.googleapis.com/css2?family=Roboto';
		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
	}

	public function test_temp_file_is_deleted_when_move_into_fonts_directory_fails(): void {
		file_put_contents( $this->temp_file, 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(https://fonts.gstatic.com/s/roboto/v1/abc.woff2) format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/fonts' ) );
		Functions\when( 'wp_mkdir_p' )->justReturn( true );
		Functions\when( 'WP_Filesystem' )->justReturn( true );
		Functions\expect( 'wp_delete_file' )->once()->with( $this->temp_file );

		$filesystem = \Mockery::mock();
		$filesystem->shouldReceive( 'move' )->once()->andReturn( false );
		$filesystem->shouldNotReceive( 'delete' );
		$GLOBALS['wp_filesystem'] = $filesystem;

		$url    = 'https://fonts.googleapis.com/css2?family=Roboto';
		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
	}

	public function test_font_directory_creation_failure_stops_before_the_filesystem_and_is_logged(): void {
		file_put_contents( $this->temp_file, 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(https://fonts.gstatic.com/s/roboto/v1/abc.woff2) format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/fonts' ) );
		Functions\expect( 'wp_mkdir_p' )->once()->with( $this->font_dir )->andReturn( false );
		Functions\expect( 'WP_Filesystem' )->never();
		Functions\expect( 'wp_delete_file' )->once()->with( $this->temp_file );

		$url    = 'https://fonts.googleapis.com/css2?family=Roboto';
		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
		$this->assertStringContainsString( 'Could not create font directory: ' . $this->font_dir, (string) file_get_contents( $this->log_file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	public function test_temp_file_is_deleted_and_src_kept_when_filesystem_cannot_connect(): void {
		file_put_contents( $this->temp_file, 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(https://fonts.gstatic.com/s/roboto/v1/abc.woff2) format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/fonts' ) );
		Functions\when( 'wp_mkdir_p' )->justReturn( true );
		Functions\expect( 'WP_Filesystem' )->once()->andReturn( false );
		Functions\expect( 'wp_delete_file' )->once()->with( $this->temp_file );

		// What WP_Filesystem() leaves behind when an FTP/SSH connection fails: an object that must not be used.
		$filesystem = \Mockery::mock();
		$filesystem->shouldNotReceive( 'move' );
		$GLOBALS['wp_filesystem'] = $filesystem;

		$url    = 'https://fonts.googleapis.com/css2?family=Roboto';
		$result = $this->downloader->process_font_families( $this->font_families_with_src( $url ) );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
	}

	public function test_temp_file_is_deleted_when_filesystem_move_throws(): void {
		file_put_contents( $this->temp_file, 'wOF2' . str_repeat( "\0", 2048 ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents

		Functions\when( 'wp_safe_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			'/* latin */ @font-face { src: url(https://fonts.gstatic.com/s/roboto/v1/abc.woff2) format("woff2"); }'
		);
		Functions\when( 'wp_font_dir' )->justReturn( array( 'path' => $this->font_dir, 'url' => 'https://example.test/wp-content/uploads/fonts' ) );
		Functions\when( 'wp_mkdir_p' )->justReturn( true );
		Functions\when( 'WP_Filesystem' )->justReturn( true );
		Functions\expect( 'wp_delete_file' )->once()->with( $this->temp_file );

		$filesystem = \Mockery::mock();
		$filesystem->shouldReceive( 'move' )->once()->andThrow( new \TypeError( 'ftp_rename(): Argument #1 ($ftp) must be of type FTP\Connection, null given' ) );
		$GLOBALS['wp_filesystem'] = $filesystem;

		$this->expectException( \TypeError::class );

		$this->downloader->process_font_families( $this->font_families_with_src( 'https://fonts.googleapis.com/css2?family=Roboto' ) );
	}

	public function test_non_string_font_settings_do_not_throw(): void {
		Functions\expect( 'wp_safe_remote_get' )->once()->andReturn( array() );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( '' );

		$url   = 'https://fonts.googleapis.com/css2?family=Roboto';
		$input = array(
			'theme' => array(
				array(
					'slug'     => 42,
					'name'     => array( 'not', 'a', 'string' ),
					'fontFace' => array(
						array(
							'fontWeight' => 400,
							'fontStyle'  => null,
							'src'        => array( $url ),
						),
						array(
							'fontWeight' => '400',
							'src'        => array( 123 ),
						),
					),
				),
			),
		);

		$result = $this->downloader->process_font_families( $input );

		$this->assertSame( $url, $result['theme'][0]['fontFace'][0]['src'][0] );
		$this->assertSame( 123, $result['theme'][0]['fontFace'][1]['src'][0] );
	}

	public function test_log_lines_cannot_be_split_by_control_characters(): void {
		$this->call_private( $this->downloader, 'log', array( "first\r\nforged line\x00end" ) );

		$log = (string) file_get_contents( $this->log_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$this->assertSame( 1, substr_count( $log, "\n" ) );
		$this->assertStringContainsString( '[Font Downloader] first  forged line end', $log );
	}

	/**
	 * @param string $url Font face src URL.
	 * @return array
	 */
	private function font_families_with_src( string $url ): array {
		return array(
			'theme' => array(
				array(
					'slug'     => 'roboto',
					'name'     => 'Roboto',
					'fontFace' => array(
						array(
							'fontWeight' => '400',
							'fontStyle'  => 'normal',
							'src'        => array( $url ),
						),
					),
				),
			),
		);
	}

	/**
	 * Stored file name for the "roboto" slug at 400 normal, as FontDownloader derives it.
	 *
	 * Derived here rather than hard-coded so a changed fixture URL fails on the name, not on an opaque hash.
	 *
	 * @param string $font_file_url gstatic font file URL the name is keyed on.
	 * @return string
	 */
	private static function roboto_400_normal( string $font_file_url ): string {
		return 'roboto-400-normal-' . substr( md5( $font_file_url ), 0, 12 ) . '.woff2';
	}
}
