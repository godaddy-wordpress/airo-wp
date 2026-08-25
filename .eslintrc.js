module.exports = {
	extends: ['plugin:@wordpress/eslint-plugin/recommended'],
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
	overrides: [
		{
			files: ['tests/**/*.js', '**/*.test.js', '**/*.spec.js'],
			env: {
				jest: true,
			},
			rules: {
				'no-unused-vars': [
					'error',
					{
						varsIgnorePattern: '^_',
						argsIgnorePattern: '^_',
						caughtErrorsIgnorePattern: '^_',
					},
				],
				'jsx-a11y/label-has-associated-control': 'off',
			},
		},
	],
};
