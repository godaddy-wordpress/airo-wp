import { test, expect } from '@wordpress/e2e-test-utils-playwright';

/**
 * Block markup must survive the tools that write post content.
 *
 * CreatePost, UpdatePost and CreatePageDraft all pass content through
 * wp_kses_post(). That is a deliberate hardening the port added over upstream, but
 * KSES rewrites HTML comments — it strips the delimiters, re-runs itself on the
 * inner text, collapses runs of dashes and trims a trailing dash before rebuilding
 * the comment. Gutenberg block delimiters *are* HTML comments, often carrying JSON
 * attributes, so "content is sanitised" and "content is preserved" are not obviously
 * compatible claims.
 *
 * CreatePageDraft is the case that matters most: when no new content is supplied it
 * applies wp_kses_post() to the *existing* post_content. So merely drafting a page
 * runs its stored blocks through KSES, and publishing that draft writes the result
 * back over the original.
 *
 * The rest of the suite only ever sends plain-text content, so none of this was
 * covered. These tests assert byte-exact round-trips.
 */

const MCP_ENDPOINT = '/wp-json/airo-wp/v1/mcp/streamable';

// Deliberately exercises the parts of KSES that rewrite comments: JSON attributes
// with quotes, braces and colons; a self-closing block; nested blocks; and an
// entity.
const BLOCK_MARKUP = [
  '<!-- wp:heading {"level":2,"textAlign":"center"} -->',
  '<h2 class="wp-block-heading has-text-align-center">Pricing &amp; plans</h2>',
  '<!-- /wp:heading -->',
  '<!-- wp:columns {"verticalAlignment":"top"} -->',
  '<div class="wp-block-columns are-vertically-aligned-top">',
  '<!-- wp:column -->',
  '<div class="wp-block-column"><!-- wp:paragraph -->',
  '<p>Starter tier</p>',
  '<!-- /wp:paragraph --></div>',
  '<!-- /wp:column -->',
  '</div>',
  '<!-- /wp:columns -->',
  '<!-- wp:spacer {"height":"32px"} -->',
  '<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>',
  '<!-- /wp:spacer -->',
].join('\n');

async function callTool(
  requestUtils: import('@wordpress/e2e-test-utils-playwright').RequestUtils,
  sessionId: string,
  toolName: string,
  args: Record<string, unknown>,
  id = 2
) {
  return requestUtils.request.post(MCP_ENDPOINT, {
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
}

function resultOf(body: { result?: { content?: Array<{ text: string }> } }) {
  const content = body.result?.content ?? [];
  expect(content.length).toBeGreaterThan(0);
  return JSON.parse(content[0].text);
}

test.describe('block markup survives the content-writing tools', () => {
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

    expect(initResponse.status(), `initialize failed: ${await initResponse.text()}`).toBe(200);
    sessionId = initResponse.headers()['mcp-session-id'];
    expect(sessionId, 'initialize did not return mcp-session-id header').toBeTruthy();
  });

  test('create-post preserves block delimiters and their JSON attributes', async ({ requestUtils }) => {
    const created = resultOf(
      await (await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
        title: 'E2E block markup round-trip',
        content: BLOCK_MARKUP,
        status: 'draft',
      }, 2)).json()
    );

    expect(created.success, `create failed: ${JSON.stringify(created)}`).toBe(true);
    expect(created.post_id).toBeGreaterThan(0);

    const stored = await requestUtils.rest<{ content: { raw: string } }>({
      path: `/wp/v2/posts/${created.post_id}?context=edit`,
    });

    expect(stored.content.raw, 'block markup was altered on the way in').toBe(BLOCK_MARKUP);
  });

  test('update-post preserves block delimiters and their JSON attributes', async ({ requestUtils }) => {
    const created = resultOf(
      await (await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
        title: 'E2E block markup update target',
        content: 'placeholder',
        status: 'draft',
      }, 3)).json()
    );
    expect(created.success, `create failed: ${JSON.stringify(created)}`).toBe(true);

    const updated = resultOf(
      await (await callTool(requestUtils, sessionId, 'airo-wp-update-post', {
        post_id: created.post_id,
        content: BLOCK_MARKUP,
      }, 4)).json()
    );
    expect(updated.success, `update failed: ${JSON.stringify(updated)}`).toBe(true);

    const stored = await requestUtils.rest<{ content: { raw: string } }>({
      path: `/wp/v2/posts/${created.post_id}?context=edit`,
    });

    expect(stored.content.raw, 'block markup was altered on update').toBe(BLOCK_MARKUP);
  });

  test('drafting a page does not rewrite the blocks already stored on it', async ({ requestUtils }) => {
    // Seeded through the REST API rather than a tool, so the stored content is known
    // to be untouched before create-page-draft copies it.
    const page = await requestUtils.rest<{ id: number }>({
      method: 'POST',
      path: '/wp/v2/pages',
      data: { title: 'E2E draft block markup', content: BLOCK_MARKUP, status: 'publish' },
    });

    const seeded = await requestUtils.rest<{ content: { raw: string } }>({
      path: `/wp/v2/pages/${page.id}?context=edit`,
    });
    expect(seeded.content.raw, 'seed did not store the markup verbatim').toBe(BLOCK_MARKUP);

    // No `content` argument: the tool copies existing post_content through
    // wp_kses_post(), which is the path under test.
    const draft = resultOf(
      await (await callTool(requestUtils, sessionId, 'airo-wp-create-page-draft', {
        post_id: page.id,
      }, 5)).json()
    );
    expect(draft.success, `draft failed: ${JSON.stringify(draft)}`).toBe(true);

    const draftId = draft.draft_id ?? draft.draft_post_id ?? draft.post_id;
    expect(draftId, `no draft id in response: ${JSON.stringify(draft)}`).toBeTruthy();

    const stored = await requestUtils.rest<{ content: { raw: string } }>({
      path: `/wp/v2/pages/${draftId}?context=edit`,
    });

    expect(stored.content.raw, 'drafting a page altered its block markup').toBe(BLOCK_MARKUP);
  });

  test('sanitisation is still active: a script tag is stripped, blocks are not', async ({ requestUtils }) => {
    // Pairs with the tests above. On its own, "markup came back unchanged" is also
    // what you would see if wp_kses_post() were not being applied at all — so this
    // asserts the filter is genuinely running by giving it something it must remove,
    // in the same request as blocks it must keep.
    const created = resultOf(
      await (await callTool(requestUtils, sessionId, 'airo-wp-create-post', {
        title: 'E2E kses is active',
        content: `${BLOCK_MARKUP}\n<script>alert(1)</script>`,
        status: 'draft',
      }, 6)).json()
    );

    expect(created.success, `create failed: ${JSON.stringify(created)}`).toBe(true);

    const stored = await requestUtils.rest<{ content: { raw: string } }>({
      path: `/wp/v2/posts/${created.post_id}?context=edit`,
    });

    expect(stored.content.raw, 'script tag survived — content is not being sanitised').not.toContain('<script');
    expect(stored.content.raw, 'blocks were stripped along with the script').toContain('<!-- wp:heading {"level":2,"textAlign":"center"} -->');
    expect(stored.content.raw).toContain('<!-- /wp:columns -->');
  });
});
