/**
 * ESLint flat config.
 *
 * @wordpress/scripts (35 and later) pulls ESLint 10, which no longer reads `.eslintrc.*`
 * or `.eslintignore`. This flat config replaces the former `.eslintrc.js` and
 * `.eslintignore` so the DSG source in `src/` — which airo-wp cannot modify —
 * keeps linting under the same rule relaxations it did before. It uses
 * @wordpress/eslint-plugin's native flat preset (its `.eslintrc` wrapper is
 * deprecated and cannot be loaded under ESLint 10 via FlatCompat).
 */
const wordpress = require( '@wordpress/eslint-plugin' );
const globals = require( 'globals' );

module.exports = [
	{
		ignores: [
			'dist/',
			'builds/',
			'dependencies/',
			'node_modules/',
			'.wp-env/',
			'vendor/',
			'playwright-report/',
			'test-results/',
			// tests/ holds Node lane-runner scripts (.mjs) + Playwright specs,
			// not block source. lint:js is scoped to src/, so tests/ is never a
			// release-gate target; ignoring it stops `lint-js --fix` from
			// over-scanning and rewriting these files.
			'tests/',
			// Root build/tooling config — airo-wp-owned, not DSG block source.
			// `lint-js --fix src/` otherwise silently reformats it on every
			// dsg-sync.
			'webpack.config.js',
			'eslint.config.js',
		],
	},
	...wordpress.configs.recommended,
	{
		rules: {
			'import/no-extraneous-dependencies': 'off',
			'import/no-unresolved': 'off',
			'jsdoc/require-param-description': 'off',
			// src/ is 100% DSG-owned and read-only (see CLAUDE.md): airo-wp never
			// authors JS here, so lint must accept DSG's own conventions rather than
			// error on source it cannot modify. The parent (DesignSetGo) lints this
			// same source clean on an older, unpinned @wordpress/eslint-plugin where
			// these rules were warnings; newer versions promote them to errors.
			'@wordpress/no-unsafe-wp-apis': 'off',
			'@wordpress/no-unused-vars-before-return': 'off',
			'jsdoc/require-param-type': 'off',
			'jsdoc/require-returns-description': 'off',
			'jsdoc/check-line-alignment': 'off',
			'no-nested-ternary': 'off',
			// ESLint 9 changed these defaults (`caughtErrors` now "all", `builtinGlobals`
			// now true). The DSG source is written against the old ones and is not
			// editable here, so restore them.
			'no-unused-vars': [
				'error',
				{ ignoreRestSiblings: true, caughtErrors: 'none' },
			],
			'no-redeclare': [ 'error', { builtinGlobals: false } ],
			'jsdoc/no-undefined-types': [
				'error',
				{
					definedTypes: [
						'JSX',
						'Element',
						'HTMLElement',
						'HTMLImageElement',
						'IntersectionObserver',
						'NodeList',
						'KeyboardEvent',
						'Document',
					],
				},
			],
		},
	},
	{
		files: [ 'tests/**/*.js', '**/*.test.js', '**/*.spec.js' ],
		languageOptions: {
			globals: {
				...globals.jest,
			},
		},
		rules: {
			// Must restate the main block's ESLint-9-default restorations: a later block's options replace, not merge.
			'no-unused-vars': [
				'error',
				{
					varsIgnorePattern: '^_',
					argsIgnorePattern: '^_',
					ignoreRestSiblings: true,
					caughtErrors: 'none',
				},
			],
			'jsx-a11y/label-has-associated-control': 'off',
		},
	},
];
