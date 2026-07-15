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
 * Verifies the airo-wp/list-global-styles-revisions tool implementation.
 */
test.describe('airo-wp/list-global-styles-revisions tool', () => {
  let sessionId: string;
  let globalStylesId: number;

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

    // Get a valid global styles ID from list-global-styles.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles', {});
    const listBody = await listResponse.json();
    const listContent: Array<{ type: string; text: string }> = listBody.result?.content ?? [];
    const listData = JSON.parse(listContent[0].text);

    expect(listData.success, `list-global-styles failed: ${JSON.stringify(listData)}`).toBe(true);
    expect(listData.styles.length, 'need at least one global style').toBeGreaterThan(0);
    globalStylesId = listData.styles[0].id;

    // Update global styles to ensure at least one revision exists.
    const updateResponse = await callTool(requestUtils, sessionId, 'airo-wp-update-global-styles', {
      id: globalStylesId,
      styles: {
        color: {
          background: '#e2e-revision-test',
        },
      },
    }, 99);

    const updateBody = await updateResponse.json();
    const updateContent: Array<{ type: string; text: string }> = updateBody.result?.content ?? [];
    const updateData = JSON.parse(updateContent[0].text);
    expect(updateData.success, `update-global-styles failed: ${JSON.stringify(updateData)}`).toBe(true);
  });

  test('lists revisions for a global styles post (happy path)', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles-revisions', {
      id: globalStylesId,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.revisions), 'revisions should be an array').toBe(true);
    expect(data.revisions.length, 'should have at least one revision').toBeGreaterThan(0);
    expect(data.total, 'total should be a number').toBeGreaterThanOrEqual(1);
    expect(data.total_pages, 'total_pages should be a number').toBeGreaterThanOrEqual(1);

    // Verify structure of the first revision.
    const revision = data.revisions[0];
    expect(revision.id, 'revision should have id').toBeDefined();
    expect(revision.parent, 'revision should have parent').toBe(globalStylesId);
  });

  test('returns error for non-existent global styles ID', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles-revisions', {
      id: 99999,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, 'should return success=false for non-existent ID').toBe(false);
    expect(data.message).toBeDefined();
  });
});
