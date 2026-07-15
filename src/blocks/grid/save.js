/**
 * DSG Grid Block - Save Component
 *
 * Saves the block content with declarative styles.
 *
 * @since 1.0.0
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import {
	convertPresetToCSSVar,
	convertColorToCSSVar,
} from '../../utils/convert-preset-to-css-var';
import {
	hasOverlayStyleClass,
	hoverVariationClasses,
} from '../../utils/style-variation-classes';

/**
 * Grid Container Save Component
 *
 * @param {Object} props            Component props
 * @param {Object} props.attributes Block attributes
 * @return {JSX.Element} Save component
 */
export default function GridSave({ attributes }) {
	const {
		tagName = 'div',
		constrainWidth,
		contentWidth,
		columnMinWidth,
		desktopColumns,
		tabletColumns,
		mobileColumns,
		rowGap,
		columnGap,
		alignItems,
		overlayColor,
		hoverBackgroundColor,
		hoverTextColor,
		hoverIconBackgroundColor,
		hoverButtonBackgroundColor,
		style,
	} = attributes;

	// Overlay is enabled by an explicit overlayColor OR by a style-kit overlay
	// variation (is-style-overlay-*) applied via className. In the variation
	// case the color is supplied by the variation's stylesheet, so no inline
	// --airo-wp-overlay-color is emitted below.
	const hasOverlay =
		!!overlayColor || hasOverlayStyleClass(attributes.className);

	// Build className with conditional classes. Hover activation classes are
	// emitted for hover style variations so their class-gated CSS can activate
	// (the inline-`style` gate can't see a variation stylesheet's vars).
	const className = [
		'airo-wp-grid',
		`airo-wp-grid-cols-${desktopColumns}`,
		`airo-wp-grid-cols-tablet-${tabletColumns}`,
		`airo-wp-grid-cols-mobile-${mobileColumns}`,
		!constrainWidth && 'airo-wp-no-width-constraint',
		hasOverlay && 'airo-wp-grid--has-overlay',
		...hoverVariationClasses(attributes.className, 'airo-wp-grid'),
	]
		.filter(Boolean)
		.join(' ');

	// Block wrapper props - outer div stays full width
	const TagName = tagName || 'div';
	const blockProps = useBlockProps.save({
		className,
		style: {
			...(hoverBackgroundColor && {
				'--airo-wp-hover-bg-color':
					convertColorToCSSVar(hoverBackgroundColor),
			}),
			...(hoverTextColor && {
				'--airo-wp-hover-text-color': convertColorToCSSVar(hoverTextColor),
			}),
			...(hoverIconBackgroundColor && {
				'--airo-wp-parent-hover-icon-bg': convertColorToCSSVar(
					hoverIconBackgroundColor
				),
			}),
			...(hoverButtonBackgroundColor && {
				'--airo-wp-parent-hover-button-bg': convertColorToCSSVar(
					hoverButtonBackgroundColor
				),
			}),
			...(overlayColor && {
				'--airo-wp-overlay-color': convertColorToCSSVar(overlayColor),
				'--airo-wp-overlay-opacity': '0.8',
			}),
		},
	});

	// Calculate inner styles declaratively (must match edit.js EXACTLY)
	// IMPORTANT: Always provide a default gap to prevent overlapping items
	// Priority: blockGap (WordPress spacing) → custom rowGap/columnGap → preset fallback
	// WordPress 6.1+ stores blockGap as object {top, left} for separate row/column gaps
	// Also need to convert preset format (var:preset|spacing|X) to CSS variable
	const blockGapValue = style?.spacing?.blockGap;
	const isBlockGapObject =
		typeof blockGapValue === 'object' && blockGapValue !== null;
	const blockGapRow = convertPresetToCSSVar(
		isBlockGapObject ? blockGapValue?.top : blockGapValue
	);
	const blockGapColumn = convertPresetToCSSVar(
		isBlockGapObject ? blockGapValue?.left : blockGapValue
	);
	const defaultGap = 'var(--wp--preset--spacing--50)';

	const innerStyles = {
		display: 'grid',
		gridTemplateColumns: columnMinWidth
			? `repeat(${desktopColumns || 3}, minmax(${columnMinWidth}, 1fr))`
			: `repeat(${desktopColumns || 3}, 1fr)`,
		alignItems: alignItems || 'stretch',
		rowGap: blockGapRow || rowGap || defaultGap,
		columnGap: blockGapColumn || columnGap || defaultGap,
	};

	// Apply width constraints to inner container
	// Use custom contentWidth if set, otherwise fallback to theme's contentSize via CSS variable
	if (constrainWidth) {
		innerStyles.maxWidth =
			contentWidth || 'var(--wp--style--global--content-size, 1140px)';
		innerStyles.marginLeft = 'auto';
		innerStyles.marginRight = 'auto';
	}

	// Merge inner blocks props
	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-grid__inner',
		style: innerStyles,
	});

	return (
		<TagName {...blockProps}>
			<div {...innerBlocksProps} />
		</TagName>
	);
}
