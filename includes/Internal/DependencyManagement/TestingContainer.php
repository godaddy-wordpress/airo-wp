<?php
/**
 * Testing container with replace/reset support.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement;

defined( 'ABSPATH' ) || exit;

/**
 * Container for unit tests.
 */
class TestingContainer extends RuntimeContainer {

	/**
	 * Replace a resolved instance.
	 *
	 * @param string $class_name Class name.
	 * @param object $instance   Replacement instance.
	 */
	public function replace( string $class_name, object $instance ): void {
		$class_name                          = trim( $class_name, '\\' );
		$this->resolved_cache[ $class_name ] = $instance;
	}

	/**
	 * Reset resolved instances to initial cache.
	 */
	public function reset(): void {
		$this->resolved_cache = $this->initial_resolved_cache;
	}
}
