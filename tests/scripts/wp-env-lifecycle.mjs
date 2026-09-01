#!/usr/bin/env node
/**
 * wp-env lifecycle engine for public-lane entry-point scripts.
 *
 * Usage:
 *   import { createLifecycle } from './wp-env-lifecycle.mjs';
 *   const lc = createLifecycle( ROOT );
 *
 *   lc.on( 'afterStart',     () => { ... } );  // fresh start only — one-time container setup
 *   lc.on( 'afterCoreSetup', () => { ... } );  // fresh start only — app-level setup
 *   lc.on( 'run',            () => { ...; lc.exit( code ); } );  // always
 *
 *   lc.execute();
 *
 * Events:
 *   afterStart      fired after wp-env starts and containers are verified
 *   afterCoreSetup  fired after core WordPress state (rewrites) is applied
 *                   (fresh start only — but the rewrites themselves always run)
 *   run             fired to perform the main work; call lc.exit(code) to set the exit code
 */

import { createHash } from 'node:crypto';
import { EventEmitter } from 'node:events';
import { execSync, spawnSync } from 'node:child_process';

// ─── internal helpers ──────────────────────────────────────────────────────────

function detectRunning(root) {
	const hash = createHash('md5').update(`${root}/.wp-env.json`).digest('hex');
	try {
		// Match the long-lived tests-wordpress service specifically, not any
		// container carrying the env hash. The mysql services can stay Up while
		// the WordPress containers have exited (e.g. OOM-killed): filtering on the
		// bare hash would misread that half-up state as "running" and skip
		// wp-env start, so commands then hit an exited tests-wordpress container.
		const out = execSync(
			`docker ps --filter "name=${hash}-tests-wordpress" --format "{{.Names}}"`,
			{ encoding: 'utf8', stdio: ['pipe', 'pipe', 'pipe'] }
		);
		return out.trim().length > 0;
	} catch {
		return false;
	}
}

function startEnv(root) {
	spawnSync('npx', ['wp-env', 'start'], { cwd: root, stdio: 'inherit' });

	if (!detectRunning(root)) {
		console.error('✗ wp-env start failed and no containers are running.');
		process.exit(1);
	}

	// MySQL startup timing race: wp-env start sometimes skips WordPress installation
	// when MySQL isn't fully initialised. A second start always succeeds.
	const wpCheck = spawnSync(
		'npx',
		['wp-env', 'run', 'tests-cli', '--', 'wp', 'core', 'is-installed'],
		{ cwd: root, stdio: 'pipe' }
	);
	if (wpCheck.status !== 0) {
		console.log(
			'WordPress not installed after wp-env start (MySQL timing race) — retrying wp-env start...'
		);
		spawnSync('npx', ['wp-env', 'start'], { cwd: root, stdio: 'inherit' });
	}
}

function stopEnv(root) {
	spawnSync('npx', ['wp-env', 'stop'], { cwd: root, stdio: 'inherit' });
}

function coreSetup(root) {
	spawnSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-cli',
			'--',
			'wp',
			'rewrite',
			'structure',
			'/%postname%/',
		],
		{ cwd: root, stdio: 'inherit' }
	);
	spawnSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-cli',
			'--',
			'wp',
			'rewrite',
			'flush',
			'--hard',
		],
		{ cwd: root, stdio: 'inherit' }
	);
}

// ─── lifecycle ─────────────────────────────────────────────────────────────────

class WpEnvLifecycle extends EventEmitter {
	constructor(root) {
		super();
		this.root = root;
		this._exitCode = 0;
		this._alreadyRunning = false;
	}

	/**
	 * Set the process exit code from inside a 'run' handler.
	 * @param code
	 */
	exit(code) {
		this._exitCode = code ?? 1;
	}

	/**
	 * Hard-fail: stop the env if we started it (so the next run retries setup),
	 * then exit immediately with the given code.
	 * Use from 'afterStart' / 'afterCoreSetup' handlers when a setup step fails.
	 * @param code
	 */
	fail(code) {
		if (!this._alreadyRunning) {
			stopEnv(this.root);
		}
		process.exit(code ?? 1);
	}

	/** Start env (if needed), emit lifecycle events, then stop and exit. */
	execute() {
		this._alreadyRunning = detectRunning(this.root);

		if (!this._alreadyRunning) {
			startEnv(this.root);
			this.emit('afterStart');
		}

		// coreSetup runs unconditionally, NOT just on a fresh start. Pretty
		// permalinks are core state every REST-based spec depends on, and they have
		// to hold no matter who started the environment. Both CI workflows run
		// `npm run wp-env:start` as a separate step before `npm run test:e2e`, so by
		// the time the lifecycle runs the env is already up — when this was inside
		// the fresh-start branch the rewrites were silently skipped and every
		// /wp-json/ request failed with:
		//   { code: 'rest_no_route', data: { status: 404 } }
		// A developer leaving wp-env running locally hit the same thing. Both
		// commands are idempotent, so running them again costs ~1s and guarantees
		// the state.
		coreSetup(this.root);

		if (!this._alreadyRunning) {
			this.emit('afterCoreSetup');
		}

		this.emit('run');

		if (!this._alreadyRunning) {
			stopEnv(this.root);
		}

		process.exit(this._exitCode);
	}
}

export function createLifecycle(root) {
	return new WpEnvLifecycle(root);
}
