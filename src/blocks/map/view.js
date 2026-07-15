/**
 * Map Block - Frontend JavaScript
 *
 * Handles map initialization, privacy mode, and interactive functionality.
 */

import DSGMap from './handlers/DSGMap';

/**
 * Initialize all map blocks.
 */
function initMaps() {
	const mapBlocks = document.querySelectorAll('.airo-wp-map');

	mapBlocks.forEach((element) => {
		// Prevent duplicate initialization
		if (element.hasAttribute('data-airo-wp-initialized')) {
			return;
		}
		element.setAttribute('data-airo-wp-initialized', 'true');

		new DSGMap(element);
	});
}

// Run on DOM ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initMaps);
} else {
	initMaps();
}

// Re-initialize after soft navigation (bfcache, AJAX)
document.addEventListener('airo-wp-content-loaded', initMaps);

// Expose to window for external access
window.DSGMap = DSGMap;
