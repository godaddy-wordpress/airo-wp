import { test, expect } from '@wordpress/e2e-test-utils-playwright';
import { globSync } from 'glob';
import fs from 'fs';
import path from 'path';

import {
  waitForEditor,
  openBlockInserter,
  insertPattern,
  publishPage,
  checkFrontend,
  setParentPage,
} from '../support/gutenberg';

test.setTimeout(90_000);

// ---------------------------------------------------------------------------
// Build the pattern manifest at load time from patterns/**/*.php docblocks.
// ---------------------------------------------------------------------------
const REPO_ROOT = path.resolve( __dirname, '../../../..' );

interface PatternManifest {
  slug:     string;
  title:    string;
  category: string; // first category slug, e.g. 'airo-wp-hero'
}

const PATTERN_MANIFESTS: PatternManifest[] = globSync('patterns/**/*.php', {
  cwd: REPO_ROOT,
}).reduce<PatternManifest[]>((acc, rel) => {
  const content    = fs.readFileSync(path.join(REPO_ROOT, rel), 'utf-8');
  const titleMatch = content.match(/\*\s*Title:\s*(.+)/);
  const slugMatch  = content.match(/\*\s*Slug:\s*(.+)/);
  const catMatch   = content.match(/\*\s*Categories:\s*(.+)/);

  const slug     = slugMatch?.[1].trim()  ?? '';
  const title    = titleMatch?.[1].trim() ?? '';
  // Use the first listed category as the primary one for Patterns tab navigation.
  const category = catMatch?.[1].trim().split(',')[0].trim() ?? '';

  if (slug.startsWith('airo-wp/') && title && category) {
    acc.push({ slug, title, category });
  }
  return acc;
}, []);

if (PATTERN_MANIFESTS.length === 0) {
  throw new Error('PATTERN_MANIFESTS is empty — check patterns/**/*.php files exist');
}

const manifests = PATTERN_MANIFESTS;

// ---------------------------------------------------------------------------
// Create the parent page once before all tests run.
// ---------------------------------------------------------------------------
let parentPageId: number;

test.beforeAll(async ({ requestUtils }) => {
  const resp = await requestUtils.rest<{ id: number }>( {
    method: 'POST',
    path: '/wp/v2/pages',
    data: { title: 'E2E: Patterns', status: 'publish' },
  } );
  parentPageId = resp.id;
});

// ---------------------------------------------------------------------------
// One test per pattern.
// ---------------------------------------------------------------------------
test.describe('airo-wp patterns — browser validation', () => {
  for (const { slug, title, category } of manifests) {
    test(`${slug} renders without errors`, async ({ page, requestUtils }) => {
      await page.goto('/wp-admin/post-new.php?post_type=page');
      await waitForEditor(page);
      await openBlockInserter(page);
      await insertPattern(page, category, title);

      // Note: editor block-validation warnings are intentionally NOT asserted
      // for patterns. Patterns embed core blocks (e.g. wp:embed) whose stored
      // markup legitimately fails the editor's re-validation in a headless
      // environment, and airo-wp blocks can show benign markup drift — neither
      // affects the published output. The meaningful "is this pattern broken?"
      // signal is the front-end render below, which catches PHP errors,
      // missing blocks (wp:missing), and absent airo-wp markup.
      const { pageId } = await publishPage(page, `E2E Pattern: ${slug}`);
      // anyAiroWpClass checks for any wp-block-airo-wp-* in the rendered body.
      // Template-part patterns (header/footer) render their outer wrapper only;
      // inner block rendering inside template parts is out of scope here.
      await checkFrontend(requestUtils, pageId, { anyAiroWpClass: true });
      await setParentPage(requestUtils, pageId, parentPageId);
    });
  }
});
