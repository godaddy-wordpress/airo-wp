import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';

/**
 * Verifies the airo-wp/list-plugins tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/list-plugins tool', () => {
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

  test('returns a list of installed plugins', async ({ requestUtils }) => {
    const response = await requestUtils.request.post(MCP_ENDPOINT, {
      headers: {
        'Content-Type': 'application/json',
        'Mcp-Session-Id': sessionId,
      'X-WP-Nonce': requestUtils.storageState!.nonce,
      },
      data: {
        jsonrpc: '2.0',
        id: 2,
        method: 'tools/call',
        params: {
          name: 'airo-wp-list-plugins',
          arguments: {},
        },
      },
    });

    expect(
      response.status(),
      `tools/call failed: ${await response.text()}`
    ).toBe(200);

    const body = await response.json();
    expect(body.error, `tools/call returned JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, 'tools/call result has isError=true').not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length, 'tools/call result has no content items').toBeGreaterThan(0);
    expect(content[0].type, 'content item is not text').toBe('text');

    const data = JSON.parse(content[0].text);

    expect(data.success, 'response should indicate success').toBe(true);
    expect(Array.isArray(data.plugins), 'plugins must be an array').toBe(true);
    expect(typeof data.total, 'total must be a number').toBe('number');
  });

  test('view context plugin items include update_available and new_version', async ({ requestUtils }) => {
    const response = await requestUtils.request.post(MCP_ENDPOINT, {
      headers: {
        'Content-Type': 'application/json',
        'Mcp-Session-Id': sessionId,
      'X-WP-Nonce': requestUtils.storageState!.nonce,
      },
      data: {
        jsonrpc: '2.0',
        id: 3,
        method: 'tools/call',
        params: {
          name: 'airo-wp-list-plugins',
          arguments: {},
        },
      },
    });

    expect(
      response.status(),
      `tools/call failed: ${await response.text()}`
    ).toBe(200);

    const body = await response.json();
    expect(body.error, `tools/call returned JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, 'tools/call result has isError=true').not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length, 'tools/call result has no content items').toBeGreaterThan(0);
    expect(content[0].type, 'content item is not text').toBe('text');

    const data = JSON.parse(content[0].text);

    expect(Array.isArray(data.plugins), 'plugins must be an array').toBe(true);

    for (const plugin of data.plugins as Record<string, unknown>[]) {
      expect(typeof plugin['update_available'], `update_available must be boolean for plugin: ${plugin['slug']}`).toBe('boolean');
      const newVersion = plugin['new_version'];
      expect(
        newVersion === null || typeof newVersion === 'string',
        `new_version must be string or null for plugin: ${plugin['slug']}`
      ).toBe(true);
    }
  });
});
