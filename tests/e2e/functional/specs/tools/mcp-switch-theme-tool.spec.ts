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
 * Verifies the airo-wp/switch-theme tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/switch-theme tool', () => {
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
    const response = await callTool(requestUtils, sessionId, 'airo-wp-switch-theme', { theme_slug: '' });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // Schema minLength validation triggers isError before execute() runs.
    expect(body.result?.isError, 'expected isError for invalid input').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('at least 1 character');
  });

  test('switches to an installed theme', async ({ requestUtils }) => {
    // Ensure we're on twentytwentyfive first.
    await callTool(requestUtils, sessionId, 'airo-wp-switch-theme', { theme_slug: 'twentytwentyfive' }, 10);

    // Switch to twentytwentyfour.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-switch-theme', { theme_slug: 'twentytwentyfour' }, 11);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toMatch(/switched/);
    expect(data.theme).toBe('twentytwentyfour');
    expect(data.previous_theme).toBe('twentytwentyfive');
  });

  test('reports theme already active when switching to current theme', async ({ requestUtils }) => {
    // twentytwentyfour was activated in the previous test.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-switch-theme', { theme_slug: 'twentytwentyfour' }, 12);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success but got: ${JSON.stringify(data)}`).toBe(true);
    expect(data.message.toLowerCase()).toContain('already active');
    expect(data.theme).toBe('twentytwentyfour');
  });

  test('returns error for a theme that is not installed', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-switch-theme', { theme_slug: 'nonexistent-theme-xyz-99' }, 13);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    const data = JSON.parse(content[0].text);

    expect(data.success, 'response should indicate failure').toBe(false);
    expect(data.message.toLowerCase()).toContain('not installed');
  });
});
