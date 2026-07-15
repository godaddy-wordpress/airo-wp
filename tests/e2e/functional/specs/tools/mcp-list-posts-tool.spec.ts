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
 * Verifies the airo-wp/list-posts tool implementation.
 *
 * Follows the MCP JSON-RPC protocol:
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/list-posts tool', () => {
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

  test('lists posts with default parameters and returns expected structure', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-posts', {});

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(Array.isArray(data.posts), 'posts should be an array').toBe(true);
    expect(data.total, 'total should be defined').toBeDefined();
    expect(data.total_pages, 'total_pages should be defined').toBeDefined();
    expect(data.page, 'page should be defined').toBeDefined();
    expect(data.per_page, 'per_page should be defined').toBeDefined();
  });

  test('filters posts by draft status and returns only draft posts', async ({ requestUtils }) => {
    // Create a draft post so there is at least one to find.
    await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Draft Post for List Filter Test',
      content: 'Draft content.',
      status: 'draft',
    }, 3);

    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-posts', {
      status: 'draft',
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(Array.isArray(data.posts), 'posts should be an array').toBe(true);

    // All returned posts must have status 'draft'.
    for (const post of data.posts) {
      expect(post.status, `post ${post.id} should have status 'draft'`).toBe('draft');
    }
  });

  test('respects per_page pagination parameter', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-posts', {
      per_page: 1,
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.per_page, 'per_page in response should be 1').toBe(1);
    expect(
      data.posts.length,
      `posts array length should be at most 1, got: ${data.posts.length}`
    ).toBeLessThanOrEqual(1);
  });

  test('posts in default response include a featured_media_id integer field', async ({ requestUtils }) => {
    // Create a published post so the list is non-empty.
    await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Post for Featured Media Field Check',
      content: 'Content.',
      status: 'publish',
      post_type: 'post',
    }, 6);

    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-posts', {
      post_type: 'post',
      status: 'publish',
    }, 7);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(Array.isArray(data.posts), 'posts should be an array').toBe(true);
    expect(data.posts.length, 'expected at least one post').toBeGreaterThan(0);

    for (const post of data.posts) {
      expect(typeof post.featured_media_id, `post ${post.id} featured_media_id should be a number`).toBe('number');
    }
  });

  test('filters posts by featured_media_id and returns only matching posts', async ({ requestUtils }) => {
    // Upload a 1×1 transparent PNG via the WP REST Media API.
    // The MCP server has no base64-upload tool; using the REST API is the
    // correct way to create a test attachment from raw bytes.
    const imageBuffer = Buffer.from(
      'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
      'base64'
    );
    const uploadResponse = await requestUtils.request.post(
      `${process.env.WP_BASE_URL}/wp-json/wp/v2/media`,
      {
        headers: {
          'Content-Disposition': 'attachment; filename="featured-filter-test.png"',
          'Content-Type': 'image/png',
          'X-WP-Nonce': requestUtils.storageState!.nonce,
        },
        data: imageBuffer,
      }
    );

    expect(uploadResponse.status(), `media upload failed: ${await uploadResponse.text()}`).toBe(201);

    const uploadData = await uploadResponse.json();
    const attachmentId: number = uploadData.id;
    expect(attachmentId, 'attachment ID should be a positive integer').toBeGreaterThan(0);

    // Create a post and set the attachment as the featured image.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Post With Featured Image for Filter Test',
      content: 'Content.',
      status: 'publish',
      post_type: 'post',
      featured_media: attachmentId,
    }, 9);

    expect(createResponse.status(), `create-post failed: ${await createResponse.text()}`).toBe(200);

    const createBody = await createResponse.json();
    expect(createBody.error, `JSON-RPC error: ${JSON.stringify(createBody.error)}`).toBeUndefined();

    const createContent: Array<{ type: string; text: string }> = createBody.result?.content ?? [];
    const createData = JSON.parse(createContent[0].text);
    const postId: number = createData.post_id;
    expect(postId, 'created post ID should be a positive integer').toBeGreaterThan(0);

    // Filter by featured_media_id — only the post we just created should match.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-posts', {
      post_type: 'post',
      status: 'publish',
      featured_media_id: attachmentId,
    }, 10);

    expect(listResponse.status(), `list-posts failed: ${await listResponse.text()}`).toBe(200);

    const listBody = await listResponse.json();
    expect(listBody.error, `JSON-RPC error: ${JSON.stringify(listBody.error)}`).toBeUndefined();
    expect(listBody.result?.isError, `isError=true: ${JSON.stringify(listBody.result)}`).not.toBe(true);

    const listContent: Array<{ type: string; text: string }> = listBody.result?.content ?? [];
    const listData = JSON.parse(listContent[0].text);

    expect(Array.isArray(listData.posts), 'posts should be an array').toBe(true);
    expect(listData.posts.length, 'expected exactly one matching post').toBe(1);

    for (const post of listData.posts) {
      expect(post.featured_media_id, `post ${post.id} should have featured_media_id = ${attachmentId}`).toBe(attachmentId);
    }
  });
});
