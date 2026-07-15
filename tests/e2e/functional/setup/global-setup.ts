import type { FullConfig } from '@playwright/test';
import { RequestUtils } from '@wordpress/e2e-test-utils-playwright';

// Logs in as admin (RequestUtils defaults: admin/password — serve-wp.sh installs
// WordPress with exactly those) and writes an authenticated storageState to
// STORAGE_STATE_PATH, where playwright.config.ts's use.storageState and the
// per-worker requestUtils fixture pick it up.
export default async function globalSetup( config: FullConfig ) {
	const { baseURL } = config.projects[ 0 ].use as { baseURL: string };

	const requestUtils = await RequestUtils.setup( {
		baseURL,
		storageStatePath: process.env.STORAGE_STATE_PATH,
	} );
	await requestUtils.setupRest();
}
