/**
 * Image Accordion Item - Deprecations
 *
 * v1: Save before the overlay color/opacity stopped being baked into the item
 * markup. Previously the item always serialized `--airo-wp-overlay-color`,
 * `--airo-wp-overlay-opacity` and `--airo-wp-overlay-opacity-expanded` custom
 * properties. Because block context is not available during save
 * serialization, those values always resolved to the defaults (`#000000`,
 * `0.4`, `0.2`) in the stored markup — so the parent's overlay settings never
 * actually reached the frontend. Current saves omit these vars entirely and
 * let the parent accordion's cascading `--airo-wp-image-accordion-overlay-*`
 * properties drive the overlay (see style.scss). This deprecation reproduces
 * the old markup and migrates silently; the attribute schema is unchanged.
 *
 * @package
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import metadata from './block.json';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { getDeprecatedBlockHTML } from '../../utils/deprecated-block-html';

const v1 = {
	attributes: metadata.attributes,
	supports: metadata.supports,
	usesContext: metadata.usesContext,
	isEligible(attributes, innerBlocks, extra) {
		const innerHTML = getDeprecatedBlockHTML(extra);
		// Only the pre-change save emitted the short overlay custom property.
		return (
			typeof innerHTML === 'string' &&
			innerHTML.includes('--airo-wp-overlay-color')
		);
	},
	migrate(attributes) {
		// Overlay values now cascade from the parent; nothing to migrate.
		return attributes;
	},
	save({ attributes, context }) {
		const { uniqueId, verticalAlignment, horizontalAlignment } = attributes;

		const enableOverlay =
			context?.['airo-wp/imageAccordion/enableOverlay'] !== undefined
				? context['airo-wp/imageAccordion/enableOverlay']
				: true;
		const overlayColor =
			context?.['airo-wp/imageAccordion/overlayColor'] || '#000000';
		const overlayOpacity =
			context?.['airo-wp/imageAccordion/overlayOpacity'] || 40;
		const overlayOpacityExpanded =
			context?.['airo-wp/imageAccordion/overlayOpacityExpanded'] || 20;

		const itemClasses = classnames('airo-wp-image-accordion-item', {
			'airo-wp-image-accordion-item--has-overlay': enableOverlay,
		});

		const overlayStyles = enableOverlay
			? {
					'--airo-wp-overlay-color':
						convertColorToCSSVar(overlayColor),
					'--airo-wp-overlay-opacity': String(overlayOpacity / 100),
					'--airo-wp-overlay-opacity-expanded': String(
						overlayOpacityExpanded / 100
					),
				}
			: {};

		const blockProps = useBlockProps.save({
			className: itemClasses,
			style: {
				...overlayStyles,
				'--airo-wp-vertical-alignment': verticalAlignment || 'center',
				'--airo-wp-horizontal-alignment':
					horizontalAlignment || 'center',
			},
			'data-unique-id': uniqueId,
			role: 'button',
			tabIndex: 0,
		});

		const innerBlocksProps = useInnerBlocksProps.save({
			className: 'airo-wp-image-accordion-item__content',
		});

		return (
			<div {...blockProps}>
				<div {...innerBlocksProps} />
			</div>
		);
	},
};

export default [v1];
