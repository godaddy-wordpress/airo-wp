import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';

test.describe( 'MCP server › auth enforcement', () => {
	test( 'rejects requests with invalid credentials with 401', async ( { requestUtils } ) => {
		// Create an unauthenticated context with bad Basic auth credentials.
		const invalidCtx = await ( requestUtils.request as any )._playwright.request.newContext( {
			baseURL: process.env.WP_BASE_URL,
			extraHTTPHeaders: { Authorization: 'Basic invalid-credentials' },
		} );
		const response = await invalidCtx.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json' },
			data: {
				jsonrpc: '2.0', id: 1, method: 'initialize',
				params: { protocolVersion: '2024-11-05', capabilities: {}, clientInfo: { name: 'playwright-e2e', version: '1.0' } },
			},
		} );
		await invalidCtx.dispose();
		expect( response.status(), `expected 401 but got ${ response.status() }` ).toBe( 401 );
	} );
} );

test.describe( 'MCP server › session', () => {
	let sessionId: string;

	test.beforeAll( async ( { requestUtils } ) => {
		const initResponse = await requestUtils.request.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': requestUtils.storageState!.nonce },
			data: { jsonrpc: '2.0', id: 1, method: 'initialize', params: { protocolVersion: '2024-11-05', capabilities: {}, clientInfo: { name: 'playwright-e2e', version: '1.0' } } },
		} );
		expect( initResponse.status() ).toBe( 200 );
		sessionId = initResponse.headers()[ 'mcp-session-id' ];
		expect( sessionId ).toBeTruthy();
	} );

	test( 'tools/list includes airo-wp-get-site-info', async ( { requestUtils } ) => {
		const response = await requestUtils.request.post( MCP_ENDPOINT, {
			headers: { 'Content-Type': 'application/json', 'Mcp-Session-Id': sessionId, 'X-WP-Nonce': requestUtils.storageState!.nonce },
			data: { jsonrpc: '2.0', id: 2, method: 'tools/list', params: {} },
		} );
		expect( response.status() ).toBe( 200 );
		const body = await response.json();
		const tools: Array< { name: string } > = body.result?.tools ?? [];
		expect( tools.some( ( t ) => t.name === 'airo-wp-get-site-info' ) ).toBe( true );
	} );
} );
