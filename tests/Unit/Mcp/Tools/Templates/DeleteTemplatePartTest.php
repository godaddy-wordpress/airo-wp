<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Templates;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\DeleteTemplatePart;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class DeleteTemplatePartTest extends TestCase {

	private DeleteTemplatePart $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new DeleteTemplatePart();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/delete-template-part', DeleteTemplatePart::TOOL_ID );
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

	public function test_theme_only_template_part_returns_success_with_no_reset(): void {
		$template_part              = new \stdClass();
		$template_part->id          = 'twentytwentyfive//header';
		$template_part->slug        = 'header';
		$template_part->theme       = 'twentytwentyfive';
		$template_part->source      = 'theme';
		$template_part->title       = 'Header';
		$template_part->description = 'Site header';
		$template_part->area        = 'header';
		$template_part->wp_id       = 0;

		Functions\expect( 'get_block_template' )
			->with( 'twentytwentyfive//header', 'wp_template_part' )
			->andReturn( $template_part );

		$result = $this->tool->execute( array( 'id' => 'twentytwentyfive//header' ) );

		$this->assertTrue( $result['success'] );
		$this->assertStringContainsString( 'theme-only', $result['message'] );
		$this->assertSame( 'theme-only', $result['deleted']['status'] );
	}
}
