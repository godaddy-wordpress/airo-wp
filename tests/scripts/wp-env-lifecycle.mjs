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
import path from 'node:path';

// ─── internal helpers ──────────────────────────────────────────────────────────

/**
 * The Docker Compose project names wp-env may be using for this config.
 *
 * wp-env names its containers after its work directory, and it derives that
 * directory two different ways: historically the full md5 of the config file
 * path, and since 11.0.0 a descriptive `wp-env-<dir>-<first 8 of that md5>`. It
 * keeps the legacy spelling only when that cache directory already exists, so a
 * machine that has run an older wp-env stays on the old name while a clean one
 * gets the new name.
 *
 * Both are accepted rather than tracking which applies. Checking only the full
 * hash made this fail exactly where it is least visible: on a clean CI runner
 * `wp-env start` succeeded and reported both sites up, then the check below found
 * nothing and aborted the run — while every developer machine, having a legacy
 * cache directory, kept working.
 */
export function composeProjectNames(root) {
	const fullHash = createHash('md5').update(`${root}/.wp-env.json`).digest('hex');
	return [
		// wp-env 10.x, and 11.x where a legacy cache directory already exists.
		fullHash,
		// wp-env 11.x on a directory it has not seen before.
		`wp-env-${path.basename(root).toLowerCase()}-${fullHash.slice(0, 8)}`,
	];
}

function detectRunning(root) {
	// Match the long-lived tests-wordpress service specifically, not any
	// container carrying the env hash. The mysql services can stay Up while
	// the WordPress containers have exited (e.g. OOM-killed): filtering on the
	// bare hash would misread that half-up state as "running" and skip
	// wp-env start, so commands then hit an exited tests-wordpress container.
	for (const project of composeProjectNames(root)) {
		try {
			const out = execSync(
				`docker ps --filter "name=${project}-tests-wordpress" --format "{{.Names}}"`,
				{ encoding: 'utf8', stdio: ['pipe', 'pipe', 'pipe'] }
			);
			if (out.trim().length > 0) {
				return true;
			}
		} catch {
			// Docker unavailable, or this spelling matched nothing: try the next.
		}
	}
	return false;
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
