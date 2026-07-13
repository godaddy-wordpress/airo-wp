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
 * Verifies the airo-wp/update-global-styles tool implementation.
 */
test.describe('airo-wp/update-global-styles tool', () => {
  let sessionId: string;
  let globalStylesId: number;

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

    // Get a valid global styles ID from list-global-styles.
    const listResponse = await callTool(requestUtils, sessionId, 'airo-wp-list-global-styles', {});
    const listBody = await listResponse.json();
    const listContent: Array<{ type: string; text: string }> = listBody.result?.content ?? [];
    const listData = JSON.parse(listContent[0].text);

    expect(listData.success, `list-global-styles failed: ${JSON.stringify(listData)}`).toBe(true);
    expect(listData.styles.length, 'need at least one global style').toBeGreaterThan(0);
    globalStylesId = listData.styles[0].id;
  });

  test('updates global styles with merge mode (default)', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-global-styles', {
      id: globalStylesId,
      styles: {
        color: {
          background: '#ffffff',
          text: '#000000',
        },
      },
    });

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.data.id).toBe(globalStylesId);
    expect(data.data.content, 'should return content object').toBeDefined();
    expect(data.data.content.styles.color.background).toBe('#ffffff');
    expect(data.data.content.styles.color.text).toBe('#000000');
    expect(data.data.status, 'should return status').toBeDefined();
    expect(data.data.date, 'should return date').toBeDefined();
    expect(data.message, 'should return success message').toBeDefined();
  });

  test('updates global styles with overwrite mode', async ({ requestUtils }) => {
    const overwriteStyles = {
      color: {
        background: '#ff0000',
      },
    };

    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-global-styles', {
      id: globalStylesId,
      styles: overwriteStyles,
      overwrite: true,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.data.id).toBe(globalStylesId);
    expect(data.data.content.styles.color.background).toBe('#ff0000');
    // Overwrite mode should replace the entire styles object; text key should not exist.
    expect(data.data.content.styles.color.text).toBeUndefined();
  });

  test('updates global styles title', async ({ requestUtils }) => {
    const newTitle = 'E2E Test Global Style Title';

    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-global-styles', {
      id: globalStylesId,
      title: newTitle,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.data.id).toBe(globalStylesId);
    expect(data.data.title).toBe(newTitle);
  });

  test('returns correct response structure on successful update', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-update-global-styles', {
      id: globalStylesId,
      settings: {
        color: {
          palette: [
            { slug: 'primary', color: '#0073aa', name: 'Primary' },
          ],
        },
      },
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);

    // Verify full response structure.
    expect(data.data, 'response should have data object').toBeDefined();
    expect(typeof data.data.id, 'id should be a number').toBe('number');
    expect(typeof data.data.title, 'title should be a string').toBe('string');
    expect(typeof data.data.content, 'content should be an object').toBe('object');
    expect(typeof data.data.status, 'status should be a string').toBe('string');
    expect(typeof data.data.date, 'date should be a string').toBe('string');
    expect(typeof data.message, 'message should be a string').toBe('string');
  });
});
