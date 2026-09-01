/**
 * Vertical Scroll Parallax - Constants
 *
 * Configuration and default settings for vertical scroll parallax effects
 *
 * @package
 * @since 1.0.0
 */

/**
 * Default parallax settings
 */
export const DEFAULT_PARALLAX_SETTINGS = {
	enabled: false,
	direction: 'up',
	speed: 5,
	viewportStart: 0,
	viewportEnd: 100,
	relativeTo: 'viewport',
	enableDesktop: true,
	enableTablet: true,
	enableMobile: false,
	rotateEnabled: false,
	rotateDirection: 'cw',
	rotateSpeed: 3,
};

/**
 * Direction values for parallax movement
 */
export const DIRECTION_VALUES = {
	UP: 'up',
	DOWN: 'down',
	LEFT: 'left',
	RIGHT: 'right',
};

/**
 * Rotation direction values
 */
export const ROTATION_DIRECTION_VALUES = {
	CW: 'cw',
	CCW: 'ccw',
};

/**
 * Reference point values for parallax calculation
 */
export const RELATIVE_TO_VALUES = {
	VIEWPORT: 'viewport',
	PAGE: 'page',
};

/**
 * Blocks that support vertical scroll parallax
 * Container and visual blocks only - excludes text-heavy blocks
 */
export const ALLOWED_BLOCKS = [
	// Core WordPress blocks
	'core/group',
	'core/cover',
	'core/image',
	'core/media-text',
	'core/columns',
	'core/column',
	// airo-wp container blocks
	'airo-wp/section',
	'airo-wp/row',
	'airo-wp/grid',
	// airo-wp visual blocks
	'airo-wp/flip-card',
	'airo-wp/flip-card-face',
	'airo-wp/flip-card-front',
	'airo-wp/flip-card-back',
	'airo-wp/icon',
	'airo-wp/icon-button',
	'airo-wp/image-accordion',
	'airo-wp/image-accordion-item',
	'airo-wp/scroll-accordion',
	'airo-wp/scroll-accordion-item',
];

/**
 * Device breakpoints (matches WordPress/theme defaults)
 */
export const BREAKPOINTS = {
	desktop: 1024,
	tablet: 768,
};
