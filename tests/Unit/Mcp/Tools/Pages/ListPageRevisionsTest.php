<?php
declare(strict_types=1);

/**
 * Minimal WP_Query stub and ListPageRevisions unit tests.
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

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Pages {

	use Brain\Monkey\Functions;
	use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\ListPageRevisions;
	use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

	final class ListPageRevisionsTest extends TestCase {

		private ListPageRevisions $tool;

		protected function setUp(): void {
			parent::setUp();

			Functions\when( '__' )->returnArg();
			Functions\when( 'sanitize_text_field' )->returnArg();

			$this->tool = new ListPageRevisions();
		}

		public function test_tool_id_has_correct_prefix(): void {
			$this->assertSame( 'airo-wp/list-page-revisions', ListPageRevisions::TOOL_ID );
		}

		public function test_missing_parent_returns_error(): void {
			$result = $this->tool->execute( array() );

			$this->assertFalse( $result['success'] );
			$this->assertStringContainsString( 'Parent page ID is required', $result['message'] );
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

		public function test_non_page_parent_returns_error(): void {
			$parent            = new \stdClass();
			$parent->post_type = 'post';

			Functions\expect( 'get_post' )
				->once()
				->with( 42 )
				->andReturn( $parent );

			$result = $this->tool->execute( array( 'parent' => 42 ) );

			$this->assertFalse( $result['success'] );
			$this->assertStringContainsString( '42', $result['message'] );
			$this->assertStringContainsString( 'not a page', $result['message'] );
		}

		public function test_valid_page_returns_empty_revisions(): void {
			$parent            = new \stdClass();
			$parent->post_type = 'page';

			Functions\expect( 'get_post' )
				->once()
				->with( 10 )
				->andReturn( $parent );

			$result = $this->tool->execute( array( 'parent' => 10 ) );

			$this->assertIsArray( $result['revisions'] );
			$this->assertEmpty( $result['revisions'] );
			$this->assertSame( 0, $result['total'] );
			$this->assertSame( 0, $result['total_pages'] );
			$this->assertSame( 1, $result['page'] );
			$this->assertSame( 10, $result['per_page'] );
		}
	}
}
