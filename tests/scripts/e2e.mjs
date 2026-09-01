#!/usr/bin/env node
/**
 * Runs e2e tests against the wp-env tests instance.
 * Starts wp-env if not running; stops it only if this script started it.
 *
 * Called by: npm run test:e2e
 */

import { spawnSync } from 'node:child_process';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { createLifecycle } from './wp-env-lifecycle.mjs';

const ROOT = path.resolve(
	path.dirname(fileURLToPath(import.meta.url)),
	'../..'
);
const extraArgs = process.argv.slice(2);

const lc = createLifecycle(ROOT);

lc.on('run', () => {
	const wpEnvConfig = JSON.parse(
		readFileSync(path.join(ROOT, '.wp-env.json'), 'utf8')
	);
	const port = wpEnvConfig.testsPort || wpEnvConfig.port + 1;

	// Build compiled block assets if not already built (public mirror commits
	// dist/, but local dev does not).
	if (
		!existsSync(path.join(ROOT, 'dist/blocks')) &&
		existsSync(path.join(ROOT, 'src/blocks'))
	) {
		console.log('Building block assets...');
		spawnSync('npm', ['run', 'build'], { cwd: ROOT, stdio: 'inherit' });
	}

	// Remove dx-lite so the install-from-.org test always exercises the install
	// path, even when wp-env persists state across runs.
	spawnSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-cli',
			'--',
			'wp',
			'theme',
			'delete',
			'dx-lite',
			'--force',
		],
		{ cwd: ROOT, stdio: 'pipe' }
	);

	// hello-dolly is used by the mcp-update-plugin e2e spec; install it
	// explicitly because the wp-env WordPress image does not bundle it under the
	// `hello-dolly` slug (the bundled "Hello Dolly" ships as `hello`). A reused
	// wp-env server skips the provisioning script, so install it here instead.
	// Idempotent across reruns.
	spawnSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-cli',
			'--',
			'wp',
			'plugin',
			'install',
			'hello-dolly',
		],
		{ cwd: ROOT, stdio: 'pipe' }
	);

	spawnSync('npx', ['playwright', 'install', 'chromium', 'ffmpeg'], {
		cwd: ROOT,
		stdio: 'inherit',
	});

	const result = spawnSync(
		'npx',
		[
			'playwright',
			'test',
			'--config',
			'tests/e2e/functional/playwright.config.ts',
			...extraArgs,
		],
		{
			cwd: ROOT,
			stdio: 'inherit',
			env: { ...process.env, WP_ENV: '1', WP_E2E_PORT: String(port) },
		}
	);

	lc.exit(result.status ?? 1);
});

lc.execute();
