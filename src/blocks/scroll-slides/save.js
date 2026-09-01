/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { overlayOpacityFraction } from '../../utils/overlay-opacity';

export default function Save({ attributes }) {
	const {
		minHeight,
		maxHeight,
		constrainWidth,
		contentWidth,
		overlayColor,
		overlayOpacity,
		navColor,
		navActiveColor,
	} = attributes;

	const className = [
		'airo-wp-scroll-slides',
		overlayColor && 'airo-wp-scroll-slides--has-overlay',
		!constrainWidth && 'airo-wp-scroll-slides--no-width-constraint',
		(navColor || navActiveColor) && 'airo-wp-scroll-slides--has-nav-color',
	]
		.filter(Boolean)
		.join(' ');

	const blockProps = useBlockProps.save({
		className,
		'data-airo-wp-min-height': minHeight || '100vh',
		...(maxHeight && { 'data-airo-wp-max-height': maxHeight }),
		style: {
			...(overlayColor && {
				'--airo-wp-overlay-color': convertColorToCSSVar(overlayColor),
				'--airo-wp-overlay-opacity': String(
					overlayOpacityFraction(overlayOpacity)
				),
			}),
			...(navColor && {
				'--airo-wp-nav-color': convertColorToCSSVar(navColor),
			}),
			...(navActiveColor && {
				'--airo-wp-nav-active-color':
					convertColorToCSSVar(navActiveColor),
			}),
		},
	});

	const innerStyle = {};
	if (constrainWidth) {
		innerStyle.maxWidth =
			contentWidth || 'var(--wp--style--global--content-size, 1140px)';
		innerStyle.marginLeft = 'auto';
		innerStyle.marginRight = 'auto';
	}

	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-scroll-slides__panels',
	});

	return (
		<div {...blockProps}>
			<div className="airo-wp-scroll-slides__inner" style={innerStyle}>
				<div {...innerBlocksProps} />
			</div>
		</div>
	);
}
