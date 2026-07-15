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

test.describe('airo-wp/list-page-revisions tool', () => {
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

  test('lists revisions for a page with at least one revision', async ({ requestUtils }) => {
    // Create a published page.
    const createResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page Revisions List',
      content: '<p>Original content</p>',
      post_type: 'page',
      status: 'publish',
    });
    const createBody = await createResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    expect(pageData.success).toBe(true);
    const pageId = pageData.post_id;

    // Update the page to generate a revision.
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
      post_id: pageId,
      content: '<p>Updated content</p>',
    }, 3);

    // List revisions.
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-page-revisions', {
      parent: pageId,
    }, 4);

    expect(response.status(), `tools/call failed: ${await response.text()}`).toBe(200);

    const body = await response.json();
    expect(body.error, `JSON-RPC error: ${JSON.stringify(body.error)}`).toBeUndefined();
    expect(body.result?.isError, `isError=true: ${JSON.stringify(body.result)}`).not.toBe(true);

    const content = JSON.parse(body.result.content[0].text);
    expect(Array.isArray(content.revisions)).toBe(true);
    expect(content.total).toBeGreaterThanOrEqual(1);

    const revision = content.revisions[0];
    expect(revision.id).toBeDefined();
    expect(revision.author_id).toBeDefined();
    expect(revision.date_created).toBeDefined();
    expect(revision.parent_id).toBe(pageId);
    expect(revision.title).toBeDefined();
    expect(revision.content).toBeDefined();
  });

  test('returns error for non-existent parent page', async ({ requestUtils }) => {
    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-page-revisions', {
      parent: 999999,
    }, 5);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('not found');
  });

  test('returns error when parent is not a page', async ({ requestUtils }) => {
    // Create a regular post (not a page).
    const createResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Regular Post for Page Revisions',
      content: 'Not a page.',
      status: 'publish',
    }, 6);
    const createBody = await createResp.json();
    const postData = JSON.parse(createBody.result.content[0].text);
    const postId = postData.post_id;

    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-page-revisions', {
      parent: postId,
    }, 7);

    expect(response.status()).toBe(200);
    const body = await response.json();
    expect(body.error).toBeUndefined();

    const content = JSON.parse(body.result.content[0].text);
    expect(content.success).toBe(false);
    expect(content.message.toLowerCase()).toContain('page');
  });

  test('respects per_page pagination parameter', async ({ requestUtils }) => {
    // Create a page and update it multiple times.
    const createResp = await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
      title: 'E2E Page Revisions Pagination',
      content: '<p>Original</p>',
      post_type: 'page',
      status: 'publish',
    }, 8);
    const createBody = await createResp.json();
    const pageData = JSON.parse(createBody.result.content[0].text);
    const pageId = pageData.post_id;

    await callTool(requestUtils, sessionId, 'airo-wp-update-post', { post_id: pageId, content: 'Update 1.' }, 9);
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', { post_id: pageId, content: 'Update 2.' }, 10);
    await callTool(requestUtils, sessionId, 'airo-wp-update-post', { post_id: pageId, content: 'Update 3.' }, 11);

    const response = await callTool(requestUtils, sessionId, 'airo-wp-list-page-revisions', {
      parent: pageId,
      per_page: 1,
    }, 12);

    expect(response.status()).toBe(200);
    const body = await response.json();
    const content = JSON.parse(body.result.content[0].text);

    expect(content.per_page).toBe(1);
    expect(content.revisions.length).toBe(1);
    expect(content.total_pages).toBeGreaterThan(1);
  });
});
