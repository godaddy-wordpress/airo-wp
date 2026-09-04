<?php
/**
 * Platform access compatibility for the MCP route.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure;

defined( 'ABSPATH' ) || exit;

/**
 * Keeps the airo-wp MCP route reachable and authenticable on GoDaddy hosting.
 *
 * Two independent platform behaviours block the MCP route on a site that has
 * not been launched yet, and both have to be answered for Application Password
 * auth to work there.
 *
 * 1. Auth timing. GoDaddy Launch's LiveSiteControlProvider hooks 'parse_request'
 *    at priority 1 and evaluates `is_restricted() && ! user_can_access()`. On an
 *    unpublished site is_restricted() is true, so user_can_access() calls
 *    is_user_logged_in() at priority 1 -- but core registers rest_api_loaded()
 *    on 'parse_request' at the default priority 10, so REST_REQUEST is not
 *    defined yet. wp_authenticate_application_password() therefore treats the
 *    request as non-API and returns early (see the "may happen too early for
 *    the constant to be available" note in wp-includes/user.php). WordPress
 *    caches the current user as ID 0 for the rest of the request, which fails
 *    Launch's check *and* poisons the MCP transport's own
 *    current_user_can( 'read' ) check later in the same request. Core provides
 *    'application_password_is_api_request' for exactly this timing problem.
 *
 * 2. Coming-soon REST restriction. Launch answers a bare 401 for any REST route
 *    not on its allowlist while the site is unpublished. Resolving the user in
 *    (1) already satisfies user_can_access(), but the allowlist entry keeps the
 *    route reachable regardless of publish state.
 *
 * Neither filter weakens authorisation. (1) only lets core proceed to *validate*
 * the credentials it would otherwise skip -- an invalid Application Password
 * still fails, and core's own SSL and availability gates still apply. (2) only
 * lifts a publish-state gate; the MCP transport's permission callback still
 * runs.
 *
 * Both are scoped to the MCP route rather than the whole airo-wp/v1 namespace,
 * because that namespace also carries a deliberately public form-submission
 * endpoint which coming-soon mode is expected to keep shielded.
 */
final class RouteAccess {

	/**
	 * Allowlist prefix for GoDaddy Launch's coming-soon REST restriction.
	 *
	 * Launch matches with str_starts_with() against the rest_route query var,
	 * which carries a leading slash.
	 */
	const MCP_ROUTE_PREFIX = '/airo-wp/v1/mcp';

	/**
	 * Register the compatibility filters.
	 *
	 * Both run at PHP_INT_MAX so they settle after any other opinion: the
	 * Application Password assertion must not be overridden, and the allowlist
	 * entry must survive a filter that replaces rather than appends.
	 */
	public function setup(): void {
		add_filter( 'application_password_is_api_request', array( $this, 'treat_mcp_route_as_api_request' ), PHP_INT_MAX );
		add_filter( 'gdl_unrestricted_rest_endpoints', array( $this, 'allow_mcp_route' ), PHP_INT_MAX );
	}

	/**
	 * Treat the MCP route as an API request so Application Passwords resolve.
	 *
	 * Only ever promotes to true, so Basic auth keeps working on core's own
	 * terms everywhere else.
	 *
	 * @param mixed $is_api_request Whether core considers this an API request.
	 * @return mixed
	 */
	public function treat_mcp_route_as_api_request( $is_api_request ) {
		return $this->is_mcp_request() ? true : $is_api_request;
	}

	/**
	 * Exempt the MCP route from GoDaddy Launch's coming-soon REST restriction.
	 *
	 * @param mixed $endpoints Unrestricted REST endpoint path prefixes.
	 * @return mixed Endpoints with the MCP route appended, or the input unchanged.
	 */
	public function allow_mcp_route( $endpoints ) {
		if ( ! is_array( $endpoints ) ) {
			return $endpoints;
		}

		$endpoints[] = self::MCP_ROUTE_PREFIX;

		return $endpoints;
	}

	/**
	 * Determine whether the current request targets the MCP route.
	 *
	 * Has to read the raw request URI, because this runs before REST dispatch: neither
	 * the rest_route query var nor WP_REST_Server exists yet. Covers the pretty
	 * /wp-json/ form and the ?rest_route= fallback.
	 *
	 * Matching is anchored on a path-segment boundary rather than a substring search.
	 * An unanchored search would also match '/page/airo-wp/v1/mcp-decoy' and
	 * '/anything?x=airo-wp/v1/mcp', letting an arbitrary URL trigger Application
	 * Password validation on a route that is not the MCP endpoint. The effect would be
	 * limited -- invalid credentials still fail -- but there is no reason to accept it.
	 *
	 * @return bool
	 */
	public function is_mcp_request(): bool {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized on the next line.
		$uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		if ( ! is_string( $uri ) || '' === $uri ) {
			return false;
		}

		// ?rest_route=/airo-wp/v1/mcp... — read just that one value rather than
		// parsing the whole query string, which keeps this free of by-reference
		// helpers and of any assumption about the rest of the query.
		$query = (string) wp_parse_url( $uri, PHP_URL_QUERY );

		if ( '' !== $query ) {
			foreach ( explode( '&', $query ) as $pair ) {
				if ( 0 !== strpos( $pair, 'rest_route=' ) ) {
					continue;
				}

				$value = rawurldecode( substr( $pair, strlen( 'rest_route=' ) ) );

				return '' !== $value && $this->is_mcp_route( $value );
			}
		}

		// /wp-json/airo-wp/v1/mcp... — the prefix is filterable, so ask for it.
		$path = (string) wp_parse_url( $uri, PHP_URL_PATH );

		if ( '' === $path ) {
			return false;
		}

		$prefix   = '/' . trim( rest_get_url_prefix(), '/' ) . '/';
		$position = strpos( $path, $prefix );

		if ( false === $position ) {
			return false;
		}

		return $this->is_mcp_route( substr( $path, $position + strlen( $prefix ) ) );
	}

	/**
	 * Whether a REST route path is the MCP route or below it.
	 *
	 * Exact match, or followed by '/' so only a real path segment counts. The server
	 * registers 'mcp/streamable', so the sub-route case is the normal one.
	 *
	 * @param string $route REST route path, with or without a leading slash.
	 * @return bool
	 */
	private function is_mcp_route( string $route ): bool {
		$route = '/' . ltrim( $route, '/' );

		return self::MCP_ROUTE_PREFIX === $route
			|| 0 === strpos( $route, self::MCP_ROUTE_PREFIX . '/' );
	}
}
