<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Media;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\UploadImage;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UploadImageTest extends TestCase {

	private UploadImage $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'esc_url_raw' )->returnArg();
		Functions\when( 'wp_kses_post' )->returnArg();

		$this->tool = new UploadImage();
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

	public function test_invalid_url_returns_error(): void {
		Functions\expect( 'esc_url_raw' )->with( 'not-a-url' )->andReturn( '' );

		$result = $this->tool->execute( array( 'url' => 'not-a-url' ) );
		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Invalid URL', $result['message'] );
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

	public function test_sideload_error_returns_error(): void {
		$wp_error = $this->createMock( \WP_Error::class );
		$wp_error->method( 'get_error_message' )->willReturn( 'HTTP error' );

		Functions\expect( 'esc_url_raw' )->with( 'https://example.com/image.jpg' )->andReturn( 'https://example.com/image.jpg' );
		Functions\expect( 'current_user_can' )->with( 'upload_files' )->andReturn( true );
		Functions\expect( 'media_sideload_image' )->with( 'https://example.com/image.jpg', null, null, 'id' )->andReturn( $wp_error );
		Functions\expect( 'is_wp_error' )->with( $wp_error )->andReturn( true );

		$result = $this->tool->execute( array( 'url' => 'https://example.com/image.jpg' ) );
		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Failed to upload image', $result['message'] );
		$this->assertStringContainsString( 'HTTP error', $result['message'] );
	}

	public function test_successful_upload(): void {
		Functions\expect( 'esc_url_raw' )->with( 'https://example.com/image.jpg' )->andReturn( 'https://example.com/image.jpg' );
		Functions\expect( 'current_user_can' )->with( 'upload_files' )->andReturn( true );
		Functions\expect( 'media_sideload_image' )->with( 'https://example.com/image.jpg', null, null, 'id' )->andReturn( 42 );
		Functions\expect( 'is_wp_error' )->with( 42 )->andReturn( false );
		Functions\expect( 'wp_get_attachment_url' )->with( 42 )->andReturn( 'https://example.com/wp-content/uploads/image.jpg' );

		$result = $this->tool->execute( array( 'url' => 'https://example.com/image.jpg' ) );
		$this->assertTrue( $result['success'] );
		$this->assertSame( 42, $result['attachment_id'] );
		$this->assertSame( 'https://example.com/wp-content/uploads/image.jpg', $result['url'] );
		$this->assertStringContainsString( 'successfully', $result['message'] );
	}

	public function test_successful_upload_with_metadata(): void {
		Functions\expect( 'esc_url_raw' )->with( 'https://example.com/image.jpg' )->andReturn( 'https://example.com/image.jpg' );
		Functions\expect( 'current_user_can' )->with( 'upload_files' )->andReturn( true );
		Functions\expect( 'sanitize_text_field' )->with( 'My Title' )->andReturn( 'My Title' );
		Functions\expect( 'sanitize_text_field' )->with( 'Alt text' )->andReturn( 'Alt text' );
		Functions\expect( 'wp_kses_post' )->with( 'A description' )->andReturn( 'A description' );
		Functions\expect( 'wp_kses_post' )->with( 'A caption' )->andReturn( 'A caption' );
		Functions\expect( 'media_sideload_image' )->with( 'https://example.com/image.jpg', null, 'My Title', 'id' )->andReturn( 55 );
		Functions\expect( 'is_wp_error' )->with( 55 )->andReturn( false );
		Functions\expect( 'wp_update_post' )->andReturn( 55 );
		Functions\expect( 'is_wp_error' )->with( 55 )->andReturn( false );
		Functions\expect( 'update_post_meta' )->with( 55, '_wp_attachment_image_alt', 'Alt text' )->andReturn( true );
		Functions\expect( 'wp_get_attachment_url' )->with( 55 )->andReturn( 'https://example.com/wp-content/uploads/image.jpg' );

		$result = $this->tool->execute(
			array(
				'url'         => 'https://example.com/image.jpg',
				'title'       => 'My Title',
				'alt_text'    => 'Alt text',
				'description' => 'A description',
				'caption'     => 'A caption',
			)
		);

		$this->assertTrue( $result['success'] );
		$this->assertSame( 55, $result['attachment_id'] );
	}
}
