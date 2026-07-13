<?php
/**
 * Sample service with init() for container tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Internal\Testing\Fixtures;

/**
 * Service with init() injection.
 */
class SampleServiceWithInit {

	/** @var bool */
	public $initialized = false;

	/**
	 * @param SampleDependency $dependency Dependency.
	 */
	public function init( SampleDependency $dependency ): void {
		$this->initialized = ( $dependency instanceof SampleDependency );
	}
}
