/**
 * Slider Templates
 *
 * Starter layouts shown by SliderPlaceholder when the block is first inserted.
 * Each template seeds three slides so the slider has enough content to demo
 * navigation, autoplay, and transitions immediately after insertion.
 */

import { __ } from '@wordpress/i18n';

const slidePadding = {
	top: 'var:preset|spacing|70',
	bottom: 'var:preset|spacing|70',
	left: 'var:preset|spacing|30',
	right: 'var:preset|spacing|30',
};

function basicSlide(heading, body) {
	return [
		'airo-wp/slide',
		{
			style: { spacing: { padding: slidePadding } },
		},
		[
			[
				'core/heading',
				{ level: 2, content: heading, textAlign: 'center' },
			],
			['core/paragraph', { content: body, align: 'center' }],
		],
	];
}

const sliderTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Three empty slides to fill in', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			[
				'airo-wp/slide',
				{ style: { spacing: { padding: slidePadding } } },
			],
			[
				'airo-wp/slide',
				{ style: { spacing: { padding: slidePadding } } },
			],
			[
				'airo-wp/slide',
				{ style: { spacing: { padding: slidePadding } } },
			],
		],
	},
	{
		name: 'hero',
		title: __('Hero', 'airo-wp'),
		description: __(
			'Single full-bleed slides with bold headlines',
			'airo-wp'
		),
		icon: 'cover-image',
		attributes: {
			slidesPerView: 1,
			slidesPerViewTablet: 1,
			slidesPerViewMobile: 1,
			effect: 'fade',
			showArrows: true,
			showDots: true,
			autoplay: true,
			autoplayInterval: 6000,
			loop: true,
		},
		innerBlocks: [
			basicSlide(
				__('Build at the speed of thought', 'airo-wp'),
				__(
					'Compose pages in minutes with a library designed for site builders.',
					'airo-wp'
				)
			),
			basicSlide(
				__('Designed for performance', 'airo-wp'),
				__(
					'Lean markup, optimized assets, and zero jank — out of the box.',
					'airo-wp'
				)
			),
			basicSlide(
				__('Ready when you are', 'airo-wp'),
				__(
					'Drop in your content, customize the look, and ship.',
					'airo-wp'
				)
			),
		],
	},
	{
		name: 'testimonial',
		title: __('Testimonial', 'airo-wp'),
		description: __(
			'Three quote-style slides for social proof',
			'airo-wp'
		),
		icon: 'format-quote',
		attributes: {
			slidesPerView: 1,
			slidesPerViewTablet: 1,
			slidesPerViewMobile: 1,
			effect: 'slide',
			showArrows: true,
			showDots: true,
			autoplay: true,
			autoplayInterval: 7000,
			loop: true,
		},
		innerBlocks: [
			basicSlide(
				__('"This saved us weeks of design work."', 'airo-wp'),
				__('— Jamie L., Product Lead', 'airo-wp')
			),
			basicSlide(
				__(
					'"The block library is exactly what our team needed."',
					'airo-wp'
				),
				__('— Priya R., Marketing Director', 'airo-wp')
			),
			basicSlide(
				__(
					'"Setup took ten minutes. Pages went live the same day."',
					'airo-wp'
				),
				__('— Marcus B., Agency Owner', 'airo-wp')
			),
		],
	},
	{
		name: 'gallery',
		title: __('Gallery', 'airo-wp'),
		description: __(
			'Three-up grid for browsing images or cards',
			'airo-wp'
		),
		icon: 'images-alt2',
		attributes: {
			slidesPerView: 3,
			slidesPerViewTablet: 2,
			slidesPerViewMobile: 1,
			effect: 'slide',
			showArrows: true,
			showDots: false,
			gap: '16px',
		},
		innerBlocks: [
			[
				'airo-wp/slide',
				{ style: { spacing: { padding: slidePadding } } },
				[['core/image', { sizeSlug: 'large' }]],
			],
			[
				'airo-wp/slide',
				{ style: { spacing: { padding: slidePadding } } },
				[['core/image', { sizeSlug: 'large' }]],
			],
			[
				'airo-wp/slide',
				{ style: { spacing: { padding: slidePadding } } },
				[['core/image', { sizeSlug: 'large' }]],
			],
		],
	},
];

export default sliderTemplates;
