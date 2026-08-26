import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';
import classnames from 'classnames';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';

/**
 * Validates URL protocol to prevent javascript: and other dangerous protocols.
 * @param {string} url - The URL to validate.
 * @return {string|null} - The safe URL or null if invalid.
 */
const getSafeUrl = (url) => {
	if (!url) {
		return null;
	}
	try {
		const parsed = new URL(url, window.location.origin);
		const allowedProtocols = ['https:', 'http:', 'mailto:', 'tel:'];
		if (allowedProtocols.includes(parsed.protocol)) {
			return url;
		}
		return null;
	} catch {
		// Relative URLs are okay
		if (url.startsWith('/') || url.startsWith('#')) {
			return url;
		}
		return null;
	}
};

// Marker shape SVGs - same as edit.js
const MarkerShapes = {
	circle: ({ size, fillColor, borderColor }) => (
		<svg
			width={size}
			height={size}
			viewBox="0 0 24 24"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
		>
			<circle
				cx="12"
				cy="12"
				r="10"
				fill={fillColor}
				stroke={borderColor}
				strokeWidth="2"
			/>
		</svg>
	),
	square: ({ size, fillColor, borderColor }) => (
		<svg
			width={size}
			height={size}
			viewBox="0 0 24 24"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
		>
			<rect
				x="2"
				y="2"
				width="20"
				height="20"
				rx="2"
				fill={fillColor}
				stroke={borderColor}
				strokeWidth="2"
			/>
		</svg>
	),
	diamond: ({ size, fillColor, borderColor }) => (
		<svg
			width={size}
			height={size}
			viewBox="0 0 24 24"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
			aria-hidden="true"
		>
			<rect
				x="12"
				y="2"
				width="14"
				height="14"
				rx="2"
				transform="rotate(45 12 2)"
				fill={fillColor}
				stroke={borderColor}
				strokeWidth="2"
			/>
		</svg>
	),
};

export default function TimelineItemSave({ attributes, context }) {
	const {
		date,
		title,
		imageUrl,
		isActive,
		linkUrl,
		linkTarget,
		customMarkerColor,
	} = attributes;

	// Get context from parent timeline
	const markerStyle = context?.['airo-wp/timeline/markerStyle'] || 'circle';
	const markerSize = context?.['airo-wp/timeline/markerSize'] || 16;
	const markerColor = context?.['airo-wp/timeline/markerColor'] || '';
	const markerBorderColor =
		context?.['airo-wp/timeline/markerBorderColor'] || '';

	// Determine marker colors (custom overrides parent)
	const effectiveMarkerColor =
		customMarkerColor ||
		markerColor ||
		'var(--wp--preset--color--primary, #2563eb)';
	const effectiveBorderColor = markerBorderColor || effectiveMarkerColor;

	// Get the marker shape component
	const MarkerShape = MarkerShapes[markerStyle] || MarkerShapes.circle;

	// Validate URL protocol for security
	const safeUrl = getSafeUrl(linkUrl);

	// Build class names - must match edit.js
	const itemClasses = classnames('airo-wp-timeline-item', {
		'airo-wp-timeline-item--active': isActive,
		'airo-wp-timeline-item--has-image': imageUrl,
		'airo-wp-timeline-item--has-link': safeUrl,
	});

	// Custom styles for marker
	const customStyles = customMarkerColor
		? {
				'--airo-wp-timeline-item-marker-color':
					convertColorToCSSVar(customMarkerColor),
			}
		: {};

	const blockProps = useBlockProps.save({
		className: itemClasses,
		style: customStyles,
	});

	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-timeline-item__content',
	});

	// Render the marker
	const renderMarker = () => (
		<div className="airo-wp-timeline-item__marker" aria-hidden="true">
			{imageUrl ? (
				<img
					src={imageUrl}
					alt=""
					className="airo-wp-timeline-item__marker-image"
					style={{
						width: markerSize,
						height: markerSize,
						borderRadius: markerStyle === 'circle' ? '50%' : '4px',
						objectFit: 'cover',
					}}
				/>
			) : (
				<MarkerShape
					size={markerSize}
					fillColor={effectiveMarkerColor}
					borderColor={effectiveBorderColor}
				/>
			)}
		</div>
	);

	// Render the content
	const renderContent = () => (
		<div className="airo-wp-timeline-item__wrapper">
			{date && (
				<RichText.Content
					tagName="span"
					className="airo-wp-timeline-item__date"
					value={date}
				/>
			)}

			{title && (
				<RichText.Content
					tagName="h3"
					className="airo-wp-timeline-item__title"
					value={title}
				/>
			)}

			<div {...innerBlocksProps} />
		</div>
	);

	// If there's a safe link, wrap the entire item
	if (safeUrl) {
		const linkRel =
			linkTarget === '_blank' ? 'noopener noreferrer' : undefined;

		return (
			<div {...blockProps}>
				{renderMarker()}
				<a
					href={safeUrl}
					target={linkTarget}
					rel={linkRel}
					className="airo-wp-timeline-item__link"
				>
					{renderContent()}
				</a>
			</div>
		);
	}

	return (
		<div {...blockProps}>
			{renderMarker()}
			{renderContent()}
		</div>
	);
}
