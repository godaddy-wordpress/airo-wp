<?php
/**
 * Domain package interface.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp;

defined( 'ABSPATH' ) || exit;

/**
 * Contract for domain packages loaded by Packages.
 */
interface PackageInterface {

	/**
	 * Initialize the package.
	 *
	 * @param Container $container Plugin container.
	 */
	public static function init( Container $container ): void;
}
