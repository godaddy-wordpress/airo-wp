<?php
/**
 * PHPUnit constants prepend file.
 *
 * Defines constants required before Composer's files autoload runs.
 * Composer's autoload triggers functions/index.php which guards against
 * direct access with `defined( 'ABSPATH' ) || exit;`. This file ensures
 * ABSPATH is defined before that guard is evaluated.
 *
 * Referenced via: composer test (auto_prepend_file directive).
 *
 * @package airo-wp
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/wordpress/' );
}

if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
	define( 'PHPUNIT_RUNNING', true );
}
