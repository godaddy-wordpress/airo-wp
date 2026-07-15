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

            $this->tool = new ListMedia();
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
    }
}
