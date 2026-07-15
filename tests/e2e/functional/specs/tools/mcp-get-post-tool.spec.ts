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
 * Verifies the airo-wp/get-post tool implementation.
 *
 * Follows the MCP JSON-RPC protocol:
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/get-post tool', () => {
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

  test('retrieves a post by ID with matching fields', async ({ requestUtils }) => {
    // Create a post to retrieve.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Post to Retrieve',
      content: 'Content of the post to retrieve.',
    }, 2);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    // Retrieve the post by ID.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-post', {
      post_id: postId,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.id ?? data.post_id, 'returned post ID should match').toBe(postId);
    expect(
      data.title ?? data.post_title,
      'returned post title should match'
    ).toContain('Post to Retrieve');
    expect(typeof data.featured_media_id, 'featured_media_id should be an integer').toBe('number');
  });

  test('returns not_found status for a non-existent post', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-post', {
      post_id: 999999,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.status, `expected status='not_found' but got: ${JSON.stringify(data)}`).toBe('not_found');
  });
});
