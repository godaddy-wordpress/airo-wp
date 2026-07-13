<?php
/**
 * Plugin orchestrator.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Blocks\Package as BlocksPackage;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Package as McpPackage;
use GoDaddy\WordPress\Plugins\AiroWp\Rest\Package as RestPackage;

/**
 * Static orchestrator — creates the container and boots domain packages.
 */
final class Plugin {

	/**
	 * Plugin container instance.
	 *
	 * @var Container|null
	 */
	private static $container = null;

	/**
	 * Prevent instantiation.
	 */
	private function __construct() {}

	/**
	 * Initialise the plugin.
	 *
	 * Creates the DI container and schedules package boot on plugins_loaded.
	 */
	public static function init(): void {
		self::$container = new Container();
		add_action( 'plugins_loaded', array( self::class, 'boot' ), 10 );
	}

	/**
	 * Boot all registered domain packages.
	 */
	public static function boot(): void {
		foreach ( self::packages() as $package_class ) {
			$package_class::init( self::$container );
		}
	}

	/**
	 * Registered domain packages.
	 *
	 * @return array<int, class-string<PackageInterface>>
	 */
	private static function packages(): array {
		return array(
			RestPackage::class,
			McpPackage::class,
			BlocksPackage::class,
		);
	}
}
