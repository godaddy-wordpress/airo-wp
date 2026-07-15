<?php
/**
 * Global plugin functions (Composer files autoload entry).
 *
 * Add domain-specific function files here as the plugin grows.
 *
 * @package airo-wp
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( file_exists( dirname( __DIR__ ) . '/data/icon-svg-library.php' ) ) {
	require_once dirname( __DIR__ ) . '/data/icon-svg-library.php';
}

if ( file_exists( __DIR__ . '/blocks/index.php' ) ) {
	require_once __DIR__ . '/blocks/index.php';
}

// Hand-written plugin functions go here.
