const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );
const { globSync } = require( 'glob' );
const CopyPlugin = require( 'copy-webpack-plugin' );

const blockEntries = Object.fromEntries(
	globSync( 'src/blocks/*/index.js' ).map( ( file ) => [
		`blocks/${ path.basename( path.dirname( file ) ) }/index`,
		path.resolve( file ),
	] )
);

const viewEntries = Object.fromEntries(
	globSync( 'src/blocks/*/view.js' ).map( ( file ) => [
		`blocks/${ path.basename( path.dirname( file ) ) }/view`,
		path.resolve( file ),
	] )
);

const styleEntries = Object.fromEntries(
	globSync( 'src/blocks/*/style.scss' ).map( ( file ) => [
		`blocks/${ path.basename( path.dirname( file ) ) }/style`,
		path.resolve( file ),
	] )
);

// Patch every sass-loader rule in the default config to silence the @import
// deprecation. src/blocks/ is DSG-owned and cannot be migrated to @use/@forward.
const patchedRules = ( defaultConfig.module?.rules ?? [] ).map( ( rule ) => {
	const uses = Array.isArray( rule.use ) ? rule.use : rule.use ? [ rule.use ] : [];
	const hasSass = uses.some(
		( u ) => typeof u === 'object' && u !== null && String( u.loader ?? '' ).includes( 'sass-loader' )
	);
	if ( ! hasSass ) return rule;
	return {
		...rule,
		use: uses.map( ( u ) => {
			if ( typeof u !== 'object' || u === null || ! String( u.loader ?? '' ).includes( 'sass-loader' ) ) {
				return u;
			}
			return {
				...u,
				options: {
					...( u.options ?? {} ),
					sassOptions: {
						...( u.options?.sassOptions ?? {} ),
						silenceDeprecations: [ 'import' ],
					},
				},
			};
		} ),
	};
} );

module.exports = {
	...defaultConfig,
	entry: {
		...blockEntries,
		...viewEntries,
		...styleEntries,
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( process.cwd(), 'dist' ),
	},
	module: {
		...defaultConfig.module,
		rules: patchedRules,
	},
	resolve: {
		...defaultConfig.resolve,
		alias: {
			...( defaultConfig.resolve?.alias ?? {} ),
			// data/ lives at the plugin root; DSG-owned JS still imports from
			// includes/data/ until the next dsg-sync applies the path rename.
			[ path.resolve( process.cwd(), 'includes/data' ) ]: path.resolve( process.cwd(), 'data' ),
		},
	},
	plugins: [
		...( defaultConfig.plugins || [] ),
		// Copy PHP helper files (e.g. render-helpers.php) that are not
		// webpack entry points and would otherwise be excluded from dist/.
		new CopyPlugin( {
			patterns: [
				{
					from: 'blocks/**/*.php',
					context: path.resolve( process.cwd(), 'src' ),
					to: path.resolve( process.cwd(), 'dist' ),
					noErrorOnMissing: true,
				},
			],
		} ),
	],
};
