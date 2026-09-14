<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Pages;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\DeletePageRevision;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class DeletePageRevisionTest extends TestCase {

	private DeletePageRevision $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();

		$this->tool = new DeletePageRevision();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/delete-page-revision', DeletePageRevision::TOOL_ID );
	}

	public function test_missing_parent_returns_error(): void {
		$result = $this->tool->execute( array( 'id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Parent page ID is required', $result['message'] );
	}

	public function test_missing_id_returns_error(): void {
		$result = $this->tool->execute( array( 'parent' => 42 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Revision ID is required', $result['message'] );
	}

	public function test_non_page_parent_returns_error(): void {
		$parent            = new \stdClass();
		$parent->post_type = 'post';

		Functions\expect( 'get_post' )
			->once()
			->with( 42 )
			->andReturn( $parent );

		$result = $this->tool->execute( array( 'parent' => 42, 'id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( '42', $result['message'] );
	}

	public function test_revision_not_belonging_to_parent_returns_error(): void {
		$parent            = new \stdClass();
		$parent->post_type = 'page';

		$revision              = new \stdClass();
		$revision->ID          = 10;
		$revision->post_parent = 99; // Different parent.

		Functions\expect( 'get_post' )
			->once()
			->with( 42 )
			->andReturn( $parent );

		Functions\expect( 'wp_get_post_revision' )
			->once()
			->with( 10 )
			->andReturn( $revision );

		$result = $this->tool->execute( array( 'parent' => 42, 'id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'does not belong to the specified parent page', $result['message'] );
	}

	/**
	 * The deleted-revision object describes every field the tool actually returns.
	 */
	public function test_output_schema_describes_deleted_revision_fields(): void {
		$schema  = $this->call_private( $this->tool, 'get_output_schema' );
		$deleted = $schema['properties']['deleted'];

		foreach ( array( 'id', 'parent_id', 'author_id', 'date_created', 'title', 'content', 'slug' ) as $field ) {
			$this->assertArrayHasKey( $field, $deleted['properties'], "missing {$field}" );
		}
	}
}
