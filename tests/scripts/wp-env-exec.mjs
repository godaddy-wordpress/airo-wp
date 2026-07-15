#!/usr/bin/env node
/**
 * Runs a wp-env tests-cli composer subcommand with smart lifecycle management.
 * Starts wp-env if not running; stops it only if this script started it.
 *
 * Usage: node tests/scripts/wp-env-exec.mjs <composer-subcommand>
 * Called by: npm run test:unit, npm run lint
 */

import { createHash } from 'node:crypto';
import { execSync, spawnSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT       = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '../..' );
const subcommand = process.argv[ 2 ];

if ( ! subcommand ) {
	console.error( 'Usage: node wp-env-exec.mjs <composer-subcommand>' );
	process.exit( 1 );
}

// Detect whether wp-env containers are already running by checking docker ps
// for any container whose name contains the env hash.
const hash = createHash( 'md5' ).update( `${ ROOT }/.wp-env.json` ).digest( 'hex' );
let alreadyRunning = false;
try {
	const out = execSync(
		`docker ps --filter "name=${ hash }" --format "{{.Names}}"`,
		{ encoding: 'utf8', stdio: [ 'pipe', 'pipe', 'pipe' ] }
	);
	alreadyRunning = out.trim().length > 0;
} catch {
	// docker unavailable — wp-env start will surface the error below
}

if ( ! alreadyRunning ) {
	spawnSync( 'npx', [ 'wp-env', 'start' ], { cwd: ROOT, stdio: 'inherit' } );

	// afterStart may fail (e.g. transient composer install error on macOS bind mounts)
	// even when the containers came up successfully. Verify containers are actually
	// running before proceeding rather than trusting wp-env start's exit code.
	let containersUp = false;
	try {
		const out = execSync(
			`docker ps --filter "name=${ hash }" --format "{{.Names}}"`,
			{ encoding: 'utf8', stdio: [ 'pipe', 'pipe', 'pipe' ] }
		);
		containersUp = out.trim().length > 0;
	} catch { /* ignore */ }

	if ( ! containersUp ) {
		console.error( '✗ wp-env start failed and no containers are running.' );
		process.exit( 1 );
	}

	// MySQL startup timing race: wp-env start sometimes skips the WordPress
	// installation when MySQL isn't fully initialised. A second start always
	// succeeds because MySQL is already up. Skip the retry if afterStart also
	// failed (non-zero exit from the composer install step is tolerated).
	const wpCheck = spawnSync(
		'npx',
		[ 'wp-env', 'run', 'tests-cli', '--', 'wp', 'core', 'is-installed' ],
		{ cwd: ROOT, stdio: 'pipe' }
	);
	if ( wpCheck.status !== 0 ) {
		console.log( 'WordPress not installed after wp-env start (MySQL timing race) — retrying wp-env start...' );
		spawnSync( 'npx', [ 'wp-env', 'start' ], { cwd: ROOT, stdio: 'inherit' } );
	}
}

// Ensure vendor/ is populated regardless of whether afterStart's composer install
// succeeded — mirrors the old CI's explicit "Install PHP dependencies" step.
const install = spawnSync(
	'npx',
	[ 'wp-env', 'run', 'tests-cli', '--env-cwd=wp-content/plugins/airo-wp', '--', 'composer', 'install', '--no-interaction', '--no-progress' ],
	{ cwd: ROOT, stdio: 'inherit' }
);
if ( install.status !== 0 ) {
	if ( ! alreadyRunning ) spawnSync( 'npx', [ 'wp-env', 'stop' ], { cwd: ROOT, stdio: 'inherit' } );
	process.exit( install.status ?? 1 );
}

const result = spawnSync(
	'npx',
	[ 'wp-env', 'run', 'tests-cli', '--env-cwd=wp-content/plugins/airo-wp', '--', 'composer', subcommand ],
	{ cwd: ROOT, stdio: 'inherit' }
);

if ( ! alreadyRunning ) {
	spawnSync( 'npx', [ 'wp-env', 'stop' ], { cwd: ROOT, stdio: 'inherit' } );
}

process.exit( result.status ?? 1 );
