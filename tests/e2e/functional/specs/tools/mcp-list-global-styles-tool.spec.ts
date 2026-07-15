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
 * Verifies the airo-wp/list-global-styles tool implementation.
 */
test.describe('airo-wp/list-global-styles tool', () => {
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

  test('lists global styles with at least one result for active theme', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles', {});

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.styles), 'styles should be an array').toBe(true);
    expect(data.styles.length, 'should have at least one global style').toBeGreaterThan(0);

    // Verify structure of the first style.
    const style = data.styles[0];
    expect(style.id, 'style should have id').toBeDefined();
    expect(style.title, 'style should have title').toBeDefined();
    expect(style.status, 'style should have status').toBeDefined();
    expect(style.date, 'style should have date').toBeDefined();
    expect(style.modified, 'style should have modified').toBeDefined();
  });

  test('returns total count matching styles array length', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles', {}, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.total, 'total should match styles array length').toBe(data.styles.length);
  });

  test('filters by theme using active theme stylesheet', async ({ requestUtils }) => {
    // Detect the active theme slug so the filter uses a value that actually
    // exists in this environment instead of a hardcoded name.
    const themes = await requestUtils.rest<Array<{ stylesheet: string }>>({
      method: 'GET',
      path: '/wp/v2/themes?status=active',
    });
    const activeTheme = themes[0]?.stylesheet;
    expect(activeTheme, 'could not determine active theme').toBeTruthy();

    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles', {
      theme: activeTheme,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.styles), 'styles should be an array').toBe(true);
    expect(data.styles.length, `should find styles for ${activeTheme}`).toBeGreaterThan(0);

    // Every returned style should belong to the filtered theme.
    for (const style of data.styles) {
      expect(style.theme, `style theme should be ${activeTheme}`).toBe(activeTheme);
    }
  });
});
