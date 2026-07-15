import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';
const REST_BASE    = '/wp-json/airo-wp/v1/drafts';

/**
 * Helper: call a tool via the MCP JSON-RPC endpoint.
 */
async function callTool(
	requestUtils: import('@wordpress/e2e-test-utils-playwright').RequestUtils,
	sessionId: string,
	toolName: string,
	args: Record<string, unknown>,
	id = 2
) {
	return requestUtils.request.post( `${ process.env.WP_BASE_URL }${ MCP_ENDPOINT }`, {
		headers: {
			'Content-Type':   'application/json',
			'Mcp-Session-Id': sessionId,
			'X-WP-Nonce':     requestUtils.storageState!.nonce,
		},
		data: {
			jsonrpc: '2.0',
			id,
			method: 'tools/call',
			params: { name: toolName, arguments: args },
		},
	} );
}

/**
 * Helper: create a published page and a draft via MCP, return { pageId, draftId }.
 */
async function createPageWithDraft(
	requestUtils: import('@wordpress/e2e-test-utils-playwright').RequestUtils,
	sessionId: string,
	title: string,
	idBase = 10
): Promise<{ pageId: number; draftId: number }> {
	const pageResp = await callTool( requestUtils, sessionId, 'airo-wp-create-post', {
		title,
		content:   '<p>Content</p>',
		post_type: 'page',
		status:    'publish',
	}, idBase );
	const pageBody = await pageResp.json();
	const pageId   = JSON.parse( pageBody.result.content[ 0 ].text ).post_id as number;

	const draftResp = await callTool( requestUtils, sessionId, 'airo-wp-create-page-draft', {
		post_id: pageId,
	}, idBase + 1 );
	const draftBody = await draftResp.json();
	const draftId   = JSON.parse( draftBody.result.content[ 0 ].text ).draft_id as number;

	return { pageId, draftId };
}

test.describe( 'REST draft-pages endpoints', () => {
	let sessionId: string;

	test.beforeAll( async ( { requestUtils } ) => {
		const initResponse = await requestUtils.request.post(
			`${ process.env.WP_BASE_URL }${ MCP_ENDPOINT }`,
			{
				headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': requestUtils.storageState!.nonce },
				data: {
					jsonrpc: '2.0',
					id:      1,
					method:  'initialize',
					params:  {
						protocolVersion: '2024-11-05',
						capabilities:    {},
						clientInfo:      { name: 'playwright-e2e', version: '1.0' },
					},
				},
			}
		);

		expect(
			initResponse.status(),
			`initialize failed: ${ await initResponse.text() }`
		).toBe( 200 );

		sessionId = initResponse.headers()[ 'mcp-session-id' ];
		expect( sessionId, 'initialize did not return mcp-session-id header' ).toBeTruthy();
	} );

	// ---------------------------------------------------------------------------
	// POST /airo-wp/v1/drafts/{id}/publish
	// ---------------------------------------------------------------------------

	test( 'POST publish — returns 200 and success on happy path', async ( { requestUtils } ) => {
		const { pageId, draftId } = await createPageWithDraft(
			requestUtils, sessionId, 'E2E REST Publish Happy Path', 10
		);

		const body = await requestUtils.rest< { success: boolean; original_id: number; redirect_url: string } >( {
			method: 'POST',
			path:   `/airo-wp/v1/drafts/${ draftId }/publish`,
		} );

		expect( body.success ).toBe( true );
		expect( body.original_id ).toBe( pageId );
		expect( typeof body.redirect_url ).toBe( 'string' );
	} );

	test( 'POST publish — returns 401 or 403 when unauthenticated', async ( { requestUtils, playwright } ) => {
		const { draftId } = await createPageWithDraft(
			requestUtils, sessionId, 'E2E REST Publish Unauth', 20
		);

		// A truly unauthenticated call needs a fresh context with neither the
		// storageState cookie nor the nonce header.
		const anonCtx = await playwright.request.newContext( {
			storageState: { cookies: [], origins: [] },
		} );
		const response = await anonCtx.post(
			`${ process.env.WP_BASE_URL }${ REST_BASE }/${ draftId }/publish`
		);
		await anonCtx.dispose();

		expect( [ 401, 403 ] ).toContain( response.status() );
	} );

	test( 'POST publish — returns 4xx when draft not found', async ( { requestUtils } ) => {
		const resp = await requestUtils.request.post(
			`${ process.env.WP_BASE_URL }${ REST_BASE }/999999/publish`,
			{ headers: { 'X-WP-Nonce': requestUtils.storageState!.nonce } }
		);

		expect( resp.status() ).toBeGreaterThanOrEqual( 400 );
		expect( resp.status() ).toBeLessThan( 500 );
	} );

	test( 'POST publish — returns 403 when post has no draft link', async ( { requestUtils } ) => {
		// A published page has no META_DRAFT_OF — permissions check returns false (403).
		const pageResp = await callTool( requestUtils, sessionId, 'airo-wp-create-post', {
			title:     'E2E REST Publish Non-Draft',
			content:   '<p>Content</p>',
			post_type: 'page',
			status:    'publish',
		}, 50 );
		const pageBody = await pageResp.json();
		const pageId = JSON.parse( pageBody.result.content[ 0 ].text ).post_id as number;

		const resp = await requestUtils.request.post(
			`${ process.env.WP_BASE_URL }${ REST_BASE }/${ pageId }/publish`,
			{ headers: { 'X-WP-Nonce': requestUtils.storageState!.nonce } }
		);

		expect( resp.status() ).toBe( 403 );
	} );

	// ---------------------------------------------------------------------------
	// DELETE /airo-wp/v1/drafts/{id}/discard
	// ---------------------------------------------------------------------------

	test( 'DELETE discard — returns 200 and success on happy path', async ( { requestUtils } ) => {
		const { pageId, draftId } = await createPageWithDraft(
			requestUtils, sessionId, 'E2E REST Discard Happy Path', 30
		);

		// requestUtils.rest() does not support DELETE — use the underlying request context.
		const resp = await requestUtils.request.delete(
			`${ process.env.WP_BASE_URL }${ REST_BASE }/${ draftId }/discard`,
			{ headers: { 'X-WP-Nonce': requestUtils.storageState!.nonce } }
		);

		expect( resp.status(), `discard failed: ${ await resp.text() }` ).toBe( 200 );

		const body = await resp.json();
		expect( body.success ).toBe( true );
		expect( body.original_id ).toBe( pageId );
		expect( typeof body.redirect_url ).toBe( 'string' );
	} );

	test( 'DELETE discard — returns 401 or 403 when unauthenticated', async ( { requestUtils, playwright } ) => {
		const { draftId } = await createPageWithDraft(
			requestUtils, sessionId, 'E2E REST Discard Unauth', 40
		);

		// A truly unauthenticated call needs a fresh context with neither the
		// storageState cookie nor the nonce header.
		const anonCtx = await playwright.request.newContext( {
			storageState: { cookies: [], origins: [] },
		} );
		const response = await anonCtx.delete(
			`${ process.env.WP_BASE_URL }${ REST_BASE }/${ draftId }/discard`
		);
		await anonCtx.dispose();

		expect( [ 401, 403 ] ).toContain( response.status() );
	} );

	test( 'DELETE discard — returns 4xx when draft not found', async ( { requestUtils } ) => {
		const resp = await requestUtils.request.delete(
			`${ process.env.WP_BASE_URL }${ REST_BASE }/999999/discard`,
			{ headers: { 'X-WP-Nonce': requestUtils.storageState!.nonce } }
		);

		expect( resp.status() ).toBeGreaterThanOrEqual( 400 );
		expect( resp.status() ).toBeLessThan( 500 );
	} );

	test( 'DELETE discard — returns 400 when post is not a draft', async ( { requestUtils } ) => {
		// A published page is not a draft — service returns not_a_draft (400).
		const pageResp = await callTool( requestUtils, sessionId, 'airo-wp-create-post', {
			title:     'E2E REST Discard Non-Draft',
			content:   '<p>Content</p>',
			post_type: 'page',
			status:    'publish',
		}, 60 );
		const pageBody = await pageResp.json();
		const pageId = JSON.parse( pageBody.result.content[ 0 ].text ).post_id as number;

		const resp = await requestUtils.request.delete(
			`${ process.env.WP_BASE_URL }${ REST_BASE }/${ pageId }/discard`,
			{ headers: { 'X-WP-Nonce': requestUtils.storageState!.nonce } }
		);

		expect( resp.status() ).toBe( 400 );
	} );
} );
