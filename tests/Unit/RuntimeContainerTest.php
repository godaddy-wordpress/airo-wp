<?php
/**
 * RuntimeContainer tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit;

use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\ContainerException;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\RuntimeContainer;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\Testing\Fixtures\SampleDependency;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\Testing\Fixtures\SampleService;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\Testing\Fixtures\SampleServiceWithInit;

/**
 * Tests for RuntimeContainer.
 */
final class RuntimeContainerTest extends TestCase {

	/**
	 * Resolves constructor dependencies.
	 */
	public function test_resolves_class_with_constructor_dependency(): void {
		$container = new RuntimeContainer( array() );
		$service   = $container->get( SampleService::class );
		$this->assertInstanceOf( SampleService::class, $service );
		$this->assertInstanceOf( SampleDependency::class, $service->dependency );
	}

	/**
	 * Calls init() with injected dependencies.
	 */
	public function test_init_method_receives_container_dependency(): void {
		$container = new RuntimeContainer( array() );
		$service   = $container->get( SampleServiceWithInit::class );
		$this->assertTrue( $service->initialized );
	}

	/**
	 * Returns cached singleton instances.
	 */
	public function test_returns_same_singleton_instance(): void {
		$container = new RuntimeContainer( array() );
		$a         = $container->get( SampleService::class );
		$b         = $container->get( SampleService::class );
		$this->assertSame( $a, $b );
	}

	/**
	 * Throws when class is outside allowed namespace.
	 */
	public function test_throws_on_unknown_class(): void {
		Functions\when( 'esc_html' )->returnArg();
		$this->expectException( ContainerException::class );
		$container = new RuntimeContainer( array() );
		$container->get( 'Not\\A\\Real\\Class' );
	}
}
