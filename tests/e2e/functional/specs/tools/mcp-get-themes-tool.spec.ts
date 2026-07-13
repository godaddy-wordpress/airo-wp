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
 * Verifies the airo-wp/get-themes tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/get-themes tool', () => {
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

  test('returns only the active theme with validated structure', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-themes', { active: true });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.themes)).toBe(true);
    expect(data.themes.length, 'should return exactly one active theme').toBe(1);

    // Validate response structure for the active theme.
    const theme = data.themes[0];
    expect(typeof theme.name).toBe('string');
    expect(theme.name.length).toBeGreaterThan(0);
    expect(typeof theme.version).toBe('string');
    expect(typeof theme.author).toBe('string');
    expect(typeof theme.stylesheet).toBe('string');
    expect(typeof theme.template).toBe('string');
    expect(theme.status).toBe('active');
    expect(typeof theme.is_block_theme).toBe('boolean');
    expect(Array.isArray(theme.tags)).toBe(true);
    // Active theme should include global_styles_id.
    expect(theme.global_styles_id).toBeDefined();
  });

  test('returns all installed themes when active is false', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-themes', { active: false }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.themes)).toBe(true);
    expect(data.themes.length, 'should have multiple themes installed').toBeGreaterThan(1);

    // Validate structure of each theme.
    for (const theme of data.themes) {
      expect(typeof theme.name).toBe('string');
      expect(typeof theme.stylesheet).toBe('string');
      expect(typeof theme.template).toBe('string');
      expect(theme.status).toMatch(/^(active|inactive)$/);
      expect(typeof theme.is_block_theme).toBe('boolean');
    }

    // Exactly one should be active.
    const activeThemes = data.themes.filter((t: { status: string }) => t.status === 'active');
    expect(activeThemes.length, 'exactly one theme should be active').toBe(1);
  });
});
