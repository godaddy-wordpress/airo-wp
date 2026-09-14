<?php
declare(strict_types=1);

/**
 * Minimal WP_Query stub and ListNavigationRevisions unit tests.
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
	use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigationRevisions;
	use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

	final class ListNavigationRevisionsTest extends TestCase {

		private ListNavigationRevisions $tool;

		protected function setUp(): void {
			parent::setUp();

			if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
				define( 'PHPUNIT_RUNNING', true );
			}

			Functions\when( '__' )->returnArg();
			Functions\when( 'sanitize_text_field' )->returnArg();

			$this->tool = new ListNavigationRevisions();
		}

		public function test_tool_id_has_correct_prefix(): void {
			$this->assertSame( 'airo-wp/list-navigation-revisions', ListNavigationRevisions::TOOL_ID );
		}

		public function test_missing_parent_returns_error(): void {
			$result = $this->tool->execute( array() );

			$this->assertFalse( $result['success'] );
			$this->assertStringContainsString( 'required', $result['message'] );
		}

		public function test_non_existent_parent_returns_error(): void {
			Functions\when( 'get_post' )->justReturn( null );

			$result = $this->tool->execute( array( 'parent' => 99999 ) );

			$this->assertFalse( $result['success'] );
		}

		public function test_valid_parent_returns_empty_revisions(): void {
			$post            = \Mockery::mock( 'WP_Post' );
			$post->post_type = 'wp_navigation';

			Functions\when( 'get_post' )->justReturn( $post );
			Functions\when( 'current_user_can' )->justReturn( true );

			$result = $this->tool->execute( array( 'parent' => 1 ) );

			$this->assertTrue( $result['success'] );
			$this->assertArrayHasKey( 'revisions', $result );
			$this->assertSame( array(), $result['revisions'] );
			$this->assertSame( 0, $result['total'] );
		}
		/**
		 * The revisions array declares its item shape rather than being an untyped array.
		 */
		public function test_output_schema_describes_revision_items(): void {
			$schema    = $this->call_private( $this->tool, 'get_output_schema' );
			$revisions = $schema['properties']['revisions'];

			$this->assertArrayHasKey( 'items', $revisions, 'revisions lost its item schema' );
			foreach ( array( 'id', 'author', 'date', 'date_gmt', 'guid', 'modified', 'modified_gmt', 'parent', 'slug', 'title', 'content' ) as $field ) {
				$this->assertArrayHasKey( $field, $revisions['items']['properties'], "missing {$field}" );
			}
		}
	}
}
