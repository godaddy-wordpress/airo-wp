<?php
/**
 * PHPUnit bootstrap.
 *
 * @package airo-wp
 */

declare(strict_types=1);

// ABSPATH must be defined before autoload so that functions/index.php
// (loaded via Composer files autoload) does not call exit.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/wordpress/' );
}

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'AIRO_WP_VERSION' ) ) {
	define( 'AIRO_WP_VERSION', '0.1.0' );
}

if ( ! defined( 'AIRO_WP_PLUGIN_FILE' ) ) {
	define( 'AIRO_WP_PLUGIN_FILE', dirname( __DIR__ ) . '/airo-wp.php' );
}

if ( ! defined( 'AIRO_WP_PLUGIN_DIR' ) ) {
	define( 'AIRO_WP_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'AIRO_WP_PLUGIN_URL' ) ) {
	define( 'AIRO_WP_PLUGIN_URL', 'http://example.org/wp-content/plugins/airo-wp/' );
}

if ( ! defined( 'PHPUNIT_RUNNING' ) ) {
	define( 'PHPUNIT_RUNNING', true );
}

if ( ! class_exists( 'WP_Post' ) ) {
	// phpcs:disable
	/**
	 * Shared WP_Post stub for unit tests.
	 *
	 * Exposes public properties that ListPosts::build_post_data() reads.
	 */
	class WP_Post {
		public int    $ID            = 0;
		public string $post_title    = '';
		public string $post_name     = '';
		public string $post_status   = '';
		public int    $post_parent   = 0;
		public int    $menu_order    = 0;
		public string $post_content  = '';
		public string $post_excerpt  = '';
		public string $post_author   = '';
		public string $post_date     = '';
		public string $post_modified = '';

		public function __construct( array $props = array() ) {
			foreach ( $props as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
	// phpcs:enable
}

if ( ! class_exists( 'WP_Query' ) ) {
	// phpcs:disable
	/**
	 * Shared WP_Query stub for unit tests.
	 *
	 * Supports the seed / last_args pattern used by ListPosts tests, while
	 * remaining compatible with the minimal stub expected by other test files.
	 */
	class WP_Query {
		/** @var array Posts to iterate over in this instance (set from static seed). */
		private array $posts = array();

		/** @var int Current iteration cursor. */
		private int $cursor = 0;

		/** @var array|null Last constructor args passed to any WP_Query instance. */
		public static ?array $last_args = null;

		/** @var array Posts to seed into the next WP_Query instance. */
		public static array $seed = array();

		public int $found_posts   = 0;
		public int $max_num_pages = 0;

		public function __construct( array $args = array() ) {
			self::$last_args   = $args;
			$this->posts       = self::$seed;
			$this->cursor      = 0;
			$this->found_posts = count( $this->posts );
		}

		public function have_posts(): bool {
			return $this->cursor < count( $this->posts );
		}

		public function the_post(): void {
			$GLOBALS['__lp_current_post'] = $this->posts[ $this->cursor ];
			$this->cursor++;
		}
	}
	// phpcs:enable
}

if ( ! class_exists( 'WP_Error' ) ) {
	// phpcs:disable
	/**
	 * Minimal WP_Error stub for unit tests.
	 */
	class WP_Error {
		/** @var string */
		private string $code;

		/** @var string */
		private string $message;

		/**
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 */
		public function __construct( string $code = '', string $message = '' ) {
			$this->code    = $code;
			$this->message = $message;
		}

		/** @return string */
		public function get_error_code(): string {
			return $this->code;
		}

		/** @return string */
		public function get_error_message(): string {
			return $this->message;
		}
	}
	// phpcs:enable
}

if ( ! class_exists( 'WP_Ajax_Upgrader_Skin' ) ) {
	// phpcs:disable
	/**
	 * Minimal WP_Ajax_Upgrader_Skin stub for unit tests.
	 */
	class WP_Ajax_Upgrader_Skin {}
	// phpcs:enable
}

if ( ! class_exists( 'Plugin_Upgrader' ) ) {
	// phpcs:disable
	/**
	 * Plugin upgrader stub for unit tests.
	 * Tests set Plugin_Upgrader::$next_install_result to drive install() return value.
	 */
	class Plugin_Upgrader {
		/**
		 * Per-test return value for install(). Default true (success).
		 * Assign a WP_Error or null/false to simulate failure.
		 *
		 * @var mixed
		 */
		public static $next_install_result = true;

		/**
		 * Last args passed to install().
		 *
		 * @var array|null
		 */
		public static $last_install_args = null;

		/** @param mixed $skin Upgrader skin (unused in tests). */
		public function __construct( $skin = null ) {}

		/**
		 * @param string $package Download URL.
		 * @param array  $args    Install options.
		 * @return mixed
		 */
		public function install( $package, $args = array() ) {
			self::$last_install_args = array( $package, $args );
			return self::$next_install_result;
		}
	}
	// phpcs:enable
}
