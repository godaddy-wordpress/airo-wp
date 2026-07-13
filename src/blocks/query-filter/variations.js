import { __ } from '@wordpress/i18n';

export default [
	{
		name: 'checkbox',
		title: __('Taxonomy (multi-select)', 'airo-wp'),
		icon: 'list-view',
		description: __(
			'Multi-select taxonomy filter — render as checkboxes, pills, or underlined tabs.',
			'airo-wp'
		),
		// New inserts opt into the modern underlined-tabs look. The block
		// default stays `default` (classic checkboxes) so legacy saved blocks
		// don't silently change appearance on upgrade.
		attributes: {
			filterKind: 'checkbox',
			paramName: 'filter_category',
			filterStyle: 'underline',
		},
		isDefault: true,
		scope: ['inserter', 'transform'],
	},
	{
		name: 'select',
		title: __('Taxonomy (dropdown)', 'airo-wp'),
		icon: 'menu',
		description: __('Single-select taxonomy dropdown.', 'airo-wp'),
		attributes: { filterKind: 'select', paramName: 'filter_category' },
		scope: ['inserter', 'transform'],
	},
	{
		name: 'search',
		title: __('Search input', 'airo-wp'),
		icon: 'search',
		description: __('Free-text search bound to ?q=.', 'airo-wp'),
		attributes: { filterKind: 'search', paramName: 'q' },
		scope: ['inserter', 'transform'],
	},
	{
		name: 'sort',
		title: __('Sort dropdown', 'airo-wp'),
		icon: 'sort',
		description: __('Sort bound to ?sort=.', 'airo-wp'),
		attributes: { filterKind: 'sort', paramName: 'sort' },
		scope: ['inserter', 'transform'],
	},
	{
		name: 'active',
		title: __('Active filters', 'airo-wp'),
		icon: 'tag',
		description: __(
			'Show removable chips for each active filter.',
			'airo-wp'
		),
		attributes: { filterKind: 'active', paramName: '' },
		scope: ['inserter', 'transform'],
	},
	{
		name: 'reset',
		title: __('Reset button', 'airo-wp'),
		icon: 'undo',
		description: __('Clear all filter params.', 'airo-wp'),
		attributes: {
			filterKind: 'reset',
			paramName: '',
			label: __('Reset', 'airo-wp'),
		},
		scope: ['inserter', 'transform'],
	},
];
