/**
 * Interaction Layers - Constants
 *
 * Option tables and the canonical interaction shape.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';

export const TRIGGERS = [
	{ value: 'click', label: __('Click', 'airo-wp') },
	{ value: 'hover', label: __('Hover', 'airo-wp') },
	{ value: 'inView', label: __('Scrolls into view', 'airo-wp') },
	{ value: 'exitIntent', label: __('Exit intent', 'airo-wp') },
	{ value: 'keydown', label: __('Key press', 'airo-wp') },
];

/**
 * Action group labels, in the order they appear in the picker.
 *
 * The list is long enough that a flat select is hard to scan, so each action
 * declares which group it belongs to.
 */
export const ACTION_GROUPS = [
	{ key: 'visibility', label: __('Visibility', 'airo-wp') },
	{ key: 'classes', label: __('Classes and attributes', 'airo-wp') },
	{ key: 'scroll', label: __('Scrolling', 'airo-wp') },
	{ key: 'blocks', label: __('Blocks', 'airo-wp') },
	{ key: 'media', label: __('Media', 'airo-wp') },
	{ key: 'other', label: __('Other', 'airo-wp') },
];

export const ACTIONS = [
	{ value: 'show', label: __('Show', 'airo-wp'), group: 'visibility' },
	{ value: 'hide', label: __('Hide', 'airo-wp'), group: 'visibility' },
	{
		value: 'toggleVisibility',
		label: __('Show / hide', 'airo-wp'),
		group: 'visibility',
	},
	{
		value: 'toggleClass',
		label: __('Toggle class', 'airo-wp'),
		group: 'classes',
	},
	{
		value: 'addClass',
		label: __('Add class', 'airo-wp'),
		group: 'classes',
	},
	{
		value: 'removeClass',
		label: __('Remove class', 'airo-wp'),
		group: 'classes',
	},
	{
		value: 'setAttribute',
		label: __('Set attribute', 'airo-wp'),
		group: 'classes',
	},
	{
		value: 'removeAttribute',
		label: __('Remove attribute', 'airo-wp'),
		group: 'classes',
	},
	{
		value: 'scrollTo',
		label: __('Scroll to', 'airo-wp'),
		group: 'scroll',
	},
	{
		value: 'scrollToTop',
		label: __('Scroll to top of page', 'airo-wp'),
		group: 'scroll',
	},
	{
		value: 'openModal',
		label: __('Open modal', 'airo-wp'),
		group: 'blocks',
	},
	{
		value: 'closeModal',
		label: __('Close modal', 'airo-wp'),
		group: 'blocks',
	},
	{
		value: 'submitForm',
		label: __('Submit form', 'airo-wp'),
		group: 'blocks',
	},
	{
		value: 'playMedia',
		label: __('Play video or audio', 'airo-wp'),
		group: 'media',
	},
	{
		value: 'pauseMedia',
		label: __('Pause video or audio', 'airo-wp'),
		group: 'media',
	},
	{
		value: 'toggleMedia',
		label: __('Play / pause video or audio', 'airo-wp'),
		group: 'media',
	},
	{
		value: 'copyToClipboard',
		label: __('Copy to clipboard', 'airo-wp'),
		group: 'other',
	},
	{
		value: 'focusTarget',
		label: __('Move focus to', 'airo-wp'),
		group: 'other',
	},
	{
		value: 'dispatchEvent',
		label: __('Fire a custom event', 'airo-wp'),
		group: 'other',
	},
];

export const TARGET_MODES = [
	{ value: 'self', label: __('This block', 'airo-wp') },
	{ value: 'selector', label: __('CSS selector', 'airo-wp') },
	{ value: 'parent', label: __('Closest ancestor', 'airo-wp') },
];

/**
 * Per-action copy for the shared `value` field.
 *
 * `value` carries a different thing for every action, so the control has to
 * relabel itself or the author is left guessing what to type. Actions absent
 * from this table take no value at all and the field is hidden.
 */
export const ACTION_VALUE_FIELD = {
	toggleClass: {
		label: __('Class name', 'airo-wp'),
		help: __('Without the leading dot. For example: is-open', 'airo-wp'),
	},
	addClass: {
		label: __('Class name', 'airo-wp'),
		help: __('Without the leading dot. For example: is-open', 'airo-wp'),
	},
	removeClass: {
		label: __('Class name', 'airo-wp'),
		help: __('Without the leading dot. For example: is-open', 'airo-wp'),
	},
	setAttribute: {
		label: __('Attribute value', 'airo-wp'),
		help: __('For example: true', 'airo-wp'),
	},
	openModal: {
		label: __('Modal ID', 'airo-wp'),
		help: __('The HTML anchor of the modal block to open.', 'airo-wp'),
	},
	closeModal: {
		label: __('Modal ID', 'airo-wp'),
		help: __('Leave empty to close whichever modal is open.', 'airo-wp'),
	},
	copyToClipboard: {
		label: __('Text to copy', 'airo-wp'),
		help: __("Leave empty to copy the target's own text.", 'airo-wp'),
	},
	dispatchEvent: {
		label: __('Event name', 'airo-wp'),
		help: __(
			'Fired on the target and allowed to bubble. For example: my-plugin-opened',
			'airo-wp'
		),
	},
};

// Re-exported so editor code has one import site for interaction constants.
// The values live in a dependency-free module; see visibility-contract.js.
export { HIDDEN_CLASS, VISIBILITY_ACTIONS } from './visibility-contract';

/** Actions that scroll, and so respect the offset field. */
export const OFFSET_ACTIONS = ['scrollTo'];

export const DEFAULT_INTERACTION = {
	id: '',
	trigger: 'click',
	targetMode: 'self',
	targetSelector: '',
	action: 'toggleClass',
	value: '',
	attributeName: '',
	key: '',
	once: false,
	offset: 0,
};
