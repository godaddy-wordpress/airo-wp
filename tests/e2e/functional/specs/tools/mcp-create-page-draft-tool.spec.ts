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

test.describe('airo-wp/create-page-draft tool', () => {
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

  test('creates a draft from a published page', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Published Page for Draft',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    });
    expect(createPageResp.status()).toBe(200);
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    expect(pageData.success).toBe(true);
    const pageId = pageData.post_id;

    // Create draft.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);
    expect(content.draft_id).toBeGreaterThan(0);
    expect(content.original_id).toBe(pageId);
    expect(content.edit_url).toBeDefined();
  });

  test('returns error when page already has a draft', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page With Existing Draft',
      content: '<p>Content</p>',
      post_type: 'page',
      status: 'publish',
    }, 4);
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create first draft.
    await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 5);

    // Attempt second draft.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 6);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('draft');
  });

  test('returns error for non-existent page', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: 999999,
    }, 7);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('not found');
  });
});
