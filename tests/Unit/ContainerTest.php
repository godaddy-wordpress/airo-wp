<?php
/**
 * Container tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit;

use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for Container facade.
 */
final class ContainerTest extends TestCase {

	/**
	 * Container resolves itself.
	 */
	public function test_can_resolve_itself(): void {
		$container = new Container();
		$this->assertSame( $container, $container->get( Container::class ) );
	}
}
