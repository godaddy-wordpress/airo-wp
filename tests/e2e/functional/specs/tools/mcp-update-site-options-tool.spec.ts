import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';

/**
 * Verifies the airo-wp/update-site-options tool implementation.
 *
 * Follows the MCP JSON-RPC protocol (2025-06-18):
 *  1. beforeAll: POST initialize  → server returns mcp-session-id header
 *  2. Each test reuses the session ID via Mcp-Session-Id header.
 */
test.describe('airo-wp/update-site-options tool', () => {
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

  test('updates a site option successfully', async ({ requestUtils }) => {
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
          name: 'airo-wp-update-site-options',
          arguments: { option_name: 'blogname', option_value: 'E2E Test Site' },
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
    expect(data.updated_count, 'updated_count should be 1').toBe(1);
  });
});
