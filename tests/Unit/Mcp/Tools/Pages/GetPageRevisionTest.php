<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Pages;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\GetPageRevision;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetPageRevisionTest extends TestCase {

	private GetPageRevision $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'current_user_can' )->justReturn( true );

		$this->tool = new GetPageRevision();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/get-page-revision', GetPageRevision::TOOL_ID );
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

	public function test_revision_not_found_returns_error(): void {
		$parent            = new \stdClass();
		$parent->post_type = 'page';

		Functions\expect( 'get_post' )
			->once()
			->with( 42 )
			->andReturn( $parent );

		Functions\expect( 'wp_get_post_revision' )
			->once()
			->with( 10 )
			->andReturn( null );

		$result = $this->tool->execute( array( 'parent' => 42, 'id' => 10 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( '10', $result['message'] );
	}

	public function test_permission_denied_on_parent_returns_error(): void {
		$parent            = new \stdClass();
		$parent->ID        = 5;
		$parent->post_type = 'page';

		Functions\expect( 'get_post' )->with( 5 )->andReturn( $parent );
		Functions\when( 'current_user_can' )->alias(
			function ( $cap, $id = null ) {
				return ! ( 'edit_post' === $cap && 5 === $id );
			}
		);
		Functions\expect( 'wp_get_post_revision' )->never();

		$result = $this->tool->execute( array( 'parent' => 5, 'id' => 6 ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'permission', $result['message'] );
	}
}
