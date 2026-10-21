<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Media;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\UploadImage;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UploadImageTest extends TestCase {

	private UploadImage $tool;

	/**
	 * Real temp files handed out as download_url() results, removed after each test.
	 *
	 * @var string[]
	 */
	private array $tmp_files = array();

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'esc_url_raw' )->returnArg();
		Functions\when( 'wp_kses_post' )->returnArg();
		Functions\when( 'wp_parse_url' )->alias( 'parse_url' );

		$this->tool = new UploadImage();
	}

	protected function tearDown(): void {
		foreach ( $this->tmp_files as $file ) {
			if ( file_exists( $file ) ) {
				unlink( $file );
			}
		}
		parent::tearDown();
	}

	/**
	 * A real file standing in for download_url()'s result, so file_exists() and
	 * filesize() — PHP internals Brain Monkey cannot stub — behave naturally.
	 */
	private function fake_download( string $bytes = 'not-really-an-image' ): string {
		$file = tempnam( sys_get_temp_dir(), 'airo-wp-test' );
		file_put_contents( $file, $bytes );
		$this->tmp_files[] = $file;

		return $file;
	}

	/**
	 * Stub the shared URL-path collaborators up to the sideload.
	 */
	private function stub_url_download( string $url, string $tmp_file, $mime ): void {
		Functions\expect( 'esc_url_raw' )->with( $url )->andReturn( $url );
		Functions\expect( 'download_url' )->once()->with( $url, 300 )->andReturn( $tmp_file );
		Functions\when( 'is_wp_error' )->alias( static fn( $value ) => $value instanceof \WP_Error );
		Functions\expect( 'wp_get_image_mime' )->once()->with( $tmp_file )->andReturn( $mime );
		Functions\when( 'sanitize_file_name' )->returnArg();
		Functions\when( 'get_allowed_mime_types' )->justReturn(
			array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'png'          => 'image/png',
				'gif'          => 'image/gif',
				'avif'         => 'image/avif',
			)
		);
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/upload-image', UploadImage::TOOL_ID );
	}

	public function test_missing_both_sources_returns_error(): void {
		$result = $this->tool->execute( array() );
		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Either url or file_data is required', $result['message'] );
	}

	public function test_both_sources_returns_error(): void {
		$result = $this->tool->execute(
			array(
				'url'       => 'https://example.com/image.jpg',
				'file_data' => 'aGVsbG8=',
			)
		);
		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not both', $result['message'] );
	}

	public function test_input_schema_accepts_either_url_or_base64(): void {
		$schema = $this->call_private( $this->tool, 'get_input_schema' );

		$this->assertArrayHasKey( 'file_data', $schema['properties'], 'base64 upload path is missing from the schema' );
		$this->assertArrayHasKey( 'filename', $schema['properties'] );
		$this->assertArrayHasKey( 'mime_type', $schema['properties'] );

		$this->assertArrayNotHasKey( 'required', $schema, 'a flat required list cannot express the url/file_data choice' );
		$this->assertSame(
			array(
				array( 'required' => array( 'url' ) ),
				array( 'required' => array( 'file_data', 'filename' ) ),
			),
			$schema['oneOf']
		);
	}

	public function test_base64_upload_requires_filename(): void {
		$result = $this->tool->execute( array( 'file_data' => 'aGVsbG8=' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'filename is required', $result['message'] );
	}

	public function test_base64_upload_rejects_undecodable_data(): void {
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\expect( 'absint' )->with( 5 )->andReturn( 5 );
		Functions\expect( 'get_post' )->with( 5 )->andReturn( (object) array( 'ID' => 5 ) );

		$result = $this->tool->execute(
			array(
				'file_data' => '!!!! not base64 !!!!',
				'filename'  => 'logo.png',
				'post_id'   => 5,
			)
		);

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Invalid base64 data', $result['message'] );
	}

	public function test_base64_upload_rejects_disallowed_mime_type(): void {
		Functions\when( 'sanitize_file_name' )->returnArg();
		Functions\expect( 'get_allowed_mime_types' )->andReturn( array( 'image/png' => 'image/png' ) );

		$result = $this->tool->execute(
			array(
				'file_data' => 'aGVsbG8=',
				'filename'  => 'logo.svg',
				'mime_type' => 'image/svg+xml',
			)
		);

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Unsupported MIME type', $result['message'] );
	}

	/**
	 * @return array<string, array{string}>
	 */
	public function invalid_urls(): array {
		return array(
			'no scheme'         => array( 'not-a-valid-url' ),
			'protocol-relative' => array( '//example.com/image.png' ),
			'non-http scheme'   => array( 'ftp://example.com/image.png' ),
			'script scheme'     => array( 'javascript:alert(1)' ),
		);
	}

	/**
	 * esc_url_raw() would prepend http:// to most of these, so the scheme must be
	 * refused on the raw input, before any sanitising or download.
	 *
	 * @dataProvider invalid_urls
	 */
	public function test_invalid_url_returns_error( string $url ): void {
		Functions\expect( 'esc_url_raw' )->never();
		Functions\expect( 'download_url' )->never();

		$result = $this->tool->execute( array( 'url' => $url ) );

		$this->assertFalse( $result['success'] );
		$this->assertSame( 'Invalid URL provided', $result['message'] );
	}

	public function test_check_permissions_returns_false_without_upload_files(): void {
		Functions\expect( 'current_user_can' )->once()->with( 'upload_files' )->andReturn( false );

		$this->assertFalse( $this->tool->check_permissions() );
	}

	public function test_nonexistent_post_id_returns_error(): void {
		Functions\expect( 'esc_url_raw' )->with( 'https://example.com/image.jpg' )->andReturn( 'https://example.com/image.jpg' );
		Functions\expect( 'current_user_can' )->with( 'upload_files' )->andReturn( true );
		Functions\expect( 'absint' )->with( 99 )->andReturn( 99 );
		Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

		$result = $this->tool->execute( array( 'url' => 'https://example.com/image.jpg', 'post_id' => 99 ) );
		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( '99', $result['message'] );
		$this->assertStringContainsString( 'not exist', $result['message'] );
	}

	public function test_url_upload_returns_error_when_download_fails(): void {
		$url = 'https://example.com/image.jpg';
		Functions\expect( 'esc_url_raw' )->with( $url )->andReturn( $url );
		Functions\expect( 'download_url' )->once()->with( $url, 300 )->andReturn( new \WP_Error( 'http_404', 'Not Found' ) );
		Functions\when( 'is_wp_error' )->alias( static fn( $value ) => $value instanceof \WP_Error );
		Functions\expect( 'media_handle_sideload' )->never();

		$result = $this->tool->execute( array( 'url' => $url ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Failed to upload image', $result['message'] );
		$this->assertStringContainsString( 'Not Found', $result['message'] );
	}

	public function test_url_upload_rejects_non_image_and_removes_temp_file(): void {
		$url = 'https://example.com/page';
		$tmp = $this->fake_download( '<html></html>' );
		$this->stub_url_download( $url, $tmp, false );
		Functions\expect( 'media_handle_sideload' )->never();
		Functions\expect( 'wp_delete_file' )->once()->with( $tmp );

		$result = $this->tool->execute( array( 'url' => $url ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not a supported image', $result['message'] );
	}

	public function test_url_upload_rejects_mime_not_allowed_on_site(): void {
		$url = 'https://example.com/photo';
		$tmp = $this->fake_download();
		$this->stub_url_download( $url, $tmp, 'image/webp' );
		Functions\expect( 'media_handle_sideload' )->never();
		Functions\expect( 'wp_delete_file' )->once()->with( $tmp );

		$result = $this->tool->execute( array( 'url' => $url ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Unsupported MIME type', $result['message'] );
	}

	public function test_url_upload_returns_error_and_removes_temp_file_when_sideload_fails(): void {
		$url = 'https://example.com/image.jpg';
		$tmp = $this->fake_download();
		$this->stub_url_download( $url, $tmp, 'image/jpeg' );
		Functions\expect( 'media_handle_sideload' )->once()->andReturn( new \WP_Error( 'upload_error', 'Disk full' ) );
		Functions\expect( 'add_post_meta' )->never();
		Functions\expect( 'wp_delete_file' )->once()->with( $tmp );

		$result = $this->tool->execute( array( 'url' => $url ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Failed to upload image', $result['message'] );
		$this->assertStringContainsString( 'Disk full', $result['message'] );
	}

	public function test_successful_extensionless_url_upload(): void {
		$url = 'https://cdn.example.com/images/abc123';
		$tmp = $this->fake_download( 'PNGBYTES' );
		$this->stub_url_download( $url, $tmp, 'image/png' );
		Functions\expect( 'media_handle_sideload' )
			->once()
			->with(
				array(
					'name'     => 'abc123.png',
					'type'     => 'image/png',
					'tmp_name' => $tmp,
					'error'    => 0,
					'size'     => 8,
				),
				null,
				null
			)
			->andReturn( 42 );
		Functions\expect( 'add_post_meta' )->once()->with( 42, '_source_url', $url );
		Functions\expect( 'wp_delete_file' )->never();
		Functions\expect( 'wp_get_attachment_url' )->with( 42 )->andReturn( 'https://example.com/wp-content/uploads/abc123.png' );

		$result = $this->tool->execute( array( 'url' => $url ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( 42, $result['attachment_id'] );
		$this->assertSame( 'https://example.com/wp-content/uploads/abc123.png', $result['url'] );
		$this->assertStringContainsString( 'successfully', $result['message'] );
	}

	/**
	 * @return array<string, array{string, string, string}>
	 */
	public function sideload_filenames(): array {
		return array(
			'sniffed type replaces URL extension'    => array( 'https://example.com/photo.jpg', 'image/png', 'photo.png' ),
			'matching extension is not doubled'      => array( 'https://example.com/hero.avif', 'image/avif', 'hero.avif' ),
			'URL extension matched case-insensitive' => array( 'https://example.com/PHOTO.JPG', 'image/png', 'PHOTO.png' ),
			'no path falls back to default name'     => array( 'https://cdn.example.com', 'image/gif', 'upload.gif' ),
			'alias list resolves to first extension' => array( 'https://example.com/raw', 'image/jpeg', 'raw.jpg' ),
			'query string is not part of the name'   => array( 'https://example.com/pic.png?w=100', 'image/png', 'pic.png' ),
			'version segment is not an extension'    => array( 'https://cdn.example.com/assets/hero-v1.2', 'image/png', 'hero-v1.2.png' ),
			'numeric segment is not an extension'    => array( 'https://cdn.example.com/1234.5678', 'image/png', '1234.5678.png' ),
		);
	}

	/**
	 * @dataProvider sideload_filenames
	 */
	public function test_url_upload_names_file_from_sniffed_mime( string $url, string $mime, string $expected_name ): void {
		$tmp = $this->fake_download();
		$this->stub_url_download( $url, $tmp, $mime );
		Functions\expect( 'media_handle_sideload' )
			->once()
			->with( \Mockery::on( static fn( $file ) => $expected_name === $file['name'] && $mime === $file['type'] ), null, null )
			->andReturn( 7 );
		Functions\when( 'add_post_meta' )->justReturn( 1 );
		Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.com/wp-content/uploads/' . $expected_name );

		$this->assertTrue( $this->tool->execute( array( 'url' => $url ) )['success'] );
	}

	public function test_successful_url_upload_with_metadata(): void {
		$url = 'https://example.com/image.jpg';
		$tmp = $this->fake_download();
		$this->stub_url_download( $url, $tmp, 'image/jpeg' );
		Functions\expect( 'sanitize_text_field' )->with( 'My Title' )->andReturn( 'My Title' );
		Functions\expect( 'sanitize_text_field' )->with( 'Alt text' )->andReturn( 'Alt text' );
		Functions\expect( 'wp_kses_post' )->with( 'A description' )->andReturn( 'A description' );
		Functions\expect( 'wp_kses_post' )->with( 'A caption' )->andReturn( 'A caption' );
		Functions\expect( 'media_handle_sideload' )
			->once()
			->with( \Mockery::type( 'array' ), null, 'My Title' )
			->andReturn( 55 );
		Functions\expect( 'add_post_meta' )->once()->with( 55, '_source_url', $url );
		Functions\expect( 'wp_update_post' )->andReturn( 55 );
		Functions\expect( 'update_post_meta' )->with( 55, '_wp_attachment_image_alt', 'Alt text' )->andReturn( true );
		Functions\expect( 'wp_get_attachment_url' )->with( 55 )->andReturn( 'https://example.com/wp-content/uploads/image.jpg' );

		$result = $this->tool->execute(
			array(
				'url'         => $url,
				'title'       => 'My Title',
				'alt_text'    => 'Alt text',
				'description' => 'A description',
				'caption'     => 'A caption',
			)
		);

		$this->assertTrue( $result['success'] );
		$this->assertSame( 55, $result['attachment_id'] );
	}

	public function test_attaching_to_uneditable_post_is_refused(): void {
		Functions\when( 'absint' )->returnArg();
		Functions\expect( 'get_post' )->with( 5 )->andReturn( (object) array( 'ID' => 5 ) );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap ) {
				return 'edit_post' !== $cap;
			}
		);
		Functions\expect( 'media_sideload_image' )->never();
		Functions\expect( 'media_handle_sideload' )->never();

		$result = $this->tool->execute( array( 'url' => 'https://example.com/a.jpg', 'post_id' => 5 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission', $result['message'] );
	}

	// Port-regression guard: the URL path used to go through media_sideload_image(), which
	// rejects extension-less URLs; this fails if that call comes back.
	public function test_url_upload_never_calls_media_sideload_image(): void {
		$url = 'https://example.com/image.jpg';
		$tmp = $this->fake_download();
		$this->stub_url_download( $url, $tmp, 'image/jpeg' );
		Functions\expect( 'media_sideload_image' )->never();
		Functions\when( 'media_handle_sideload' )->justReturn( 3 );
		Functions\when( 'add_post_meta' )->justReturn( 1 );
		Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.com/wp-content/uploads/image.jpg' );

		$this->assertTrue( $this->tool->execute( array( 'url' => $url ) )['success'] );
	}
}
