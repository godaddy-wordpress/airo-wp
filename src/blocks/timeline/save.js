import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';

export default function TimelineSave({ attributes }) {
	const {
		orientation,
		layout,
		lineColor,
		lineThickness,
		connectorStyle,
		markerStyle,
		markerSize,
		markerColor,
		markerBorderColor,
		itemSpacing,
		animateOnScroll,
		animationDuration,
		staggerDelay,
	} = attributes;

	// CSS custom properties - must match edit.js exactly
	const customStyles = {
		'--airo-wp-timeline-line-color':
			lineColor || 'var(--wp--preset--color--contrast, #e5e7eb)',
		'--airo-wp-timeline-line-thickness': `${lineThickness}px`,
		'--airo-wp-timeline-connector-style': connectorStyle,
		'--airo-wp-timeline-marker-size': `${markerSize}px`,
		'--airo-wp-timeline-marker-color':
			markerColor || 'var(--wp--preset--color--primary, #2563eb)',
		'--airo-wp-timeline-marker-border-color':
			markerBorderColor ||
			markerColor ||
			'var(--wp--preset--color--primary, #2563eb)',
		'--airo-wp-timeline-item-spacing': itemSpacing,
		'--airo-wp-timeline-animation-duration': `${animationDuration}ms`,
	};

	// Build class names - must match edit.js exactly
	const timelineClasses = classnames('airo-wp-timeline', {
		[`airo-wp-timeline--${orientation}`]: orientation,
		[`airo-wp-timeline--layout-${layout}`]: layout,
		[`airo-wp-timeline--marker-${markerStyle}`]: markerStyle,
		'airo-wp-timeline--animate': animateOnScroll,
	});

	const blockProps = useBlockProps.save({
		className: timelineClasses,
		style: customStyles,
		'data-animate': animateOnScroll,
		'data-animation-duration': animationDuration,
		'data-stagger-delay': staggerDelay,
	});

	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-timeline__items',
	});

	return (
		<div {...blockProps}>
			<div className="airo-wp-timeline__line" aria-hidden="true" />
			<div {...innerBlocksProps} />
		</div>
	);
}
