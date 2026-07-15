import { test } from '@wordpress/e2e-test-utils-playwright';

// Seeds global styles by visiting the site editor once, which causes WordPress
// to create the user's wp_global_styles CPT post. The list/get-global-styles
// MCP tool specs depend on this post existing before they run.
test( 'provision: seed global styles', async ( { page } ) => {
	test.setTimeout( 120_000 );
	await page.goto( '/wp-admin/site-editor.php', { waitUntil: 'domcontentloaded' } );
	// Wait for the editor canvas iframe — confirms WordPress rendered the editor
	// and created the wp_global_styles CPT entry that later specs depend on.
	await page.waitForSelector( 'iframe[name="editor-canvas"], .edit-site-visual-editor__editor-canvas, .is-loaded', { timeout: 90_000 } );
} );
