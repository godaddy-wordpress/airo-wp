<?php
declare(strict_types=1);

/**
 * Minimal WP_Query stub and GetAllMedia unit tests.
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses -- intentional stub + test class split.

namespace {
    if ( ! class_exists( 'WP_Query' ) ) {
        // phpcs:disable
        class WP_Query {
            public int $found_posts   = 0;
            public int $max_num_pages = 0;

            public function __construct( array $args = array() ) {}

            public function have_posts(): bool {
                return false;
            }

            public function the_post(): void {}
        }
        // phpcs:enable
    }
}

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Media {

    use Brain\Monkey\Functions;
    use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\GetAllMedia;
    use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

    final class GetAllMediaTest extends TestCase {

        private GetAllMedia $tool;

        protected function setUp(): void {
            parent::setUp();

            if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
                define( 'PHPUNIT_RUNNING', true );
            }

            Functions\when( '__' )->returnArg();

            \WP_Query::$seed = array();
            unset( $GLOBALS['__lp_current_post'] );
            Functions\when( 'get_post' )->alias( fn() => $GLOBALS['__lp_current_post'] ?? null );
            Functions\when( 'wp_reset_postdata' )->justReturn( null );
            Functions\when( 'wp_get_attachment_metadata' )->justReturn( false );
            Functions\when( 'get_attached_file' )->justReturn( false );
            Functions\when( 'get_post_meta' )->justReturn( '' );
            Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.com/x.jpg' );
            Functions\when( 'current_user_can' )->justReturn( true );

            $this->tool = new GetAllMedia();
        }

        protected function tearDown(): void {
            \WP_Query::$seed = array();
            unset( $GLOBALS['__lp_current_post'] );
            parent::tearDown();
        }

        public function test_tool_id_has_correct_prefix(): void {
            $this->assertSame( 'airo-wp/get-all-media', GetAllMedia::TOOL_ID );
        }

        public function test_returns_empty_when_no_media(): void {
            $result = $this->tool->execute( array() );

            $this->assertSame( array(), $result['media'] );
            $this->assertSame( 0, $result['total'] );
        }

        private function make_attachment( int $id ): \WP_Post {
            return new \WP_Post(
                array(
                    'ID'         => $id,
                    'post_title' => 'img-' . $id,
                    'post_name'  => 'img-' . $id,
                )
            );
        }

        public function test_unreadable_attachments_are_skipped(): void {
            \WP_Query::$seed = array( $this->make_attachment( 1 ), $this->make_attachment( 2 ) );
            Functions\when( 'current_user_can' )->alias(
                function ( $cap, $id ) {
                    return ! ( 'read_post' === $cap && 2 === $id );
                }
            );

            $result = $this->tool->execute( array() );

            $this->assertCount( 1, $result['media'] );
            $this->assertSame( 1, $result['media'][0]['id'] );
            $this->assertSame( 1, $result['total'] );
        }

        public function test_attachment_parents_are_primed_in_one_call(): void {
            $first              = $this->make_attachment( 1 );
            $first->post_parent = 10;
            $second              = $this->make_attachment( 2 );
            $second->post_parent = 11;
            \WP_Query::$seed = array( $first, $second );
            Functions\expect( '_prime_post_caches' )->once()->with( array( 10, 11 ), false, false );

            $result = $this->tool->execute( array() );

            $this->assertCount( 2, $result['media'] );
        }

        public function test_readable_attachments_are_all_returned(): void {
            \WP_Query::$seed = array( $this->make_attachment( 1 ), $this->make_attachment( 2 ) );

            $result = $this->tool->execute( array() );

            $this->assertCount( 2, $result['media'] );
        }
    }
}
