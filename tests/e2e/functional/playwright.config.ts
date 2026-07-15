import { defineConfig } from '@playwright/test';
import * as path from 'node:path';

const ROOT = path.resolve( __dirname, '../../..' );
const PORT = Number( process.env.WP_E2E_PORT ) || 8881;
const BASE_URL = `http://localhost:${ PORT }`;

// RequestUtils reads WP_BASE_URL (defaults to localhost:8889). Override it.
process.env.WP_BASE_URL = BASE_URL;

// Written by global-setup.ts. Browser contexts (use.storageState) and the
// per-worker requestUtils fixture both read from here.
const STORAGE_STATE_PATH = path.join( ROOT, 'artifacts/storage-states/admin.json' );
process.env.STORAGE_STATE_PATH = STORAGE_STATE_PATH;

export default defineConfig( {
	testDir: '.',
	// Excluded: browser-based suites that open a Gutenberg editor session per
	// block/pattern (60+ blocks, 100+ patterns). They are too slow for routine
	// e2e runs and clutter results. Run them manually with --grep or by removing
	// this exclusion after a bulk DSG sync or a Gutenberg/WP upgrade.
	testIgnore: [
		'**/specs/blocks-browser.spec.ts',
		'**/specs/patterns-browser.spec.ts',
	],
	fullyParallel: false,
	forbidOnly: !! process.env.CI,
	timeout: process.env.CI ? 60_000 : 30_000,
	retries: process.env.CI ? 2 : 0,
	workers: 1,
	reporter: 'list',
	outputDir: path.join( ROOT, 'test-results' ),
	use: {
		baseURL: BASE_URL,
		storageState: STORAGE_STATE_PATH,
		trace: 'retain-on-failure',
		screenshot: 'only-on-failure',
		video: 'on',
		// Chromium refuses its sandbox as root. The e2e Docker image sets
		// AIRO_WP_CHROMIUM_NO_SANDBOX=1; host runs and CI's non-root runner stay sandboxed.
		launchOptions: process.env.AIRO_WP_CHROMIUM_NO_SANDBOX
			? { args: [ '--no-sandbox' ] }
			: {},
	},
	projects: [
		{
			name: 'setup',
			testMatch: 'setup/**/*.setup.ts',
			retries: 0,
		},
		{
			name: 'default',
			testMatch: 'specs/**/*.spec.ts',
			dependencies: [ 'setup' ],
		},
	],
	globalSetup: './setup/global-setup.ts',
	webServer: {
		// Native PHP + real WordPress + SQLite drop-in. Installation completes
		// BEFORE the server binds the port, so the URL readiness poll is truthful.
		command: 'sh tests/e2e/functional/environment/serve-wp.sh',
		cwd: ROOT,
		url: BASE_URL,
		reuseExistingServer: ! process.env.CI || !! process.env.WP_ENV,
		timeout: 120_000,
	},
} );
