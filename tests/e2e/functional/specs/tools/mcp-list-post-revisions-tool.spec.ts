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
 * Verifies the airo-wp/list-post-revisions tool implementation.
 *
 * Follows the MCP JSON-RPC protocol:
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/list-post-revisions tool', () => {
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

  test('lists revisions for a post with at least one revision', async ({ requestUtils }) => {
    // Create a post.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Revisions Test Post',
      content: 'Original content.',
    }, 2);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    // Update the post to generate a revision.
    const updateResponse = await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: postId,
      title: 'Revisions Test Post Updated',
      content: 'Updated content.',
    }, 3);

    const updateBody = await updateResponse.json();
    const updateData = JSON.parse(updateBody.result?.content[0].text);
    expect(updateData.success, `failed to update post: ${JSON.stringify(updateData)}`).toBe(true);

    // List revisions.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-post-revisions', {
      parent: postId,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(Array.isArray(data.revisions), 'revisions should be an array').toBe(true);
    expect(data.total, 'total should be >= 1').toBeGreaterThanOrEqual(1);

    // Verify revision fields.
    const revision = data.revisions[0];
    expect(revision.id, 'revision should have id').toBeDefined();
    expect(revision.author_id, 'revision should have author_id').toBeDefined();
    expect(revision.date_created, 'revision should have date_created').toBeDefined();
    expect(revision.parent_id, 'revision should have parent_id').toBe(postId);
    expect(revision.title, 'revision should have title').toBeDefined();
    expect(revision.content, 'revision should have content').toBeDefined();
  });

  test('returns error for a non-existent parent post', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-post-revisions', {
      parent: 999999,
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
    expect(data.message.toLowerCase()).toContain('not found');
  });

  test('returns revisions for a freshly created post', async ({ requestUtils }) => {
    // WordPress creates an initial revision on wp_insert_post, so even a
    // brand-new post has at least 1 revision.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Fresh Post Revisions Check',
      content: 'This post will not be updated.',
    }, 6);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-post-revisions', {
      parent: postId,
    }, 7);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(Array.isArray(data.revisions), 'revisions should be an array').toBe(true);
    expect(data.revisions.length, 'should have at least 1 revision (initial)').toBeGreaterThanOrEqual(1);
    expect(data.total, 'total should be >= 1').toBeGreaterThanOrEqual(1);
  });

  test('respects per_page pagination parameter', async ({ requestUtils }) => {
    // Create a post and update it multiple times to generate several revisions.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Pagination Test Post',
      content: 'Original content.',
    }, 8);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    // Update three times to generate at least 3 revisions.
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: postId,
      content: 'Update 1.',
    }, 9);
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: postId,
      content: 'Update 2.',
    }, 10);
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: postId,
      content: 'Update 3.',
    }, 11);

    // List revisions with per_page=1.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-post-revisions', {
      parent: postId,
      per_page: 1,
    }, 12);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.per_page, 'per_page should be 1').toBe(1);
    expect(data.revisions.length, 'revisions array should have exactly 1 item').toBe(1);
    expect(data.total_pages, 'total_pages should be > 1').toBeGreaterThan(1);
  });
});
