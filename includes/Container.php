<?php
/**
 * Plugin dependency injection container.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement\RuntimeContainer;
use GoDaddy\WordPress\Plugins\AiroWp\Dependencies\Psr\Container\ContainerInterface;

/**
 * PSR-11 compliant container facade.
 */
final class Container implements ContainerInterface {

	/**
	 * Underlying runtime container.
	 *
	 * @var RuntimeContainer
	 */
	private $container;

	/**
	 * Constructor.
	 *
	 * @param RuntimeContainer|null $runtime_container Optional pre-built container (for testing).
	 */
	public function __construct( ?RuntimeContainer $runtime_container = null ) {
		$this->container = $runtime_container ?? new RuntimeContainer(
			array(
				self::class               => $this,
				ContainerInterface::class => $this,
			)
		);
	}

	/**
	 * Get a service from the container.
	 *
	 * @param string $id Class name.
	 * @return mixed
	 */
	public function get( $id ) {
		return $this->container->get( $id );
	}

	/**
	 * Check if the container has a service.
	 *
	 * @param string $id Class name.
	 * @return bool
	 */
	public function has( $id ): bool {
		return $this->container->has( $id );
	}
}
