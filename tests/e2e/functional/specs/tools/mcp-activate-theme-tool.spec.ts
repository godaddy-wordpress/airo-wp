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
 * Verifies the airo-wp/activate-theme tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/activate-theme tool', () => {
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

  test('returns error when theme slug is empty', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-theme', { theme_slug: '' });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // Schema minLength validation triggers isError before execute() runs.
    expect(body.result?.isError, 'expected isError for invalid input').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('at least 1 character');
  });

  test('activates an already-installed theme', async ({ requestUtils }) => {
    // twentytwentyfive is bundled with WP — activate it from a different theme.
    await callTool(requestUtils, sessionId, 'airo-wp-activate-theme', { theme_slug: 'twentytwentyfour' }, 10);

    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-theme', { theme_slug: 'twentytwentyfive' }, 11);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toMatch(/activated/);
    expect(data.theme).toBe('twentytwentyfive');
    expect(data.previous_theme).toBe('twentytwentyfour');
    expect(typeof data.version).toBe('string');
  });

  test('reports theme already active on second activation', async ({ requestUtils }) => {
    // twentytwentyfive was activated in the previous test.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-theme', { theme_slug: 'twentytwentyfive' }, 12);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toContain('already active');
    expect(data.theme).toBe('twentytwentyfive');
  });

  test('installs and activates a theme from WordPress.org', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-theme', { theme_slug: 'dx-lite' }, 13);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toMatch(/installed and activated/);
    expect(data.theme).toBe('dx-lite');
    expect(typeof data.version).toBe('string');
  });

  test('activates twentytwentyfive after installing a remote theme', async ({ requestUtils }) => {
    // Switch back to twentytwentyfive (already installed, was active earlier).
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-theme', { theme_slug: 'twentytwentyfive' }, 15);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toMatch(/activated/);
    expect(data.theme).toBe('twentytwentyfive');
  });

  test('returns error when theme does not exist on WordPress.org', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-activate-theme', { theme_slug: 'this-theme-does-not-exist-xyz-99' }, 14);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure but got: ${JSON.stringify(data)}`).toBe(false);
    expect(data.theme).toBe('this-theme-does-not-exist-xyz-99');
  });
});
