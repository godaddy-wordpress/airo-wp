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
 * Verifies the airo-wp/update-template tool implementation.
 */
test.describe('airo-wp/update-template tool', () => {
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

  test('successfully updates a template with new HTML content', async ({ requestUtils }) => {
    const htmlContent = '<!-- wp:paragraph --><p>E2E update template test</p><!-- /wp:paragraph -->';

    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-template', {
      theme: 'twentytwentyfive',
      template_name: 'page',
      html: htmlContent,
    });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.data.id).toBe('twentytwentyfive//page');
    expect(data.data.slug).toBe('page');
    expect(data.data.theme).toBe('twentytwentyfive');
    expect(data.data.content).toContain('E2E update template test');
  });

  test('returns error when theme parameter is missing', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-template', {
      template_name: 'page',
      html: '<!-- wp:paragraph --><p>Test</p><!-- /wp:paragraph -->',
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // MCP schema validation rejects missing required 'theme' with isError=true.
    expect(body.result?.isError, 'should reject missing required theme').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('theme');
  });

  test('returns error when html parameter is missing', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-template', {
      theme: 'twentytwentyfive',
      template_name: 'page',
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // MCP schema validation rejects missing required 'html' with isError=true.
    expect(body.result?.isError, 'should reject missing required html').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content[0].text.toLowerCase()).toContain('html');
  });

  test('successfully updates a template using id parameter', async ({ requestUtils }) => {
    const htmlContent = '<!-- wp:paragraph --><p>E2E update via id</p><!-- /wp:paragraph -->';

    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-template', {
      theme: 'twentytwentyfive',
      id: 'twentytwentyfive//page',
      html: htmlContent,
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.data.id).toBe('twentytwentyfive//page');
    expect(data.data.content).toContain('E2E update via id');
  });
});
