#!/usr/bin/env node
/**
 * Runs one or more commands inside wp-env tests-wordpress with smart lifecycle management.
 * Starts wp-env if not running; stops it only if this script started it.
 *
 * Usage: node tests/scripts/wp-env-exec.mjs <cmd> [args...] [-- <cmd> [args...] ...]
 * Called by: npm run test:unit:php, npm run lint, npm run lint:php, npm run format:php
 */

import { spawnSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { createLifecycle } from './wp-env-lifecycle.mjs';

const ROOT = path.resolve(
	path.dirname(fileURLToPath(import.meta.url)),
	'../..'
);

// Split argv on '--' to support multiple sequential commands in one lifecycle run.
const commands = [];
let current = [];
for (const arg of process.argv.slice(2)) {
	if (arg === '--') {
		if (current.length) {
			commands.push(current);
		}
		current = [];
	} else {
		current.push(arg);
	}
}
if (current.length) {
	commands.push(current);
}

if (!commands.length) {
	console.error(
		'Usage: node wp-env-exec.mjs <cmd> [args...] [-- <cmd> [args...] ...]'
	);
	process.exit(1);
}

const lc = createLifecycle(ROOT);

/**
 * Ensure the plugin's Composer dependencies are installed inside the container.
 *
 * wp-env installs the composer *binary* and a global phpunit, but never runs
 * `composer install` for the mapped plugin. vendor/ is gitignored (absent on a
 * fresh checkout / CI), so `composer lint` (phpcs) and `composer test`
 * (vendor/bin/phpunit) would fail with "not found". Install once, guarded on
 * vendor/bin/phpcs so repeat runs stay fast. The post-install-cmd also runs
 * Strauss to regenerate dependencies/.
 */
function ensureComposerDeps() {
	const check = spawnSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-wordpress',
			'--env-cwd=wp-content/plugins/airo-wp',
			'--',
			'test',
			'-f',
			'vendor/bin/phpcs',
		],
		{ cwd: ROOT, stdio: 'pipe' }
	);
	if (check.status === 0) {
		return;
	}

	console.log('Installing Composer dependencies (vendor/ missing)…');
	const install = spawnSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-wordpress',
			'--env-cwd=wp-content/plugins/airo-wp',
			'--',
			'composer',
			'install',
			'--no-interaction',
		],
		{ cwd: ROOT, stdio: 'inherit' }
	);
	if ((install.status ?? 1) !== 0) {
		console.error('✗ composer install failed.');
		lc.exit(install.status ?? 1);
		process.exit(install.status ?? 1);
	}
}

lc.on('run', () => {
	ensureComposerDeps();

	for (const cmd of commands) {
		const result = spawnSync(
			'npx',
			[
				'wp-env',
				'run',
				'tests-wordpress',
				'--env-cwd=wp-content/plugins/airo-wp',
				'--',
				...cmd,
			],
			{ cwd: ROOT, stdio: 'inherit' }
		);

		// PHPCBF exits 1 when it applies fixes (not an error), 2 when unfixable errors remain.
		// Normalise 1 → 0 for `composer format` so npm doesn't report a false failure.
		const isComposerFormat = cmd[0] === 'composer' && cmd[1] === 'format';
		const exitCode =
			isComposerFormat && result.status === 1 ? 0 : (result.status ?? 1);
		if (exitCode !== 0) {
			lc.exit(exitCode);
			return;
		}
	}
	lc.exit(0);
});

lc.execute();
