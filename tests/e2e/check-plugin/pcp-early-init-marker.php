<?php
/**
 * wp-cli --require marker: proves Plugin Check's CLI runner actually
 * early-initialized. CLI_Runner::allow_runtime_checks() returns false if the
 * object-cache.php drop-in is absent, causing PCP to SILENTLY omit all runtime
 * checks from a full run. This marker lets run-plugin-check.mjs distinguish
 * "ran and found nothing" from "silently never ran".
 *
 * @package AiroWp
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

WP_CLI::add_hook(
	'after_wp_load',
	static function () {
		$GLOBALS['pcp_early_init'] =
			class_exists( 'WordPress\\Plugin_Check\\Utilities\\Plugin_Request_Utility' )
			&& null !== \WordPress\Plugin_Check\Utilities\Plugin_Request_Utility::get_runner();
	}
);

register_shutdown_function(
	static function () {
		echo "\npcp_early_init=" . ( empty( $GLOBALS['pcp_early_init'] ) ? 'no' : 'yes' ) . "\n";
	}
);
