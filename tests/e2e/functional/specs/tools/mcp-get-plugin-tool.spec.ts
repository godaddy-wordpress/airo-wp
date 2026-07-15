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
 * Verifies the airo-wp/get-plugin tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/get-plugin tool', () => {
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

  test('returns not-found error for a non-existent plugin', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-plugin', { plugin_slug: 'nonexistent-plugin-xyz' });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success).toBe(false);
    expect(data.message.toLowerCase()).toContain('not found');
  });

  test('returns full plugin details for an installed plugin', async ({ requestUtils }) => {
    // akismet is bundled with WordPress.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-plugin', { plugin_slug: 'akismet' }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);

    // Validate response structure (default "view" context).
    const plugin = data.plugin;
    expect(plugin, 'plugin object must be present').toBeDefined();
    expect(plugin.slug).toBe('akismet');
    expect(typeof plugin.name).toBe('string');
    expect(plugin.name.length).toBeGreaterThan(0);
    expect(typeof plugin.version).toBe('string');
    expect(typeof plugin.author).toBe('string');
    expect(typeof plugin.description).toBe('string');
    expect(typeof plugin.plugin_uri).toBe('string');
    expect(typeof plugin.author_uri).toBe('string');
    expect(typeof plugin.text_domain).toBe('string');
    expect(plugin.status).toMatch(/^(active|inactive)$/);
    expect(typeof plugin.file).toBe('string');
    expect(plugin.file).toContain('akismet');
  });

  test('returns embed context with reduced fields', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-plugin', { plugin_slug: 'akismet', context: 'embed' }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);

    // Embed context returns only slug, name, version, status.
    const plugin = data.plugin;
    expect(plugin.slug).toBe('akismet');
    expect(typeof plugin.name).toBe('string');
    expect(typeof plugin.version).toBe('string');
    expect(plugin.status).toMatch(/^(active|inactive)$/);

    // Full-context fields should NOT be present in embed.
    expect(plugin.author).toBeUndefined();
    expect(plugin.description).toBeUndefined();
    expect(plugin.file).toBeUndefined();
  });

  test('view context returns update_available and new_version fields', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-plugin', { plugin_slug: 'akismet' }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);

    const plugin = data.plugin;
    expect(typeof plugin.update_available, 'update_available must be boolean').toBe('boolean');
    expect(
      plugin.new_version === null || typeof plugin.new_version === 'string',
      'new_version must be string or null'
    ).toBe(true);
  });
});
