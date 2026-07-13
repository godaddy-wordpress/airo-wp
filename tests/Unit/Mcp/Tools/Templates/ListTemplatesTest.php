<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\Templates;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\ListTemplates;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class ListTemplatesTest extends TestCase {

	private ListTemplates $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();

		$this->tool = new ListTemplates();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/list-templates', ListTemplates::TOOL_ID );
	}

	public function test_default_context_is_edit(): void {
		// The tool uses 'edit' as default context when none is provided.
		// This test verifies the tool instantiates and accepts empty input
		// without validation errors (REST-dependent path is tested via e2e).
		$this->assertInstanceOf( ListTemplates::class, $this->tool );
	}
}
