#!/usr/bin/env node
/**
 * Runs e2e tests against the wp-env tests instance.
 * Starts wp-env if not running; stops it only if this script started it.
 *
 * Called by: npm run test:e2e
 */

import { createHash } from 'node:crypto';
import { execSync, spawnSync } from 'node:child_process';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT      = path.resolve( path.dirname( fileURLToPath( import.meta.url ) ), '../..' );
const extraArgs = process.argv.slice( 2 );

// Detect whether wp-env containers are already running.
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

	// MySQL startup timing race: wp-env start sometimes skips the WordPress
	// installation when MySQL isn't fully initialised. A second start always
	// succeeds because MySQL is already up.
	const wpCheck = spawnSync(
		'npx',
		[ 'wp-env', 'run', 'tests-cli', '--', 'wp', 'core', 'is-installed' ],
		{ cwd: ROOT, stdio: 'pipe' }
	);
	if ( wpCheck.status !== 0 ) {
		console.log( 'WordPress not installed after wp-env start (MySQL timing race) — retrying...' );
		spawnSync( 'npx', [ 'wp-env', 'start' ], { cwd: ROOT, stdio: 'inherit' } );
	}
}

// Resolve the tests port from .wp-env.json.
const wpEnvConfig = JSON.parse( readFileSync( path.join( ROOT, '.wp-env.json' ), 'utf8' ) );
const port = wpEnvConfig.testsPort || ( wpEnvConfig.port + 1 );

// Build compiled block assets if not already built (public mirror commits
// dist/, but local dev does not).
if ( ! existsSync( path.join( ROOT, 'dist/blocks' ) ) && existsSync( path.join( ROOT, 'src/blocks' ) ) ) {
	console.log( 'Building block assets...' );
	spawnSync( 'npm', [ 'run', 'build' ], { cwd: ROOT, stdio: 'inherit' } );
}

// Remove dx-lite so the install-from-.org test always exercises the install
// path, even when wp-env persists state across runs.
spawnSync(
	'npx',
	[ 'wp-env', 'run', 'tests-cli', '--', 'wp', 'theme', 'delete', 'dx-lite', '--force' ],
	{ cwd: ROOT, stdio: 'pipe' }
);

// Install Playwright browsers.
spawnSync( 'npx', [ 'playwright', 'install', 'chromium', 'ffmpeg' ], { cwd: ROOT, stdio: 'inherit' } );

// Run Playwright.
const result = spawnSync(
	'npx',
	[ 'playwright', 'test', '--config', 'tests/e2e/functional/playwright.config.ts', ...extraArgs ],
	{
		cwd: ROOT,
		stdio: 'inherit',
		env: { ...process.env, WP_ENV: '1', WP_E2E_PORT: String( port ) },
	}
);

if ( ! alreadyRunning ) {
	spawnSync( 'npx', [ 'wp-env', 'stop' ], { cwd: ROOT, stdio: 'inherit' } );
}

process.exit( result.status ?? 1 );
