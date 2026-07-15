import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';

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
  const response = await requestUtils.request.post(MCP_ENDPOINT, {
    headers: {
      'Content-Type': 'application/json',
      'Mcp-Session-Id': sessionId,
      'X-WP-Nonce': requestUtils.storageState!.nonce,
    },
    data: {
      jsonrpc: '2.0',
      id,
      method: 'tools/call',
      params: { name: toolName, arguments: args },
    },
  });
  return response;
}

test.describe('airo-wp/get-page-draft-status tool', () => {
  let sessionId: string;

  test.beforeAll(async ({ requestUtils }) => {
    const initResponse = await requestUtils.request.post(MCP_ENDPOINT, {
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': requestUtils.storageState!.nonce },
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
    });

    expect(
      initResponse.status(),
      `initialize failed: ${await initResponse.text()}`
    ).toBe(200);

    sessionId = initResponse.headers()['mcp-session-id'];
    expect(sessionId, 'initialize did not return mcp-session-id header').toBeTruthy();
  });

  test('returns can_create=true for published page with no draft', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page Draft Status - No Draft',
      content: '<p>Content</p>',
      post_type: 'page',
      status: 'publish',
    });
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-page-draft-status', {
      post_id: pageId,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);
    expect(content.is_draft).toBe(false);
    expect(content.has_draft).toBe(false);
    expect(content.can_create).toBe(true);
    expect(content.draft_id).toBeNull();
    expect(content.original_id).toBe(pageId);
  });

  test('returns has_draft=true after creating a draft', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page Draft Status - Has Draft',
      content: '<p>Content</p>',
      post_type: 'page',
      status: 'publish',
    }, 4);
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create draft.
    const draftResp = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 5);
    const draftBody = await draftResp.json();
    const draftData = JSON.parse(draftBody.result.content[0].text);
    expect(draftData.success).toBe(true);
    const draftId = draftData.draft_id;

    // Check status of original page.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-page-draft-status', {
      post_id: pageId,
    }, 6);

    const body = await response.json();
    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);
    expect(content.is_draft).toBe(false);
    expect(content.has_draft).toBe(true);
    expect(content.draft_id).toBe(draftId);
    expect(content.can_create).toBe(false);
  });

  test('returns is_draft=true when checking the draft itself', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page Draft Status - Is Draft',
      content: '<p>Content</p>',
      post_type: 'page',
      status: 'publish',
    }, 7);
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create draft.
    const draftResp = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 8);
    const draftBody = await draftResp.json();
    const draftData = JSON.parse(draftBody.result.content[0].text);
    const draftId = draftData.draft_id;

    // Check status of draft page.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-page-draft-status', {
      post_id: draftId,
    }, 9);

    const body = await response.json();
    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);
    expect(content.is_draft).toBe(true);
    expect(content.original_id).toBe(pageId);
    expect(content.can_create).toBe(false);
  });

  test('returns error for non-existent page', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-page-draft-status', {
      post_id: 999999,
    }, 10);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('not found');
  });
});
