/**
 * Sticky Sections Templates
 *
 * Pre-configured templates for the template chooser
 * shown when the block is first inserted.
 */

import { __ } from '@wordpress/i18n';

/**
 * Create a section template with a background color and inner content
 *
 * @param {Object} config           Section configuration
 * @param {string} config.bgColor   Background color hex
 * @param {string} config.textColor Text color hex
 * @param {string} config.heading   Heading text
 * @param {string} config.paragraph Body text
 * @param {string} config.minHeight Minimum height CSS value
 * @return {Array} Inner block template definition
 */
function sectionCard({
	bgColor,
	textColor = '#ffffff',
	heading = '',
	paragraph = '',
	minHeight = '',
}) {
	const sectionAttrs = {
		align: 'full',
		style: {
			color: { background: bgColor, text: textColor },
			...(minHeight && {
				dimensions: { minHeight },
			}),
			spacing: {
				padding: {
					top: 'var:preset|spacing|60',
					bottom: 'var:preset|spacing|60',
					left: 'var:preset|spacing|40',
					right: 'var:preset|spacing|40',
				},
			},
		},
	};

	const innerContent = [];

	if (heading) {
		innerContent.push(['core/heading', { level: 2, content: heading }]);
	} else {
		innerContent.push([
			'core/heading',
			{
				level: 2,
				placeholder: __('Section title…', 'airo-wp'),
			},
		]);
	}

	if (paragraph) {
		innerContent.push(['core/paragraph', { content: paragraph }]);
	} else {
		innerContent.push([
			'core/paragraph',
			{
				placeholder: __('Section content…', 'airo-wp'),
			},
		]);
	}

	return ['airo-wp/section', sectionAttrs, innerContent];
}

const stickySectionsTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Start with empty sections', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			sectionCard({ bgColor: '#1a1a2e' }),
			sectionCard({ bgColor: '#16213e' }),
			sectionCard({ bgColor: '#0f3460' }),
		],
	},
	{
		name: 'feature-cards',
		title: __('Feature Cards', 'airo-wp'),
		description: __(
			'Pre-filled sections showcasing features',
			'airo-wp'
		),
		icon: 'screenoptions',
		attributes: {
			align: 'full',
		},
		innerBlocks: [
			sectionCard({
				bgColor: '#0a0a1a',
				heading: __('Design that stands out', 'airo-wp'),
				paragraph: __(
					'Create stunning layouts with pixel-perfect precision. Every detail is crafted to deliver an exceptional visual experience.',
					'airo-wp'
				),
			}),
			sectionCard({
				bgColor: '#1a0a2e',
				heading: __('Built for performance', 'airo-wp'),
				paragraph: __(
					'Optimized for speed at every level. Adaptive loading and responsive assets keep your site performing at its best.',
					'airo-wp'
				),
			}),
			sectionCard({
				bgColor: '#0d1b2a',
				heading: __('Scale with confidence', 'airo-wp'),
				paragraph: __(
					'From launch-ready basics to enterprise-grade experiences. Grow your site without limits.',
					'airo-wp'
				),
			}),
		],
	},
	{
		name: 'fullscreen',
		title: __('Full Screen', 'airo-wp'),
		description: __(
			'Full-viewport sections for dramatic stacking',
			'airo-wp'
		),
		icon: 'desktop',
		attributes: {
			align: 'full',
		},
		innerBlocks: [
			sectionCard({
				bgColor: '#0a0a1a',
				minHeight: '100vh',
			}),
			sectionCard({
				bgColor: '#1a0a2e',
				minHeight: '100vh',
			}),
			sectionCard({
				bgColor: '#0d1b2a',
				minHeight: '100vh',
			}),
		],
	},
];

export default stickySectionsTemplates;
