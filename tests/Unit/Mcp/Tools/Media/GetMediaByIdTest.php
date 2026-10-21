<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Media;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\GetMediaById;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetMediaByIdTest extends TestCase {

    private GetMediaById $tool;

    protected function setUp(): void {
        parent::setUp();

        if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
            define( 'PHPUNIT_RUNNING', true );
        }

        Functions\when( '__' )->returnArg();

        $this->tool = new GetMediaById();
    }

    public function test_tool_id_has_correct_prefix(): void {
        $this->assertSame( 'airo-wp/get-media-by-id', GetMediaById::TOOL_ID );
    }

    public function test_missing_media_id_returns_error(): void {
        $result = $this->tool->execute( array() );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'Media ID is required', $result['message'] );
    }

    public function test_nonexistent_media_returns_error(): void {
        Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

        $result = $this->tool->execute( array( 'media_id' => 99 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'not found', $result['message'] );
    }

    public function test_non_attachment_post_returns_error(): void {
        $post            = new \stdClass();
        $post->post_type = 'post';

        Functions\expect( 'get_post' )->with( 5 )->andReturn( $post );

        $result = $this->tool->execute( array( 'media_id' => 5 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'not a media attachment', $result['message'] );
    }

    public function test_successful_retrieval(): void {
        $post                = new \stdClass();
        $post->ID            = 10;
        $post->post_type     = 'attachment';
        $post->post_title    = 'My Image';
        $post->post_content  = 'Description text';
        $post->post_excerpt  = 'Caption text';
        $post->post_status   = 'inherit';
        $post->post_author   = 1;
        $post->post_date     = '2025-01-01 00:00:00';
        $post->post_modified = '2025-01-02 00:00:00';
        $post->post_name     = 'my-image';
        $post->post_mime_type = 'image/png';

        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'wp_get_attachment_metadata' )->with( 10 )->andReturn(
            array( 'width' => 1024, 'height' => 768 )
        );
        Functions\expect( 'get_attached_file' )->with( 10 )->andReturn( false );
        Functions\expect( 'get_post_meta' )->with( 10, '_wp_attachment_image_alt', true )->andReturn( 'Alt' );
        Functions\expect( 'wp_get_attachment_url' )->with( 10 )->andReturn( 'https://example.com/img.png' );

        $result = $this->tool->execute( array( 'media_id' => 10 ) );

        $this->assertSame( 10, $result['id'] );
        $this->assertSame( 'My Image', $result['title'] );
        $this->assertSame( 1024, $result['dimensions']['width'] );
        $this->assertSame( 768, $result['dimensions']['height'] );
        $this->assertSame( 'Alt', $result['alt_text'] );
    }

    public function test_unreadable_attachment_returns_error(): void {
        $post            = new \stdClass();
        $post->ID        = 10;
        $post->post_type = 'attachment';
        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'current_user_can' )->with( 'read_post', 10 )->andReturn( false );
        Functions\expect( 'wp_get_attachment_url' )->never();

        $result = $this->tool->execute( array( 'media_id' => 10 ) );

        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'not found', $result['message'] );
    }

    public function test_meta_requires_edit_post(): void {
        $post                 = new \stdClass();
        $post->ID             = 10;
        $post->post_type      = 'attachment';
        $post->post_title     = 'My Image';
        $post->post_content   = '';
        $post->post_excerpt   = '';
        $post->post_status    = 'inherit';
        $post->post_author    = 1;
        $post->post_date      = '2025-01-01 00:00:00';
        $post->post_modified  = '2025-01-02 00:00:00';
        $post->post_name      = 'my-image';
        $post->post_mime_type = 'image/png';

        Functions\when( 'current_user_can' )->alias(
            function ( $cap ) {
                return 'edit_post' !== $cap;
            }
        );
        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'wp_get_attachment_metadata' )->with( 10 )->andReturn( false );
        Functions\expect( 'get_attached_file' )->with( 10 )->andReturn( false );
        Functions\expect( 'wp_get_attachment_url' )->with( 10 )->andReturn( 'https://example.com/img.png' );
        Functions\expect( 'get_post_meta' )->once()->with( 10, '_wp_attachment_image_alt', true )->andReturn( 'Alt' );

        $result = $this->tool->execute( array( 'media_id' => 10, 'include_meta' => true ) );

        $this->assertSame( 10, $result['id'] );
        $this->assertSame( array(), $result['meta'] );
    }

    public function test_meta_included_when_user_can_edit_post(): void {
        $post                 = new \stdClass();
        $post->ID             = 10;
        $post->post_type      = 'attachment';
        $post->post_title     = 'My Image';
        $post->post_content   = '';
        $post->post_excerpt   = '';
        $post->post_status    = 'inherit';
        $post->post_author    = 1;
        $post->post_date      = '2025-01-01 00:00:00';
        $post->post_modified  = '2025-01-02 00:00:00';
        $post->post_name      = 'my-image';
        $post->post_mime_type = 'image/png';

        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'wp_get_attachment_metadata' )->with( 10 )->andReturn( false );
        Functions\expect( 'get_attached_file' )->with( 10 )->andReturn( false );
        Functions\expect( 'wp_get_attachment_url' )->with( 10 )->andReturn( 'https://example.com/img.png' );
        Functions\expect( 'get_post_meta' )->once()->with( 10, '_wp_attachment_image_alt', true )->andReturn( 'Alt' );
        Functions\expect( 'get_post_meta' )->once()->with( 10 )->andReturn( array( 'k' => array( 'v' ) ) );

        $result = $this->tool->execute( array( 'media_id' => 10, 'include_meta' => true ) );

        $this->assertSame( array( 'k' => array( 'v' ) ), $result['meta'] );
    }
}
