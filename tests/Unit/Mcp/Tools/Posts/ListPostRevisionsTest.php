<?php
declare(strict_types=1);

/**
 * Minimal WP_Query stub and ListPostRevisions unit tests.
 *
 * Brain Monkey does not define WP_Query. We provide a lightweight stub in the
 * global namespace using the bracket-form of namespace blocks so the test file
 * can include both the global-namespace stub and the namespaced test class.
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

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Posts {

	use Brain\Monkey\Functions;
	use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\ListPostRevisions;
	use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

	final class ListPostRevisionsTest extends TestCase {

		private ListPostRevisions $tool;

		protected function setUp(): void {
			parent::setUp();

			Functions\when( '__' )->returnArg();
			Functions\when( 'sanitize_text_field' )->returnArg();

			$this->tool = new ListPostRevisions();
		}

		public function test_tool_id_has_correct_prefix(): void {
			$this->assertSame( 'airo-wp/list-post-revisions', ListPostRevisions::TOOL_ID );
		}

		public function test_missing_parent_returns_error(): void {
			$result = $this->tool->execute( array() );

			$this->assertFalse( $result['success'] );
			$this->assertStringContainsString( 'Parent post ID is required', $result['message'] );
		}

		public function test_nonexistent_parent_returns_error(): void {
			Functions\expect( 'get_post' )
				->once()
				->with( 999 )
				->andReturn( null );

			$result = $this->tool->execute( array( 'parent' => 999 ) );

			$this->assertFalse( $result['success'] );
			$this->assertStringContainsString( '999', $result['message'] );
		}
	}
}
