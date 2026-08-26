/**
 * Vertical Scroll Parallax - Editor Extension
 *
 * Adds inspector controls and save props for parallax effects
 *
 * @package
 * @since 1.0.0
 */

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';

import ParallaxPanel from './components/ParallaxPanel';
import { ALLOWED_BLOCKS, DEFAULT_PARALLAX_SETTINGS } from './constants';

/**
 * Add parallax controls to block inspector
 */
const withParallaxControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { name, attributes, setAttributes } = props;

		// Only render for allowed blocks
		if (!ALLOWED_BLOCKS.includes(name)) {
			return <BlockEdit {...props} />;
		}

		return (
			<Fragment>
				<BlockEdit {...props} />
				<InspectorControls>
					<ParallaxPanel
						attributes={attributes}
						setAttributes={setAttributes}
					/>
				</InspectorControls>
			</Fragment>
		);
	};
}, 'withParallaxControls');

addFilter(
	'editor.BlockEdit',
	'airo-wp/vertical-scroll-parallax/controls',
	withParallaxControls,
	20
);

/**
 * Add parallax data attributes to saved block content
 *
 * @param {Object} extraProps Additional props
 * @param {Object} blockType  Block type
 * @param {Object} attributes Block attributes
 * @return {Object} Modified props
 */
function addParallaxSaveProps(extraProps, blockType, attributes) {
	// Only process allowed blocks
	if (!ALLOWED_BLOCKS.includes(blockType.name)) {
		return extraProps;
	}

	const {
		dsgoParallaxEnabled = DEFAULT_PARALLAX_SETTINGS.enabled,
		dsgoParallaxDirection = DEFAULT_PARALLAX_SETTINGS.direction,
		dsgoParallaxSpeed = DEFAULT_PARALLAX_SETTINGS.speed,
		dsgoParallaxViewportStart = DEFAULT_PARALLAX_SETTINGS.viewportStart,
		dsgoParallaxViewportEnd = DEFAULT_PARALLAX_SETTINGS.viewportEnd,
		dsgoParallaxRelativeTo = DEFAULT_PARALLAX_SETTINGS.relativeTo,
		dsgoParallaxDesktop = DEFAULT_PARALLAX_SETTINGS.enableDesktop,
		dsgoParallaxTablet = DEFAULT_PARALLAX_SETTINGS.enableTablet,
		dsgoParallaxMobile = DEFAULT_PARALLAX_SETTINGS.enableMobile,
		dsgoParallaxRotateEnabled = DEFAULT_PARALLAX_SETTINGS.rotateEnabled,
		dsgoParallaxRotateDirection = DEFAULT_PARALLAX_SETTINGS.rotateDirection,
		dsgoParallaxRotateSpeed = DEFAULT_PARALLAX_SETTINGS.rotateSpeed,
	} = attributes;

	// Only add attributes if parallax is enabled
	if (!dsgoParallaxEnabled) {
		return extraProps;
	}

	// Add data attributes for frontend JS
	const dataAttributes = {
		'data-airo-wp-parallax-enabled': 'true',
		'data-airo-wp-parallax-direction': dsgoParallaxDirection,
		'data-airo-wp-parallax-speed': String(dsgoParallaxSpeed),
		'data-airo-wp-parallax-viewport-start': String(
			dsgoParallaxViewportStart
		),
		'data-airo-wp-parallax-viewport-end': String(dsgoParallaxViewportEnd),
		'data-airo-wp-parallax-relative-to': dsgoParallaxRelativeTo,
		'data-airo-wp-parallax-desktop': dsgoParallaxDesktop ? 'true' : 'false',
		'data-airo-wp-parallax-tablet': dsgoParallaxTablet ? 'true' : 'false',
		'data-airo-wp-parallax-mobile': dsgoParallaxMobile ? 'true' : 'false',
		'data-airo-wp-parallax-rotate-enabled': dsgoParallaxRotateEnabled
			? 'true'
			: 'false',
		'data-airo-wp-parallax-rotate-direction': dsgoParallaxRotateDirection,
		'data-airo-wp-parallax-rotate-speed': String(dsgoParallaxRotateSpeed),
	};

	// Add class for CSS targeting
	const className = [extraProps.className, 'airo-wp-has-parallax']
		.filter(Boolean)
		.join(' ');

	return {
		...extraProps,
		...dataAttributes,
		className,
	};
}

addFilter(
	'blocks.getSaveContent.extraProps',
	'airo-wp/vertical-scroll-parallax/save-props',
	addParallaxSaveProps
);
