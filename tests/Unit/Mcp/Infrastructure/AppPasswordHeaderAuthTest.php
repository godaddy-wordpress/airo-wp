<?php
/**
 * AppPasswordHeaderAuth tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Infrastructure;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\AppPasswordHeaderAuth;
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for AppPasswordHeaderAuth.
 *
 * The negative cases carry as much weight as the positive ones. Anything this
 * class fails to reject becomes a credential shape handed to core, and anything it
 * wrongly claims would shadow a real OAuth Bearer token if one is added later.
 */
final class AppPasswordHeaderAuthTest extends TestCase {

	/**
	 * Set up WordPress function stubs.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		Functions\when( 'wp_unslash' )->returnArg();
		// Faithful enough for these inputs: base64 and the scheme contain no tags,
		// octets or line breaks, so core would return them unchanged.
		Functions\when( 'sanitize_text_field' )->alias(
			static function ( $value ) {
				return trim( (string) preg_replace( '/\s+/', ' ', (string) $value ) );
			}
		);
		Functions\when( 'wp_is_application_passwords_available' )->justReturn( true );
	}

	/**
	 * Clear header state between tests.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $_SERVER['HTTP_AUTHORIZATION'], $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] );

		parent::tearDown();
	}

	/**
	 * Helper: encode a credential the way a client would.
	 *
	 * @param string $username Username.
	 * @param string $password Application Password.
	 * @return string
	 */
	private function encode( string $username, string $password ): string {
		return base64_encode( $username . ':' . $password );
	}

	// ── Header parsing ────────────────────────────────────────────────────────

	public function test_parses_custom_scheme(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'abcd EFGH ijkl MNOP' );

		$this->assertSame(
			array(
				'username' => 'admin',
				'password' => 'abcd EFGH ijkl MNOP',
			),
			( new AppPasswordHeaderAuth() )->parse_header()
		);
	}

	public function test_scheme_match_is_case_insensitive(): void {
		// RFC 7235 defines auth-scheme as case-insensitive.
		$_SERVER['HTTP_AUTHORIZATION'] = 'AiRoWp ' . $this->encode( 'admin', 'secret pass' );

		$this->assertNotNull( ( new AppPasswordHeaderAuth() )->parse_header() );
	}

	public function test_parses_bearer_form_with_marker(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer airowp_' . $this->encode( 'editor', 'wxyz 1234' );

		$this->assertSame(
			array(
				'username' => 'editor',
				'password' => 'wxyz 1234',
			),
			( new AppPasswordHeaderAuth() )->parse_header()
		);
	}

	public function test_falls_back_to_redirect_header(): void {
		$_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'secret pass' );

		$this->assertNotNull( ( new AppPasswordHeaderAuth() )->parse_header() );
	}

	public function test_prefers_primary_header_over_redirect(): void {
		$_SERVER['HTTP_AUTHORIZATION']          = 'airowp ' . $this->encode( 'primary', 'secret pass' );
		$_SERVER['REDIRECT_HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'fallback', 'other pass' );

		$parsed = ( new AppPasswordHeaderAuth() )->parse_header();

		$this->assertSame( 'primary', $parsed['username'] );
	}

	public function test_password_keeps_everything_after_the_first_colon(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'has:colons:inside' );

		$parsed = ( new AppPasswordHeaderAuth() )->parse_header();

		$this->assertSame( 'admin', $parsed['username'] );
		$this->assertSame( 'has:colons:inside', $parsed['password'] );
	}

	/**
	 * A genuine OAuth Bearer token must be left entirely alone, so an OAuth
	 * provider can be added later without this one shadowing it.
	 *
	 * @return void
	 */
	public function test_ignores_plain_bearer_token(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.payload.sig';

		$this->assertNull( ( new AppPasswordHeaderAuth() )->parse_header() );
	}

	public function test_ignores_basic_auth(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'Basic ' . $this->encode( 'admin', 'secret pass' );

		$this->assertNull( ( new AppPasswordHeaderAuth() )->parse_header() );
	}

	/**
	 * Malformed and hostile header values, each of which must parse to null.
	 *
	 * @return array<string, array{0: string}>
	 */
	public function malformed_headers(): array {
		return array(
			'empty value'               => array( '' ),
			'scheme only'               => array( 'airowp' ),
			'scheme with no credential' => array( 'airowp   ' ),
			'not base64'                => array( 'airowp !!!not-base64!!!' ),
			'base64 without colon'      => array( 'airowp ' . base64_encode( 'nocolonhere' ) ),
			'empty username'            => array( 'airowp ' . base64_encode( ':onlypassword' ) ),
			'empty password'            => array( 'airowp ' . base64_encode( 'onlyuser:' ) ),
			'empty decoded'             => array( 'airowp ' . base64_encode( '' ) ),
			'bearer marker only'        => array( 'Bearer airowp_' ),
			'unknown scheme'            => array( 'Digest ' . base64_encode( 'admin:secret' ) ),
			'marker without bearer'     => array( 'airowp_' . base64_encode( 'admin:secret' ) ),
		);
	}

	/**
	 * @dataProvider malformed_headers
	 *
	 * @param string $header Raw Authorization header value.
	 * @return void
	 */
	public function test_rejects_malformed_header( string $header ): void {
		$_SERVER['HTTP_AUTHORIZATION'] = $header;

		$this->assertNull( ( new AppPasswordHeaderAuth() )->parse_header(), 'Expected rejection of: ' . $header );
	}

	public function test_returns_null_when_no_header_present(): void {
		$this->assertNull( ( new AppPasswordHeaderAuth() )->parse_header() );
	}

	// ── determine_current_user ────────────────────────────────────────────────

	public function test_resolves_user_from_valid_credential(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'abcd EFGH' );

		$user     = \Mockery::mock( 'WP_User' );
		$user->ID = 42;

		Functions\expect( 'wp_authenticate_application_password' )
			->once()
			->with( null, 'admin', 'abcd EFGH' )
			->andReturn( $user );

		$this->assertSame( 42, ( new AppPasswordHeaderAuth() )->determine_current_user( false ) );
	}

	/**
	 * An already-resolved user must win, so this can never downgrade or override a
	 * cookie session or another provider's answer.
	 *
	 * @return void
	 */
	public function test_never_authenticates_twice(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'abcd EFGH' );

		Functions\expect( 'wp_authenticate_application_password' )->never();

		$this->assertSame( 7, ( new AppPasswordHeaderAuth() )->determine_current_user( 7 ) );
	}

	public function test_passes_through_when_credential_is_rejected(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'wrong pass' );

		$error = \Mockery::mock( 'WP_Error' );

		Functions\expect( 'wp_authenticate_application_password' )->once()->andReturn( $error );

		$this->assertFalse( ( new AppPasswordHeaderAuth() )->determine_current_user( false ) );
	}

	public function test_passes_through_without_a_credential(): void {
		Functions\expect( 'wp_authenticate_application_password' )->never();

		$this->assertFalse( ( new AppPasswordHeaderAuth() )->determine_current_user( false ) );
	}

	/**
	 * Site-wide disablement of Application Passwords must be respected, and
	 * checked before the header is even read.
	 *
	 * @return void
	 */
	public function test_respects_application_passwords_being_unavailable(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'abcd EFGH' );

		Functions\when( 'wp_is_application_passwords_available' )->justReturn( false );
		Functions\expect( 'wp_authenticate_application_password' )->never();

		$this->assertFalse( ( new AppPasswordHeaderAuth() )->determine_current_user( false ) );
	}

	public function test_setup_registers_both_filters(): void {
		Filters\expectAdded( 'determine_current_user' )->once();
		Filters\expectAdded( 'application_password_is_api_request' )->once();

		( new AppPasswordHeaderAuth() )->setup();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * The api-request assertion is what makes any of this work: WP::main() resolves
	 * the current user before REST_REQUEST is defined, so core would otherwise skip
	 * validating the credential entirely.
	 *
	 * @return void
	 */
	public function test_asserts_api_request_when_our_credential_is_present(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'airowp ' . $this->encode( 'admin', 'abcd EFGH' );

		$this->assertTrue( ( new AppPasswordHeaderAuth() )->treat_as_api_request( false ) );
	}

	/**
	 * ...but it must never change how any other request is treated, which is what
	 * keeps Basic auth behaving exactly as core intends everywhere else.
	 *
	 * @return void
	 */
	public function test_leaves_api_request_untouched_without_our_credential(): void {
		$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer some.oauth.token';

		$auth = new AppPasswordHeaderAuth();

		$this->assertFalse( $auth->treat_as_api_request( false ) );
		$this->assertTrue( $auth->treat_as_api_request( true ), 'must never demote' );
	}
}
