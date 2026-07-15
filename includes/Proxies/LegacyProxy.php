<?php
/**
 * Legacy WordPress function proxy (escape hatch).
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Proxies;

defined( 'ABSPATH' ) || exit;

/**
 * Proxies hard-to-mock WordPress functions in tests.
 */
final class LegacyProxy {

	/**
	 * Call a global WordPress function.
	 *
	 * @param string $callback Callable function name.
	 * @param array  $args     Arguments.
	 * @return mixed
	 */
	public function call( string $callback, array $args = array() ) {
		return call_user_func_array( $callback, $args );
	}
}
