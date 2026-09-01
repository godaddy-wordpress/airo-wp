/**
 * context-filter.js
 *
 * Appends item-context keys to every block's `usesContext` so the visibility
 * gate (Task B5) can read airo-wp/itemIndex, airo-wp/itemMeta, and
 * airo-wp/itemTerms from any block inside a Dynamic Query template.
 *
 * @since 2.3.0
 */
import { addFilter } from '@wordpress/hooks';

/**
 * Blocks whose usesContext should NOT be touched.
 *
 * Container/query-family blocks never appear inside a query item template,
 * so they never need the item-context keys.
 */
const BLOCKED = new Set([
	'core/freeform',
	'core/missing',
	'core/template-part',
	'core/query',
	'core/query-loop',
	'core/query-pagination',
	'core/query-pagination-next',
	'core/query-pagination-previous',
	'core/query-pagination-numbers',
	'core/query-no-results',
	'core/post-template',
	'airo-wp/query',
	'airo-wp/query-pagination',
	'airo-wp/query-filter',
	'airo-wp/query-no-results',
	'airo-wp/query-group-header',
]);

/** Context keys provided by the Dynamic Query block per rendered item. */
const CONTEXT_KEYS = [
	'airo-wp/itemIndex',
	'airo-wp/itemMeta',
	'airo-wp/itemTerms',
	'airo-wp/isAuthenticated',
	'airo-wp/groupItemIndex',
];

/**
 * Filter callback — appends CONTEXT_KEYS to settings.usesContext, deduped.
 *
 * @param {Object} settings Block type settings.
 * @param {string} name     Block type name.
 * @return {Object} Updated settings.
 */
function addItemContextUses(settings, name) {
	if (BLOCKED.has(name)) {
		return settings;
	}
	const existing = settings.usesContext ?? [];
	const merged = [...new Set([...existing, ...CONTEXT_KEYS])];
	return { ...settings, usesContext: merged };
}

addFilter(
	'blocks.registerBlockType',
	'airo-wp/visibility/uses-context',
	addItemContextUses
);
