<?php
declare(strict_types=1);

/**
 * Minimal WP_Query stub and ListMedia unit tests.
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
    use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\ListMedia;
    use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

    final class ListMediaTest extends TestCase {

        private ListMedia $tool;

        protected function setUp(): void {
            parent::setUp();

            if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
                define( 'PHPUNIT_RUNNING', true );
            }

            Functions\when( '__' )->returnArg();
            Functions\when( 'sanitize_text_field' )->returnArg();
            Functions\when( 'sanitize_key' )->returnArg();
            Functions\when( 'sanitize_title' )->returnArg();
            Functions\when( 'sanitize_mime_type' )->returnArg();

            \WP_Query::$seed = array();
            unset( $GLOBALS['__lp_current_post'] );
            Functions\when( 'get_post' )->alias( fn() => $GLOBALS['__lp_current_post'] ?? null );
            Functions\when( 'wp_reset_postdata' )->justReturn( null );
            Functions\when( 'wp_get_attachment_metadata' )->justReturn( false );
            Functions\when( 'get_attached_file' )->justReturn( false );
            Functions\when( 'get_post_meta' )->justReturn( '' );
            Functions\when( 'wp_get_attachment_url' )->justReturn( 'https://example.com/x.jpg' );
            Functions\when( 'current_user_can' )->justReturn( true );

            $this->tool = new ListMedia();
        }

        protected function tearDown(): void {
            \WP_Query::$seed = array();
            unset( $GLOBALS['__lp_current_post'] );
            parent::tearDown();
        }

        public function test_tool_id_has_correct_prefix(): void {
            $this->assertSame( 'airo-wp/list-media', ListMedia::TOOL_ID );
        }

        public function test_empty_result_returns_pagination_structure(): void {
            $result = $this->tool->execute( array() );

            $this->assertArrayHasKey( 'media', $result );
            $this->assertArrayHasKey( 'total', $result );
            $this->assertArrayHasKey( 'total_pages', $result );
            $this->assertArrayHasKey( 'page', $result );
            $this->assertArrayHasKey( 'per_page', $result );
            $this->assertSame( array(), $result['media'] );
            $this->assertSame( 0, $result['total'] );
            $this->assertSame( 1, $result['total_pages'] );
            $this->assertSame( 1, $result['page'] );
            $this->assertSame( 10, $result['per_page'] );
        }

        public function test_add_filter_called_when_search_param_provided(): void {
            Functions\expect( 'add_filter' )
                ->once()
                ->with( 'posts_clauses', '_filter_query_attachment_filenames' );

            $result = $this->tool->execute( array( 'search' => 'photo' ) );

            $this->assertArrayHasKey( 'media', $result );
        }

        public function test_add_filter_not_called_when_search_param_absent(): void {
            Functions\expect( 'add_filter' )->never();

            $result = $this->tool->execute( array() );

            $this->assertArrayHasKey( 'media', $result );
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
