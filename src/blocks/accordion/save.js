import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';

export default function AccordionSave({ attributes }) {
	const {
		allowMultipleOpen,
		iconStyle,
		iconPosition,
		borderBetween,
		borderBetweenColor,
		itemGap,
		openBackgroundColor,
		openTextColor,
		hoverBackgroundColor,
		hoverTextColor,
	} = attributes;

	// Smart default: hover mirrors open unless explicitly set
	const effectiveHoverBg = hoverBackgroundColor || openBackgroundColor;
	const effectiveHoverText = hoverTextColor || openTextColor;

	// Same classes as edit.js - MUST MATCH EXACTLY
	const accordionClasses = classnames('airo-wp-accordion', {
		'airo-wp-accordion--multiple': allowMultipleOpen,
		'airo-wp-accordion--icon-left': iconPosition === 'left',
		'airo-wp-accordion--icon-right': iconPosition === 'right',
		'airo-wp-accordion--no-icon': iconStyle === 'none',
		'airo-wp-accordion--border-between': borderBetween,
	});

	// Apply colors and gap as CSS custom properties that will cascade to accordion items
	const customStyles = {
		'--airo-wp-accordion-open-bg':
			convertColorToCSSVar(openBackgroundColor),
		'--airo-wp-accordion-open-text': convertColorToCSSVar(openTextColor),
		'--airo-wp-accordion-hover-bg': convertColorToCSSVar(effectiveHoverBg),
		'--airo-wp-accordion-hover-text':
			convertColorToCSSVar(effectiveHoverText),
		'--airo-wp-accordion-gap': itemGap,
		...(borderBetweenColor && {
			'--airo-wp-accordion-border-color':
				convertColorToCSSVar(borderBetweenColor),
		}),
	};

	// Use .save() variant for save function
	const blockProps = useBlockProps.save({
		className: accordionClasses,
		style: customStyles,
		'data-allow-multiple': allowMultipleOpen,
		'data-icon-style': iconStyle,
	});

	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-accordion__items',
	});

	return (
		<div {...blockProps}>
			<div {...innerBlocksProps} />
		</div>
	);
}
