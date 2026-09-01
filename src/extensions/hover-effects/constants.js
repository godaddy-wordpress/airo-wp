/**
 * Hover Effects - Constants
 *
 * Preset hover micro-interactions and supported block list.
 *
 * @package
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';

/**
 * Hover effect presets
 *
 * Values map 1:1 to CSS class modifiers:
 * `.airo-wp-hover-effect--{value}` in styles.scss.
 */
export const HOVER_EFFECTS = [
	{ label: __('None', 'airo-wp'), value: '' },
	{ label: __('Lift', 'airo-wp'), value: 'lift' },
	{ label: __('Sink', 'airo-wp'), value: 'sink' },
	{ label: __('Grow', 'airo-wp'), value: 'grow' },
	{ label: __('Shrink', 'airo-wp'), value: 'shrink' },
	{ label: __('Tilt', 'airo-wp'), value: 'tilt' },
	{ label: __('Glow', 'airo-wp'), value: 'glow' },
];

/**
 * Blocks that receive the hover effect control
 */
export const SUPPORTED_BLOCKS = [
	'core/group',
	'core/cover',
	'core/column',
	'core/columns',
	'core/image',
	'core/button',
	'core/buttons',
	'core/media-text',
	'core/post-template',
	'core/query',
];
