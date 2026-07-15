<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\UpdatePost;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UpdatePostTest extends TestCase {

    private UpdatePost $tool;

    protected function setUp(): void {
        parent::setUp();

        if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
            define( 'PHPUNIT_RUNNING', true );
        }

        Functions\when( '__' )->returnArg();
        Functions\when( 'sanitize_text_field' )->returnArg();
        Functions\when( 'sanitize_textarea_field' )->returnArg();
        Functions\when( 'sanitize_title' )->returnArg();
        Functions\when( 'wp_kses_post' )->returnArg();

        $this->tool = new UpdatePost();
    }

    public function test_tool_id_has_correct_prefix(): void {
        $this->assertSame( 'airo-wp/update-post', UpdatePost::TOOL_ID );
    }

    public function test_missing_post_id_returns_error(): void {
        $result = $this->tool->execute( array() );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'Post ID is required', $result['message'] );
    }

    public function test_nonexistent_post_returns_error(): void {
        Functions\expect( 'get_post' )->with( 999 )->andReturn( null );

        $result = $this->tool->execute( array( 'post_id' => 999 ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( '999', $result['message'] );
    }

    public function test_no_updates_returns_success_with_empty_fields(): void {
        $post        = new \stdClass();
        $post->ID    = 42;
        $post->post_title = 'Existing';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );

        $result = $this->tool->execute( array( 'post_id' => 42 ) );
        $this->assertTrue( $result['success'] );
        $this->assertSame( 42, $result['post_id'] );
        $this->assertSame( array(), $result['updated_fields'] );
        $this->assertSame( array(), $result['updated_meta'] );
    }

    public function test_successful_title_update(): void {
        $post        = new \stdClass();
        $post->ID    = 42;
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\expect( 'wp_update_post' )->once()->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'post_id' => 42, 'title' => 'New Title' ) );
        $this->assertTrue( $result['success'] );
        $this->assertSame( 42, $result['post_id'] );
        $this->assertContains( 'title', $result['updated_fields'] );
    }

    public function test_invalid_slug_returns_error(): void {
        $post     = new \stdClass();
        $post->ID = 42;
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\when( 'sanitize_title' )->justReturn( '' );

        $result = $this->tool->execute( array( 'post_id' => 42, 'slug' => '!!!' ) );
        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'slug', $result['message'] );
        $this->assertStringContainsString( 'empty', strtolower( $result['message'] ) );
    }

    public function test_slug_is_sanitized_into_post_name(): void {
        $post     = new \stdClass();
        $post->ID = 42;
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\when( 'sanitize_title' )->alias(
            static function ( $title ) {
                $s = strtolower( (string) $title );
                $s = preg_replace( '/[^a-z0-9]+/', '-', $s );
                return trim( (string) $s, '-' );
            }
        );

        Functions\expect( 'wp_update_post' )
            ->once()
            ->with(
                \Mockery::on(
                    static function ( $data ) {
                        return 42 === $data['ID']
                            && isset( $data['post_name'] )
                            && 'sale-cafe-nino' === $data['post_name'];
                    }
                ),
                true
            )
            ->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array(
            'post_id' => 42,
            'slug'    => 'Sale!!! Cafe Nino',
        ) );

        $this->assertTrue( $result['success'] );
        $this->assertContains( 'slug', $result['updated_fields'] );
    }

    public function test_empty_slug_does_nothing(): void {
        $post     = new \stdClass();
        $post->ID = 42;
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\expect( 'wp_update_post' )->never();

        $result = $this->tool->execute( array(
            'post_id' => 42,
            'slug'    => '',
        ) );

        $this->assertTrue( $result['success'] );
        $this->assertSame( array(), $result['updated_fields'] );
    }

    public function test_meta_update_tracks_keys(): void {
        $post     = new \stdClass();
        $post->ID = 42;
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\expect( 'wp_update_post' )->once()->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );
        Functions\expect( 'update_post_meta' )->once()->with( 42, 'my_key', 'my_value' )->andReturn( true );

        $result = $this->tool->execute( array(
            'post_id' => 42,
            'title'   => 'Test',
            'meta'    => array( array( 'key' => 'my_key', 'value' => 'my_value' ) ),
        ) );
        $this->assertTrue( $result['success'] );
        $this->assertContains( 'my_key', $result['updated_meta'] );
    }
}
