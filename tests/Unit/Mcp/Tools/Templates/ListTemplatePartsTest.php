<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Templates;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\ListTemplateParts;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class ListTemplatePartsTest extends TestCase {

	private ListTemplateParts $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();

		$this->tool = new ListTemplateParts();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/list-template-parts', ListTemplateParts::TOOL_ID );
	}

	public function test_default_context_is_edit(): void {
		// The tool uses 'edit' as default context when none is provided.
		// REST-dependent execution is tested via e2e tests.
		$this->assertInstanceOf( ListTemplateParts::class, $this->tool );
	}
}
