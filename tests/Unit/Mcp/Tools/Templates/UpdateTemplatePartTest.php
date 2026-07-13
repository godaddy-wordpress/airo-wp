<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Templates;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\UpdateTemplatePart;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class UpdateTemplatePartTest extends TestCase {

	private UpdateTemplatePart $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new UpdateTemplatePart();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/update-template-part', UpdateTemplatePart::TOOL_ID );
	}

	public function test_missing_theme_returns_error(): void {
		$result = $this->tool->execute( array(
			'part_name' => 'header',
			'html'      => '<header>Content</header>',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Theme parameter is required', $result['message'] );
	}

	public function test_missing_id_and_part_name_returns_error(): void {
		$result = $this->tool->execute( array(
			'theme' => 'twentytwentyfive',
			'html'  => '<header>Content</header>',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'Either template part ID or part_name is required', $result['message'] );
	}

	public function test_missing_html_returns_error(): void {
		$result = $this->tool->execute( array(
			'theme'     => 'twentytwentyfive',
			'part_name' => 'header',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'HTML content is required', $result['message'] );
	}

	public function test_empty_html_returns_error(): void {
		$result = $this->tool->execute( array(
			'theme'     => 'twentytwentyfive',
			'part_name' => 'header',
			'html'      => '',
		) );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'HTML content is required', $result['message'] );
	}
}
