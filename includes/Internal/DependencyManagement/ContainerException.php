<?php
/**
 * Container exception.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Internal\DependencyManagement;

defined( 'ABSPATH' ) || exit;

use Exception;

/**
 * Thrown when the dependency injection container cannot resolve a class.
 */
class ContainerException extends Exception {}
