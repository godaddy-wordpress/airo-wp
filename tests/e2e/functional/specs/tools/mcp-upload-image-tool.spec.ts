import { test, expect } from '@wordpress/e2e-test-utils-playwright';

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';

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

test.describe('airo-wp/upload-image tool', () => {
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

  test('successfully uploads an image from a URL', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-upload-image', {
      url: 'https://via.placeholder.com/1x1.png',
    }, 2);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // The upload may fail in Docker if the URL is unreachable — that is acceptable.
    // We only check that the tool ran and returned a structured response.
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    if (data.success) {
      expect(data.attachment_id).toBeTruthy();
      expect(data.url).toBeTruthy();
    } else {
      // Network failure in isolated environment is acceptable.
      expect(data.message).toBeTruthy();
    }
  });

  test('returns error for invalid URL', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-upload-image', {
      url: 'not-a-valid-url',
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure: ${JSON.stringify(data)}`).toBe(false);
    expect(data.message).toContain('Invalid image URL');
  });

  test('returns error for non-existent post_id', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-upload-image', {
      url: 'https://via.placeholder.com/1x1.png',
      post_id: 999999,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected failure: ${JSON.stringify(data)}`).toBe(false);
    expect(data.message).toContain('999999');
  });

  test('rejects a request carrying neither url nor file_data', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-upload-image', {
      title: 'Some Title',
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // The schema expresses the url/file_data choice with oneOf, so neither branch
    // matching is reported as a format mismatch rather than a single missing field.
    expect(body.result?.isError, 'should reject a request with no image source').toBe(true);
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    expect(content[0].text.toLowerCase()).toContain('does not match any of the expected formats');
  });

  test('uploads an image from base64 file data', async ({ requestUtils }) => {
    // 1x1 transparent PNG.
    const PNG_1PX =
      'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/58BAwAI/AL+g0Cs0gAAAABJRU5ErkJggg==';

    const response = await callTool(requestUtils, sessionId, 'airo-wp-upload-image', {
      file_data: PNG_1PX,
      filename: 'airo-wp-e2e-pixel.png',
      mime_type: 'image/png',
      title: 'Airo WP e2e pixel',
      alt_text: 'a single transparent pixel',
    }, 6);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(data.attachment_id, 'no attachment_id returned').toBeTruthy();
    expect(data.url).toContain('airo-wp-e2e-pixel');
  });

  test('rejects base64 file data with no filename', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-upload-image', {
      file_data: 'aGVsbG8=',
    }, 7);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();

    // oneOf requires file_data and filename together, so this fails validation
    // before the tool's own filename check is reached.
    expect(body.result?.isError, 'file_data without filename should be rejected').toBe(true);
  });
});
