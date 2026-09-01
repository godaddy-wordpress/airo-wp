/**
 * Form Builder Templates
 *
 * Preset structures surfaced in the first-insert placeholder.
 * Each template seeds its own innerBlocks and any notification/messaging
 * defaults appropriate for the use case.
 */

import { __ } from '@wordpress/i18n';

export const formBuilderTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Start with a single text field', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [
			[
				'airo-wp/form-text-field',
				{
					label: __('Name', 'airo-wp'),
					fieldName: 'name',
					required: true,
				},
			],
		],
	},
	{
		name: 'contact',
		title: __('Contact', 'airo-wp'),
		description: __(
			'Name, email, and message — the classic contact form',
			'airo-wp'
		),
		icon: 'email',
		attributes: {
			submitButtonText: __('Send Message', 'airo-wp'),
			successMessage: __(
				"Thanks — we'll be in touch shortly.",
				'airo-wp'
			),
			enableEmail: true,
			emailSubject: __('New contact form submission', 'airo-wp'),
			emailReplyTo: 'email',
		},
		innerBlocks: [
			[
				'airo-wp/form-text-field',
				{
					label: __('Name', 'airo-wp'),
					fieldName: 'name',
					required: true,
				},
			],
			[
				'airo-wp/form-email-field',
				{
					label: __('Email', 'airo-wp'),
					fieldName: 'email',
					required: true,
				},
			],
			[
				'airo-wp/form-textarea-field',
				{
					label: __('Message', 'airo-wp'),
					fieldName: 'message',
					required: true,
				},
			],
		],
	},
	{
		name: 'newsletter',
		title: __('Newsletter', 'airo-wp'),
		description: __(
			'Single email field with an inline subscribe button',
			'airo-wp'
		),
		icon: 'email-alt',
		attributes: {
			submitButtonText: __('Subscribe', 'airo-wp'),
			submitButtonPosition: 'inline',
			successMessage: __('Thanks for subscribing!', 'airo-wp'),
			enableEmail: true,
			emailSubject: __('New newsletter signup', 'airo-wp'),
			emailReplyTo: 'email',
		},
		innerBlocks: [
			[
				'airo-wp/form-email-field',
				{
					label: __('Email Address', 'airo-wp'),
					fieldName: 'email',
					required: true,
					placeholder: __('you@example.com', 'airo-wp'),
				},
			],
		],
	},
	{
		name: 'event-registration',
		title: __('Event Registration', 'airo-wp'),
		description: __('Name, email, phone, and number of guests', 'airo-wp'),
		icon: 'calendar-alt',
		attributes: {
			submitButtonText: __('Register', 'airo-wp'),
			successMessage: __(
				"You're on the list — check your inbox for details.",
				'airo-wp'
			),
			enableEmail: true,
			emailSubject: __('New event registration', 'airo-wp'),
			emailReplyTo: 'email',
		},
		innerBlocks: [
			[
				'airo-wp/form-text-field',
				{
					label: __('Full Name', 'airo-wp'),
					fieldName: 'name',
					required: true,
				},
			],
			[
				'airo-wp/form-email-field',
				{
					label: __('Email', 'airo-wp'),
					fieldName: 'email',
					required: true,
				},
			],
			[
				'airo-wp/form-phone-field',
				{
					label: __('Phone', 'airo-wp'),
					fieldName: 'phone',
				},
			],
			[
				'airo-wp/form-number-field',
				{
					label: __('Number of Guests', 'airo-wp'),
					fieldName: 'guests',
					min: 1,
					max: 10,
					defaultValue: 1,
				},
			],
		],
	},
	{
		name: 'lead-capture',
		title: __('Lead Capture', 'airo-wp'),
		description: __(
			'Name, work email, company, and phone for B2B leads',
			'airo-wp'
		),
		icon: 'businessman',
		attributes: {
			submitButtonText: __('Get in Touch', 'airo-wp'),
			successMessage: __("Thanks — we'll reach out shortly.", 'airo-wp'),
			enableEmail: true,
			emailSubject: __('New lead submission', 'airo-wp'),
			emailReplyTo: 'email',
		},
		innerBlocks: [
			[
				'airo-wp/form-text-field',
				{
					label: __('Full Name', 'airo-wp'),
					fieldName: 'name',
					required: true,
				},
			],
			[
				'airo-wp/form-email-field',
				{
					label: __('Work Email', 'airo-wp'),
					fieldName: 'email',
					required: true,
				},
			],
			[
				'airo-wp/form-text-field',
				{
					label: __('Company', 'airo-wp'),
					fieldName: 'company',
					required: true,
				},
			],
			[
				'airo-wp/form-phone-field',
				{
					label: __('Phone', 'airo-wp'),
					fieldName: 'phone',
				},
			],
		],
	},
];
