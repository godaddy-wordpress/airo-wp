<?php
/**
 * Application Password authentication via the Authorization header.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure;

defined( 'ABSPATH' ) || exit;

use WP_User;

/**
 * Accepts a WordPress Application Password carried in the Authorization header.
 *
 * Why this exists: hosted MCP clients cannot authenticate to this plugin today.
 * They will not send HTTP Basic auth, and custom header *names* require vendor
 * approval, so neither of the plugin's existing paths (cookie + nonce, or Basic)
 * is reachable from Claude or ChatGPT. The Authorization header itself is
 * available, so the credential travels there under its own auth scheme:
 *
 *   Authorization: airowp <base64(username:app-password)>
 *   Authorization: Bearer airowp_<base64(username:app-password)>
 *
 * Both forms are accepted because some clients let the operator supply a whole
 * Authorization value while others only offer a bearer token field. The literal
 * "airowp" marker is the switch either way.
 *
 * This is a bridge, not authorization. It is deliberately NOT OAuth and NOT the
 * MCP specification's authorization flow: there is no discovery document, no
 * consent screen, no PKCE and no audience binding. A spec-compliant client that
 * performs discovery will find nothing, which is why the marker matters -- a real
 * OAuth 2.1 Bearer token has no "airowp" prefix, so it parses to null here and is
 * left entirely untouched. An OAuth provider can be added later alongside this
 * with no ambiguity and no migration.
 *
 * Security posture is exactly that of Basic auth over TLS, because base64 is
 * encoding rather than encryption. That is the point rather than an oversight: it
 * carries the very credential WordPress core already ships and documents for the
 * REST API, so no new secret type is introduced, and revocation stays where users
 * already look for it (Users -> Profile -> Application Passwords). Validation is
 * delegated wholly to core, so a credential accepted here is one Basic auth would
 * also have accepted.
 *
 * It is not scoped to the MCP route on purpose. The credential is a standard
 * Application Password validated by core, so honouring it across the REST API is
 * precisely what Basic auth does; narrowing it would add a fragile URI check for
 * no security gain, since the same credential would be equally valid either way.
 *
 * Authorization is untouched. This answers only "which user is this?". The MCP
 * transport still requires the 'read' capability, and every tool still runs its
 * own capability check.
 *
 * HTTPS is effectively required, and not by anything here: core refuses to issue or
 * accept Application Passwords unless is_ssl() or the environment type is 'local'
 * (see wp_is_application_passwords_supported), so a plain-HTTP site cannot even
 * create the credential -- its REST endpoint answers 501. The availability check
 * below respects that rather than working around it, which means this scheme
 * inherits the same HTTPS requirement Basic auth already has.
 *
 * One incidental fix: core's own Application Password path requires
 * $_SERVER['PHP_AUTH_USER'] and ['PHP_AUTH_PW'], which many CGI and FastCGI hosts
 * never populate without an .htaccess rewrite -- so Basic auth can fail there even
 * when the client sends it correctly. Reading the header directly, with the
 * fallbacks below, sidesteps that.
 */
final class AppPasswordHeaderAuth {

	/**
	 * Custom auth scheme. Matched case-insensitively, per RFC 7235.
	 *
	 * @var string
	 */
	private const SCHEME = 'airowp';

	/**
	 * Marker prefixing the credential when a client forces the Bearer scheme.
	 *
	 * @var string
	 */
	private const BEARER_MARKER = 'airowp_';

	/**
	 * Memoised parse result: false until parsed, then the credentials or null.
	 *
	 * Two filters consult this per request, and the header cannot change mid-request.
	 *
	 * @var array{username: string, password: string}|null|false
	 */
	private $parsed = false;

	/**
	 * Register the authentication filter.
	 *
	 * Priority 20 matches core's own wp_validate_application_password, and the
	 * "don't authenticate twice" guard below means whichever runs first wins --
	 * so this never overrides an already-resolved user.
	 *
	 * @return void
	 */
	public function setup(): void {
		// PHP_INT_MAX so no other opinion can undo the assertion. Both filters are
		// no-ops unless this request actually carries an airowp credential.
		add_filter( 'application_password_is_api_request', array( $this, 'treat_as_api_request' ), PHP_INT_MAX );
		add_filter( 'determine_current_user', array( $this, 'determine_current_user' ), 20 );
	}

	/**
	 * Assert that a request carrying an airowp credential is an API request.
	 *
	 * Without this, nothing here works -- and the reason is a core ordering quirk
	 * rather than anything specific to this plugin. WP::main() calls $this->init(),
	 * which calls wp_get_current_user(), BEFORE it calls parse_request(). But
	 * REST_REQUEST is only defined by rest_api_loaded(), which runs ON
	 * parse_request. So the very first attempt to resolve the user happens while
	 * REST_REQUEST is still undefined, wp_authenticate_application_password() sees
	 * $is_api_request as false and returns early, and WordPress then caches the
	 * current user as 0 for the remainder of the request -- which fails the MCP
	 * transport's own current_user_can( 'read' ) check later on. Core documents the
	 * hazard in wp-includes/user.php ("may happen too early for the constant to be
	 * available") and provides this filter for exactly it.
	 *
	 * Deliberately narrow: it only promotes when one of our credentials is present,
	 * so it never changes how Basic auth behaves on any other request, and it never
	 * demotes. It also only lets core proceed to *validate* the credential it would
	 * otherwise skip -- an invalid Application Password still fails, and core's own
	 * availability and SSL gates still apply.
	 *
	 * @param mixed $is_api_request Whether core considers this an API request.
	 * @return mixed
	 */
	public function treat_as_api_request( $is_api_request ) {
		return null !== $this->parse_header() ? true : $is_api_request;
	}

	/**
	 * Resolve the current user from an airowp Authorization header.
	 *
	 * @param int|false $user_id User ID resolved so far, or false.
	 * @return int|false Resolved user ID, or the input untouched.
	 */
	public function determine_current_user( $user_id ) {
		// Never authenticate twice: a cookie or another provider already answered.
		if ( ! empty( $user_id ) ) {
			return $user_id;
		}

		if ( ! function_exists( 'wp_is_application_passwords_available' ) || ! wp_is_application_passwords_available() ) {
			return $user_id;
		}

		$credentials = $this->parse_header();

		if ( null === $credentials ) {
			return $user_id;
		}

		// Delegated to core so every check it applies still applies: whether
		// Application Passwords are available for this user, the hash comparison,
		// last-used and last-IP recording, and the did/failed authentication hooks.
		$authenticated = wp_authenticate_application_password( null, $credentials['username'], $credentials['password'] );

		if ( $authenticated instanceof WP_User ) {
			return (int) $authenticated->ID;
		}

		// Deliberately silent about why. Returning the input unchanged lets core
		// produce its own 401, and core's hooks already carry the detail for
		// anyone auditing a failure. The credential is never logged.
		return $user_id;
	}

	/**
	 * Parse the Authorization header into credentials.
	 *
	 * @return array{username: string, password: string}|null Null when this request
	 *                                                        carries no airowp
	 *                                                        credential.
	 */
	public function parse_header(): ?array {
		if ( false !== $this->parsed ) {
			return $this->parsed;
		}

		$this->parsed = $this->parse_header_uncached();

		return $this->parsed;
	}

	/**
	 * Parse the Authorization header, ignoring the memo.
	 *
	 * @return array{username: string, password: string}|null
	 */
	private function parse_header_uncached(): ?array {
		$header = $this->read_header();

		if ( null === $header ) {
			return null;
		}

		$blob = $this->extract_blob( $header );

		if ( null === $blob ) {
			return null;
		}

		return $this->decode( $blob );
	}

	/**
	 * Read the Authorization header from the first source that carries it.
	 *
	 * HTTP_AUTHORIZATION is the normal location. REDIRECT_HTTP_AUTHORIZATION is
	 * where it lands when a host forwards the header through an .htaccess rewrite,
	 * the documented workaround for Apache and CGI dropping it. getallheaders()
	 * covers SAPIs that expose it nowhere in $_SERVER.
	 *
	 * @return string|null Raw header value, or null when absent.
	 */
	private function read_header(): ?string {
		foreach ( array( 'HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION' ) as $key ) {
			if ( empty( $_SERVER[ $key ] ) ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized on the next line.
			$value = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );

			if ( is_string( $value ) && '' !== $value ) {
				return $value;
			}
		}

		if ( ! function_exists( 'getallheaders' ) ) {
			return null;
		}

		$headers = getallheaders();

		if ( ! is_array( $headers ) ) {
			return null;
		}

		foreach ( $headers as $name => $value ) {
			if ( ! is_string( $name ) || 'authorization' !== strtolower( $name ) ) {
				continue;
			}

			$value = sanitize_text_field( (string) $value );

			if ( '' !== $value ) {
				return $value;
			}
		}

		return null;
	}

	/**
	 * Pull the base64 credential out of a raw Authorization header value.
	 *
	 * @param string $header Raw header value.
	 * @return string|null The base64 blob, or null when this header is not ours.
	 */
	private function extract_blob( string $header ): ?string {
		$parts = preg_split( '/\s+/', trim( $header ), 2 );

		if ( ! is_array( $parts ) || 2 !== count( $parts ) ) {
			return null;
		}

		$scheme     = strtolower( $parts[0] );
		$credential = trim( $parts[1] );

		if ( '' === $credential ) {
			return null;
		}

		if ( self::SCHEME === $scheme ) {
			return $credential;
		}

		// A client offering only a bearer field carries the marker inline instead.
		if ( 'bearer' === $scheme && 0 === strpos( $credential, self::BEARER_MARKER ) ) {
			$blob = substr( $credential, strlen( self::BEARER_MARKER ) );

			return '' === $blob ? null : $blob;
		}

		return null;
	}

	/**
	 * Decode a base64 credential into a username and password.
	 *
	 * Splits on the first colon: WordPress usernames cannot contain one, so
	 * everything after it belongs to the password.
	 *
	 * @param string $blob Base64-encoded "username:password".
	 * @return array{username: string, password: string}|null Null when malformed.
	 */
	private function decode( string $blob ): ?array {
		// Strict mode, so a mangled credential is rejected rather than silently
		// decoded into something else.
		$decoded = base64_decode( $blob, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- transport encoding, not obfuscation.

		if ( ! is_string( $decoded ) || '' === $decoded ) {
			return null;
		}

		$separator = strpos( $decoded, ':' );

		if ( false === $separator || 0 === $separator ) {
			return null;
		}

		$username = substr( $decoded, 0, $separator );
		$password = substr( $decoded, $separator + 1 );

		if ( '' === $username || '' === $password ) {
			return null;
		}

		return array(
			'username' => $username,
			'password' => $password,
		);
	}
}
