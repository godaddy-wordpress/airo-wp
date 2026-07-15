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

            $this->tool = new GetAllMedia();
        }

        public function test_tool_id_has_correct_prefix(): void {
            $this->assertSame( 'airo-wp/get-all-media', GetAllMedia::TOOL_ID );
        }

        public function test_returns_empty_when_no_media(): void {
            $result = $this->tool->execute( array() );

            $this->assertSame( array(), $result['media'] );
            $this->assertSame( 0, $result['total'] );
        }
    }
}
