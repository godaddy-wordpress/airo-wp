<?php
/**
 * Sample service for container tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Internal\Testing\Fixtures;

/**
 * Service with constructor injection.
 */
class SampleService {

	/** @var SampleDependency */
	public $dependency;

	/**
	 * @param SampleDependency $dependency Dependency.
	 */
	public function __construct( SampleDependency $dependency ) {
		$this->dependency = $dependency;
	}
}
