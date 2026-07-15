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

test.describe('airo-wp/list-navigation-revisions tool', () => {
  let sessionId: string;
  let navigationId: number;

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

    expect(initResponse.status(), `initialize failed: ${await initResponse.text()}`).toBe(200);
    sessionId = initResponse.headers()['mcp-session-id'];
    expect(sessionId, 'initialize did not return mcp-session-id header').toBeTruthy();

    // Create a navigation and update it to ensure at least one revision exists.
    const createResponse = await callTool(requestUtils, sessionId, 'airo-wp-create-navigation', {
      title: 'E2E Revisions Nav Test',
      content: '<!-- wp:navigation-link {"label":"Home","url":"/"} /-->',
    }, 10);

    const createBody = await createResponse.json();
    const createContent: Array<{ type: string; text: string }> = createBody.result?.content ?? [];
    const createData = JSON.parse(createContent[0].text);
    expect(createData.success, `create-navigation failed: ${JSON.stringify(createData)}`).toBe(true);
    navigationId = createData.navigation.id;

    // Update to generate a revision.
    const updateResponse = await callTool(requestUtils, sessionId, 'airo-wp-update-navigation', {
      id: navigationId,
      content: '<!-- wp:navigation-link {"label":"Updated","url":"/updated"} /-->',
    }, 11);

    const updateBody = await updateResponse.json();
    const updateContent: Array<{ type: string; text: string }> = updateBody.result?.content ?? [];
    const updateData = JSON.parse(updateContent[0].text);
    expect(updateData.success, `update-navigation failed: ${JSON.stringify(updateData)}`).toBe(true);
  });

  test('lists revisions for a navigation post (happy path)', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-navigation-revisions', {
      parent: navigationId,
    }, 3);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, `expected success: ${JSON.stringify(data)}`).toBe(true);
    expect(Array.isArray(data.revisions), 'revisions should be an array').toBe(true);
    expect(data.revisions.length, 'should have at least one revision').toBeGreaterThan(0);
    expect(data.total, 'total should be a number').toBeGreaterThanOrEqual(1);

    const revision = data.revisions[0];
    expect(revision.id, 'revision should have id').toBeDefined();
    expect(revision.parent, 'revision should reference parent').toBe(navigationId);
  });

  test('returns error for non-existent parent navigation ID', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-navigation-revisions', {
      parent: 99999,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    const content: Array<{ type: string; text: string }> = body.result?.content ?? [];
    expect(content.length).toBeGreaterThan(0);
    const data = JSON.parse(content[0].text);

    expect(data.success, 'should return success=false for non-existent parent').toBe(false);
    expect(data.message).toBeDefined();
  });
});
