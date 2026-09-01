#!/usr/bin/env node
/**
 * Plugin Check suite orchestrator — public (wp-env) lane.
 * Called by: npm run plugin-check
 *
 * Expects builds/airo-wp.zip to already exist (npm run build:zip).
 * Spins up a dedicated wp-env instance on ports 9175/9192, runs PCP,
 * always tears down even on failure.
 */

import { createHash } from 'node:crypto';
import { execSync, spawnSync } from 'node:child_process';
import { existsSync, mkdirSync, rmSync, writeFileSync, copyFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import AdmZip from 'adm-zip';

const ROOT        = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '../..' );
const ZIP_SRC     = path.join( ROOT, 'builds', 'airo-wp.zip' );
const ZIP_TEST    = path.join( ROOT, 'builds', 'airo-wp-test.zip' );
const EXTRACT_DIR = path.join( ROOT, 'builds', 'tmp', 'airo-wp-pcp' );
const WP_ENV_DIR  = path.join( ROOT, 'builds', 'pcp-wp-env' );
const WP_ENV_JSON = path.join( WP_ENV_DIR, '.wp-env.json' );
const WP_ENV_HOME = path.join( ROOT, 'builds', 'pcp-wp-env-home' );

// Pin WordPress core. Left unset, wp-env clones WordPress/WordPress and checks
// out the newest tag, so this lane silently tracks whatever core shipped most
// recently. Default matches readme.txt's "Tested up to".
const WP_VERSION = process.env.WP_VERSION || '7.1';

if ( ! existsSync( ZIP_SRC ) ) {
	console.error( '✗ builds/airo-wp.zip not found — run npm run build:zip first.' );
	process.exit( 1 );
}

// run-plugin-check.mjs guards on builds/airo-wp-test.zip (hardcoded name, existence check only).
copyFileSync( ZIP_SRC, ZIP_TEST );

// Extract zip to a clean staging directory.
rmSync( EXTRACT_DIR, { recursive: true, force: true } );
mkdirSync( EXTRACT_DIR, { recursive: true } );
new AdmZip( ZIP_SRC ).extractAllTo( EXTRACT_DIR, true );
const pluginDir = path.join( EXTRACT_DIR, 'airo-wp' );

// Write isolated wp-env config (separate ports avoid clash with dev instance 9173/9190).
mkdirSync( WP_ENV_DIR, { recursive: true } );
writeFileSync( WP_ENV_JSON, JSON.stringify( {
	core: `https://wordpress.org/wordpress-${ WP_VERSION }.zip`,
	phpVersion: '8.3',
	port: 9175,
	testsPort: 9192,
	plugins: [
		pluginDir,
		'https://downloads.wordpress.org/plugin/plugin-check.2.0.0.zip',
	],
}, null, 2 ) );

// Start wp-env with retry: MySQL is not always accepting connections on first try.
for ( let attempt = 1; attempt <= 3; attempt++ ) {
	const r = spawnSync( 'npx', [ 'wp-env', 'start' ], {
		cwd: WP_ENV_DIR,
		stdio: 'inherit',
		shell: true,
		env: { ...process.env, WP_ENV_HOME },
	} );
	if ( r.status === 0 ) { break; }
	if ( attempt >= 3 ) {
		console.error( `✗ wp-env start failed after ${ attempt } attempts.` );
		cleanup( false );
		process.exit( 1 );
	}
	console.log( `wp-env start failed (attempt ${ attempt }), retrying in 20s…` );
	sleep( 20_000 );
}

try {
	// Copy PCP early-init marker into the CLI container.
	// wp-env stores compose files at ~/.wp-env/<md5-of-wp-env.json-path>/
	const hash       = createHash( 'md5' ).update( WP_ENV_JSON ).digest( 'hex' );
	const composeDir = path.join( WP_ENV_HOME, hash );
	const containerId = execSync(
		'docker compose ps -q cli',
		{ cwd: composeDir, encoding: 'utf8' }
	).trim().split( '\n' )[ 0 ];

	const marker = path.join( ROOT, 'tests', 'e2e', 'check-plugin', 'pcp-early-init-marker.php' );
	execSync( `docker cp "${ marker }" "${ containerId }:/tmp/pcp-early-init-marker.php"` );

	// Run PCP — exit code from run-plugin-check.mjs is the suite result.
	const pcp = spawnSync(
		'node',
		[ path.join( ROOT, 'tests', 'e2e', 'check-plugin', 'run-plugin-check.mjs' ) ],
		{
			cwd: ROOT,
			stdio: 'inherit',
			// WP_ENV_HOME must be propagated: wp-env resolves its instance dir as
			// <WP_ENV_HOME>/<md5 of config path>. Without it the child falls back to
			// ~/.wp-env, where nothing was started, and every `wp-env run cli` call
			// targets a non-existent environment.
			env: {
				...process.env,
				WP_ENV: '1',
				WP_ENV_DIR: WP_ENV_DIR,
				WP_ENV_HOME,
			},
		}
	);

	cleanup( true );
	process.exit( pcp.status ?? 1 );
} catch ( error ) {
	console.error( `✗ Post-start command failed: ${ error.message }` );
	cleanup( true );
	process.exit( 1 );
}

// ─── helpers ──────────────────────────────────────────────────────────────────

function cleanup( stopEnv ) {
	if ( stopEnv ) {
		spawnSync( 'npx', [ 'wp-env', 'stop' ], {
			cwd: WP_ENV_DIR,
			stdio: 'inherit',
			shell: true,
			env: { ...process.env, WP_ENV_HOME },
		} );
	}
	rmSync( EXTRACT_DIR, { recursive: true, force: true } );
	rmSync( ZIP_TEST, { force: true } );
}

function sleep( ms ) {
	Atomics.wait( new Int32Array( new SharedArrayBuffer( 4 ) ), 0, 0, ms );
}
