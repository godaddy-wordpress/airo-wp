<?php
declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Tools\GlobalStyles;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\GetBlockPatterns;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

final class GetBlockPatternsTest extends TestCase {

	private GetBlockPatterns $tool;

	protected function setUp(): void {
		parent::setUp();

		if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
			define( 'PHPUNIT_RUNNING', true );
		}

		Functions\when( '__' )->returnArg();

		$this->tool = new GetBlockPatterns();
	}

	public function test_tool_id_has_correct_prefix(): void {
		$this->assertSame( 'airo-wp/get-block-patterns', GetBlockPatterns::TOOL_ID );
	}

	public function test_registry_unavailable_returns_error(): void {
		// WP_Block_Patterns_Registry does not exist in the PHPUnit environment,
		// so class_exists check fails and execute() returns the error response.
		$result = $this->tool->execute( array() );

		$this->assertFalse( $result['success'] );
		$this->assertStringContainsString( 'not available', $result['message'] );
	}
}
