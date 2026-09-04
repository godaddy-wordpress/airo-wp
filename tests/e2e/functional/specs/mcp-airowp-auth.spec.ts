import { test, expect } from '@wordpress/e2e-test-utils-playwright';

/**
 * End-to-end coverage for the `airowp` Authorization scheme.
 *
 * This is the only test in the suite that exercises a real Application Password
 * over the MCP route. Every other MCP spec authenticates with the browser cookie
 * plus X-WP-Nonce, which is the same-origin path — so none of them would notice if
 * credential-based auth broke entirely. That matters here because the whole point
 * of this scheme is to serve hosted clients, which have no cookie.
 *
 * The credential is created through core's own REST endpoint rather than fabricated,
 * so the test fails if core's Application Password behaviour changes underneath us.
 */

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';
const APP_PASSWORDS_ENDPOINT = '/wp-json/wp/v2/users/me/application-passwords';

const initialize = ( id = 1 ) => ( {
	jsonrpc: '2.0',
	id,
	method: 'initialize',
	params: {
		protocolVersion: '2024-11-05',
		capabilities: {},
		clientInfo: { name: 'playwright-airowp-auth', version: '1.0' },
	},
} );

test.describe( 'MCP server › airowp Authorization scheme', () => {
	let credential: string;
	let appPasswordUuid: string;

	test.beforeAll( async ( { requestUtils } ) => {
		const created = await requestUtils.request.post( APP_PASSWORDS_ENDPOINT, {
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': requestUtils.storageState!.nonce,
			},
			data: { name: `airowp-e2e-${ Date.now() }` },
		} );

		// 501 means core has Application Passwords switched off, which it does
		// whenever the site is not HTTPS and the environment type is not 'local'.
		// That is a property of the environment rather than of this feature, so skip
		// loudly instead of reporting a failure that says nothing useful.
		test.skip(
			created.status() === 501,
			'Application Passwords unavailable in this environment (core requires HTTPS or a local environment type)'
		);

		expect(
			created.status(),
			`could not create an Application Password (got ${ created.status() })`
		).toBe( 201 );

		const body = await created.json();
		appPasswordUuid = body.uuid;

		// core returns the password once, space-separated, and never again.
		expect( body.password ).toBeTruthy();
		credential = Buffer.from( `admin:${ body.password }` ).toString( 'base64' );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		if ( ! appPasswordUuid ) {
			return;
		}
		await requestUtils.request.delete(
			`${ APP_PASSWORDS_ENDPOINT }/${ appPasswordUuid }`,
			{ headers: { 'X-WP-Nonce': requestUtils.storageState!.nonce } }
		);
	} );

	/**
	 * A context with genuinely no cookies.
	 *
	 * `storageState: undefined` is load-bearing, not tidiness. Without it the context
	 * inherits the admin storageState from the Playwright config, and that cookie
	 * changes the outcome in a way that silently invalidates the whole test: core's
	 * wp_validate_logged_in_cookie is also on determine_current_user at priority 20
	 * but registered in default-filters.php, so it resolves the user before this
	 * plugin's filter and the credential is never exercised. Worse, a valid cookie
	 * sets $wp_rest_auth_cookie = true, which disables the escape hatch in
	 * rest_cookie_check_errors() — so with no X-WP-Nonce core then deliberately calls
	 * wp_set_current_user( 0 ) ("act as if it's an unauthenticated request") and the
	 * request 401s having never touched the Authorization header at all.
	 */
	const anonymousContext = async ( playwright: any, authorization?: string ) =>
		playwright.request.newContext( {
			baseURL: process.env.WP_BASE_URL,
			storageState: undefined,
			extraHTTPHeaders: authorization ? { Authorization: authorization } : {},
		} );

	test( 'authenticates with the custom airowp scheme', async ( { playwright } ) => {
		const ctx = await anonymousContext( playwright, `airowp ${ credential }` );
		const response = await ctx.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json' },
			data: initialize(),
		} );
		const status = response.status();
		const body = await response.text();
		await ctx.dispose();

		expect( status, `expected 200 but got ${ status }: ${ body }` ).toBe( 200 );
		expect( response.headers()[ 'mcp-session-id' ] ).toBeTruthy();
	} );

	test( 'authenticates with the Bearer airowp_ form', async ( { playwright } ) => {
		const ctx = await anonymousContext( playwright, `Bearer airowp_${ credential }` );
		const response = await ctx.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json' },
			data: initialize( 2 ),
		} );
		const status = response.status();
		const body = await response.text();
		await ctx.dispose();

		expect( status, `expected 200 but got ${ status }: ${ body }` ).toBe( 200 );
	} );

	test( 'a tool call succeeds on the credential alone', async ( { playwright } ) => {
		const ctx = await anonymousContext( playwright, `airowp ${ credential }` );

		const init = await ctx.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json' },
			data: initialize( 3 ),
		} );
		expect( init.status() ).toBe( 200 );
		const sessionId = init.headers()[ 'mcp-session-id' ];

		// tools/list is enough: it proves the transport's own 'read' capability
		// check passed for the user this credential resolved to.
		const listed = await ctx.post( MCP_ENDPOINT, {
			headers: {
				'Content-Type': 'application/json',
				'Mcp-Session-Id': sessionId,
			},
			data: { jsonrpc: '2.0', id: 4, method: 'tools/list', params: {} },
		} );
		const status = listed.status();
		const body = await listed.text();
		await ctx.dispose();

		expect( status, `expected 200 but got ${ status }: ${ body }` ).toBe( 200 );
		expect( body ).toContain( 'tools' );
	} );

	test( 'rejects a wrong password under the airowp scheme', async ( { playwright } ) => {
		const wrong = Buffer.from( 'admin:wrong wrong wrong wrong' ).toString( 'base64' );
		const ctx = await anonymousContext( playwright, `airowp ${ wrong }` );
		const response = await ctx.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json' },
			data: initialize( 5 ),
		} );
		const status = response.status();
		await ctx.dispose();

		expect( status, `expected 401 but got ${ status }` ).toBe( 401 );
	} );

	/**
	 * A credential without the marker must not be honoured — this is what keeps a
	 * future OAuth Bearer token from being mistaken for one of ours.
	 */
	test( 'ignores the same credential sent as a plain Bearer token', async ( { playwright } ) => {
		const ctx = await anonymousContext( playwright, `Bearer ${ credential }` );
		const response = await ctx.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json' },
			data: initialize( 6 ),
		} );
		const status = response.status();
		await ctx.dispose();

		expect( status, `expected 401 but got ${ status }` ).toBe( 401 );
	} );
} );
