<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\DeletePost;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class DeletePostTest extends TestCase {

    private DeletePost $tool;

    protected function setUp(): void {
        parent::setUp();

        if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
            define( 'PHPUNIT_RUNNING', true );
        }

        Functions\when( '__' )->returnArg();

        $this->tool = new DeletePost();
    }

    public function test_tool_id_has_correct_prefix(): void {
        $this->assertSame( 'airo-wp/delete-post', DeletePost::TOOL_ID );
    }

    public function test_missing_post_id_returns_error(): void {
        $result = $this->tool->execute( array() );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'Post ID is required', $result['message'] );
    }

    public function test_nonexistent_post_returns_error(): void {
        Functions\expect( 'get_post' )->with( 99 )->andReturn( null );

        $result = $this->tool->execute( array( 'post_id' => 99 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'not found', $result['message'] );
    }

    public function test_trashing_already_trashed_post_returns_error(): void {
        $post              = new \stdClass();
        $post->post_status = 'trash';

        Functions\expect( 'get_post' )->with( 5 )->andReturn( $post );

        $result = $this->tool->execute( array( 'post_id' => 5, 'force_delete' => false ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'already in trash', $result['message'] );
    }

    public function test_permission_denied_returns_error(): void {
        $post              = new \stdClass();
        $post->post_status = 'publish';

        Functions\expect( 'get_post' )->with( 7 )->andReturn( $post );
        Functions\expect( 'current_user_can' )->with( 'delete_post', 7 )->andReturn( false );

        $result = $this->tool->execute( array( 'post_id' => 7 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'permission', $result['message'] );
    }

    public function test_successful_permanent_delete(): void {
        $post              = new \stdClass();
        $post->post_status = 'publish';

        Functions\expect( 'get_post' )->with( 10 )->andReturn( $post );
        Functions\expect( 'current_user_can' )->with( 'delete_post', 10 )->andReturn( true );
        Functions\expect( 'wp_delete_post' )->with( 10, true )->andReturn( $post );

        $result = $this->tool->execute( array( 'post_id' => 10, 'force_delete' => true ) );
        $this->assertTrue( $result['success'] );
        $this->assertSame( 10, $result['post_id'] );
        $this->assertTrue( $result['deleted_permanently'] );
        $this->assertStringContainsString( 'permanently deleted', $result['message'] );
    }

    public function test_successful_trash(): void {
        $post              = new \stdClass();
        $post->post_status = 'publish';

        Functions\expect( 'get_post' )->with( 11 )->andReturn( $post );
        Functions\expect( 'current_user_can' )->with( 'delete_post', 11 )->andReturn( true );
        Functions\expect( 'wp_delete_post' )->with( 11, false )->andReturn( $post );

        $result = $this->tool->execute( array( 'post_id' => 11, 'force_delete' => false ) );
        $this->assertTrue( $result['success'] );
        $this->assertSame( 11, $result['post_id'] );
        $this->assertFalse( $result['deleted_permanently'] );
        $this->assertStringContainsString( 'trash', $result['message'] );
    }
}
