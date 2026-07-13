/**
 * Accordion Templates
 *
 * Starter layouts shown by AccordionPlaceholder when the block is first
 * inserted. Every template ends with at least one accordion-item so the user
 * never lands in a silent-empty state after picking a tile.
 */

import { __ } from '@wordpress/i18n';

const accordionTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Two empty items to fill in', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			[
				'airo-wp/accordion-item',
				{ title: __('Accordion Item 1', 'airo-wp') },
			],
			[
				'airo-wp/accordion-item',
				{ title: __('Accordion Item 2', 'airo-wp') },
			],
		],
	},
	{
		name: 'faq',
		title: __('FAQ', 'airo-wp'),
		description: __(
			'Frequently asked questions with short answers',
			'airo-wp'
		),
		icon: 'editor-help',
		attributes: {
			iconStyle: 'plus-minus',
			iconPosition: 'right',
		},
		innerBlocks: [
			[
				'airo-wp/accordion-item',
				{
					title: __('What is included with my plan?', 'airo-wp'),
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'Every plan includes the full block library, regular updates, and access to support. Upgrade at any time to unlock advanced layouts.',
								'airo-wp'
							),
						},
					],
				],
			],
			[
				'airo-wp/accordion-item',
				{
					title: __(
						'How do I cancel my subscription?',
						'airo-wp'
					),
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'You can cancel anytime from your account dashboard. Your access continues until the end of the billing period.',
								'airo-wp'
							),
						},
					],
				],
			],
			[
				'airo-wp/accordion-item',
				{
					title: __('Do you offer refunds?', 'airo-wp'),
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'We offer a 30-day money-back guarantee. Just reach out to support and we will process your refund.',
								'airo-wp'
							),
						},
					],
				],
			],
		],
	},
	{
		name: 'content',
		title: __('Content', 'airo-wp'),
		description: __(
			'Rich content sections with headings and media',
			'airo-wp'
		),
		icon: 'editor-alignleft',
		attributes: {
			iconStyle: 'chevron',
			iconPosition: 'right',
			borderBetween: false,
		},
		innerBlocks: [
			[
				'airo-wp/accordion-item',
				{
					title: __('Overview', 'airo-wp'),
					isOpen: true,
				},
				[
					[
						'core/heading',
						{
							level: 3,
							content: __('Section heading', 'airo-wp'),
						},
					],
					[
						'core/paragraph',
						{
							content: __(
								'Use this section to explain a concept in depth. Add any blocks you need — paragraphs, images, columns, or buttons all work inside an accordion item.',
								'airo-wp'
							),
						},
					],
				],
			],
			[
				'airo-wp/accordion-item',
				{
					title: __('Details', 'airo-wp'),
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'Drop in supporting details, examples, or step-by-step instructions.',
								'airo-wp'
							),
						},
					],
				],
			],
		],
	},
	{
		name: 'icon-list',
		title: __('Icon List', 'airo-wp'),
		description: __(
			'Compact list with icons and short blurbs',
			'airo-wp'
		),
		icon: 'list-view',
		attributes: {
			iconStyle: 'caret',
			iconPosition: 'left',
			borderBetween: false,
			itemGap: '0.25rem',
		},
		innerBlocks: [
			[
				'airo-wp/accordion-item',
				{
					title: __('Lightning fast', 'airo-wp'),
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'Optimized assets and minimal markup keep page loads quick.',
								'airo-wp'
							),
						},
					],
				],
			],
			[
				'airo-wp/accordion-item',
				{
					title: __('Accessible by default', 'airo-wp'),
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'ARIA roles and keyboard navigation are wired up automatically.',
								'airo-wp'
							),
						},
					],
				],
			],
			[
				'airo-wp/accordion-item',
				{
					title: __('Designed to extend', 'airo-wp'),
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'Style with theme.json or override individual items in the editor.',
								'airo-wp'
							),
						},
					],
				],
			],
		],
	},
];

export default accordionTemplates;
