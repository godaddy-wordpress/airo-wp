import { test, expect } from '@wordpress/e2e-test-utils-playwright';

/**
 * Verifies DSG-sourced blocks and pattern categories are registered.
 *
 * Uses requestUtils.rest() from @wordpress/e2e-test-utils-playwright which
 * authenticates via the global storageState cookie + WP REST nonce.
 */

type BlockPattern = { name: string; categories: string[]; content: string };

async function fetchPatternsByCategory(
	requestUtils: import('@wordpress/e2e-test-utils-playwright').RequestUtils,
	category: string
): Promise<BlockPattern[]> {
	return requestUtils.rest<BlockPattern[]>( {
		method: 'GET',
		path: `/wp/v2/block-patterns/patterns?per_page=100&category=${ category }`,
	} );
}

test.describe( 'DSG blocks and patterns registration', () => {

	test( 'all 16 pattern categories are registered with airo-wp- prefix', async ( { requestUtils } ) => {
		const categories = await requestUtils.rest< Array< { name: string } > >( {
			method: 'GET',
			path: '/wp/v2/block-patterns/categories',
		} );

		const slugs = categories.map( ( c ) => c.name );

		const expected = [
			'airo-wp-contact',
			'airo-wp-content',
			'airo-wp-cta',
			'airo-wp-faq',
			'airo-wp-features',
			'airo-wp-footer',
			'airo-wp-gallery',
			'airo-wp-header',
			'airo-wp-headings',
			'airo-wp-hero',
			'airo-wp-homepage',
			'airo-wp-modal',
			'airo-wp-pricing',
			'airo-wp-services',
			'airo-wp-team',
			'airo-wp-testimonials',
		];

		for ( const slug of expected ) {
			expect( slugs, `missing pattern category: ${ slug }` ).toContain( slug );
		}
	} );

	test( 'airo-wp block types are registered with correct namespace', async ( { requestUtils } ) => {
		const blockTypes = await requestUtils.rest< Array< { name: string } > >( {
			method: 'GET',
			path: '/wp/v2/block-types?namespace=airo-wp&per_page=100',
		} );

		// dist/blocks/ is committed to the repository and ships with the plugin,
		// so blocks should always be registered in the e2e environment.
		expect(
			blockTypes.length,
			`expected at least 60 airo-wp block types, got ${ blockTypes.length }`
		).toBeGreaterThanOrEqual( 60 );

		for ( const block of blockTypes ) {
			expect( block.name, `block ${ block.name } should start with airo-wp/` ).toMatch( /^airo-wp\// );
		}
	} );
} );

// ---------------------------------------------------------------------------
// Pattern insertion
// ---------------------------------------------------------------------------

test.describe( 'Pattern insertion from each category', () => {

	test( 'one pattern from each category can be saved to a page', async ( { requestUtils } ) => {
		const categories = [
			'airo-wp-contact',
			'airo-wp-content',
			'airo-wp-cta',
			'airo-wp-faq',
			'airo-wp-features',
			'airo-wp-footer',
			'airo-wp-gallery',
			'airo-wp-header',
			'airo-wp-headings',
			'airo-wp-hero',
			'airo-wp-homepage',
			'airo-wp-modal',
			'airo-wp-pricing',
			'airo-wp-services',
			'airo-wp-team',
			'airo-wp-testimonials',
		];

		const contents: string[] = [];

		for ( const category of categories ) {
			const patterns = await fetchPatternsByCategory( requestUtils, category );
			expect( patterns.length, `no pattern registered for category: ${ category }` ).toBeGreaterThan( 0 );
			contents.push( patterns[ 0 ].content );
		}

		expect( contents.length, 'expected content for all 16 categories' ).toBe( 16 );

		// Collect every wp-block-airo-wp-* class present in the pattern HTML.
		// Static blocks save their rendered HTML inside the block comment, so
		// these same classes must survive the REST API round-trip unchanged.
		const expectedClasses = new Set<string>();
		for ( const content of contents ) {
			const matches = content.match( /wp-block-airo-wp-[a-z-]+/g ) ?? [];
			matches.forEach( ( cls ) => expectedClasses.add( cls ) );
		}

		const page = await requestUtils.rest< { content: { rendered: string } } >( {
			method: 'POST',
			path: '/wp/v2/pages',
			data: {
				title:   'E2E: One Pattern Per Category',
				content: contents.join( '\n' ),
				status:  'publish',
			},
		} );

		const rendered = page.content.rendered as string;

		for ( const cls of expectedClasses ) {
			expect( rendered, `block class "${ cls }" missing from rendered content` ).toContain( cls );
		}
	} );
} );

// ---------------------------------------------------------------------------
// Custom block rendering
// ---------------------------------------------------------------------------

test.describe( 'Custom block rendering', () => {

	test( 'static airo-wp block markup renders correctly in a page', async ( { requestUtils } ) => {
		const contentPatterns = await fetchPatternsByCategory( requestUtils, 'airo-wp-content' );

		// content-stats-counters uses: section, grid, counter-group, counter, icon.
		const pattern = contentPatterns.find(
			( p: BlockPattern ) => p.name === 'airo-wp/content/content-stats-counters'
		);
		expect( pattern, 'content-stats-counters pattern not found' ).toBeTruthy();
		if ( ! pattern ) return;

		const page = await requestUtils.rest< { content: { rendered: string } } >( {
			method: 'POST',
			path: '/wp/v2/pages',
			data: {
				title:   'E2E: Static Block Rendering',
				content: pattern.content,
				status:  'publish',
			},
		} );

		const rendered = page.content.rendered as string;

		const expected = [
			'wp-block-airo-wp-section',
			'wp-block-airo-wp-grid',
			'wp-block-airo-wp-counter-group',
			'wp-block-airo-wp-counter',
			'wp-block-airo-wp-icon',
		];

		for ( const cls of expected ) {
			expect( rendered, `expected CSS class "${ cls }" in rendered content` ).toContain( cls );
		}
	} );
} );
