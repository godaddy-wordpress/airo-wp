/**
 * Scroll Slides Templates
 *
 * Pre-configured templates for the template chooser
 * shown when the block is first inserted.
 */

import { __ } from '@wordpress/i18n';

/**
 * Create a blank slide template with placeholder content
 *
 * @return {Array} Inner block template definition
 */
function blankSlide() {
	return [
		'airo-wp/scroll-slide',
		{ navHeading: '' },
		[
			[
				'airo-wp/section',
				{},
				[
					['core/image'],
					[
						'core/heading',
						{
							level: 3,
							placeholder: __('Slide title…', 'airo-wp'),
						},
					],
					[
						'core/paragraph',
						{
							placeholder: __(
								'Slide description…',
								'airo-wp'
							),
						},
					],
				],
			],
		],
	];
}

/**
 * Create a showcase slide with pre-filled content
 *
 * @param {Object} config            Slide configuration
 * @param {string} config.navHeading Navigation heading text
 * @param {string} config.bgColor    Background color hex
 * @param {string} config.heading    Slide heading text
 * @param {string} config.paragraph  Slide body text
 * @return {Array} Inner block template definition
 */
function showcaseSlide({ navHeading, bgColor, heading, paragraph }) {
	return [
		'airo-wp/scroll-slide',
		{
			navHeading,
			style: {
				color: { background: bgColor, text: '#ffffff' },
			},
		},
		[
			[
				'airo-wp/section',
				{},
				[
					['core/image'],
					['core/heading', { level: 2, content: heading }],
					['core/paragraph', { content: paragraph }],
				],
			],
		],
	];
}

const scrollSlidesTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Start with empty slides', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [blankSlide(), blankSlide(), blankSlide()],
	},
	{
		name: 'showcase',
		title: __('Feature Showcase', 'airo-wp'),
		description: __(
			'Pre-filled slides highlighting product features',
			'airo-wp'
		),
		icon: 'slides',
		attributes: {
			align: 'full',
			overlayColor: '#000000',
			navColor: '#ffffffb3',
			navActiveColor: '#ffffff',
		},
		innerBlocks: [
			showcaseSlide({
				navHeading: __('Design', 'airo-wp'),
				bgColor: '#0a0a1a',
				heading: __('Beautiful by default', 'airo-wp'),
				paragraph: __(
					'Create stunning layouts with pixel-perfect precision. Every detail is crafted to deliver an exceptional visual experience.',
					'airo-wp'
				),
			}),
			showcaseSlide({
				navHeading: __('Performance', 'airo-wp'),
				bgColor: '#0d1b2a',
				heading: __('Lightning fast', 'airo-wp'),
				paragraph: __(
					'Optimized for speed at every level. Adaptive loading, responsive assets, and built-in enhancements keep your site performing at its best.',
					'airo-wp'
				),
			}),
			showcaseSlide({
				navHeading: __('Accessibility', 'airo-wp'),
				bgColor: '#1a0a2e',
				heading: __('Inclusive by design', 'airo-wp'),
				paragraph: __(
					'Reach every user with inclusive design powered by accessibility tools that identify issues and guide improvements.',
					'airo-wp'
				),
			}),
		],
	},
];

export default scrollSlidesTemplates;
