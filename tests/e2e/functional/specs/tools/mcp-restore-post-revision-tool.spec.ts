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
 * Verifies the airo-wp/restore-post-revision tool implementation.
 *
 * Follows the MCP JSON-RPC protocol:
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/restore-post-revision tool', () => {
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

  test('restores a post to a previous revision', async ({ requestUtils }) => {
    // Create a post with an initial title.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Original Title',
      content: 'Original content for restore test.',
    }, 2);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    // Update the post to generate a new revision with different content.
    const updateResponse = await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: postId,
      title: 'Updated Title',
      content: 'Updated content for restore test.',
    }, 3);

    const updateBody = await updateResponse.json();
    const updateData = JSON.parse(updateBody.result?.content[0].text);
    expect(updateData.success, `failed to update post: ${JSON.stringify(updateData)}`).toBe(true);

    // List revisions — ordered DESC by date, so [0] is newest (the update).
    // We want to restore the OLDEST revision (original content) so that the
    // restore actually changes content and creates a new revision.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-post-revisions', {
      parent: postId,
      order: 'asc',
    }, 4);

    const listBody = await listResponse.json();
    const listData = JSON.parse(listBody.result?.content[0].text);
    expect(listData.revisions.length, 'should have at least 2 revisions').toBeGreaterThanOrEqual(2);

    // Pick the oldest revision (the original content).
    const oldestRevisionId: number = listData.revisions[0].id;

    // Restore that older revision.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-restore-post-revision', {
      parent: postId,
      id: oldestRevisionId,
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.post_id, 'post_id should be present').toBe(postId);
    expect(data.restored_revision_id, 'restored_revision_id should match').toBe(oldestRevisionId);
    expect(data.current_revision_id, 'current_revision_id should be defined').toBeDefined();
  });

  test('returns error for a non-existent revision ID', async ({ requestUtils }) => {
    // Create a valid post to use as parent.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Post for Non-Existent Revision Test',
      content: 'Some content.',
    }, 6);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create post: ${JSON.stringify(createData)}`).toBe(true);
    const postId: number = createData.post_id;

    const response = await callTool(requestUtils, sessionId, 'airo-wp-restore-post-revision', {
      parent: postId,
      id: 999999,
    }, 7);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
  });

  test('returns error when parent parameter is missing', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-restore-post-revision', {
      id: 1,
    }, 8);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // MCP schema validation rejects missing required 'parent' with isError=true.
    expect(body.result?.isError, 'should reject missing required parent').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('required');
  });

  test('returns error when revision belongs to a different parent', async ({ requestUtils }) => {
    // Create first post and update it to generate a revision.
    const create1Response = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'First Post',
      content: 'Content of first post.',
    }, 9);

    const create1Body = await create1Response.json();
    const create1Data = JSON.parse(create1Body.result?.content[0].text);
    expect(create1Data.success, `failed to create first post: ${JSON.stringify(create1Data)}`).toBe(true);
    const firstPostId: number = create1Data.post_id;

    // Update to generate a revision.
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: firstPostId,
      content: 'Updated content for first post.',
    }, 10);

    // Get a revision ID from the first post.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-post-revisions', {
      parent: firstPostId,
    }, 11);

    const listBody = await listResponse.json();
    const listData = JSON.parse(listBody.result?.content[0].text);
    expect(listData.revisions.length, 'first post should have at least 1 revision').toBeGreaterThanOrEqual(1);
    const revisionId: number = listData.revisions[0].id;

    // Create a second post.
    const create2Response = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Second Post',
      content: 'Content of second post.',
    }, 12);

    const create2Body = await create2Response.json();
    const create2Data = JSON.parse(create2Body.result?.content[0].text);
    expect(create2Data.success, `failed to create second post: ${JSON.stringify(create2Data)}`).toBe(true);
    const secondPostId: number = create2Data.post_id;

    // Try to restore first post's revision on the second post.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-restore-post-revision', {
      parent: secondPostId,
      id: revisionId,
    }, 13);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
    expect(data.message.toLowerCase()).toContain('does not belong');
  });
});
