<?php
declare(strict_types=1);

/**
 * Minimal WP_Query stub and ListNavigations unit tests.
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

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Navigation {

	use Brain\Monkey\Functions;
	use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigations;
	use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

	final class ListNavigationsTest extends TestCase {

		private ListNavigations $tool;

		protected function setUp(): void {
			parent::setUp();

			if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
				define( 'PHPUNIT_RUNNING', true );
			}

			Functions\when( '__' )->returnArg();
			Functions\when( 'sanitize_text_field' )->returnArg();
			Functions\when( 'sanitize_title' )->returnArg();

			$this->tool = new ListNavigations();
		}

		public function test_tool_id_has_correct_prefix(): void {
			$this->assertSame( 'airo-wp/list-navigations', ListNavigations::TOOL_ID );
		}

		public function test_empty_result_returns_pagination_structure(): void {
			$result = $this->tool->execute( array() );

			$this->assertArrayHasKey( 'navigations', $result );
			$this->assertArrayHasKey( 'total', $result );
			$this->assertArrayHasKey( 'total_pages', $result );
			$this->assertArrayHasKey( 'page', $result );
			$this->assertArrayHasKey( 'per_page', $result );
			$this->assertSame( array(), $result['navigations'] );
			$this->assertSame( 0, $result['total'] );
			$this->assertSame( 1, $result['page'] );
			$this->assertSame( 10, $result['per_page'] );
		}
	}
}
