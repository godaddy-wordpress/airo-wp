/**
 * Tabs Templates
 *
 * Starter layouts shown by TabsPlaceholder when the block is first inserted.
 * Each template seeds three tabs so the user has a real starting point rather
 * than an empty tablist after picking a tile.
 */

import { __ } from '@wordpress/i18n';

function tabPanel(title, body) {
	return [
		'airo-wp/tab',
		{ title },
		[
			['core/heading', { level: 3, content: title }],
			['core/paragraph', { content: body }],
		],
	];
}

const tabsTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Three empty tabs to fill in', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			['airo-wp/tab', { title: __('Tab 1', 'airo-wp') }],
			['airo-wp/tab', { title: __('Tab 2', 'airo-wp') }],
			['airo-wp/tab', { title: __('Tab 3', 'airo-wp') }],
		],
	},
	{
		name: 'horizontal',
		title: __('Horizontal', 'airo-wp'),
		description: __('Classic top-aligned tabs', 'airo-wp'),
		icon: 'editor-table',
		attributes: {
			orientation: 'horizontal',
			tabStyle: 'default',
			alignment: 'left',
			showNavBorder: true,
		},
		innerBlocks: [
			tabPanel(
				__('Overview', 'airo-wp'),
				__(
					'Introduce the topic of this tab. Use any blocks you need for the body.',
					'airo-wp'
				)
			),
			tabPanel(
				__('Features', 'airo-wp'),
				__(
					'Highlight the key features or differentiators in this section.',
					'airo-wp'
				)
			),
			tabPanel(
				__('Pricing', 'airo-wp'),
				__(
					'Outline pricing tiers, what they include, and how to upgrade.',
					'airo-wp'
				)
			),
		],
	},
	{
		name: 'vertical',
		title: __('Vertical', 'airo-wp'),
		description: __(
			'Side-aligned tabs for longer-form content',
			'airo-wp'
		),
		icon: 'align-pull-left',
		attributes: {
			orientation: 'vertical',
			tabStyle: 'default',
			alignment: 'left',
		},
		innerBlocks: [
			tabPanel(
				__('Getting started', 'airo-wp'),
				__(
					'Walk readers through the first thing they need to do.',
					'airo-wp'
				)
			),
			tabPanel(
				__('Configuration', 'airo-wp'),
				__(
					'Document the settings or options that matter most.',
					'airo-wp'
				)
			),
			tabPanel(
				__('FAQ', 'airo-wp'),
				__(
					'Answer the questions readers ask most often.',
					'airo-wp'
				)
			),
		],
	},
	{
		name: 'pill',
		title: __('Pill', 'airo-wp'),
		description: __(
			'Rounded pill-style tabs with subtle hover',
			'airo-wp'
		),
		icon: 'marker',
		attributes: {
			orientation: 'horizontal',
			tabStyle: 'pills',
			alignment: 'center',
			gap: '8px',
		},
		innerBlocks: [
			tabPanel(
				__('Design', 'airo-wp'),
				__(
					'Highlight the design philosophy and visual decisions.',
					'airo-wp'
				)
			),
			tabPanel(
				__('Build', 'airo-wp'),
				__(
					'Explain the build process and the tooling involved.',
					'airo-wp'
				)
			),
			tabPanel(
				__('Ship', 'airo-wp'),
				__(
					'Cover deployment, monitoring, and ongoing iteration.',
					'airo-wp'
				)
			),
		],
	},
];

export default tabsTemplates;
