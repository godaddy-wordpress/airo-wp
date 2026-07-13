/**
 * Modal Templates
 *
 * Pre-configured modal templates for quick setup.
 * These are used in the template chooser when a modal is first inserted.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';

export const modalTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Start with an empty modal', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			[
				'core/heading',
				{
					level: 2,
					placeholder: __('Modal Title', 'airo-wp'),
				},
			],
			[
				'core/paragraph',
				{
					placeholder: __(
						'Add your modal content here…',
						'airo-wp'
					),
				},
			],
		],
	},
	{
		name: 'newsletter',
		title: __('Newsletter Signup', 'airo-wp'),
		description: __('Email collection form with CTA', 'airo-wp'),
		icon: 'email-alt',
		attributes: {
			width: '500px',
			maxWidth: '90vw',
			overlayOpacity: 80,
			animationType: 'slide-up',
			autoTriggerType: 'exitIntent',
			exitIntentSensitivity: 'medium',
			autoTriggerFrequency: 'once',
			cookieDuration: 30,
		},
		innerBlocks: [
			[
				'core/heading',
				{
					level: 2,
					content: __('Subscribe to Our Newsletter', 'airo-wp'),
					textAlign: 'center',
				},
			],
			[
				'core/paragraph',
				{
					content: __(
						'Get the latest updates and exclusive content delivered to your inbox.',
						'airo-wp'
					),
					align: 'center',
				},
			],
			[
				'airo-wp/form-builder',
				{
					submitButtonText: __('Subscribe', 'airo-wp'),
					submitButtonAlignment: 'center',
					ajaxSubmit: true,
					successMessage: __(
						'Thank you for subscribing!',
						'airo-wp'
					),
				},
				[
					[
						'airo-wp/form-email-field',
						{
							label: __('Email Address', 'airo-wp'),
							placeholder: 'your@email.com',
							required: true,
							fieldName: 'email',
						},
					],
				],
			],
		],
	},
	{
		name: 'announcement',
		title: __('Announcement', 'airo-wp'),
		description: __('Important notice or promo', 'airo-wp'),
		icon: 'megaphone',
		attributes: {
			width: '600px',
			maxWidth: '90vw',
			overlayOpacity: 70,
			animationType: 'zoom',
			autoTriggerType: 'pageLoad',
			autoTriggerDelay: 2,
			autoTriggerFrequency: 'session',
		},
		innerBlocks: [
			[
				'core/heading',
				{
					level: 2,
					content: __('🎉 Special Offer!', 'airo-wp'),
					textAlign: 'center',
				},
			],
			[
				'core/paragraph',
				{
					content: __(
						'Get 20% off your first purchase. Use code <strong>WELCOME20</strong> at checkout.',
						'airo-wp'
					),
					align: 'center',
					fontSize: 'medium',
				},
			],
			[
				'airo-wp/icon-button',
				{
					text: __('Shop Now', 'airo-wp'),
					fullWidth: true,
					icon: 'cart',
					iconPosition: 'end',
				},
			],
		],
	},
	{
		name: 'video',
		title: __('Video Player', 'airo-wp'),
		description: __('Video embed with optimal sizing', 'airo-wp'),
		icon: 'video-alt3',
		attributes: {
			width: '800px',
			maxWidth: '95vw',
			overlayOpacity: 95,
			overlayColor: '#000000',
			animationType: 'zoom',
			closeButtonPosition: 'top-right',
		},
		innerBlocks: [
			[
				'core/embed',
				{
					url: '',
					type: 'video',
					providerNameSlug: 'youtube',
					responsive: true,
					className: 'wp-embed-aspect-16-9',
				},
			],
		],
	},
	{
		name: 'image-lightbox',
		title: __('Image Lightbox', 'airo-wp'),
		description: __('Full-screen image display', 'airo-wp'),
		icon: 'format-image',
		attributes: {
			width: 'auto',
			maxWidth: '95vw',
			overlayOpacity: 90,
			overlayColor: '#000000',
			overlayBlur: 5,
			animationType: 'fade',
			closeButtonPosition: 'inside-top-right',
			closeButtonSize: 32,
			closeButtonIconColor: '#ffffff',
			closeButtonBgColor: 'rgba(0, 0, 0, 0.5)',
		},
		innerBlocks: [
			[
				'core/image',
				{
					url: '',
					alt: '',
					sizeSlug: 'full',
					linkDestination: 'none',
				},
			],
			[
				'core/paragraph',
				{
					content: __('Image caption…', 'airo-wp'),
					align: 'center',
					fontSize: 'small',
				},
			],
		],
	},
	{
		name: 'contact-form',
		title: __('Contact Form', 'airo-wp'),
		description: __('Basic contact form layout', 'airo-wp'),
		icon: 'admin-comments',
		attributes: {
			width: '600px',
			maxWidth: '90vw',
			overlayOpacity: 75,
			animationType: 'slide-up',
		},
		innerBlocks: [
			[
				'core/heading',
				{
					level: 2,
					content: __('Get in Touch', 'airo-wp'),
				},
			],
			[
				'core/paragraph',
				{
					content: __(
						"Have a question? We'd love to hear from you.",
						'airo-wp'
					),
				},
			],
			[
				'airo-wp/form-builder',
				{
					submitButtonText: __('Send Message', 'airo-wp'),
					submitButtonAlignment: 'left',
					ajaxSubmit: true,
					successMessage: __(
						'Thank you! Your message has been sent.',
						'airo-wp'
					),
				},
				[
					[
						'airo-wp/form-text-field',
						{
							label: __('Name', 'airo-wp'),
							placeholder: 'Your name',
							required: true,
							fieldName: 'name',
						},
					],
					[
						'airo-wp/form-email-field',
						{
							label: __('Email', 'airo-wp'),
							placeholder: 'your@email.com',
							required: true,
							fieldName: 'email',
						},
					],
					[
						'airo-wp/form-textarea-field',
						{
							label: __('Message', 'airo-wp'),
							placeholder: 'Your message...',
							required: true,
							fieldName: 'message',
						},
					],
				],
			],
		],
	},
	{
		name: 'product-details',
		title: __('Product Details', 'airo-wp'),
		description: __('Product showcase with image', 'airo-wp'),
		icon: 'cart',
		attributes: {
			width: '900px',
			maxWidth: '95vw',
			overlayOpacity: 85,
			animationType: 'slide-up',
		},
		innerBlocks: [
			[
				'core/columns',
				{
					verticalAlignment: 'center',
				},
				[
					[
						'core/column',
						{
							width: '50%',
						},
						[
							[
								'core/image',
								{
									url: '',
									alt: '',
									sizeSlug: 'large',
								},
							],
						],
					],
					[
						'core/column',
						{
							width: '50%',
						},
						[
							[
								'core/heading',
								{
									level: 3,
									content: __('Product Name', 'airo-wp'),
								},
							],
							[
								'core/paragraph',
								{
									content: __(
										'Product description and key features go here…',
										'airo-wp'
									),
								},
							],
							[
								'core/paragraph',
								{
									content: __(
										'<strong style="font-size: 1.5em; color: #2d7a4c;">$99.00</strong>',
										'airo-wp'
									),
								},
							],
							[
								'airo-wp/icon-button',
								{
									text: __('Add to Cart', 'airo-wp'),
									icon: 'cart',
									iconPosition: 'start',
								},
							],
						],
					],
				],
			],
		],
	},
	{
		name: 'cookie-notice',
		title: __('Cookie Notice', 'airo-wp'),
		description: __('GDPR-style cookie consent', 'airo-wp'),
		icon: 'shield',
		attributes: {
			width: '500px',
			maxWidth: '95vw',
			overlayOpacity: 50,
			animationType: 'slide-up',
			closeOnBackdrop: false,
			closeOnEsc: false,
			showCloseButton: false,
			autoTriggerType: 'pageLoad',
			autoTriggerDelay: 1,
			autoTriggerFrequency: 'once',
			cookieDuration: 365,
		},
		innerBlocks: [
			[
				'core/heading',
				{
					level: 3,
					content: __('🍪 Cookie Notice', 'airo-wp'),
				},
			],
			[
				'core/paragraph',
				{
					content: __(
						'We use cookies to enhance your browsing experience and analyze our traffic. By clicking "Accept", you consent to our use of cookies.',
						'airo-wp'
					),
					fontSize: 'small',
				},
			],
			[
				'core/buttons',
				{
					layout: { type: 'flex', justifyContent: 'left' },
				},
				[
					[
						'airo-wp/icon-button',
						{
							text: __('Learn More', 'airo-wp'),
							icon: 'info',
							iconPosition: 'start',
							url: '#privacy-policy',
						},
					],
					[
						'airo-wp/icon-button',
						{
							text: __('Accept', 'airo-wp'),
							icon: 'yes',
							iconPosition: 'end',
							modalCloseId: 'true',
						},
					],
				],
			],
		],
	},
	{
		name: 'promo-split',
		title: __('Promo Split', 'airo-wp'),
		description: __('Two-column promo with image', 'airo-wp'),
		icon: 'megaphone',
		attributes: {
			width: '800px',
			maxWidth: '90vw',
			overlayOpacity: 80,
			overlayBlur: 8,
			animationType: 'slide-up',
			autoTriggerType: 'exitIntent',
			autoTriggerDelay: 1900,
			autoTriggerFrequency: 'always',
			exitIntentSensitivity: 'medium',
			closeButtonPosition: 'inside-top-right',
			closeButtonIconColor: '#ffffff',
			backgroundColor: 'accent-1',
			style: {
				spacing: {
					padding: {
						top: '0',
						bottom: '0',
						left: '0',
						right: '0',
					},
				},
			},
		},
		innerBlocks: [
			[
				'airo-wp/grid',
				{
					desktopColumns: 2,
					alignItems: 'center',
					style: {
						spacing: {
							blockGap: 'var(--wp--preset--spacing--50)',
							padding: {
								top: '0',
								bottom: '0',
								left: '0',
								right: '0',
							},
						},
					},
				},
				[
					[
						'airo-wp/section',
						{
							style: {
								spacing: {
									padding: {
										top: 'var(--wp--preset--spacing--50)',
										bottom: 'var(--wp--preset--spacing--50)',
										left: 'var(--wp--preset--spacing--50)',
										right: 'var(--wp--preset--spacing--50)',
									},
								},
							},
							layout: {
								type: 'flex',
								orientation: 'vertical',
								verticalAlignment: 'center',
								justifyContent: 'left',
							},
						},
						[
							[
								'core/heading',
								{
									level: 2,
									content: __('Special Offer', 'airo-wp'),
								},
							],
							[
								'core/paragraph',
								{
									content: __(
										"Here's a message about the special offer",
										'airo-wp'
									),
								},
							],
							[
								'airo-wp/icon-button',
								{
									text: __('Download', 'airo-wp'),
									icon: 'download',
									justification: 'left',
								},
							],
						],
					],
					[
						'airo-wp/section',
						{
							style: {
								spacing: {
									padding: {
										top: 'var(--wp--preset--spacing--50)',
										bottom: 'var(--wp--preset--spacing--50)',
										left: 'var(--wp--preset--spacing--30)',
										right: 'var(--wp--preset--spacing--30)',
									},
								},
							},
						},
						[
							[
								'core/spacer',
								{
									height: '274px',
									style: {
										layout: {
											flexSize: '274px',
											selfStretch: 'fixed',
										},
									},
								},
							],
						],
					],
				],
			],
		],
	},
];

export default modalTemplates;
