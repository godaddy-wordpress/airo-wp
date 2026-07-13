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
 * Verifies the airo-wp/list-template-part-revisions tool implementation.
 */
test.describe('airo-wp/list-template-part-revisions tool', () => {
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

  test('returns empty revisions for a theme-only template part (no DB override)', async ({ requestUtils }) => {
    // The "footer" template part should be theme-only with no revisions.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-template-part-revisions', {
      id: 'twentytwentyfive//footer',
    });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.revisions), 'revisions should be an array').toBe(true);
    expect(data.revisions.length).toBe(0);
    expect(data.total).toBe(0);
  });

  test('returns revisions for a template part with DB override', async ({ requestUtils }) => {
    // Create a DB override first.
    await callTool(requestUtils, sessionId, 'airo-wp-update-template-part', {
      theme: 'twentytwentyfive',
      part_name: 'header',
      html: '<!-- wp:paragraph --><p>Part revision test v1</p><!-- /wp:paragraph -->',
    }, 3);

    // Update again to generate another revision.
    await callTool(requestUtils, sessionId, 'airo-wp-update-template-part', {
      theme: 'twentytwentyfive',
      part_name: 'header',
      html: '<!-- wp:paragraph --><p>Part revision test v2</p><!-- /wp:paragraph -->',
    }, 4);

    // List revisions.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-template-part-revisions', {
      id: 'twentytwentyfive//header',
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.revisions), 'revisions should be an array').toBe(true);
    expect(data.total).toBeGreaterThanOrEqual(1);
  });

  test('returns error for non-existent template part ID', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-template-part-revisions', {
      id: 'twentytwentyfive//nonexistent-part-slug',
    }, 6);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure for non-existent template part: ${JSON.stringify(data)}`).toBe(false);
    expect(data.message).toContain('not found');
  });

  test('returns error when id is missing', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-template-part-revisions', {}, 7);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // MCP schema validation rejects missing required 'id' with isError=true.
    expect(body.result?.isError, 'should reject missing required id').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('id');
  });
});
