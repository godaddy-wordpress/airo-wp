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
 * Verifies the airo-wp/get-global-styles tool implementation.
 */
test.describe('airo-wp/get-global-styles tool', () => {
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

  test('retrieves a global style by ID (happy path)', async ({ requestUtils }) => {
    // First, list global styles to obtain a valid ID.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles', {});

    expect(listResponse.status(), `list call failed: ${await listResponse.text()}`).toBe(200);

    const listBody = await listResponse.json();
    expect(listBody.error, `JSON-RPC error: ${JSON.stringify(listBody.error)}`).toBeUndefined();
    expect(listBody.result?.isError).not.toBe(true);

    const listContent: Array<{ type: string; text: string }> = listBody.result?.content ?? [];
    expect(listContent.length).toBeGreaterThan(0);
    const listData = JSON.parse(listContent[0].text);

    expect(listData.success, `list-global-styles failed: ${JSON.stringify(listData)}`).toBe(true);
    expect(listData.styles.length, 'should have at least one global style').toBeGreaterThan(0);

    const styleId = listData.styles[0].id;

    // Now get the global style by ID.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-global-styles', {
      id: styleId,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.data.id).toBe(styleId);
    expect(data.data.title).toBeDefined();
    expect(data.data.title.rendered).toBeDefined();
  });

  test('returns error for non-existent ID', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-global-styles', {
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

  test('edit context returns additional fields (slug, date_gmt)', async ({ requestUtils }) => {
    // First, list global styles to obtain a valid ID.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles', {}, 5);

    const listBody = await listResponse.json();
    const listContent: Array<{ type: string; text: string }> = listBody.result?.content ?? [];
    const listData = JSON.parse(listContent[0].text);
    const styleId = listData.styles[0].id;

    // Get with edit context.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-global-styles', {
      id: styleId,
      context: 'edit',
    }, 6);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.data.id).toBe(styleId);

    // Edit context should include additional fields.
    expect(data.data.slug, 'edit context should include slug').toBeDefined();
    expect(data.data.date_gmt, 'edit context should include date_gmt').toBeDefined();
    expect(data.data.modified_gmt, 'edit context should include modified_gmt').toBeDefined();
    expect(data.data.title.raw, 'edit context should include title.raw').toBeDefined();
    expect(data.data.settings, 'edit context should include settings').toBeDefined();
    expect(data.data.styles, 'edit context should include styles').toBeDefined();
  });
});
