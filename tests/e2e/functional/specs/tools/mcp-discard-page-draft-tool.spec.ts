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

test.describe('airo-wp/discard-page-draft tool', () => {
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

  test('discards a draft without affecting original page', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page for Discard Draft',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    });
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create draft.
    const draftResp = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 3);
    const draftBody = await draftResp.json();
    const draftData = JSON.parse(draftBody.result.content[0].text);
    expect(draftData.success).toBe(true);
    const draftId = draftData.draft_id;

    // Discard the draft.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-discard-page-draft', {
      draft_id: draftId,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);
    expect(content.original_id).toBe(pageId);

    // Verify page can create a new draft (original is unaffected).
    const statusResp = await callTool(requestUtils, sessionId, 'airo-wp-get-page-draft-status', {
      post_id: pageId,
    }, 5);
    const statusBody = await statusResp.json();
    const statusData = JSON.parse(statusBody.result.content[0].text);
    expect(statusData.has_draft).toBe(false);
    expect(statusData.can_create).toBe(true);
  });

  test('returns error for non-existent draft', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-discard-page-draft', {
      draft_id: 999999,
    }, 6);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('does not exist');
  });

  test('discards draft with force_delete: true permanently deletes draft', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page for Discard Draft (force_delete true)',
      content: '<p>Original content</p>',
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
    expect(draftData.success).toBe(true);
    const draftId = draftData.draft_id;

    // Discard with force_delete: true.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-discard-page-draft', {
      draft_id: draftId,
      force_delete: true,
    }, 9);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);

    // Verify the original page no longer has a draft linked.
    const statusResp = await callTool(requestUtils, sessionId, 'airo-wp-get-page-draft-status', {
      post_id: pageId,
    }, 10);
    const statusBody = await statusResp.json();
    const statusData = JSON.parse(statusBody.result.content[0].text);
    expect(statusData.has_draft).toBe(false);
    expect(statusData.can_create).toBe(true);

    // Verify the draft post was permanently deleted (not trashed).
    const getPostResp = await callTool(requestUtils, sessionId, 'airo-wp-get-post', {
      post_id: draftId,
    }, 11);
    const getPostBody = await getPostResp.json();
    const getPostData = JSON.parse(getPostBody.result.content[0].text);
    expect(getPostData.status).toBe('not_found');
  });

  test('discards draft with force_delete: false (default) moves draft to trash', async ({ requestUtils }) => {
    // Create a published page.
    const createPageResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page for Discard Draft (force_delete false)',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    }, 11);
    const createBody = await createPageResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Create draft.
    const draftResp = await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
      post_id: pageId,
    }, 12);
    const draftBody = await draftResp.json();
    const draftData = JSON.parse(draftBody.result.content[0].text);
    expect(draftData.success).toBe(true);
    const draftId = draftData.draft_id;

    // Discard with force_delete: false.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-discard-page-draft', {
      draft_id: draftId,
      force_delete: false,
    }, 13);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(true);

    // Verify the original page no longer has an active draft linked.
    const statusResp = await callTool(requestUtils, sessionId, 'airo-wp-get-page-draft-status', {
      post_id: pageId,
    }, 14);
    const statusBody = await statusResp.json();
    const statusData = JSON.parse(statusBody.result.content[0].text);
    expect(statusData.has_draft).toBe(false);
    expect(statusData.can_create).toBe(true);

    // Verify the draft post was moved to trash (not permanently deleted).
    const getPostResp = await callTool(requestUtils, sessionId, 'airo-wp-get-post', {
      post_id: draftId,
    }, 15);
    const getPostBody = await getPostResp.json();
    const getPostData = JSON.parse(getPostBody.result.content[0].text);
    expect(getPostData.status).toBe('trash');
  });
});
