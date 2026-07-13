import { test, expect } from '@wordpress/e2e-test-utils-playwright';
import { globSync } from 'glob';
import fs from 'fs';
import path from 'path';

import {
  waitForEditor,
  openBlockInserter,
  insertBlock,
  checkNoRepairUI,
  publishPage,
  checkFrontend,
  setParentPage,
} from '../support/gutenberg';

test.setTimeout(90_000);

// ---------------------------------------------------------------------------
// Build the block manifest at load time from dist/blocks/*/block.json.
// Blocks with a 'parent' field are child-only and cannot be inserted
// standalone, so they are filtered out.
// ---------------------------------------------------------------------------
const REPO_ROOT = path.resolve( __dirname, '../../../..' );

interface BlockManifest {
  name: string;
  title: string;
  cssClass: string;
}

const BLOCK_MANIFESTS: BlockManifest[] = globSync('dist/blocks/*/block.json', {
  cwd: REPO_ROOT,
}).reduce<BlockManifest[]>((acc, rel) => {
  const data = JSON.parse(fs.readFileSync(path.join(REPO_ROOT, rel), 'utf-8'));
  if (Array.isArray(data.parent) && data.parent.length > 0) return acc;
  if (Array.isArray(data.ancestor) && data.ancestor.length > 0) return acc;
  acc.push({
    name:     data.name  as string,
    title:    data.title as string,
    // airo-wp/fifty-fifty → wp-block-airo-wp-fifty-fifty
    cssClass: 'wp-block-' + (data.name as string).replace(/\//g, '-'),
  });
  return acc;
}, []);

if (BLOCK_MANIFESTS.length === 0) {
  throw new Error('BLOCK_MANIFESTS is empty — run make dsg-build to compile dist/blocks/');
}

// ---------------------------------------------------------------------------
// Configuration-required blocks.
//
// These blocks render a setup/placeholder UI when inserted without
// configuration (a product, an image, a query layout, an active WooCommerce
// install, or inner form fields) and therefore produce no front-end markup —
// or, for the Query block, cannot even be published — on a bare page.
//
// They are still inserted and checked for editor validity (no block-repair /
// validation UI), which is the meaningful "is this block broken?" signal. The
// publish + front-end class assertion is skipped because there is nothing
// configured for them to render. Verified against WP 6.9.4 (wp-now):
//   - query:                  shows a "Pick a starting layout" chooser; an
//                             unconfigured Query block fails to publish.
//   - product-showcase-hero:  shows "Search for a product"; renders nothing.
//   - product-categories-grid: renders "ensure WooCommerce is active" (no Woo).
//   - dynamic-image:          render.php returns '' until an image is bound.
//   - form-builder:           renders nothing until form fields are added.
// ---------------------------------------------------------------------------
const CONFIG_REQUIRED_BLOCKS = new Set<string>([
  'airo-wp/query',
  'airo-wp/product-showcase-hero',
  'airo-wp/product-categories-grid',
  'airo-wp/dynamic-image',
  'airo-wp/form-builder',
]);

// ---------------------------------------------------------------------------
// Create the parent page once before all tests run.
// ---------------------------------------------------------------------------
let parentPageId: number;

test.beforeAll(async ({ requestUtils }) => {
  const resp = await requestUtils.rest<{ id: number }>( {
    method: 'POST',
    path: '/wp/v2/pages',
    data: { title: 'E2E: Blocks', status: 'publish' },
  } );
  parentPageId = resp.id;
});

// ---------------------------------------------------------------------------
// One test per insertable block.
// ---------------------------------------------------------------------------
test.describe('airo-wp blocks — browser validation', () => {
  for (const { name, title, cssClass } of BLOCK_MANIFESTS) {
    test(`${name} renders without errors`, async ({ page, requestUtils }) => {
      const shortName = name.replace('airo-wp/', '');

      await page.goto('/wp-admin/post-new.php?post_type=page');
      await waitForEditor(page);
      await openBlockInserter(page);
      await insertBlock(page, shortName, title);
      await checkNoRepairUI(page);

      // Configuration-required blocks render a placeholder with no front-end
      // output (or can't be published) until configured. Insertion + the
      // no-repair check above is the meaningful breakage signal for them.
      if (CONFIG_REQUIRED_BLOCKS.has(name)) {
        return;
      }

      const { pageId } = await publishPage(page, `E2E Block: ${name}`);
      await checkFrontend(requestUtils, pageId, { cssClass });
      await setParentPage(requestUtils, pageId, parentPageId);
    });
  }
});
