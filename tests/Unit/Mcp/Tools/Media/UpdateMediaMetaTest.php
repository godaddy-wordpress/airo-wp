<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Media;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\UpdateMediaMeta;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UpdateMediaMetaTest extends TestCase {

    private UpdateMediaMeta $tool;

    protected function setUp(): void {
        parent::setUp();

        if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
            define( 'PHPUNIT_RUNNING', true );
        }

        Functions\when( '__' )->returnArg();
        Functions\when( 'sanitize_text_field' )->returnArg();
        Functions\when( 'wp_kses_post' )->returnArg();

        $this->tool = new UpdateMediaMeta();
    }

    public function test_tool_id_has_correct_prefix(): void {
        $this->assertSame( 'airo-wp/update-media-meta', UpdateMediaMeta::TOOL_ID );
    }

    public function test_missing_media_id_returns_error(): void {
        $result = $this->tool->execute( array() );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'Media ID is required', $result['message'] );
    }

    public function test_no_update_fields_returns_error(): void {
        $result = $this->tool->execute( array( 'media_id' => 10 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'At least one field', $result['message'] );
    }

    public function test_nonexistent_media_returns_error(): void {
        Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

        $result = $this->tool->execute( array( 'media_id' => 99, 'title' => 'New Title' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'not found', $result['message'] );
    }

    public function test_non_attachment_returns_error(): void {
        $post            = new \stdClass();
        $post->post_type = 'post';

        Functions\expect( 'get_post' )->with( 5 )->andReturn( $post );

        $result = $this->tool->execute( array( 'media_id' => 5, 'title' => 'New Title' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'not a media attachment', $result['message'] );
    }

    public function test_successful_title_update(): void {
        $post                 = new \stdClass();
        $post->ID             = 10;
        $post->post_type      = 'attachment';
        $post->post_title     = 'Updated Title';
        $post->post_content   = 'Description';
        $post->post_excerpt   = 'Caption';
        $post->post_status    = 'inherit';
        $post->post_author    = 1;
        $post->post_date      = '2025-01-01 00:00:00';
        $post->post_modified  = '2025-01-02 00:00:00';
        $post->post_name      = 'updated-title';
        $post->post_mime_type = 'image/jpeg';

        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'wp_update_post' )->andReturn( 10 );
        Functions\expect( 'is_wp_error' )->andReturn( false );
        Functions\expect( 'wp_get_attachment_metadata' )->with( 10 )->andReturn( false );
        Functions\expect( 'get_attached_file' )->with( 10 )->andReturn( false );
        Functions\expect( 'get_post_meta' )->with( 10, '_wp_attachment_image_alt', true )->andReturn( 'Alt' );
        Functions\expect( 'wp_get_attachment_url' )->with( 10 )->andReturn( 'https://example.com/img.jpg' );

        $result = $this->tool->execute( array( 'media_id' => 10, 'title' => 'Updated Title' ) );

        $this->assertTrue( $result['success'] );
        $this->assertStringContainsString( 'Successfully updated', $result['message'] );
        $this->assertSame( 10, $result['updated_media']['id'] );
        $this->assertSame( 'Updated Title', $result['updated_media']['title'] );
    }
}
