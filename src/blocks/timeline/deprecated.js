/**
 * Timeline Block - Deprecations
 *
 * v1: save() before Marker Size became inheritable. markerSize defaulted to
 * 16 and save() always wrote `--airo-wp-timeline-marker-size: 16px` inline, so a
 * theme could never change the default marker size. markerSize now has no
 * default; save() writes the custom property only for an explicit size, and
 * style.scss otherwise resolves it from the theme token
 * (settings.custom.airowp.timeline.defaultSize).
 *
 * No isEligible: a timeline saved at the old default no longer matches the
 * current save() (it carries the inline 16px), and this frozen copy — parsed
 * with the old `default: 16` — reproduces it. One with an explicit size still
 * matches the current save() byte-for-byte and never reaches here.
 *
 * migrate() drops the implicit 16 so migrated timelines inherit. WordPress
 * never serializes a default-valued attribute, so a stored 16 can only be the
 * old default, never an author's explicit choice.
 *
 * @package
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import metadata from './block.json';

function saveV1({ attributes }) {
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

const v1 = {
	apiVersion: 3,
	attributes: {
		...metadata.attributes,
		markerSize: {
			type: 'number',
			default: 16,
		},
	},
	supports: metadata.supports,
	save: saveV1,
	migrate(attributes) {
		if (attributes.markerSize !== 16) {
			return attributes;
		}

		const { markerSize, ...rest } = attributes;
		return rest;
	},
};

export default [v1];
