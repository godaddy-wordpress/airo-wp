<?php
/**
 * REST domain package.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Rest;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\PackageInterface;
use GoDaddy\WordPress\Plugins\AiroWp\Rest\Endpoints\DraftPages;

/**
 * Registers all REST API endpoints.
 */
final class Package implements PackageInterface {

	/**
	 * Prevent instantiation.
	 */
	private function __construct() {}

	/**
	 * Initialise the REST package.
	 *
	 * @param Container $container Plugin container.
	 */
	public static function init( Container $container ): void {
		foreach ( self::endpoints() as $endpoint_class ) {
			$endpoint = $container->get( $endpoint_class );
			add_action( 'rest_api_init', array( $endpoint, 'register_routes' ) );
		}
	}

	/**
	 * Registered REST endpoint classes.
	 *
	 * @return array<int, class-string>
	 */
	private static function endpoints(): array {
		return array(
			DraftPages::class,
		);
	}
}
