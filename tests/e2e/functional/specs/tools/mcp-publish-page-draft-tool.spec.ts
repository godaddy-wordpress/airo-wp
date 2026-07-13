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

test.describe('airo-wp/publish-page-draft tool', () => {
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

  test('publishes a draft back to its original page', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page for Publish Draft',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    });
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create draft with modified content.
    const draftResp = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 3);
    const draftBody = await draftResp.json();
    const draftData = JSON.parse(draftBody.result.content[0].text);
    expect(draftData.success).toBe(true);
    const draftId = draftData.draft_id;

    // Publish the draft.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-publish-page-draft', {
      draft_id: draftId,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);
    expect(content.original_id).toBe(pageId);
  });

  test('returns error for non-existent draft', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-publish-page-draft', {
      draft_id: 999999,
    }, 5);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('does not exist');
  });

  test('publishes draft with force_delete: true permanently deletes draft', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page for Publish Draft (force_delete true)',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    }, 6);
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create draft.
    const draftResp = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 7);
    const draftBody = await draftResp.json();
    const draftData = JSON.parse(draftBody.result.content[0].text);
    expect(draftData.success).toBe(true);
    const draftId = draftData.draft_id;

    // Publish with force_delete: true.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-publish-page-draft', {
      draft_id: draftId,
      force_delete: true,
    }, 8);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);

    // Verify the draft post was permanently deleted (not trashed).
    const getPostResp = await callTool(requestUtils, sessionId, 'airo-wp-get-post', {
      post_id: draftId,
    }, 9);
    const getPostBody = await getPostResp.json();
    const getPostData = JSON.parse(getPostBody.result.content[0].text);
    expect(getPostData.status).toBe('not_found');
  });

  test('publishes draft with force_delete: false (default) leaves draft in trash', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page for Publish Draft (force_delete false)',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    }, 10);
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create draft.
    const draftResp = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 11);
    const draftBody = await draftResp.json();
    const draftData = JSON.parse(draftBody.result.content[0].text);
    expect(draftData.success).toBe(true);
    const draftId = draftData.draft_id;

    // Publish with force_delete: false.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-publish-page-draft', {
      draft_id: draftId,
      force_delete: false,
    }, 12);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);

    // Verify the draft post was moved to trash (not permanently deleted).
    const getPostResp = await callTool(requestUtils, sessionId, 'airo-wp-get-post', {
      post_id: draftId,
    }, 13);
    const getPostBody = await getPostResp.json();
    const getPostData = JSON.parse(getPostBody.result.content[0].text);
    expect(getPostData.status).toBe('trash');
  });
});
