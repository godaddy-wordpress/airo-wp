#!/usr/bin/env node
/**
 * Rewrites .wp-env.json for one PHP × WP combination, installing the plugin from
 * a built zip.
 *
 * Called by .github/workflows/pre-release.yml before the E2E matrix, and usable
 * directly for a one-off local run against a specific PHP x WP combination.
 *
 * Usage:
 *   node tests/scripts/configure-wp-env.mjs --php 8.3 --core <url> [--zip builds/airo-wp.zip]
 *
 * wp-env's "plugins" array accepts a local *directory*, an https://….zip URL, or
 * an org/repo shorthand — it does NOT accept a local .zip path. Passing one makes
 * wp-env derive the slug from the filename and fail activation with:
 *
 *   Warning: The 'airo-wp.zip' plugin could not be found.
 *   Error: No plugins activated.
 *
 * So the zip is extracted here and the extracted directory is what wp-env mounts.
 * The distributable zip already contains vendor/, dependencies/ and dist/, so the
 * extracted tree is a complete, activatable plugin with no composer step needed.
 */

import { existsSync, mkdirSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import AdmZip from 'adm-zip';

const ROOT = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '../..' );

function arg( name, fallback = undefined ) {
	const i = process.argv.indexOf( `--${ name }` );
	if ( i !== -1 && process.argv[ i + 1 ] ) {
		return process.argv[ i + 1 ];
	}
	return fallback;
}

const php     = arg( 'php' );
const core    = arg( 'core' );
const zipPath = path.resolve( ROOT, arg( 'zip', 'builds/airo-wp.zip' ) );
// Extracted alongside the zip; builds/ is gitignored so this never pollutes the tree.
const extractDir = path.join( ROOT, 'builds', 'tmp', 'airo-wp-e2e' );

if ( ! php || ! core ) {
	console.error( 'Usage: configure-wp-env.mjs --php <version> --core <url> [--zip <path>]' );
	process.exit( 1 );
}

if ( ! existsSync( zipPath ) ) {
	console.error( `✗ ${ path.relative( ROOT, zipPath ) } not found — run npm run build:zip first.` );
	process.exit( 1 );
}

// Extract into a clean directory so a stale tree from a previous run can't be tested.
rmSync( extractDir, { recursive: true, force: true } );
mkdirSync( extractDir, { recursive: true } );
new AdmZip( zipPath ).extractAllTo( extractDir, true );

const pluginDir = path.join( extractDir, 'airo-wp' );
if ( ! existsSync( path.join( pluginDir, 'airo-wp.php' ) ) ) {
	console.error( `✗ ${ path.relative( ROOT, pluginDir ) }/airo-wp.php missing — unexpected zip layout.` );
	process.exit( 1 );
}
// The plugin must carry its own autoloader: airo-wp.php no-ops without it, which
// would let the whole E2E matrix pass against an inert plugin.
if ( ! existsSync( path.join( pluginDir, 'vendor', 'autoload.php' ) ) ) {
	console.error( '✗ vendor/autoload.php missing from the zip — the plugin would load as a no-op.' );
	process.exit( 1 );
}

const cfgPath = path.join( ROOT, '.wp-env.json' );
const cfg     = JSON.parse( readFileSync( cfgPath, 'utf8' ) );

cfg.phpVersion = php;
cfg.core       = core;
cfg.plugins    = Array.isArray( cfg.plugins ) ? cfg.plugins : [];
cfg.plugins[ 0 ] = pluginDir;

writeFileSync( cfgPath, JSON.stringify( cfg, null, 2 ) + '\n' );

console.log( `✔ .wp-env.json → PHP ${ php }, core ${ core }` );
console.log( `  plugin: ${ path.relative( ROOT, pluginDir ) } (extracted from ${ path.relative( ROOT, zipPath ) })` );
