// tests/e2e/functional/support/gutenberg.ts
import type { FrameLocator, Page } from '@playwright/test';
import { expect } from '@playwright/test';
import type { RequestUtils } from '@wordpress/e2e-test-utils-playwright';

// ---------------------------------------------------------------------------
// Selectors — update these constants when WP editor markup changes (WP 6.9)
// ---------------------------------------------------------------------------
export const SEL = {
  CANVAS:          '.editor-styles-wrapper',
  CANVAS_BLOCKS:   '.block-editor-block-list__layout',
  INSERTER_TOGGLE: 'button[aria-label="Block Inserter"]', // WP 6.9; was "Toggle block inserter"
  INSERTER_SEARCH: '.block-editor-inserter__search input',
  BLOCK_WARNING:   '.block-editor-warning',
  POST_TITLE:      '[aria-label="Add title"]',
  PUBLISH_BTN:     'button.editor-post-publish-button__button',
  VIEW_PAGE:       '.editor-post-publish-panel a[href*="page_id"]', // WP 6.9 post-publish "View Page"
} as const;

// Front-end error strings that indicate a broken block or PHP error.
export const ERROR_STRINGS = [
  'wp:missing',
  'Fatal error',
  'Parse error',
] as const;

/**
 * Return the FrameLocator for the editor canvas iframe used in WP 6.9+.
 * All block-rendering selectors live inside this iframe; toolbar/inserter
 * selectors remain in the main frame.
 */
export function getEditorFrame( page: Page ): FrameLocator {
  return page.frameLocator( '[name="editor-canvas"]' );
}

// Wait for the Gutenberg block canvas iframe to be ready (WP 6.9+).
export async function waitForEditor( page: Page ): Promise<void> {
  // Detect auth failure: if not authenticated, we'll be redirected to login.php
  // which has no editor iframe. Without this check the test waits the full timeout.
  const currentUrl = page.url();
  if ( currentUrl.includes( 'wp-login' ) ) {
    throw new Error(
      `[waitForEditor] Not authenticated — redirected to login: ${currentUrl}. ` +
      'Verify that the storageState cookie is valid for the running WordPress instance.',
    );
  }

  // Wait for the editor canvas iframe to appear in the DOM.
  // In WP 6.9+ Gutenberg the entire editing surface lives in an iframe
  // rendered by @wordpress/block-editor after JS initialisation.
  await page.locator( 'iframe[name="editor-canvas"]' ).waitFor( { state: 'attached', timeout: 30_000 } ).catch( () => {
    throw new Error(
      `[waitForEditor] Editor canvas iframe not found within 30 s at ${page.url()}. ` +
      'The Gutenberg editor may not have loaded — check for JS errors or a non-editor page.',
    );
  } );

  const frame = getEditorFrame( page );
  await frame.locator( SEL.CANVAS ).waitFor( { state: 'visible', timeout: 60_000 } );
  await frame.locator( SEL.CANVAS_BLOCKS ).waitFor( { state: 'visible', timeout: 60_000 } );

  // Fresh users see the "Welcome to the editor" guide modal on first load, and
  // WP 6.9 shows a "Choose a pattern" starter modal for every new page.  Both
  // overlay the editor and apply inert/aria-hidden to the toolbar, blocking the
  // block inserter toggle.  Dismiss both before any interaction.
  await dismissWelcomeGuide( page );
  await dismissStarterPatternModal( page );
}

/**
 * Dismiss the "Welcome to the editor" guide modal shown to users who have not
 * yet dismissed it.  No-op if not present.
 */
export async function dismissWelcomeGuide( page: Page ): Promise<void> {
  const modal = page
    .locator( '.components-modal__frame' )
    .filter( { hasText: 'Welcome to the editor' } );

  const appeared = await modal
    .waitFor( { state: 'visible', timeout: 5_000 } )
    .then( () => true )
    .catch( () => false );

  if ( ! appeared ) {
    return;
  }

  // The guide's Close button dismisses it and persists the preference so it
  // won't reappear for subsequent pages in the same session.
  await modal.getByRole( 'button', { name: 'Close' } ).click();
  await modal.waitFor( { state: 'hidden', timeout: 3_000 } ).catch( async () => {
    await page.keyboard.press( 'Escape' );
    await modal.waitFor( { state: 'hidden', timeout: 3_000 } );
  } );
}

/**
 * Dismiss the "Choose a pattern" starter-pattern modal shown for new pages in
 * WP 6.9+.  No-op if the modal is not present (e.g. when starter patterns are
 * disabled or the post already has content).
 */
export async function dismissStarterPatternModal( page: Page ): Promise<void> {
  const modal = page
    .locator( '.components-modal__frame' )
    .filter( { hasText: 'Choose a pattern' } );

  // The modal mounts shortly after the editor canvas; give it a brief window.
  const appeared = await modal
    .waitFor( { state: 'visible', timeout: 5_000 } )
    .then( () => true )
    .catch( () => false );

  if ( ! appeared ) {
    return;
  }

  // Escape is the standard Gutenberg modal dismissal; fall back to the Close
  // button if focus isn't inside the modal.
  await page.keyboard.press( 'Escape' );
  await modal.waitFor( { state: 'hidden', timeout: 3_000 } ).catch( async () => {
    await modal.getByRole( 'button', { name: 'Close' } ).click();
    await modal.waitFor( { state: 'hidden', timeout: 3_000 } );
  } );
}

// Open the block inserter panel (lives in the main frame, not the iframe).
// The toggle's accessible name changed across WP versions ("Toggle block
// inserter" → "Block Inserter" in 6.9), so match either via role.
export async function openBlockInserter( page: Page ): Promise<void> {
  await page
    .getByRole( 'button', { name: /^(Block Inserter|Toggle block inserter)$/i } )
    .first()
    .click();
  await page.waitForSelector( SEL.INSERTER_SEARCH, { state: 'visible' } );
}

// Search for a block by its display title and click the matching list item.
// The inserter UI is in the main frame; the inserted block appears in the iframe.
export async function insertBlock(
  page: Page,
  shortName: string,
  displayTitle: string,
): Promise<void> {
  await page.fill( SEL.INSERTER_SEARCH, displayTitle );
  // A display title alone is ambiguous: WP 6.9 ships a core "Accordion" block
  // and several airo-wp blocks share title words ("Accordion Item", "Scroll
  // Accordion").  Target the airo-wp item by its deterministic inserter class,
  // editor-block-list-item-airo-wp-<shortName>, so the correct block inserts.
  const item = page.locator( `.editor-block-list-item-airo-wp-${shortName}` );
  await item.waitFor( { state: 'visible' } );
  await item.click();
  // The inserted block wrapper appears inside the editor canvas iframe.
  const frame = getEditorFrame( page );
  await frame.locator( `[data-type="airo-wp/${shortName}"]` ).first().waitFor( { state: 'visible' } );
}

// Insert a pattern from the Patterns tab.
//
// Patterns are located by searching their title rather than navigating the
// category sidebar: the active theme can register pattern categories with the
// same label as airo-wp (WP 6.9 shows duplicate "Contact"/"Testimonials"
// tabs), so category navigation is ambiguous.  The categorySlug parameter is
// retained for caller context but not used for navigation.
export async function insertPattern(
  page: Page,
  _categorySlug: string,
  patternTitle: string,
): Promise<void> {
  await page.getByRole( 'tab', { name: 'Patterns' } ).click();

  // The Patterns tab reuses the inserter search input.
  const search = page.locator( SEL.INSERTER_SEARCH );
  await search.waitFor( { state: 'visible' } );
  await search.fill( patternTitle );

  // Pattern preview cards render in the patterns list; match the one whose
  // title text equals the pattern we're inserting.
  const card = page
    .locator( '.block-editor-block-patterns-list__item' )
    .filter( { hasText: patternTitle } )
    .first();
  await card.waitFor( { state: 'visible' } );
  await card.click();

  // Wait for at least one block to appear in the canvas iframe.
  const frame = getEditorFrame( page );
  await frame.locator( `${SEL.CANVAS_BLOCKS} [data-type]` ).first().waitFor( { state: 'visible' } );
}

// Assert that no block-repair / block-validation-error UI is visible.
export async function checkNoRepairUI( page: Page ): Promise<void> {
  const frame = getEditorFrame( page );
  await expect( frame.locator( SEL.BLOCK_WARNING ) ).toHaveCount( 0 );
}

// Fill the page title, publish, wait for the post-publish panel, and return
// the front-end URL and the newly-published page's ID.
// The title input lives in the editor canvas iframe; the publish panel is in the main frame.
export async function publishPage(
  page: Page,
  title: string,
): Promise<{ url: string; pageId: number }> {
  const frame = getEditorFrame( page );
  // A freshly-inserted block keeps its floating block toolbar, which can sit
  // over the post-title <h1> and intercept pointer events (seen with small
  // blocks like pill/icon inserted at the top).  Force-clicking focuses the
  // title directly — deselecting the block and dismissing its toolbar —
  // regardless of the overlay.
  const titleField = frame.locator( SEL.POST_TITLE );
  await titleField.click( { force: true } );
  await titleField.fill( title );

  // Open the pre-publish panel, then confirm.
  await page.click( SEL.PUBLISH_BTN );

  const panel = page.locator( '.editor-post-publish-panel' );
  const confirmBtn = panel.getByRole( 'button', { name: 'Publish', exact: true } );
  await confirmBtn.waitFor( { state: 'visible' } );
  await confirmBtn.click();

  // Post-publish state: the panel-scoped "View Page" link confirms success and
  // carries the front-end URL (e.g. http://host/?page_id=10).  Scoping to the
  // panel avoids matching the toolbar's separate "View Page" link.
  const viewLink = panel.getByRole( 'link', { name: /^View Page/ } );
  await viewLink.waitFor( { state: 'visible', timeout: 30_000 } );

  const href = await viewLink.getAttribute( 'href' );
  if ( ! href ) {
    throw new Error( '[publishPage] View Page link has no href after publishing.' );
  }

  // Page ID from the front-end URL (?page_id= / ?p=), falling back to the
  // editor URL (?post=) for pretty-permalink configurations.
  const idMatch = href.match( /[?&](?:page_id|p)=(\d+)/ ) ?? page.url().match( /[?&]post=(\d+)/ );
  if ( ! idMatch ) {
    throw new Error( `[publishPage] Cannot determine page ID from "${href}" or "${page.url()}".` );
  }

  return { url: href, pageId: parseInt( idMatch[ 1 ], 10 ) };
}

// Fetch the published page via REST and assert CSS classes + no error strings.
export async function checkFrontend(
  requestUtils: RequestUtils,
  pageId: number,
  checks: { cssClass?: string; anyAiroWpClass?: boolean },
): Promise<void> {
  const post = await requestUtils.rest< { link: string; content: { rendered: string } } >( {
    method: 'GET',
    path: `/wp/v2/pages/${ pageId }?context=edit`,
  } );
  const html = post.content?.rendered ?? '';
  const link = post.link ?? '';
  if ( checks.cssClass ) {
    expect( html, `CSS class "${ checks.cssClass }" missing from rendered content` ).toContain( checks.cssClass );
  }
  if ( checks.anyAiroWpClass ) {
    expect( html, 'No wp-block-airo-wp-* class found in rendered content' ).toMatch( /wp-block-airo-wp-[a-z-]+/ );
  }
  for ( const err of ERROR_STRINGS ) {
    expect( html, `front-end rendered "${ err }" at ${ link }` ).not.toContain( err );
  }
}

// REST POST: set the parent page of a published page.
export async function setParentPage(
  requestUtils: RequestUtils,
  pageId: number,
  parentId: number,
): Promise<void> {
  await requestUtils.rest( {
    method: 'POST',
    path: `/wp/v2/pages/${ pageId }`,
    data: { parent: parentId },
  } );
}
