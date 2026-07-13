<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Media;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\DeleteMedia;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class DeleteMediaTest extends TestCase {

    private DeleteMedia $tool;

    protected function setUp(): void {
        parent::setUp();

        if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
            define( 'PHPUNIT_RUNNING', true );
        }

        Functions\when( '__' )->returnArg();

        $this->tool = new DeleteMedia();
    }

    public function test_tool_id_has_correct_prefix(): void {
        $this->assertSame( 'airo-wp/delete-media', DeleteMedia::TOOL_ID );
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
        $post             = new \stdClass();
        $post->post_type  = 'post';

        Functions\expect( 'get_post' )->with( 5 )->andReturn( $post );

        $result = $this->tool->execute( array( 'media_id' => 5 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'not a media attachment', $result['message'] );
    }

    public function test_failed_deletion_returns_error(): void {
        $post              = new \stdClass();
        $post->ID          = 10;
        $post->post_type   = 'attachment';
        $post->post_title  = 'Test Image';

        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'wp_get_attachment_url' )->with( 10 )->andReturn( 'https://example.com/image.jpg' );
        Functions\expect( 'wp_delete_attachment' )->with( 10, true )->andReturn( false );

        $result = $this->tool->execute( array( 'media_id' => 10 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'Failed to delete', $result['message'] );
    }

    public function test_successful_deletion(): void {
        $post              = new \stdClass();
        $post->ID          = 10;
        $post->post_type   = 'attachment';
        $post->post_title  = 'Test Image';

        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'wp_get_attachment_url' )->with( 10 )->andReturn( 'https://example.com/image.jpg' );
        Functions\expect( 'wp_delete_attachment' )->with( 10, true )->andReturn( $post );

        $result = $this->tool->execute( array( 'media_id' => 10 ) );
        $this->assertTrue( $result['success'] );
        $this->assertStringContainsString( 'Successfully deleted', $result['message'] );
        $this->assertSame( 10, $result['deleted_media']['id'] );
        $this->assertSame( 'Test Image', $result['deleted_media']['title'] );
        $this->assertSame( 'https://example.com/image.jpg', $result['deleted_media']['url'] );
    }
}
