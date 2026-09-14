#!/usr/bin/env node
/**
 * Assembles builds/airo-wp.zip and builds/airo-wp-{version}.zip from the repo root.
 * Uses wp-scripts plugin-zip, which reads the package.json "files" allowlist.
 * Called by: npm run build:zip (after npm run build populates dist/).
 */

import { readFileSync, mkdirSync, readdirSync, renameSync, copyFileSync, rmSync, existsSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '../../..' );
const OUTPUT_DIR = path.join( ROOT, 'builds' );

const { version } = JSON.parse( readFileSync( path.join( ROOT, 'package.json' ), 'utf8' ) );
const OUTPUT_ZIP = path.join( OUTPUT_DIR, 'airo-wp.zip' );
const OUTPUT_ZIP_VERSIONED = path.join( OUTPUT_DIR, `airo-wp-${ version }.zip` );
const TEMP_ZIP = path.join( ROOT, 'airo-wp.zip' );

mkdirSync( OUTPUT_DIR, { recursive: true } );
rmSync( OUTPUT_ZIP, { force: true } );
rmSync( OUTPUT_ZIP_VERSIONED, { force: true } );
rmSync( TEMP_ZIP, { force: true } );

console.log( `build-zip.mjs: assembling ${ OUTPUT_ZIP }` );

// Install production Composer dependencies (generates vendor/ and dependencies/ via Strauss)
for ( const cmd of [
	[ 'composer', [ 'install', '--no-dev', '--no-interaction', '--prefer-dist' ] ],
	[ 'composer', [ 'dump-autoload', '--no-dev' ] ],
] ) {
	const r = spawnSync( cmd[ 0 ], cmd[ 1 ], { cwd: ROOT, stdio: 'inherit' } );
	if ( r.error || r.status !== 0 ) {
		console.error( `build-zip.mjs: ${ cmd[ 0 ] } ${ cmd[ 1 ].join( ' ' ) } failed` );
		process.exit( r.status ?? 1 );
	}
}

// Strip dev artifacts from Strauss-prefixed dependencies (mirrors build-zip.sh cleanup)
const DEV_DIR_NAMES = new Set( [ '.github', 'tests', 'docs' ] );
function cleanDevArtifacts( dir ) {
	for ( const entry of readdirSync( dir, { withFileTypes: true } ) ) {
		const entryPath = path.join( dir, entry.name );
		if ( entry.isDirectory() ) {
			if ( DEV_DIR_NAMES.has( entry.name ) ) {
				rmSync( entryPath, { recursive: true, force: true } );
			} else {
				cleanDevArtifacts( entryPath );
			}
		} else if ( entry.isFile() && entry.name.endsWith( '.md' ) ) {
			rmSync( entryPath, { force: true } );
		}
	}
}
const depsDir = path.join( ROOT, 'dependencies' );
if ( existsSync( depsDir ) ) {
	cleanDevArtifacts( depsDir );
}

// The zip no longer ships src/, so dist/ is the only copy of the block code that
// reaches users. That makes a skipped or stale build silent and fatal rather than
// merely wasteful: the archive would contain neither compiled blocks nor the source
// they came from, and a plugin that registers nothing can still pass a lint.
//
// Assert instead that every block in src/ has a compiled counterpart in dist/. This
// catches a build that never ran AND a build that predates a newly added block.
// npm run build:zip runs wp-scripts build first, so a failure here means the build
// was skipped or it failed without stopping the pipeline.
{
	const blockNames = ( dir ) => {
		const base = path.join( ROOT, dir, 'blocks' );
		if ( ! existsSync( base ) ) return null;
		return new Set(
			readdirSync( base, { withFileTypes: true } )
				.filter( ( e ) => e.isDirectory() && existsSync( path.join( base, e.name, 'block.json' ) ) )
				.map( ( e ) => e.name )
		);
	};

	const src = blockNames( 'src' );
	const dist = blockNames( 'dist' );

	if ( ! src ) {
		console.error( 'build-zip.mjs: src/blocks/ not found — cannot verify the build.' );
		process.exit( 1 );
	}

	if ( ! dist ) {
		console.error(
			'build-zip.mjs: dist/blocks/ is missing. The asset build did not run.\n' +
				'  Run `npm run build:zip` (which builds first), not build-zip.mjs directly.'
		);
		process.exit( 1 );
	}

	const missing = [ ...src ].filter( ( name ) => ! dist.has( name ) ).sort();

	if ( missing.length > 0 ) {
		console.error(
			`build-zip.mjs: ${ missing.length } block(s) in src/ have no compiled output in dist/:\n` +
				missing.map( ( n ) => `  ${ n }` ).join( '\n' ) +
				'\n  dist/ is stale or the build failed. Re-run `npm run build`.'
		);
		process.exit( 1 );
	}

	console.log( `build-zip.mjs: build verified — ${ dist.size } compiled blocks match src/.` );
}

const result = spawnSync( 'npx', [ 'wp-scripts', 'plugin-zip' ], {
	cwd: ROOT,
	stdio: 'inherit',
} );

if ( result.error ) {
	console.error( `build-zip.mjs: ${ result.error.message }` );
	process.exit( 1 );
}

if ( result.status !== 0 ) {
	process.exit( result.status ?? 1 );
}

// Remove package.json from zip (npm-packlist always includes it regardless of files allowlist)
console.log( 'build-zip.mjs: removing package.json from zip...' );
{
	const { createRequire } = await import( 'node:module' );
	const req = createRequire( import.meta.url );
	const AdmZip = req( 'adm-zip' );
	const zip = new AdmZip( TEMP_ZIP );
	zip.deleteFile( 'airo-wp/package.json' );

	// Test-support code ships only because package.json "files" allowlists
	// includes/ wholesale. Nothing in the plugin's runtime references any of it —
	// the only referents are tests/, and phpcs.xml already excludes
	// includes/Internal/Testing/ — so it installs on every user's site, unused.
	//
	// Dropped here rather than by narrowing the "files" allowlist, because the
	// files must stay in the repo: PHPUnit's container tests use these fixtures.
	// The allowlist cannot express "ship includes/ except this subtree".
	//
	// This list is duplicated in the release tooling's other zip builder. Change
	// both together: if they drift, the archive that gets tested stops being the
	// archive that gets published, which is the one property this strip exists to
	// preserve.
	const TEST_ONLY_PREFIXES = [
		'airo-wp/includes/Internal/Testing/',
		'airo-wp/includes/Internal/DependencyManagement/TestingContainer.php',
	];

	const testOnly = zip
		.getEntries()
		.map( ( entry ) => entry.entryName )
		.filter( ( name ) =>
			TEST_ONLY_PREFIXES.some( ( prefix ) => name.startsWith( prefix ) )
		);

	for ( const name of testOnly ) {
		zip.deleteFile( name );
	}

	console.log(
		`build-zip.mjs: removed ${ testOnly.length } test-support entries from zip`
	);

	// AdmZip silently ignores a deleteFile for a name it does not hold, so assert
	// the removal actually happened rather than trusting the call.
	const stillThere = zip
		.getEntries()
		.map( ( entry ) => entry.entryName )
		.filter( ( name ) =>
			TEST_ONLY_PREFIXES.some( ( prefix ) => name.startsWith( prefix ) )
		);

	if ( stillThere.length > 0 ) {
		console.error(
			`build-zip.mjs: test-support code survived removal:\n  ${ stillThere.join(
				'\n  '
			) }`
		);
		rmSync( TEMP_ZIP, { force: true } );
		process.exit( 1 );
	}

	// .wordpress-org/ holds the WordPress.org plugin-directory art (banner, icon,
	// screenshots). Those belong in SVN assets/, never inside the plugin users
	// install. It is absent from package.json "files" so it is excluded already;
	// this asserts that stays true if someone edits that allowlist later.
	const stowaways = zip
		.getEntries()
		.map( ( entry ) => entry.entryName )
		.filter( ( name ) => name.includes( '.wordpress-org' ) );

	if ( stowaways.length > 0 ) {
		console.error(
			`build-zip.mjs: .wordpress-org/ must not ship inside the plugin zip:\n  ${ stowaways.join( '\n  ' ) }`
		);
		// Drop the half-built archive: TEMP_ZIP sits at the repo root and is not
		// gitignored, so leaving it behind invites committing a stray zip.
		rmSync( TEMP_ZIP, { force: true } );
		process.exit( 1 );
	}

	zip.writeZip( TEMP_ZIP );
}

renameSync( TEMP_ZIP, OUTPUT_ZIP );

console.log( `build-zip.mjs: copying to ${ OUTPUT_ZIP_VERSIONED }` );
copyFileSync( OUTPUT_ZIP, OUTPUT_ZIP_VERSIONED );

process.exit( 0 );
