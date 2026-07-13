/**
 * Scroll Accordion Templates
 *
 * Starter layouts shown by ScrollAccordionPlaceholder when the block is first
 * inserted. Each template seeds three sticky-stacking cards so authors can
 * scroll-test the effect immediately.
 */

import { __ } from '@wordpress/i18n';

const cardPadding = {
	top: 'var:preset|spacing|60',
	right: 'var:preset|spacing|60',
	bottom: 'var:preset|spacing|60',
	left: 'var:preset|spacing|60',
};

function card({ background, text = '#ffffff', heading, body, accentText }) {
	return [
		'airo-wp/scroll-accordion-item',
		{
			// Shadow lives under `style.shadow` because the block exposes
			// shadow via `supports.shadow: true` rather than declaring a
			// top-level `shadow` attribute. A top-level `shadow:` here
			// would be silently dropped on save.
			style: {
				spacing: { padding: cardPadding },
				color: { background, text },
				border: { radius: '16px' },
				shadow: '0 10px 40px rgba(0, 0, 0, 0.1)',
			},
		},
		[
			[
				'core/heading',
				{
					level: 2,
					content: heading,
					style: {
						typography: {
							fontSize: '2.5rem',
							fontWeight: '700',
						},
					},
				},
			],
			[
				'core/paragraph',
				{
					content: body,
					style: {
						typography: { fontSize: '1.125rem' },
						color: { text: accentText || '#cbd5e1' },
					},
				},
			],
		],
	];
}

const scrollAccordionTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Three empty cards to fill in', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			[
				'airo-wp/scroll-accordion-item',
				{
					style: {
						spacing: { padding: cardPadding },
						border: { radius: '16px' },
					},
				},
			],
			[
				'airo-wp/scroll-accordion-item',
				{
					style: {
						spacing: { padding: cardPadding },
						border: { radius: '16px' },
					},
				},
			],
			[
				'airo-wp/scroll-accordion-item',
				{
					style: {
						spacing: { padding: cardPadding },
						border: { radius: '16px' },
					},
				},
			],
		],
	},
	{
		name: 'product',
		title: __('Product', 'airo-wp'),
		description: __(
			'Showcase product capabilities as readers scroll',
			'airo-wp'
		),
		icon: 'screenoptions',
		attributes: { alignItems: 'flex-start' },
		innerBlocks: [
			card({
				background: '#1e293b',
				heading: __('Design Systems', 'airo-wp'),
				body: __(
					'Build consistent, scalable interfaces with reusable components and design tokens.',
					'airo-wp'
				),
			}),
			card({
				background: '#0f172a',
				heading: __('Component Library', 'airo-wp'),
				body: __(
					'Pre-built, accessible components that work seamlessly together for rapid development.',
					'airo-wp'
				),
			}),
			card({
				background: '#7c3aed',
				heading: __('Launch & Scale', 'airo-wp'),
				body: __(
					'Deploy with confidence and scale effortlessly with performance-optimized architecture.',
					'airo-wp'
				),
				accentText: '#ede9fe',
			}),
		],
	},
	{
		name: 'process',
		title: __('Process', 'airo-wp'),
		description: __(
			'Walk through a multi-step process or timeline',
			'airo-wp'
		),
		icon: 'list-view',
		attributes: { alignItems: 'flex-start' },
		innerBlocks: [
			card({
				background: '#0f3460',
				heading: __('1. Discover', 'airo-wp'),
				body: __(
					'Understand the problem space, the users, and the constraints.',
					'airo-wp'
				),
			}),
			card({
				background: '#16213e',
				heading: __('2. Design', 'airo-wp'),
				body: __(
					'Sketch, prototype, and validate solutions with stakeholders.',
					'airo-wp'
				),
			}),
			card({
				background: '#1a1a2e',
				heading: __('3. Deliver', 'airo-wp'),
				body: __(
					'Ship the work, measure impact, and iterate on what we learn.',
					'airo-wp'
				),
			}),
		],
	},
];

export default scrollAccordionTemplates;
