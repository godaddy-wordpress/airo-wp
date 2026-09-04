<?php
/**
 * RouteAccess tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Infrastructure;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\RouteAccess;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for RouteAccess.
 */
final class RouteAccessTest extends TestCase {

	/**
	 * Saved $_SERVER['REQUEST_URI'] so each test starts from a known state.
	 *
	 * @var string|null
	 */
	private $saved_request_uri;

	/**
	 * Set up test environment.
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->saved_request_uri = $_SERVER['REQUEST_URI'] ?? null;
		unset( $_SERVER['REQUEST_URI'] );

		Functions\when( 'wp_unslash' )->returnArg();
		Functions\when( 'sanitize_text_field' )->returnArg();
		Functions\when( 'rest_get_url_prefix' )->justReturn( 'wp-json' );
		Functions\when( 'wp_parse_url' )->alias(
			static function ( $url, $component = -1 ) {
				return parse_url( $url, $component );
			}
		);
	}

	/**
	 * Restore superglobal state.
	 */
	protected function tearDown(): void {
		if ( null === $this->saved_request_uri ) {
			unset( $_SERVER['REQUEST_URI'] );
		} else {
			$_SERVER['REQUEST_URI'] = $this->saved_request_uri;
		}

		parent::tearDown();
	}

	/**
	 * setup() must register both compatibility filters.
	 */
	public function test_setup_registers_both_filters(): void {
		Filters\expectAdded( 'application_password_is_api_request' )->once();
		Filters\expectAdded( 'gdl_unrestricted_rest_endpoints' )->once();

		( new RouteAccess() )->setup();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * The pretty-permalink MCP URL must be treated as an API request.
	 */
	public function test_treats_pretty_permalink_mcp_uri_as_api_request(): void {
		$_SERVER['REQUEST_URI'] = '/wp-json/airo-wp/v1/mcp/streamable';

		$this->assertTrue( ( new RouteAccess() )->treat_mcp_route_as_api_request( false ) );
	}

	/**
	 * The ?rest_route= fallback form must also be treated as an API request.
	 */
	public function test_treats_rest_route_query_form_as_api_request(): void {
		$_SERVER['REQUEST_URI'] = '/index.php?rest_route=/airo-wp/v1/mcp/streamable';

		$this->assertTrue( ( new RouteAccess() )->treat_mcp_route_as_api_request( false ) );
	}

	/**
	 * A percent-encoded route must still match, since clients may encode the slash.
	 */
	public function test_treats_urlencoded_mcp_uri_as_api_request(): void {
		$_SERVER['REQUEST_URI'] = '/index.php?rest_route=%2Fairo-wp%2Fv1%2Fmcp%2Fstreamable';

		$this->assertTrue( ( new RouteAccess() )->treat_mcp_route_as_api_request( false ) );
	}

	/**
	 * Unrelated routes must keep core's own answer rather than being forced true.
	 */
	public function test_passes_through_for_unrelated_uri(): void {
		$_SERVER['REQUEST_URI'] = '/wp-json/wp/v2/posts';

		$this->assertFalse( ( new RouteAccess() )->treat_mcp_route_as_api_request( false ) );
	}

	/**
	 * URIs that merely CONTAIN the route string must not match. Each of these was
	 * accepted by the previous unanchored substring search, which let an arbitrary
	 * URL trigger Application Password validation off the MCP endpoint.
	 *
	 * @return array<string, array{0: string}>
	 */
	public function non_mcp_uris(): array {
		return array(
			'route string in a query value'  => array( '/wp-json/wp/v2/posts?x=airo-wp/v1/mcp' ),
			'route string in a path segment' => array( '/page/airo-wp/v1/mcp' ),
			'longer sibling segment'         => array( '/wp-json/airo-wp/v1/mcp-decoy' ),
			'sibling under rest_route'       => array( '/index.php?rest_route=/airo-wp/v1/mcpx' ),
			'different namespace'            => array( '/wp-json/other/v1/mcp' ),
			'percent-encoded path traversal' => array( '/wp-json/foo%2Fairo-wp/v1/mcp' ),
		);
	}

	/**
	 * @dataProvider non_mcp_uris
	 *
	 * @param string $uri Request URI.
	 * @return void
	 */
	public function test_does_not_treat_non_mcp_uri_as_api_request( string $uri ): void {
		$_SERVER['REQUEST_URI'] = $uri;

		$this->assertFalse(
			( new RouteAccess() )->is_mcp_request(),
			'Must not match: ' . $uri
		);
	}

	/**
	 * The exact route with no sub-route must still match.
	 */
	public function test_treats_bare_mcp_route_as_api_request(): void {
		$_SERVER['REQUEST_URI'] = '/wp-json/airo-wp/v1/mcp';

		$this->assertTrue( ( new RouteAccess() )->is_mcp_request() );
	}

	/**
	 * A filtered REST prefix must be honoured rather than assumed to be wp-json.
	 */
	public function test_honours_a_filtered_rest_prefix(): void {
		Functions\when( 'rest_get_url_prefix' )->justReturn( 'api' );

		$_SERVER['REQUEST_URI'] = '/api/airo-wp/v1/mcp/streamable';

		$this->assertTrue( ( new RouteAccess() )->is_mcp_request() );
	}

	/**
	 * A missing REQUEST_URI must not be treated as an MCP request.
	 */
	public function test_passes_through_when_request_uri_absent(): void {
		$this->assertFalse( ( new RouteAccess() )->treat_mcp_route_as_api_request( false ) );
	}

	/**
	 * The filter must never downgrade a true value core already decided on.
	 */
	public function test_does_not_downgrade_an_existing_true_value(): void {
		$_SERVER['REQUEST_URI'] = '/wp-json/wp/v2/posts';

		$this->assertTrue( ( new RouteAccess() )->treat_mcp_route_as_api_request( true ) );
	}

	/**
	 * The MCP route prefix must be appended to GoDaddy Launch's allowlist.
	 */
	public function test_allow_mcp_route_appends_prefix(): void {
		$this->assertSame(
			array( '/wpaas/v1', '/airo-wp/v1/mcp' ),
			( new RouteAccess() )->allow_mcp_route( array( '/wpaas/v1' ) )
		);
	}

	/**
	 * A non-array value from a misbehaving filter must pass through untouched.
	 */
	public function test_allow_mcp_route_returns_non_array_unchanged(): void {
		$this->assertNull( ( new RouteAccess() )->allow_mcp_route( null ) );
	}

	/**
	 * The allowlist entry must not cover the whole namespace, because
	 * airo-wp/v1 also carries a deliberately public form-submission endpoint
	 * that coming-soon mode is expected to keep shielded.
	 */
	public function test_allowlist_entry_is_scoped_to_the_mcp_route(): void {
		$endpoints = ( new RouteAccess() )->allow_mcp_route( array() );

		$this->assertSame( array( '/airo-wp/v1/mcp' ), $endpoints );
		$this->assertNotContains( '/airo-wp/v1', $endpoints );
	}
}
