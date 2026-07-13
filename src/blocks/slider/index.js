/**
 * Slider Block Registration
 */

import { registerBlockType, registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import Edit from './edit';
import Save from './save';
import deprecated from './deprecated';
import { ICON_COLOR } from '../shared/constants';
import './style.scss';
import './editor.scss';

registerBlockType(metadata.name, {
	...metadata,
	deprecated,
	icon: {
		src: (
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
				<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-7-8l-4 5h12l-3-4-2.03 2.71L12 11z" />
				<circle cx="8.5" cy="8.5" r="1.5" />
			</svg>
		),
		foreground: ICON_COLOR,
	},
	edit: Edit,
	save: Save,
});

// Block Variations
registerBlockVariation(metadata.name, {
	name: 'hero-slider',
	title: __('Hero Slider', 'airo-wp'),
	description: __(
		'Full-height slider for hero sections with centered content',
		'airo-wp'
	),
	icon: { src: 'cover-image', foreground: ICON_COLOR },
	attributes: {
		height: '100vh',
		styleVariation: 'fullbleed',
		effect: 'fade',
		transitionDuration: '1s',
		autoplay: true,
		autoplayInterval: 5000,
		showDots: true,
		showArrows: true,
	},
	innerBlocks: [
		[
			'airo-wp/slide',
			{},
			[
				[
					'core/heading',
					{
						level: 1,
						content: __('Welcome to Your Site', 'airo-wp'),
						textAlign: 'center',
						style: { typography: { fontSize: '3.5rem' } },
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'Create something amazing with beautiful, full-screen hero sliders',
							'airo-wp'
						),
						align: 'center',
						style: { typography: { fontSize: '1.25rem' } },
					},
				],
				[
					'core/buttons',
					{ layout: { type: 'flex', justifyContent: 'center' } },
					[
						[
							'core/button',
							{
								text: __('Get Started', 'airo-wp'),
								style: { color: { background: '#2563eb' } },
							},
						],
					],
				],
			],
		],
		[
			'airo-wp/slide',
			{},
			[
				[
					'core/heading',
					{
						level: 1,
						content: __('Powerful Features', 'airo-wp'),
						textAlign: 'center',
						style: { typography: { fontSize: '3.5rem' } },
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'Everything you need to build stunning websites',
							'airo-wp'
						),
						align: 'center',
						style: { typography: { fontSize: '1.25rem' } },
					},
				],
				[
					'core/buttons',
					{ layout: { type: 'flex', justifyContent: 'center' } },
					[
						[
							'core/button',
							{
								text: __('Learn More', 'airo-wp'),
								style: { color: { background: '#ea580c' } },
							},
						],
					],
				],
			],
		],
	],
	scope: ['inserter'],
	isActive: (blockAttributes) =>
		blockAttributes.styleVariation === 'fullbleed',
});

registerBlockVariation(metadata.name, {
	name: 'gallery-carousel',
	title: __('Gallery Carousel', 'airo-wp'),
	description: __('Show multiple images in a carousel view', 'airo-wp'),
	icon: { src: 'images-alt2', foreground: ICON_COLOR },
	attributes: {
		slidesPerView: 3,
		slidesPerViewTablet: 2,
		slidesPerViewMobile: 1,
		gap: '16px',
		height: '400px',
		styleVariation: 'card',
		effect: 'slide',
		autoplay: false,
		loop: true,
		showDots: false,
		showArrows: true,
	},
	innerBlocks: [
		[
			'airo-wp/slide',
			{},
			[
				[
					'core/heading',
					{
						level: 3,
						content: __('Image 1', 'airo-wp'),
						textAlign: 'center',
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{},
			[
				[
					'core/heading',
					{
						level: 3,
						content: __('Image 2', 'airo-wp'),
						textAlign: 'center',
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{},
			[
				[
					'core/heading',
					{
						level: 3,
						content: __('Image 3', 'airo-wp'),
						textAlign: 'center',
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{},
			[
				[
					'core/heading',
					{
						level: 3,
						content: __('Image 4', 'airo-wp'),
						textAlign: 'center',
					},
				],
			],
		],
	],
	scope: ['inserter'],
});

registerBlockVariation(metadata.name, {
	name: 'testimonial-slider',
	title: __('Testimonial Slider', 'airo-wp'),
	description: __(
		'Slider optimized for testimonials with fade transitions',
		'airo-wp'
	),
	icon: { src: 'format-quote', foreground: ICON_COLOR },
	attributes: {
		height: '350px',
		styleVariation: 'classic',
		effect: 'fade',
		transitionDuration: '0.8s',
		autoplay: true,
		autoplayInterval: 6000,
		centeredSlides: true,
		showDots: true,
		showArrows: false,
	},
	innerBlocks: [
		[
			'airo-wp/slide',
			{ contentVerticalAlign: 'center' },
			[
				[
					'core/paragraph',
					{
						content: __('★★★★★', 'airo-wp'),
						align: 'center',
						style: {
							typography: { fontSize: '1.5rem' },
							color: { text: '#fbbf24' },
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'"This product exceeded all my expectations. Highly recommended!"',
							'airo-wp'
						),
						align: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontStyle: 'italic',
							},
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __('— Sarah Johnson', 'airo-wp'),
						align: 'center',
						style: {
							typography: { fontSize: '1rem', fontWeight: '600' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ contentVerticalAlign: 'center' },
			[
				[
					'core/paragraph',
					{
						content: __('★★★★★', 'airo-wp'),
						align: 'center',
						style: {
							typography: { fontSize: '1.5rem' },
							color: { text: '#fbbf24' },
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'"Outstanding quality and amazing customer service. Will buy again!"',
							'airo-wp'
						),
						align: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontStyle: 'italic',
							},
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __('— Michael Chen', 'airo-wp'),
						align: 'center',
						style: {
							typography: { fontSize: '1rem', fontWeight: '600' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ contentVerticalAlign: 'center' },
			[
				[
					'core/paragraph',
					{
						content: __('★★★★★', 'airo-wp'),
						align: 'center',
						style: {
							typography: { fontSize: '1.5rem' },
							color: { text: '#fbbf24' },
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'"Best purchase I\'ve made this year. Absolutely love it!"',
							'airo-wp'
						),
						align: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontStyle: 'italic',
							},
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __('— Emily Rodriguez', 'airo-wp'),
						align: 'center',
						style: {
							typography: { fontSize: '1rem', fontWeight: '600' },
						},
					},
				],
			],
		],
	],
	scope: ['inserter'],
});

registerBlockVariation(metadata.name, {
	name: 'logo-slider',
	title: __('Logo Slider', 'airo-wp'),
	description: __('Continuous scrolling logo slider', 'airo-wp'),
	icon: { src: 'grid-view', foreground: ICON_COLOR },
	attributes: {
		slidesPerView: 4,
		slidesPerViewTablet: 3,
		slidesPerViewMobile: 2,
		gap: '32px',
		height: '120px',
		styleVariation: 'minimal',
		effect: 'slide',
		transitionDuration: '0.4s',
		autoplay: true,
		autoplayInterval: 2000,
		loop: true,
		showDots: false,
		showArrows: false,
	},
	innerBlocks: [
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 1', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 2', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 3', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 4', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 5', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 6', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 7', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ enableOverlay: false, contentVerticalAlign: 'center' },
			[
				[
					'core/heading',
					{
						level: 4,
						content: __('Brand 8', 'airo-wp'),
						textAlign: 'center',
						style: {
							typography: {
								fontSize: '1.25rem',
								fontWeight: '700',
							},
							color: { text: '#1f2937' },
						},
					},
				],
			],
		],
	],
	scope: ['inserter'],
});

registerBlockVariation(metadata.name, {
	name: 'scroll-carousel',
	title: __('Scroll Carousel', 'airo-wp'),
	description: __(
		'Horizontal carousel that advances as the user scrolls down the page',
		'airo-wp'
	),
	icon: { src: 'slides', foreground: ICON_COLOR },
	attributes: {
		slidesPerView: 2.5,
		slidesPerViewTablet: 1.5,
		slidesPerViewMobile: 1,
		gap: '24px',
		styleVariation: 'card',
		effect: 'slide',
		scrollDriven: true,
		scrollDrivenSpeed: 1,
		showArrows: false,
		showDots: false,
		autoplay: false,
		loop: false,
	},
	innerBlocks: [
		[
			'airo-wp/slide',
			{ contentVerticalAlign: 'top', contentHorizontalAlign: 'left' },
			[
				[
					'core/image',
					{
						url: '',
						alt: __('Pixel-perfect editing', 'airo-wp'),
						style: { border: { radius: '12px' } },
					},
				],
				[
					'core/heading',
					{
						level: 3,
						content: __('Pixel-perfect editing', 'airo-wp'),
						style: {
							typography: {
								fontSize: '1.5rem',
								fontWeight: '700',
							},
							spacing: {
								margin: {
									top: '16px',
									bottom: '8px',
								},
							},
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'Design with total precision using the drag-and-drop Editor, global styles, CSS transforms, masks, motion effects, and more.',
							'airo-wp'
						),
						style: {
							color: { text: '#6b7280' },
							typography: { fontSize: '1rem' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ contentVerticalAlign: 'top', contentHorizontalAlign: 'left' },
			[
				[
					'core/image',
					{
						url: '',
						alt: __('Engage and capture', 'airo-wp'),
						style: { border: { radius: '12px' } },
					},
				],
				[
					'core/heading',
					{
						level: 3,
						content: __('Engage and capture', 'airo-wp'),
						style: {
							typography: {
								fontSize: '1.5rem',
								fontWeight: '700',
							},
							spacing: {
								margin: {
									top: '16px',
									bottom: '8px',
								},
							},
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'Convert visitors into customers with high-performance forms, popups, and lead-capture tools.',
							'airo-wp'
						),
						style: {
							color: { text: '#6b7280' },
							typography: { fontSize: '1rem' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ contentVerticalAlign: 'top', contentHorizontalAlign: 'left' },
			[
				[
					'core/image',
					{
						url: '',
						alt: __('Dynamic content', 'airo-wp'),
						style: { border: { radius: '12px' } },
					},
				],
				[
					'core/heading',
					{
						level: 3,
						content: __('Dynamic content', 'airo-wp'),
						style: {
							typography: {
								fontSize: '1.5rem',
								fontWeight: '700',
							},
							spacing: {
								margin: {
									top: '16px',
									bottom: '8px',
								},
							},
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'Scale data-driven pages, personalize experiences, and connect to any data source effortlessly.',
							'airo-wp'
						),
						style: {
							color: { text: '#6b7280' },
							typography: { fontSize: '1rem' },
						},
					},
				],
			],
		],
		[
			'airo-wp/slide',
			{ contentVerticalAlign: 'top', contentHorizontalAlign: 'left' },
			[
				[
					'core/image',
					{
						url: '',
						alt: __('Theme builder', 'airo-wp'),
						style: { border: { radius: '12px' } },
					},
				],
				[
					'core/heading',
					{
						level: 3,
						content: __('Theme builder', 'airo-wp'),
						style: {
							typography: {
								fontSize: '1.5rem',
								fontWeight: '700',
							},
							spacing: {
								margin: {
									top: '16px',
									bottom: '8px',
								},
							},
						},
					},
				],
				[
					'core/paragraph',
					{
						content: __(
							'Design every part of your site from headers and footers to archive pages and single posts.',
							'airo-wp'
						),
						style: {
							color: { text: '#6b7280' },
							typography: { fontSize: '1rem' },
						},
					},
				],
			],
		],
	],
	scope: ['inserter'],
	isActive: (blockAttributes) => blockAttributes.scrollDriven === true,
});
