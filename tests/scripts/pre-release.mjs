#!/usr/bin/env node
/**
 * Pre-release orchestrator — run all CI checks locally before pushing.
 *
 * Usage:
 *   npm run task:pre-release [-- --php <version>] [-- --wp <version>]
 *
 * Examples:
 *   npm run task:pre-release                        # PHP 8.3 × WP 7.0 (defaults)
 *   npm run task:pre-release -- --php 7.4 --wp 6.9
 *   npm run task:pre-release -- --php 8.4 --wp nightly
 *
 * Steps (mirrors .github/public/workflows/pre-release.yml):
 *   1. PHPCS lint          (wp-env, --php applies)
 *   2. JS lint (ESLint)    (no wp-env; requires src/ — see note below)
 *   3. SCSS lint           (no wp-env; requires src/)
 *   4. PHPUnit             (wp-env, --php applies)
 *   5. Build zip           (npm run build:zip → builds/airo-wp.zip)
 *   6. E2E tests           (wp-env, --php + --wp + built zip)
 *   7. Plugin Check        (dedicated wp-env instance, always PHP 8.3)
 *
 * Note: the public mirror ships src/ committed, so it is present after checkout.
 * In the private repo src/ is gitignored and must be synced before running this
 * script (steps 2–3 and the build require src/blocks/).
 */

import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.resolve(
	path.dirname(fileURLToPath(import.meta.url)),
	'../..'
);

// ── Argument parsing ──────────────────────────────────────────────────────────

const argv = process.argv.slice(2);
function flag(name, fallback) {
	const i = argv.indexOf(`--${name}`);
	return i !== -1 && argv[i + 1] ? argv[i + 1] : fallback;
}
const PHP = flag('php', '8.3');
const WP = flag('wp', '7.0');

const WP_CORE_URL =
	WP === 'nightly'
		? 'https://wordpress.org/nightly-builds/wordpress-latest.zip'
		: `https://wordpress.org/wordpress-${WP}.zip`;

// ── .wp-env.json patch / restore ─────────────────────────────────────────────

const WP_ENV_FILE = path.join(ROOT, '.wp-env.json');
const WP_ENV_ORIG = readFileSync(WP_ENV_FILE, 'utf8');

const restore = () => writeFileSync(WP_ENV_FILE, WP_ENV_ORIG);
process.on('exit', restore);
process.on('SIGINT', () => {
	restore();
	process.exit(130);
});
process.on('SIGTERM', () => {
	restore();
	process.exit(143);
});

function patchWpEnv({ useZip = false, setCore = false } = {}) {
	const cfg = JSON.parse(WP_ENV_ORIG);
	let changed = false;
	if (cfg.phpVersion !== PHP) {
		cfg.phpVersion = PHP;
		changed = true;
	}
	if (setCore) {
		cfg.core = WP_CORE_URL;
		changed = true;
	}
	if (useZip && cfg.plugins[0] !== './builds/airo-wp.zip') {
		cfg.plugins[0] = './builds/airo-wp.zip';
		changed = true;
	}
	if (changed) {
		writeFileSync(WP_ENV_FILE, JSON.stringify(cfg, null, 2) + '\n');
	}
}

// ── Run helpers ───────────────────────────────────────────────────────────────

function run(label, cmd, args) {
	console.log(`\n── ${label} ──`);
	const r = spawnSync(cmd, args, { cwd: ROOT, stdio: 'inherit' });
	if ((r.status ?? 1) !== 0) {
		console.error(`\n✗  ${label} failed (exit ${r.status})`);
		process.exit(r.status ?? 1);
	}
	console.log(`✓  ${label}`);
}

const npm = (label, script) => run(label, 'npm', ['run', script]);

// ── Pre-flight ────────────────────────────────────────────────────────────────

if (!existsSync(path.join(ROOT, 'src', 'blocks'))) {
	console.error(
		'Error: src/blocks/ not found.\nBlock sources must be present before running pre-release (the public mirror ships them committed; in the private repo, sync them first).'
	);
	process.exit(1);
}

// ── Run ───────────────────────────────────────────────────────────────────────

console.log(`\nPre-release  PHP ${PHP}  ×  WP ${WP}\n`);

run('npm ci', 'npm', ['ci']);

// Phase 1 — lint + unit against source (phpVersion only)
patchWpEnv();
npm('PHPCS lint', 'lint:php');
npm('JS lint', 'lint:js');
npm('SCSS lint', 'lint:style');
npm('PHPUnit', 'test:unit:php');

// Phase 2 — build distributable zip
restore();
npm('Build zip', 'build:zip');

// Phase 3 — e2e + plugin-check against built zip (phpVersion + WP core)
patchWpEnv({ useZip: true, setCore: true });
npm('E2E tests', 'test:e2e');

restore();
npm('Plugin Check', 'plugin-check');

console.log('\n✓  All pre-release checks passed\n');
