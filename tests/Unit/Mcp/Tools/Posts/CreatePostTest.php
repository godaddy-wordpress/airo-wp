<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\CreatePost;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class CreatePostTest extends TestCase {

    private CreatePost $tool;

    protected function setUp(): void {
        parent::setUp();

        if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
            define( 'PHPUNIT_RUNNING', true );
        }

        Functions\when( '__' )->returnArg();
        Functions\when( 'sanitize_text_field' )->returnArg();
        Functions\when( 'sanitize_textarea_field' )->returnArg();
        Functions\when( 'sanitize_key' )->returnArg();
        Functions\when( 'sanitize_title' )->returnArg();
        Functions\when( 'wp_kses_post' )->returnArg();

        $this->tool = new CreatePost();
    }

    public function test_tool_id_has_correct_prefix(): void {
        $this->assertSame( 'airo-wp/create-post', CreatePost::TOOL_ID );
    }

    public function test_empty_title_returns_error(): void {
        $result = $this->tool->execute( array() );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'Title is required', $result['message'] );
    }

    public function test_invalid_post_type_returns_error(): void {
        Functions\expect( 'post_type_exists' )->with( 'nonexistent' )->andReturn( false );

        $result = $this->tool->execute( array( 'title' => 'Test', 'post_type' => 'nonexistent' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'does not exist', $result['message'] );
    }

    public function test_permission_denied_returns_error(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->with( 'publish_posts' )->andReturn( false );

        $result = $this->tool->execute( array( 'title' => 'Test' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'permission', $result['message'] );
    }

    public function test_invalid_slug_returns_error(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );
        Functions\when( 'sanitize_title' )->justReturn( '' );

        $result = $this->tool->execute( array( 'title' => 'Test', 'slug' => '!!!' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'slug', $result['message'] );
    }

    public function test_successful_creation_returns_post_id(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );
        Functions\expect( 'wp_insert_post' )->andReturn( 42 );
        Functions\expect( 'wp_save_post_revision' )->once()->with( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'title' => 'Hello World', 'content' => '<p>Body</p>' ) );
        $this->assertTrue( $result['success'] );
        $this->assertSame( 42, $result['post_id'] );
    }

    public function test_skips_revision_on_insert_failure(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );
        Functions\expect( 'wp_insert_post' )->once()->andReturn( new \WP_Error( 'insert_failed', 'Insert failed' ) );
        Functions\when( 'is_wp_error' )->alias( function ( $thing ) {
            return $thing instanceof \WP_Error;
        } );
        Functions\expect( 'wp_save_post_revision' )->never();

        $result = $this->tool->execute( array( 'title' => 'Test Post', 'content' => 'Test content' ) );
        $this->assertFalse( $result['success'] );
        $this->assertArrayHasKey( 'message', $result );
    }

    public function test_creates_post_with_slug(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );
        Functions\when( 'sanitize_title' )->justReturn( 'my-custom-slug' );
        Functions\expect( 'wp_insert_post' )
            ->once()
            ->with( \Mockery::on( function ( $data ) {
                return isset( $data['post_name'] ) && 'my-custom-slug' === $data['post_name'];
            } ), true )
            ->andReturn( 456 );
        Functions\expect( 'wp_save_post_revision' )->once()->with( 456 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'title' => 'Test Post', 'slug' => 'my-custom-slug' ) );
        $this->assertTrue( $result['success'] );
        $this->assertSame( 456, $result['post_id'] );
    }

    public function test_invalid_status_returns_error(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );

        $result = $this->tool->execute( array( 'title' => 'Test', 'status' => 'invalid-status' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'invalid-status', $result['message'] );
    }

    public function test_future_status_without_date_returns_error(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );

        $result = $this->tool->execute( array( 'title' => 'Test', 'status' => 'future' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'Date is required', $result['message'] );
    }

    public function test_meta_fields_are_set(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );
        Functions\expect( 'wp_insert_post' )->andReturn( 10 );
        Functions\expect( 'wp_save_post_revision' )->once();
        Functions\when( 'is_wp_error' )->justReturn( false );
        Functions\expect( 'update_post_meta' )->once()->with( 10, 'my_key', 'my_value' );

        $result = $this->tool->execute( array(
            'title' => 'Test',
            'meta'  => array( array( 'key' => 'my_key', 'value' => 'my_value' ) ),
        ) );
        $this->assertTrue( $result['success'] );
    }

    public function test_featured_media_is_set(): void {
        Functions\expect( 'post_type_exists' )->andReturn( true );
        Functions\expect( 'current_user_can' )->andReturn( true );
        Functions\expect( 'wp_insert_post' )->andReturn( 10 );
        Functions\expect( 'wp_save_post_revision' )->once();
        Functions\when( 'is_wp_error' )->justReturn( false );
        Functions\expect( 'set_post_thumbnail' )->once()->with( 10, 99 );

        $result = $this->tool->execute( array( 'title' => 'Test', 'featured_media' => 99 ) );
        $this->assertTrue( $result['success'] );
    }
}
