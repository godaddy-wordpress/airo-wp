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

test.describe('airo-wp/get-page-revision tool', () => {
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

  test('gets a specific page revision', async ({ requestUtils }) => {
    // Create a published page.
    const createResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page Get Revision',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    });
    const createBody = await createResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    // Update to generate a revision.
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: pageId,
      title: 'E2E Page Get Revision Updated',
      content: '<p>Updated content</p>',
    }, 3);

    // List revisions to get a revision ID.
    const listResp = await callTool(requestUtils, sessionId, 'airo-wp-list-page-revisions', {
      parent: pageId,
    }, 4);
    const listBody = await listResp.json();
    const listData = JSON.parse(listBody.result.content[0].text);
    expect(listData.revisions.length).toBeGreaterThan(0);
    const revisionId = listData.revisions[0].id;

    // Get specific revision.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-page-revision', {
      parent: pageId,
      id: revisionId,
    }, 5);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(content.id).toBe(revisionId);
    expect(content.parent_id).toBe(pageId);
    expect(content.author_id).toBeDefined();
    expect(content.date_created).toBeDefined();
    expect(content.title).toBeDefined();
    expect(content.content).toBeDefined();
  });

  test('returns error for non-existent revision', async ({ requestUtils }) => {
    // Create a page first.
    const createResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page Get Revision Missing',
      content: '<p>Content</p>',
      post_type: 'page',
      status: 'publish',
    }, 6);
    const createBody = await createResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-page-revision', {
      parent: pageId,
      id: 999999,
    }, 7);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('not found');
  });

  test('returns error when parent is not a page', async ({ requestUtils }) => {
    // Create a regular post.
    const createResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Regular Post for Get Page Revision',
      content: 'Not a page.',
      status: 'publish',
    }, 8);
    const createBody = await createResp.json();
    const postData = JSON.parse(createBody.result.content[0].text);
    const postId = postData.post_id;

    const response = await callTool(requestUtils, sessionId, 'airo-wp-get-page-revision', {
      parent: postId,
      id: 1,
    }, 9);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('page');
  });
});
