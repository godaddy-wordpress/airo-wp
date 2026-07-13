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

/**
 * Verifies the airo-wp/delete-post tool implementation.
 *
 * Follows the MCP JSON-RPC protocol:
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/delete-post tool', () => {
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

  test('permanently deletes a post with force_delete=true', async ({ requestUtils }) => {
    // Create a post to delete.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Post to Permanently Delete',
      content: 'This post will be permanently deleted.',
    }, 2);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    // Delete the post permanently.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-delete-post', {
      post_id: postId,
      force_delete: true,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.deleted_permanently, 'deleted_permanently should be true').toBe(true);
  });

  test('returns error for a non-existent post', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-delete-post', {
      post_id: 999999,
      force_delete: true,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
  });

  test('returns error when attempting to trash an already-trashed post', async ({ requestUtils }) => {
    // Create a post to trash.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Post to Trash Twice',
      content: 'This post will be trashed and then trashed again.',
    }, 5);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    // Trash the post (force_delete=false).
    const trashResponse = await callTool(requestUtils, sessionId, 'airo-wp-delete-post', {
      post_id: postId,
      force_delete: false,
    }, 6);

    const trashBody = await trashResponse.json();
    const trashData = JSON.parse(trashBody.result?.content[0].text);
    expect(trashData.success, `failed to trash post: ${JSON.stringify(trashData)}`).toBe(true);

    // Attempt to trash the already-trashed post again.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-delete-post', {
      post_id: postId,
      force_delete: false,
    }, 7);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
    expect(
      JSON.stringify(data).toLowerCase(),
      'response should mention "already in trash"'
    ).toContain('already in trash');
  });
});
