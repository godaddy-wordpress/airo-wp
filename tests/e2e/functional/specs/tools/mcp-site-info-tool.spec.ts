import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';

test.describe( 'airo-wp/get-site-info tool', () => {
	let sessionId: string;

	test.beforeAll( async ( { requestUtils } ) => {
		const initResponse = await requestUtils.request.post( MCP_ENDPOINT, {
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': requestUtils.storageState!.nonce,
			},
			data: {
				jsonrpc: '2.0',
				id: 1,
				method: 'initialize',
				params: {
					protocolVersion: '2024-11-05',
					capabilities: {},
					clientInfo: { name: 'playwright-e2e', version: '1.0' },
				},
			},
		} );

		expect(
			initResponse.status(),
			`initialize failed: ${ await initResponse.text() }`
		).toBe( 200 );

		sessionId = initResponse.headers()[ 'mcp-session-id' ];
		expect( sessionId, 'initialize did not return mcp-session-id header' ).toBeTruthy();
	} );

	test( 'returns expected site info format', async ( { requestUtils } ) => {
		const response = await requestUtils.request.post( MCP_ENDPOINT, {
			headers: {
				'Content-Type': 'application/json',
				'Mcp-Session-Id': sessionId,
				'X-WP-Nonce': requestUtils.storageState!.nonce,
			},
			data: {
				jsonrpc: '2.0',
				id: 2,
				method: 'tools/call',
				params: { name: 'airo-wp-get-site-info', arguments: {} },
			},
		} );

		expect( response.status(), `tools/call failed: ${ await response.text() }` ).toBe( 200 );

		const body = await response.json();
		expect( body.error, `tools/call returned JSON-RPC error: ${ JSON.stringify( body.error ) }` ).toBeUndefined();
		expect( body.result?.isError, 'tools/call result has isError=true' ).not.toBe( true );

		const content: Array< { type: string; text: string } > = body.result?.content ?? [];
		expect( content.length, 'tools/call result has no content items' ).toBeGreaterThan( 0 );
		expect( content[ 0 ].type, 'content item is not text' ).toBe( 'text' );

		const info = JSON.parse( content[ 0 ].text );
		expect( typeof info.site_name ).toBe( 'string' );
		expect( typeof info.site_url ).toBe( 'string' );
		expect( info.site_url ).toMatch( /^https?:\/\// );
		expect( typeof info.description ).toBe( 'string' );
		expect( typeof info.wordpress_version ).toBe( 'string' );
		expect( info.wordpress_version ).toBeTruthy();
		expect( typeof info.is_published ).toBe( 'boolean' );
		expect( typeof info.site_locale ).toBe( 'string' );
		expect( info.site_locale ).toBeTruthy();
		expect( typeof info.timezone ).toBe( 'string' );
		expect( typeof info.date_format ).toBe( 'string' );
		expect( typeof info.time_format ).toBe( 'string' );
		expect( typeof info.posts_per_page ).toBe( 'number' );
		expect( typeof info.blog_public ).toBe( 'boolean' );
		expect( info.site_logo === null || typeof info.site_logo === 'number' ).toBe( true );
		expect( info.site_icon === null || typeof info.site_icon === 'number' ).toBe( true );
		expect( info.stats ).toBeUndefined();
		expect( info.theme_info ).toBeUndefined();
	} );
} );
