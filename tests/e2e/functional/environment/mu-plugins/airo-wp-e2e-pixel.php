<?php
declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

// Test environment only: an image whose URL carries no extension.
add_action(
	'init',
	static function () {
		if ( '/airo-wp-e2e/pixel' !== wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ) {
			return;
		}
		header( 'Content-Type: image/png' );
		echo base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==' );
		exit;
	},
	0
);
