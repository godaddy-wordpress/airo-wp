<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Templates;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\DeleteTemplate;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class DeleteTemplateTest extends TestCase {

	private DeleteTemplate $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new DeleteTemplate();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/delete-template', DeleteTemplate::TOOL_ID );
	}

	public function test_missing_id_returns_error(): void {
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Template ID is required', $result['message'] );
	}

	public function test_empty_id_returns_error(): void {
		$result = $this->tool->execute( array( 'id' => '' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Template ID is required', $result['message'] );
	}

	public function test_template_not_found_returns_error(): void {
		Functions\expect( 'get_block_template' )
			->with( 'mytheme//nonexistent', 'wp_template' )
			->andReturn( null );

		$result = $this->tool->execute( array( 'id' => 'mytheme//nonexistent' ) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not found', $result['message'] );
	}

	public function test_theme_only_template_returns_success_with_no_reset(): void {
		$template         = new \stdClass();
		$template->id     = 'twentytwentyfive//page';
		$template->slug   = 'page';
		$template->theme  = 'twentytwentyfive';
		$template->source = 'theme';
		$template->title  = 'Page';
		$template->description = 'Page template';
		$template->wp_id  = 0;

		Functions\expect( 'get_block_template' )
			->with( 'twentytwentyfive//page', 'wp_template' )
			->andReturn( $template );

		$result = $this->tool->execute( array( 'id' => 'twentytwentyfive//page' ) );

		$this->assertTrue( $result['success'] );
		$this->assertStringContainsString( 'theme-only', $result['message'] );
		$this->assertSame( 'theme-only', $result['deleted']['status'] );
	}
}
