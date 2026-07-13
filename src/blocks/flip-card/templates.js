/**
 * Flip Card Templates
 *
 * Starter layouts shown by FlipCardPlaceholder when the block is first
 * inserted. Every template seeds one front face and one back face so the
 * card is immediately interactive — never a single-faced state.
 *
 * Children are `airo-wp/flip-card-face` (the consolidated block
 * introduced in Theme 2) distinguished by the `side` attribute; the
 * legacy flip-card-front / flip-card-back siblings remain registered
 * with `inserter: false` for existing content but are not seeded here.
 */

import { __ } from '@wordpress/i18n';

// Face children support `spacing.padding` but default to none, so templates
// seed it explicitly — otherwise text sits flush against the card edge on
// first insert. Authors can still adjust via Style → Padding.
const facePadding = {
	spacing: {
		padding: {
			top: '32px',
			right: '32px',
			bottom: '32px',
			left: '32px',
		},
	},
};

const face = (side, extra = {}, innerBlocks = []) => [
	'airo-wp/flip-card-face',
	{
		side,
		style: {
			...facePadding,
			...(extra.style || {}),
		},
	},
	innerBlocks,
];

// Theme-agnostic neutral colors for template starters. Hardcoded hex keeps
// the faces visibly "card-like" on any theme; authors override via Style.
// Palette matches the airo-wp wider system (slate-inspired neutrals).
const neutralBack = {
	style: {
		color: {
			background: '#f1f5f9',
			text: '#0f172a',
		},
	},
};

const contrastBack = {
	style: {
		color: {
			background: '#0f172a',
			text: '#ffffff',
		},
	},
};

const flipCardTemplates = [
	{
		name: 'blank',
		title: __('Blank', 'airo-wp'),
		description: __('Empty front and back to fill in', 'airo-wp'),
		icon: 'welcome-add-page',
		attributes: {},
		innerBlocks: [face('front'), face('back')],
	},
	{
		name: 'feature',
		title: __('Feature', 'airo-wp'),
		description: __(
			'Title up front, supporting detail on the back',
			'airo-wp'
		),
		icon: 'star-filled',
		attributes: { flipTrigger: 'hover', flipEffect: 'flip' },
		innerBlocks: [
			face('front', {}, [
				[
					'core/heading',
					{
						level: 3,
						content: __('Feature title', 'airo-wp'),
						textAlign: 'center',
					},
				],
			]),
			face('back', neutralBack, [
				[
					'core/paragraph',
					{
						content: __(
							'Add a short description of the feature, the value it delivers, or how it works.',
							'airo-wp'
						),
						align: 'center',
					},
				],
			]),
		],
	},
	{
		name: 'profile',
		title: __('Profile', 'airo-wp'),
		description: __('Headshot up front, bio on the back', 'airo-wp'),
		icon: 'admin-users',
		attributes: { flipTrigger: 'click', flipEffect: 'flip' },
		innerBlocks: [
			face('front', {}, [
				['core/image', { sizeSlug: 'medium' }],
				[
					'core/heading',
					{
						level: 4,
						content: __('Name', 'airo-wp'),
						textAlign: 'center',
					},
				],
				[
					'core/paragraph',
					{
						content: __('Role / Title', 'airo-wp'),
						align: 'center',
					},
				],
			]),
			face('back', {}, [
				[
					'core/paragraph',
					{
						content: __(
							'Short bio. Mention background, current focus, and how to get in touch.',
							'airo-wp'
						),
						align: 'center',
					},
				],
			]),
		],
	},
	{
		name: 'cta',
		title: __('Call to Action', 'airo-wp'),
		description: __(
			'Lead with a hook, finish with a button',
			'airo-wp'
		),
		icon: 'megaphone',
		attributes: { flipTrigger: 'hover', flipEffect: 'flip' },
		innerBlocks: [
			face('front', {}, [
				[
					'core/heading',
					{
						level: 3,
						content: __('Try it free', 'airo-wp'),
						textAlign: 'center',
					},
				],
				[
					'core/paragraph',
					{
						content: __('Hover to learn more.', 'airo-wp'),
						align: 'center',
					},
				],
			]),
			face('back', contrastBack, [
				[
					'core/paragraph',
					{
						content: __(
							'No credit card required. Cancel anytime.',
							'airo-wp'
						),
						align: 'center',
					},
				],
				[
					'airo-wp/icon-button',
					{
						text: __('Get started', 'airo-wp'),
						justification: 'center',
					},
				],
			]),
		],
	},
];

export default flipCardTemplates;
