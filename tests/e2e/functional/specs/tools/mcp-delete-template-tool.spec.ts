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
 * Verifies the airo-wp/delete-template tool implementation.
 */
test.describe('airo-wp/delete-template tool', () => {
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

  test('returns theme-only status for a template without DB override', async ({ requestUtils }) => {
    // The "single" template in TT5 should be theme-only (no prior customization).
    const response = await callTool(requestUtils, sessionId, 'airo-wp-delete-template', {
      id: 'twentytwentyfive//single',
    });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.deleted.id).toBe('twentytwentyfive//single');
    expect(data.deleted.status).toBe('theme-only');
  });

  test('successfully deletes a template with DB override', async ({ requestUtils }) => {
    // First, create a DB override by updating the template.
    await callTool(requestUtils, sessionId, 'airo-wp-update-template', {
      theme: 'twentytwentyfive',
      template_name: 'page',
      html: '<!-- wp:paragraph --><p>Temporary override for delete test</p><!-- /wp:paragraph -->',
    }, 3);

    // Now delete the override (resets to theme default).
    const response = await callTool(requestUtils, sessionId, 'airo-wp-delete-template', {
      id: 'twentytwentyfive//page',
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.deleted.id).toBe('twentytwentyfive//page');
    expect(data.deleted.status).toBe('reset');
    expect(data.previous, 'should include previous template data').toBeDefined();
  });

  test('returns error for non-existent template ID', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-delete-template', {
      id: 'twentytwentyfive//nonexistent-template-slug',
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure for non-existent template: ${JSON.stringify(data)}`).toBe(false);
    expect(data.message).toContain('not found');
  });

  test('returns error when id is missing', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-delete-template', {}, 6);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // MCP schema validation rejects missing required 'id' with isError=true.
    expect(body.result?.isError, 'should reject missing required id').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('id');
  });
});
