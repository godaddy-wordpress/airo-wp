<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Templates;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\ListTemplatePartRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class ListTemplatePartRevisionsTest extends TestCase {

	private ListTemplatePartRevisions $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new ListTemplatePartRevisions();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/list-template-part-revisions', ListTemplatePartRevisions::TOOL_ID );
	}

	public function test_missing_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Template part ID is required', $result['message'] );
	}

	public function test_empty_id_returns_error(): void {
		$result = $this->tool->execute( array( 'id' => '' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Template part ID is required', $result['message'] );
	}

	public function test_template_part_not_found_returns_error(): void {
		Functions\expect( 'get_block_template' )
			->with( 'mytheme//nonexistent', 'wp_template_part' )
			->andReturn( null );

		$result = $this->tool->execute( array( 'id' => 'mytheme//nonexistent' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found', $result['message'] );
	}

	public function test_template_part_without_wp_id_returns_empty_revisions(): void {
		$template_part        = new \stdClass();
		$template_part->wp_id = 0;

		Functions\expect( 'get_block_template' )
			->with( 'twentytwentyfive//header', 'wp_template_part' )
			->andReturn( $template_part );

		$result = $this->tool->execute( array( 'id' => 'twentytwentyfive//header' ) );

		$this->assertTrue( $result['success'] );
		$this->assertSame( array(), $result['revisions'] );
		$this->assertSame( 0, $result['total'] );
		$this->assertStringContainsString( 'No revisions found', $result['message'] );
	}
}
