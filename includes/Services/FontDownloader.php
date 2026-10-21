<?php
/**
 * FontDownloader — downloads Google Fonts and converts them to local file paths.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Handles downloading Google Fonts from CSS URLs and converting them to local file paths.
 */
class FontDownloader {

	/**
	 * Maximum number of attempts for downloads.
	 *
	 * @var int
	 */
	const MAX_ATTEMPTS = 3;

	/**
	 * Initial retry delay in seconds.
	 *
	 * @var int
	 */
	const INITIAL_RETRY_DELAY = 1;

	/**
	 * Host serving Google Fonts CSS.
	 *
	 * @var string
	 */
	const CSS_HOST = 'fonts.googleapis.com';

	/**
	 * Host serving Google Fonts font files.
	 *
	 * @var string
	 */
	const FONT_FILE_HOST = 'fonts.gstatic.com';

	/**
	 * Download fonts from Google Fonts URLs and update font families array.
	 *
	 * @param array $font_families Font families array from global styles.
	 * @return array Modified font families array with local file paths.
	 */
	public function process_font_families( array $font_families ): array {
		// Ensure WordPress file functions are available.
		if ( ! function_exists( 'download_url' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( empty( $font_families['theme'] ) || ! is_array( $font_families['theme'] ) ) {
			return $font_families;
		}

		foreach ( $font_families['theme'] as &$font_family ) {
			if ( empty( $font_family['fontFace'] ) || ! is_array( $font_family['fontFace'] ) ) {
				continue;
			}

			$font_slug = $this->to_string( $font_family['slug'] ?? null, 'unknown' );
			$font_name = $this->to_string( $font_family['name'] ?? null, 'Unknown' );

			foreach ( $font_family['fontFace'] as &$font_face ) {
				// theme.json allows src to be a single string as well as a list.
				$sources = is_string( $font_face['src'] ?? null ) ? array( $font_face['src'] ) : ( $font_face['src'] ?? null );

				if ( empty( $sources ) || ! is_array( $sources ) ) {
					continue;
				}

				$src_url = $sources[0] ?? '';

				if ( ! is_string( $src_url ) ) {
					continue;
				}

				$weight = $this->to_string( $font_face['fontWeight'] ?? null, '400' );
				$style  = $this->to_string( $font_face['fontStyle'] ?? null, 'normal' );

				$css_url = $this->normalise_google_fonts_url( $src_url );

				// Skip if not a Google Fonts URL.
				if ( null === $css_url ) {
					continue;
				}

				// Download the font and get local path.
				$local_path = $this->download_google_font(
					$css_url,
					$font_slug,
					$weight,
					$style
				);

				if ( $local_path ) {
					// Update src to use local file path.
					$font_face['src'] = array( $local_path );
				} else {
					$this->log(
						sprintf(
							'Failed to download %s (%s %s) from %s',
							$font_name,
							$weight,
							$style,
							$src_url
						)
					);
				}
			}
		}

		return $font_families;
	}

	/**
	 * Coerce a value from the (untyped) settings schema to a string.
	 *
	 * @param mixed  $value    Raw value.
	 * @param string $fallback Used when the value is not a non-empty scalar.
	 * @return string
	 */
	private function to_string( $value, string $fallback ): string {
		return is_scalar( $value ) && '' !== (string) $value ? (string) $value : $fallback;
	}

	/**
	 * Write a diagnostic line to the error log.
	 *
	 * Control characters are replaced so a URL or font name taken from tool input
	 * cannot forge extra log lines.
	 *
	 * @param string $message Message, without the "[Font Downloader]" prefix.
	 * @return void
	 */
	private function log( string $message ): void {
		error_log( '[Font Downloader] ' . (string) preg_replace( '/[\x00-\x1F\x7F]/', ' ', $message ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	/**
	 * Return the HTTPS form of a Google Fonts CSS URL, or null if it is not one.
	 *
	 * Sources using http:// or a protocol-relative form are upgraded rather than rejected:
	 * skipping them would leave the site loading fonts from Google at runtime,
	 * which is what localising them is meant to prevent.
	 *
	 * @param string $url URL to check.
	 * @return string|null HTTPS Google Fonts CSS URL, or null.
	 */
	private function normalise_google_fonts_url( string $url ): ?string {
		if ( ! $this->url_has_host( $url, self::CSS_HOST, array( 'https', 'http', '' ) ) ) {
			return null;
		}

		$parts = wp_parse_url( $url );

		return 'https://' . self::CSS_HOST . ( $parts['path'] ?? '' ) . ( isset( $parts['query'] ) ? '?' . $parts['query'] : '' );
	}

	/**
	 * Check that a URL uses an allowed scheme and its host is exactly the given host.
	 *
	 * Parsed rather than substring-matched, so a host that only appears in the
	 * path, query, userinfo or a look-alike domain does not pass.
	 *
	 * @param string   $url     URL to check.
	 * @param string   $host    Expected host.
	 * @param string[] $schemes Allowed schemes; '' means no scheme (protocol-relative).
	 * @return bool True if the URL has an allowed scheme and is on that host.
	 */
	private function url_has_host( string $url, string $host, array $schemes = array( 'https' ) ): bool {
		$parts = wp_parse_url( $url );

		if ( ! is_array( $parts ) || empty( $parts['host'] ) ) {
			return false;
		}

		if ( isset( $parts['user'] ) || isset( $parts['pass'] ) ) {
			return false;
		}

		$scheme = isset( $parts['scheme'] ) ? strtolower( $parts['scheme'] ) : '';

		if ( ! in_array( $scheme, $schemes, true ) ) {
			return false;
		}

		// An explicit port must be the scheme's default; anything else is a different service.
		if ( isset( $parts['port'] ) && ( 'http' === $scheme ? 80 : 443 ) !== (int) $parts['port'] ) {
			return false;
		}

		return strtolower( $parts['host'] ) === $host;
	}

	/**
	 * Download a Google Font and return local file path.
	 *
	 * @param string $google_fonts_css_url Google Fonts CSS URL.
	 * @param string $font_slug            Font slug for filename.
	 * @param string $weight               Font weight.
	 * @param string $style                Font style.
	 * @return string|null URL of the local font file, or null on failure.
	 */
	private function download_google_font( string $google_fonts_css_url, string $font_slug, string $weight, string $style ): ?string {
		// Parse Google Fonts CSS to get actual font file URL.
		$font_file_url = $this->parse_google_fonts_css( $google_fonts_css_url );

		if ( ! $font_file_url ) {
			$this->log( 'Failed to parse Google Fonts CSS: ' . $google_fonts_css_url );
			return null;
		}

		// Generate filename.
		$filename = $this->generate_filename( $font_file_url, $font_slug, $weight, $style );

		// Get WordPress fonts directory.
		$font_dir = wp_font_dir();
		if ( ! $font_dir || empty( $font_dir['path'] ) || empty( $font_dir['url'] ) ) {
			$this->log( 'Could not get WordPress font directory' );
			return null;
		}

		$font_dir_path  = $font_dir['path'];
		$font_file_path = trailingslashit( $font_dir_path ) . $filename;

		// wp_font_dir() varies per multisite subsite and is filterable, so the src is its URL rather than a theme-relative path.
		$font_file_src = trailingslashit( $font_dir['url'] ) . $filename;

		// Check if font already exists.
		if ( file_exists( $font_file_path ) ) {
			return $font_file_src;
		}

		// Download the font file with retry logic.
		$downloaded_file = $this->download_with_retry( $font_file_url );

		if ( ! $downloaded_file ) {
			return null;
		}

		try {
			if ( ! $this->store_font_file( $downloaded_file, $font_dir_path, $font_file_path ) ) {
				return null;
			}
		} finally {
			// On every exit path, including a throw from the filesystem, the temp download must not outlive this call.
			if ( file_exists( $downloaded_file ) ) {
				wp_delete_file( $downloaded_file );
			}
		}

		return $font_file_src;
	}

	/**
	 * Validate a downloaded font and move it into the fonts directory.
	 *
	 * Leaves the temp download in place on failure; the caller removes it.
	 *
	 * @param string $downloaded_file Path to the temp download.
	 * @param string $font_dir_path   Fonts directory.
	 * @param string $font_file_path  Destination path inside the fonts directory.
	 * @return bool True if the font now lives at $font_file_path.
	 */
	private function store_font_file( string $downloaded_file, string $font_dir_path, string $font_file_path ): bool {
		// Validate before the file reaches the public fonts directory.
		if ( ! $this->validate_font_file( $downloaded_file ) ) {
			$this->log( 'Font file validation failed: ' . basename( $font_file_path ) );
			return false;
		}

		// Ensure fonts directory exists.
		if ( ! file_exists( $font_dir_path ) && ! wp_mkdir_p( $font_dir_path ) ) {
			$this->log( 'Could not create font directory: ' . $font_dir_path );
			return false;
		}

		// Move downloaded file to fonts directory using WP_Filesystem.
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Test writability in the fonts directory, not wp-content: uploads is writable on hosts where wp-content is not.
		// False means an FTP/SSH method could not connect; $wp_filesystem is then set but unusable.
		if ( ! WP_Filesystem( false, $font_dir_path, true ) ) {
			$this->log( 'Could not initialise the filesystem to store: ' . $font_file_path );
			return false;
		}

		global $wp_filesystem;

		if ( ! $wp_filesystem->move( $downloaded_file, $font_file_path, true ) ) {
			$this->log( 'Failed to move font file to: ' . $font_file_path );
			return false;
		}

		return true;
	}

	/**
	 * Parse Google Fonts CSS to extract actual font file URL.
	 *
	 * @param string $css_url Google Fonts CSS URL.
	 * @return string|null Font file URL on fonts.gstatic.com, or null on failure.
	 */
	private function parse_google_fonts_css( string $css_url ): ?string {
		// Fetch the CSS content.
		$response = wp_safe_remote_get(
			$css_url,
			array(
				'timeout'    => 3,
				'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->log( 'Failed to fetch Google Fonts CSS: ' . $response->get_error_message() );
			return null;
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $status ) {
			$this->log( sprintf( 'Google Fonts CSS returned HTTP %d: %s', $status, $css_url ) );
			return null;
		}

		$css_content = wp_remote_retrieve_body( $response );

		if ( empty( $css_content ) ) {
			return null;
		}

		// Extract font file URL from CSS.
		// Google Fonts returns multiple @font-face rules with unicode-range subsetting.
		// We want the Latin subset (U+0000-00FF) which covers English and basic Latin characters.
		// This is typically the last @font-face rule in the response.

		// Try to find the Latin subset specifically (look for comment or unicode-range).
		if ( preg_match( '/\/\*\s*latin\s*\*\/.*?url\(([^)]+\.woff2)\)/s', $css_content, $matches ) ) {
			$font_file_url = $matches[1];
		} elseif ( preg_match_all( '/url\(([^)]+\.woff2)\)/', $css_content, $matches ) ) {
			// Fallback: the last woff2 URL (Latin subset is typically last).
			$font_file_url = end( $matches[1] );
		} else {
			return null;
		}

		// The CSS body is remote input; never hand back a URL on any other host.
		if ( ! $this->url_has_host( $font_file_url, self::FONT_FILE_HOST ) ) {
			$this->log( 'Refusing font file from unexpected host: ' . $font_file_url );
			return null;
		}

		return $font_file_url;
	}

	/**
	 * Download file with retry logic and exponential backoff.
	 *
	 * @param string $url URL to download.
	 * @return string|null Path to downloaded file or null on failure.
	 */
	private function download_with_retry( string $url ): ?string {
		$attempt = 0;
		$delay   = self::INITIAL_RETRY_DELAY;

		while ( $attempt < self::MAX_ATTEMPTS ) {
			++$attempt;

			$temp_file = download_url( $url, 30 ); // 30 second timeout.

			if ( ! is_wp_error( $temp_file ) ) {
				return $temp_file;
			}

			$error_message = $temp_file->get_error_message();

			// Don't retry if it's a 404 or similar permanent error.
			if ( strpos( $error_message, '404' ) !== false || strpos( $error_message, '403' ) !== false ) {
				return null;
			}

			if ( $attempt < self::MAX_ATTEMPTS ) {
				sleep( $delay ); // phpcs:ignore WordPress.WP.AlternativeFunctions.sleep_sleep
				$delay *= 2; // Exponential backoff.
			}
		}

		return null;
	}

	/**
	 * Generate filename for font file.
	 *
	 * @param string $font_file_url Font file URL on fonts.gstatic.com.
	 * @param string $font_slug     Font slug.
	 * @param string $weight        Font weight.
	 * @param string $style         Font style.
	 * @return string Filename.
	 */
	private function generate_filename( string $font_file_url, string $font_slug, string $weight, string $style ): string {
		// Sanitize components.
		$font_slug = sanitize_file_name( $font_slug );
		$weight    = sanitize_file_name( $weight );
		$style     = sanitize_file_name( $style );

		// The slug is a theme.json role ("heading", "body") that can be pointed at another family,
		// so the name also carries the font file's identity. The gstatic URL encodes family and
		// version, so a changed family or a new upstream version is a cache miss, not a stale hit.
		// Superseded files are left in place on purpose: global-styles revisions and other
		// themes' global-styles posts keep the old src URL, and deleting it would 404 them.
		$font_id = substr( md5( $font_file_url ), 0, 12 );

		// Always .woff2: only .woff2 URLs are extracted and validate_font_file() only accepts WOFF2.
		return sprintf( '%s-%s-%s-%s.woff2', $font_slug, $weight, $style, $font_id );
	}

	/**
	 * Validate that the downloaded file is a valid font file.
	 *
	 * @param string $file_path Path to font file.
	 * @return bool True if valid.
	 */
	private function validate_font_file( string $file_path ): bool {
		// Check file exists and is readable.
		if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
			return false;
		}

		// Check file size (fonts should be at least 1KB, max 5MB).
		$file_size = filesize( $file_path );
		if ( $file_size < 1024 || $file_size > 5 * 1024 * 1024 ) {
			return false;
		}

		// Check file signature.
		$handle = fopen( $file_path, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		if ( ! $handle ) {
			return false;
		}

		$signature = fread( $handle, 4 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

		// WOFF2 signature: "wOF2" — the only format Google Fonts serves to the request's user agent.
		return 'wOF2' === $signature;
	}
}
