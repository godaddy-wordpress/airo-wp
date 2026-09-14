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
 * Verifies the airo-wp/activate-plugin tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/activate-plugin tool', () => {
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

  test('rejects an empty plugin_slug at the schema boundary', async ({ requestUtils }) => {
    // minLength on plugin_slug means an empty string never reaches execute(): the MCP
    // layer rejects it during input validation and returns isError=true, rather than
    // the tool returning its own success:false payload.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-plugin', { plugin_slug: '' });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, 'empty plugin_slug should fail input validation').toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    expect(content[0].text.toLowerCase()).toContain('invalid input');
  });

  test('activates an installed but inactive plugin', async ({ requestUtils }) => {
    // First, deactivate akismet to ensure it is inactive (it ships with WP).
    await callTool(requestUtils, sessionId, 'airo-wp-deactivate-plugin', { plugin_slug: 'akismet' }, 10);

    // Now activate it.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-plugin', { plugin_slug: 'akismet' }, 11);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toMatch(/activated/);
    expect(data.plugin).toBe('akismet');
  });

  test('reports plugin already active on second activation', async ({ requestUtils }) => {
    // Akismet was activated in the previous test; activating again should say "already active".
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-plugin', { plugin_slug: 'akismet' }, 12);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toContain('already active');
    expect(data.plugin).toBe('akismet');
  });

  test('installs and activates a plugin from WordPress.org', async ({ requestUtils }) => {
    // Use a small, real WordPress.org plugin that won't be pre-installed.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-plugin', { plugin_slug: 'classic-widgets' }, 13);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toMatch(/activated/);
    expect(data.plugin).toBe('classic-widgets');
  });

  test('returns error when plugin does not exist on WordPress.org', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-plugin', { plugin_slug: 'this-plugin-does-not-exist-xyz-99' }, 14);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
    expect(data.plugin).toBe('this-plugin-does-not-exist-xyz-99');
  });
});
