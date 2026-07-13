import { test, expect } from '@wordpress/e2e-test-utils-playwright';

test( 'airo-wp plugin is active', async ( { requestUtils } ) => {
	const plugins = await requestUtils.rest< Array< { plugin: string; status: string } > >( {
		method: 'GET',
		path: '/wp/v2/plugins',
	} );

	const airoWp = plugins.find( ( p ) => p.plugin.endsWith( '/airo-wp' ) );
	expect( airoWp, 'airo-wp plugin not found in plugin list' ).toBeDefined();
	expect( airoWp!.status ).toBe( 'active' );
} );
