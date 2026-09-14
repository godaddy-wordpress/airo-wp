<?php
/**
 * Base test case.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests;

use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Dependencies\Psr\Container\ContainerInterface;
use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\TestingContainer;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base test case with Brain Monkey setup.
 */
abstract class TestCase extends PHPUnitTestCase {

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down test environment.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Invoke a private or protected method on an object.
	 *
	 * Tool schemas are declared in private get_input_schema() / get_output_schema()
	 * methods, and wp_register_ability() cannot be stubbed (the Strauss-bundled
	 * Abilities API defines it before Patchwork loads), so reflection is the only
	 * way to assert a tool's declared schema contract.
	 *
	 * @param object $target      Object to call on.
	 * @param string $method_name Method name.
	 * @param array  $args        Positional arguments.
	 * @return mixed
	 */
	protected function call_private( object $target, string $method_name, array $args = array() ) {
		$method = new \ReflectionMethod( $target, $method_name );
		$method->setAccessible( true );

		return $method->invokeArgs( $target, $args );
	}

	/**
	 * Build a Container backed by a TestingContainer, with optional replacements.
	 *
	 * Container self-registers itself (and ContainerInterface) only on its default
	 * construction path. When a pre-built runtime container is supplied — the test
	 * seam — it does not, so any service that takes a Container parameter fails to
	 * resolve. This helper seeds those two entries so the test seam behaves like the
	 * production path.
	 *
	 * @param array<string, object> $replacements Instances to substitute, keyed by class name.
	 * @return Container
	 */
	protected function make_container( array $replacements = array() ): Container {
		$testing_container = new TestingContainer( array() );
		$container         = new Container( $testing_container );

		$testing_container->replace( Container::class, $container );
		$testing_container->replace( ContainerInterface::class, $container );

		foreach ( $replacements as $class_name => $instance ) {
			$testing_container->replace( $class_name, $instance );
		}

		return $container;
	}
}
