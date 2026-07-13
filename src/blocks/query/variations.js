import { __ } from '@wordpress/i18n';

/**
 * Dynamic Query inserter variations.
 *
 * Post-restructure (v2.6) the outer airo-wp/query block is a pure
 * container; presentation attrs (columns, tagName, groupBy…) and the item
 * template live on the required airo-wp/query-results child.
 *
 * Each variation carries a `layoutVariant` on the query-results child so the
 * scoped SCSS in query-results/style.scss can style each layout distinctly.
 * Pair the layoutVariant with a per-variation template that plays to that
 * layout's strengths (quote-card omits the image, compact-row uses a square
 * thumb, etc.) so the inserter previews read as visually different at a
 * glance rather than "same grid, different columns".
 */
export default [
	{
		name: 'blog-index',
		title: __('Blog index', 'airo-wp'),
		description: __(
			'Magazine-style cards with featured image, date, and excerpt. Search + sort + numbered pagination.',
			'airo-wp'
		),
		icon: 'admin-post',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 9,
			orderBy: 'date',
			order: 'DESC',
		},
		innerBlocks: [
			[
				'airo-wp/query-filter',
				{
					filterKind: 'search',
					paramName: 'q',
					label: __('Search posts', 'airo-wp'),
					placeholder: __('Search…', 'airo-wp'),
				},
			],
			[
				'airo-wp/query-filter',
				{
					filterKind: 'sort',
					paramName: 'sort',
					label: __('Sort by', 'airo-wp'),
				},
			],
			[
				'airo-wp/query-results',
				{
					tagName: 'ul',
					itemTagName: 'li',
					columns: 3,
					layoutVariant: 'magazine',
				},
				[
					[
						'airo-wp/section',
						{},
						[
							[
								'core/post-featured-image',
								{ isLink: true, aspectRatio: '3/2' },
							],
							['core/post-date'],
							['core/post-title', { level: 3, isLink: true }],
							['core/post-excerpt', { excerptLength: 25 }],
						],
					],
				],
			],
			['airo-wp/query-no-results'],
			['airo-wp/query-pagination', { paginationKind: 'numbered' }],
		],
	},
	{
		name: 'team',
		title: __('Team directory', 'airo-wp'),
		description: __(
			'Circular avatars in a centered grid. Switch Post type to your `team` CPT in the inspector.',
			'airo-wp'
		),
		icon: 'groups',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 12,
			orderBy: 'menu_order',
			order: 'ASC',
		},
		innerBlocks: [
			[
				'airo-wp/query-filter',
				{
					filterKind: 'search',
					paramName: 'q',
					label: __('Search team', 'airo-wp'),
					placeholder: __('Search by name…', 'airo-wp'),
				},
			],
			[
				'airo-wp/query-results',
				{
					tagName: 'ul',
					itemTagName: 'li',
					columns: 4,
					layoutVariant: 'avatar-grid',
				},
				[
					[
						'airo-wp/section',
						{},
						[
							[
								'core/post-featured-image',
								{
									align: 'center',
									aspectRatio: '1',
									width: '140px',
								},
							],
							['core/post-title', { level: 3 }],
							['core/post-excerpt', { excerptLength: 12 }],
						],
					],
				],
			],
			['airo-wp/query-no-results'],
		],
	},
	{
		name: 'testimonials',
		title: __('Testimonials', 'airo-wp'),
		description: __(
			'Pull-quote cards with oversized excerpt and attribution. Load-more pagination.',
			'airo-wp'
		),
		icon: 'format-quote',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 6,
			orderBy: 'date',
			order: 'DESC',
		},
		innerBlocks: [
			[
				'airo-wp/query-results',
				{
					tagName: 'ul',
					itemTagName: 'li',
					columns: 2,
					layoutVariant: 'quote-card',
				},
				[
					[
						'airo-wp/section',
						{},
						[
							['core/post-excerpt', { excerptLength: 40 }],
							['core/post-title', { level: 4 }],
						],
					],
				],
			],
			['airo-wp/query-pagination', { paginationKind: 'loadmore' }],
		],
	},
	{
		name: 'portfolio',
		title: __('Portfolio', 'airo-wp'),
		description: __(
			'Image tiles with overlay title. Category filter and load-more pagination.',
			'airo-wp'
		),
		icon: 'portfolio',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 12,
			orderBy: 'date',
			order: 'DESC',
		},
		innerBlocks: [
			[
				'airo-wp/query-filter',
				{
					filterKind: 'checkbox',
					taxonomy: 'category',
					paramName: 'filter_category',
					label: __('Filter by category', 'airo-wp'),
					filterStyle: 'pill',
				},
			],
			[
				'airo-wp/query-results',
				{
					tagName: 'ul',
					itemTagName: 'li',
					columns: 3,
					layoutVariant: 'image-tile',
				},
				[
					[
						'airo-wp/section',
						{},
						[
							[
								'core/post-featured-image',
								{ isLink: true, aspectRatio: '4/3' },
							],
							['core/post-title', { level: 3, isLink: true }],
						],
					],
				],
			],
			['airo-wp/query-no-results'],
			['airo-wp/query-pagination', { paginationKind: 'loadmore' }],
		],
	},
	{
		name: 'related-posts',
		title: __('Related posts', 'airo-wp'),
		description: __(
			'Compact horizontal rows — small thumbnail + title. Excludes current post by default.',
			'airo-wp'
		),
		icon: 'controls-repeat',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 6,
			orderBy: 'rand',
			order: 'DESC',
			excludeCurrent: true,
		},
		innerBlocks: [
			[
				'airo-wp/query-results',
				{
					tagName: 'ul',
					itemTagName: 'li',
					columns: 3,
					layoutVariant: 'compact-row',
				},
				[
					[
						'airo-wp/section',
						{},
						[
							[
								'core/post-featured-image',
								{
									isLink: true,
									aspectRatio: '1',
									width: '96px',
								},
							],
							[
								'airo-wp/section',
								{},
								[
									[
										'core/post-title',
										{ level: 4, isLink: true },
									],
									['core/post-date'],
								],
							],
						],
					],
				],
			],
		],
	},
	{
		name: 'featured-carousel',
		title: __('Featured carousel', 'airo-wp'),
		description: __(
			'Cinematic slider — one post per slide, oversized image, centered title. Dots + arrows.',
			'airo-wp'
		),
		icon: 'images-alt2',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 5,
			orderBy: 'date',
			order: 'DESC',
		},
		innerBlocks: [
			[
				'airo-wp/slider',
				{
					slidesPerView: 3,
					slidesPerViewTablet: 2,
					slidesPerViewMobile: 1,
					showArrows: true,
					showDots: true,
					arrowPosition: 'outside',
					dotPosition: 'outside',
					effect: 'slide',
					loop: true,
					autoplay: false,
				},
				[
					[
						'airo-wp/slide',
						{
							contentVerticalAlign: 'center',
							contentHorizontalAlign: 'center',
						},
						[
							[
								'core/post-featured-image',
								{ aspectRatio: '21/9' },
							],
							['core/post-title', { level: 3, isLink: true }],
							['core/post-excerpt', { excerptLength: 20 }],
						],
					],
				],
			],
			['airo-wp/query-no-results'],
		],
	},
	{
		name: 'post-spotlight',
		title: __('Post spotlight (scroll-slides)', 'airo-wp'),
		description: __(
			'Scroll-driven story panels. One post per panel, big title, no image. Ideal for narrative lists.',
			'airo-wp'
		),
		icon: 'format-gallery',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 4,
			orderBy: 'date',
			order: 'DESC',
			// Scroll-slides is a pinned, viewport-height experience; a
			// constrained outer query container would letterbox it.
			align: 'full',
		},
		innerBlocks: [
			[
				'airo-wp/scroll-slides',
				{
					align: 'full',
					minHeight: '100vh',
					// Default maxHeight on the block is 900px, which caps the
					// pinned section below the 100vh the variation asks for on
					// tall viewports. Clear it so 100vh lands at viewport
					// height.
					maxHeight: '',
					overlayColor: '#000000',
					style: { color: { text: '#ffffff' } },
				},
				[
					[
						'airo-wp/scroll-slide',
						{},
						[
							[
								'core/post-excerpt',
								{
									excerptLength: 30,
									moreText: __('Read more', 'airo-wp'),
									showMoreOnNewLine: true,
								},
							],
						],
					],
				],
			],
			['airo-wp/query-no-results'],
		],
	},
	{
		name: 'events',
		title: __('Events', 'airo-wp'),
		description: __(
			'Date-forward cards with accent rail. Sort controls and numbered pagination. Switch Post type to your `events` CPT.',
			'airo-wp'
		),
		icon: 'calendar-alt',
		attributes: {
			source: 'posts',
			postType: 'post',
			perPage: 6,
			orderBy: 'date',
			order: 'ASC',
		},
		innerBlocks: [
			[
				'airo-wp/query-filter',
				{
					filterKind: 'sort',
					paramName: 'sort',
					label: __('Sort by', 'airo-wp'),
				},
			],
			[
				'airo-wp/query-results',
				{
					tagName: 'ul',
					itemTagName: 'li',
					columns: 2,
					layoutVariant: 'date-forward',
				},
				[
					[
						'airo-wp/section',
						{},
						[
							['core/post-date'],
							['core/post-title', { level: 3, isLink: true }],
							['core/post-excerpt', { excerptLength: 20 }],
						],
					],
				],
			],
			['airo-wp/query-no-results'],
			['airo-wp/query-pagination', { paginationKind: 'numbered' }],
		],
	},
];
