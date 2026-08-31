/**
 * Image Accordion Templates
 *
 * Starter layouts shown by ImageAccordionPlaceholder when the block is first
 * inserted. The image-accordion's frontend behavior is driven by per-item
 * background images, so the template seeds three items with placeholder
 * heading + paragraph content the author can replace.
 */

import { __ } from '@wordpress/i18n';

function imageItem(heading, body) {
	return [
		'airo-wp/image-accordion-item',
		{},
		[
			['core/heading', { level: 3, content: heading }],
			['core/paragraph', { content: body, align: 'center' }],
		],
	];
}

const imageAccordionTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Three empty panels to fill in', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			['airo-wp/image-accordion-item', {}],
			['airo-wp/image-accordion-item', {}],
			['airo-wp/image-accordion-item', {}],
		],
	},
	{
		name: 'showcase',
		title: __('Showcase', 'airo-wp'),
		description: __('Three feature panels with hover-to-expand', 'airo-wp'),
		icon: 'images-alt2',
		attributes: {
			triggerType: 'hover',
			enableOverlay: true,
			overlayOpacity: 50,
			overlayOpacityExpanded: 20,
		},
		innerBlocks: [
			imageItem(
				__('Design', 'airo-wp'),
				__(
					'Craft beautiful interfaces with carefully chosen typography and color.',
					'airo-wp'
				)
			),
			imageItem(
				__('Build', 'airo-wp'),
				__(
					'Compose layouts from accessible, reusable building blocks.',
					'airo-wp'
				)
			),
			imageItem(
				__('Ship', 'airo-wp'),
				__(
					'Publish polished pages without writing a line of code.',
					'airo-wp'
				)
			),
		],
	},
	{
		name: 'gallery',
		title: __('Gallery', 'airo-wp'),
		description: __(
			'Click-to-expand gallery for image-led storytelling',
			'airo-wp'
		),
		icon: 'format-gallery',
		attributes: {
			triggerType: 'click',
			expandedRatio: 4,
			enableOverlay: true,
			overlayOpacity: 30,
			overlayOpacityExpanded: 0,
		},
		innerBlocks: [
			imageItem(
				__('Series One', 'airo-wp'),
				__('Add a caption for the first image.', 'airo-wp')
			),
			imageItem(
				__('Series Two', 'airo-wp'),
				__('Add a caption for the second image.', 'airo-wp')
			),
			imageItem(
				__('Series Three', 'airo-wp'),
				__('Add a caption for the third image.', 'airo-wp')
			),
			imageItem(
				__('Series Four', 'airo-wp'),
				__('Add a caption for the fourth image.', 'airo-wp')
			),
		],
	},
];

export default imageAccordionTemplates;
