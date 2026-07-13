import { __ } from '@wordpress/i18n';
import { DEFAULT_TEMPLATE } from './edit-template';
import variations from './variations';

const minimal = {
	name: 'minimal',
	title: __('Minimal', 'airo-wp'),
	description: __(
		'Bare skeleton: results, no-results, and pagination. Build from scratch.',
		'airo-wp'
	),
	icon: 'welcome-add-page',
	attributes: {},
	innerBlocks: DEFAULT_TEMPLATE,
};

const queryTemplates = [minimal, ...variations];

export default queryTemplates;
