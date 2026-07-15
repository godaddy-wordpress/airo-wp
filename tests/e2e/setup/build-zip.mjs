#!/usr/bin/env node
/**
 * Assembles builds/airo-wp.zip and builds/airo-wp-{version}.zip from the repo root.
 * Uses wp-scripts plugin-zip (reads package.json "files") — same mechanism as the private lane.
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

function removeTestDirs( dir ) {
	for ( const entry of readdirSync( dir, { withFileTypes: true } ) ) {
		if ( ! entry.isDirectory() ) continue;
		const entryPath = path.join( dir, entry.name );
		if ( entry.name === 'test' ) {
			rmSync( entryPath, { recursive: true, force: true } );
		} else {
			removeTestDirs( entryPath );
		}
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
const srcDir = path.join( ROOT, 'src' );
if ( existsSync( srcDir ) ) {
	removeTestDirs( srcDir );
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
	zip.writeZip( TEMP_ZIP );
}

renameSync( TEMP_ZIP, OUTPUT_ZIP );

console.log( `build-zip.mjs: copying to ${ OUTPUT_ZIP_VERSIONED }` );
copyFileSync( OUTPUT_ZIP, OUTPUT_ZIP_VERSIONED );

process.exit( 0 );
