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
 * Verifies the airo-wp/deactivate-plugin tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/deactivate-plugin tool', () => {
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
    const response = await callTool(requestUtils, sessionId, 'airo-wp-deactivate-plugin', { plugin_slug: 'nonexistent-test-plugin-xyz' });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, 'response should indicate failure').toBe(false);
    expect(data.message.toLowerCase()).toContain('not found');
  });

  test('deactivates an active plugin', async ({ requestUtils }) => {
    // Ensure akismet is active first.
    await callTool(requestUtils, sessionId, 'airo-wp-activate-plugin', { plugin_slug: 'akismet' }, 10);

    // Now deactivate it.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-deactivate-plugin', { plugin_slug: 'akismet' }, 11);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toContain('deactivated');
    expect(data.plugin).toBe('akismet');
  });

  test('deactivates an already-inactive plugin gracefully', async ({ requestUtils }) => {
    // Akismet was deactivated in the previous test — deactivating again should still succeed.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-deactivate-plugin', { plugin_slug: 'akismet' }, 12);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toContain('deactivated');
    expect(data.plugin).toBe('akismet');
  });

  test('deactivates and uninstalls a plugin (file removal)', async ({ requestUtils }) => {
    // First install classic-widgets so we have something to uninstall.
    await callTool(requestUtils, sessionId, 'airo-wp-activate-plugin', { plugin_slug: 'classic-widgets' }, 13);

    // Deactivate with uninstall=true to delete files.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-deactivate-plugin', {
      plugin_slug: 'classic-widgets',
      uninstall: true,
    }, 14);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toContain('uninstalled');
    expect(data.plugin).toBe('classic-widgets');

    // Verify files are gone: get-plugin scans WP_PLUGIN_DIR via get_plugins().
    // If delete_plugins() removed the files, the filesystem scan won't find them.
    const getResponse = await callTool(requestUtils, sessionId, 'airo-wp-get-plugin', { plugin_slug: 'classic-widgets' }, 15);
    const getBody = await getResponse.json();
    const getContent: Array<{ type: string; text: string }> = getBody.result?.content ?? [];
    const getData = JSON.parse(getContent[0].text);
    expect(getData.success, 'get-plugin should not find deleted plugin').toBe(false);
    expect(getData.message.toLowerCase()).toContain('not found');

    // Double-check via list-plugins: classic-widgets must not appear.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-plugins', {}, 16);
    const listBody = await listResponse.json();
    const listContent: Array<{ type: string; text: string }> = listBody.result?.content ?? [];
    const listData = JSON.parse(listContent[0].text);
    const slugs: string[] = (listData.plugins ?? []).map((p: { slug: string }) => p.slug);
    expect(slugs, 'classic-widgets should not appear in plugin list after file removal').not.toContain('classic-widgets');
  });
});
