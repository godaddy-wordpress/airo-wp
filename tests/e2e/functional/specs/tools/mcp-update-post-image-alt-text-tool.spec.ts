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
 * Verifies the airo-wp/update-post-image-alt-text tool implementation.
 *
 * Follows the MCP JSON-RPC protocol:
 *  1. beforeAll: POST initialize → server returns mcp-session-id header
 *  2. beforeAll: create a test post with an image block
 *  3. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/update-post-image-alt-text tool', () => {
  let sessionId: string;
  let testPostId: number;
  const imageUrl = 'https://example.com/test-image.jpg';

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

    // Create a test post with an image block.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'Image Alt Text Test Post',
      content: `<!-- wp:image {"url":"${imageUrl}"} --><figure class="wp-block-image"><img src="${imageUrl}" alt="original alt"/></figure><!-- /wp:image -->`,
    }, 2);

    const createBody = await createResponse.json();
    const createData = JSON.parse(createBody.result?.content[0].text);
    expect(createData.success, `failed to create test post: ${JSON.stringify(createData)}`).toBe(true);
    testPostId = createData.post_id;
  });

  test('successfully updates image alt text', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-post-image-alt-text', {
      post_id: testPostId,
      image_src: imageUrl,
      alt: 'Updated alt text',
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.post_id, 'post_id should match').toBe(testPostId);
    expect(data.image_src, 'image_src should match').toBe(imageUrl);
  });

  test('returns error for a non-existent post', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-post-image-alt-text', {
      post_id: 999999,
      image_src: imageUrl,
      alt: 'Some alt text',
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
    expect(data.message).toContain('999999');
  });

  test('returns error when image not found in post', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-post-image-alt-text', {
      post_id: testPostId,
      image_src: 'https://example.com/nonexistent-image.jpg',
      alt: 'Some alt text',
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

  test('rejects missing required fields via schema validation', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-post-image-alt-text', {
      image_src: imageUrl,
      alt: 'Some alt text',
      // Missing post_id
    }, 6);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // MCP schema validation rejects missing required 'post_id' with isError=true.
    expect(body.result?.isError, 'should reject missing required post_id').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('required');
  });
});
