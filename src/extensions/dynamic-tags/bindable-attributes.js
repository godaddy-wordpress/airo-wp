import { __ } from '@wordpress/i18n';

/**
 * Allowlist of bindable attributes per core block, per the WordPress
 * Block Bindings API. Keep in sync with WP core — extending an
 * unsupported attribute would write bindings that core silently ignores.
 *
 * https://developer.wordpress.org/block-editor/reference-guides/block-api/block-bindings/
 */
export const BINDABLE_ATTRIBUTES = {
	'core/paragraph': [
		{
			attribute: 'content',
			returns: ['text'],
			label: __('Content', 'airo-wp'),
		},
	],
	'core/heading': [
		{
			attribute: 'content',
			returns: ['text'],
			label: __('Content', 'airo-wp'),
		},
	],
	'core/image': [
		{
			attribute: 'url',
			returns: ['image', 'url'],
			label: __('Image URL', 'airo-wp'),
			subkey: 'url',
		},
		{
			attribute: 'id',
			returns: ['image', 'number'],
			label: __('Attachment ID', 'airo-wp'),
			subkey: 'id',
		},
		{
			attribute: 'alt',
			returns: ['text'],
			label: __('Alt text', 'airo-wp'),
			subkey: 'alt',
		},
		{
			attribute: 'title',
			returns: ['text'],
			label: __('Title', 'airo-wp'),
			subkey: 'title',
		},
	],
	'core/button': [
		{ attribute: 'url', returns: ['url'], label: __('URL', 'airo-wp') },
		{
			attribute: 'text',
			returns: ['text'],
			label: __('Text', 'airo-wp'),
		},
		{
			attribute: 'linkTarget',
			returns: ['text'],
			label: __('Link target', 'airo-wp'),
		},
		{
			attribute: 'rel',
			returns: ['text'],
			label: __('Rel', 'airo-wp'),
		},
	],
	'core/post-date': [
		{
			attribute: 'datetime',
			returns: ['date', 'text'],
			label: __('Date', 'airo-wp'),
		},
	],
};

export function getBindableAttributes(blockName) {
	return BINDABLE_ATTRIBUTES[blockName] || null;
}
