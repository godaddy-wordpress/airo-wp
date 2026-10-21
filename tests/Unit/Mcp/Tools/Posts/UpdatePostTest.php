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
        Functions\when( 'current_user_can' )->justReturn( true );

        $result = $this->tool->execute( array( 'post_id' => 42 ) );
        $this->assertTrue( $result['success'] );
        $this->assertSame( 42, $result['post_id'] );
        $this->assertSame( array(), $result['updated_fields'] );
        $this->assertSame( array(), $result['updated_meta'] );
        $this->assertSame( array(), $result['skipped_meta'] );
    }

    public function test_successful_title_update(): void {
        $post        = new \stdClass();
        $post->ID    = 42;
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\when( 'current_user_can' )->justReturn( true );
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
        Functions\when( 'current_user_can' )->justReturn( true );
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
        Functions\when( 'current_user_can' )->justReturn( true );
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
        Functions\when( 'current_user_can' )->justReturn( true );
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
        Functions\when( 'current_user_can' )->justReturn( true );
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
        $this->assertSame( 'Post updated successfully', $result['message'] );
    }

    public function test_permission_denied_returns_error(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'post';
        $post->post_status = 'draft';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\expect( 'current_user_can' )->with( 'edit_post', 42 )->andReturn( false );
        Functions\expect( 'wp_update_post' )->never();
        Functions\expect( 'update_post_meta' )->never();

        $result = $this->tool->execute( array( 'post_id' => 42, 'title' => 'Hijack' ) );

        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'permission', $result['message'] );
    }

    public function test_publish_transition_requires_post_type_publish_cap(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'page';
        $post->post_status = 'draft';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );

        $type_obj      = new \stdClass();
        $type_obj->cap = (object) array( 'publish_posts' => 'publish_pages' );
        Functions\expect( 'get_post_type_object' )->with( 'page' )->andReturn( $type_obj );

        Functions\when( 'current_user_can' )->alias(
            function ( $cap ) {
                return 'publish_pages' !== $cap;
            }
        );
        Functions\expect( 'wp_update_post' )->never();

        $result = $this->tool->execute( array( 'post_id' => 42, 'status' => 'publish' ) );

        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'publish', $result['message'] );
    }

    public function test_publish_transition_allowed_with_publish_cap(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'post';
        $post->post_status = 'draft';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );

        $type_obj      = new \stdClass();
        $type_obj->cap = (object) array( 'publish_posts' => 'publish_posts' );
        Functions\expect( 'get_post_type_object' )->with( 'post' )->andReturn( $type_obj );
        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\expect( 'wp_update_post' )->once()->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'post_id' => 42, 'status' => 'publish' ) );

        $this->assertTrue( $result['success'] );
        $this->assertContains( 'status', $result['updated_fields'] );
    }

    public function test_resaving_published_post_does_not_recheck_publish_cap(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'post';
        $post->post_status = 'publish';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\expect( 'get_post_type_object' )->never();
        Functions\expect( 'current_user_can' )->once()->with( 'edit_post', 42 )->andReturn( true );
        Functions\expect( 'wp_update_post' )->once()->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'post_id' => 42, 'status' => 'publish' ) );

        $this->assertTrue( $result['success'] );
    }

    public function test_meta_key_the_user_may_not_edit_is_skipped(): void {
        $post     = new \stdClass();
        $post->ID = 42;
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        // map_meta_cap() denies edit_post_meta for protected keys that have no auth callback.
        Functions\when( 'current_user_can' )->alias(
            static function ( $cap, ...$args ) {
                return ! ( 'edit_post_meta' === $cap && '_wp_page_template' === ( $args[1] ?? null ) );
            }
        );
        Functions\expect( 'wp_update_post' )->once()->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );
        Functions\expect( 'update_post_meta' )->once()->with( 42, 'my_key', 'my_value' )->andReturn( true );

        $result = $this->tool->execute( array(
            'post_id' => 42,
            'meta'    => array(
                array( 'key' => '_wp_page_template', 'value' => 'evil.php' ),
                array( 'key' => 'my_key', 'value' => 'my_value' ),
            ),
        ) );

        $this->assertTrue( $result['success'] );
        $this->assertSame( array( 'my_key' ), $result['updated_meta'] );
        $this->assertSame( array( '_wp_page_template' ), $result['skipped_meta'] );
        $this->assertStringContainsString( 'skipped', $result['message'] );
        $this->assertStringContainsString( '_wp_page_template', $result['message'] );
    }

    public function test_date_sets_local_and_gmt_dates_with_edit_date(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'post';
        $post->post_status = 'draft';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\expect( 'get_gmt_from_date' )->once()->with( '2099-01-01 12:00:00' )->andReturn( '2099-01-01 10:00:00' );
        Functions\expect( 'get_date_from_gmt' )->once()->with( '2099-01-01 10:00:00' )->andReturn( '2099-01-01 12:00:00' );
        Functions\expect( 'wp_update_post' )
            ->once()
            ->with(
                \Mockery::on(
                    static function ( $data ) {
                        return '2099-01-01 12:00:00' === $data['post_date']
                            && '2099-01-01 10:00:00' === $data['post_date_gmt']
                            && true === $data['edit_date'];
                    }
                ),
                true
            )
            ->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'post_id' => 42, 'date' => '2099-01-01 12:00:00' ) );

        $this->assertTrue( $result['success'] );
        $this->assertContains( 'date', $result['updated_fields'] );
    }

    public function test_moving_scheduled_post_date_to_the_past_requires_publish_cap(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'page';
        $post->post_status = 'future';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );

        $type_obj      = new \stdClass();
        $type_obj->cap = (object) array( 'publish_posts' => 'publish_pages' );
        Functions\expect( 'get_post_type_object' )->with( 'page' )->andReturn( $type_obj );
        Functions\when( 'current_user_can' )->alias(
            static function ( $cap ) {
                return 'publish_pages' !== $cap;
            }
        );
        Functions\when( 'get_gmt_from_date' )->justReturn( '2000-01-01 00:00:00' );
        Functions\when( 'get_date_from_gmt' )->returnArg();
        Functions\expect( 'wp_update_post' )->never();

        $result = $this->tool->execute( array( 'post_id' => 42, 'date' => '2000-01-01 00:00:00' ) );

        $this->assertFalse( $result['success'] );
        $this->assertStringContainsString( 'publish', $result['message'] );
    }

    public function test_moving_scheduled_post_date_to_the_past_allowed_with_publish_cap(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'post';
        $post->post_status = 'future';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );

        $type_obj      = new \stdClass();
        $type_obj->cap = (object) array( 'publish_posts' => 'publish_posts' );
        Functions\expect( 'get_post_type_object' )->with( 'post' )->andReturn( $type_obj );
        Functions\when( 'current_user_can' )->justReturn( true );
        Functions\when( 'get_gmt_from_date' )->justReturn( '2000-01-01 00:00:00' );
        Functions\when( 'get_date_from_gmt' )->returnArg();
        Functions\expect( 'wp_update_post' )->once()->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'post_id' => 42, 'date' => '2000-01-01 00:00:00' ) );

        $this->assertTrue( $result['success'] );
    }

    public function test_rescheduling_a_future_post_does_not_check_publish_cap(): void {
        $post              = new \stdClass();
        $post->ID          = 42;
        $post->post_type   = 'post';
        $post->post_status = 'future';
        Functions\expect( 'get_post' )->with( 42 )->andReturn( $post );
        Functions\expect( 'get_post_type_object' )->never();
        Functions\expect( 'current_user_can' )->once()->with( 'edit_post', 42 )->andReturn( true );
        Functions\when( 'get_gmt_from_date' )->justReturn( '2099-01-01 00:00:00' );
        Functions\when( 'get_date_from_gmt' )->returnArg();
        Functions\expect( 'wp_update_post' )->once()->andReturn( 42 );
        Functions\when( 'is_wp_error' )->justReturn( false );

        $result = $this->tool->execute( array( 'post_id' => 42, 'date' => '2099-01-01 00:00:00' ) );

        $this->assertTrue( $result['success'] );
    }
}
