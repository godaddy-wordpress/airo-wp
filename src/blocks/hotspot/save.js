import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { getSafeHotspotColor } from '../hotspot-item/utils';

export default function HotspotSave({ attributes }) {
	const {
		imageUrl,
		imageAlt,
		trigger,
		tooltipPosition,
		tooltipWidth,
		animation,
		sequenceDuration,
		markerColor,
		markerBackgroundColor,
		tooltipBackgroundColor,
		tooltipTextColor,
	} = attributes;
	const safeMarkerColor = getSafeHotspotColor(markerColor);
	const safeMarkerBackgroundColor = getSafeHotspotColor(
		markerBackgroundColor
	);
	const safeTooltipBackgroundColor = getSafeHotspotColor(
		tooltipBackgroundColor
	);
	const safeTooltipTextColor = getSafeHotspotColor(tooltipTextColor);
	const blockProps = useBlockProps.save({
		className: `airo-wp-hotspot airo-wp-hotspot--position-${tooltipPosition} airo-wp-hotspot--animation-${animation}`,
		style: {
			'--airo-wp-hotspot-tooltip-width': `${tooltipWidth}px`,
			'--airo-wp-hotspot-sequence-duration': `${sequenceDuration}ms`,
			...(safeMarkerColor && {
				'--airo-wp-hotspot-marker-color':
					convertColorToCSSVar(safeMarkerColor),
			}),
			...(safeMarkerBackgroundColor && {
				'--airo-wp-hotspot-marker-background': convertColorToCSSVar(
					safeMarkerBackgroundColor
				),
			}),
			...(safeTooltipBackgroundColor && {
				'--airo-wp-hotspot-tooltip-background': convertColorToCSSVar(
					safeTooltipBackgroundColor
				),
			}),
			...(safeTooltipTextColor && {
				'--airo-wp-hotspot-tooltip-color':
					convertColorToCSSVar(safeTooltipTextColor),
			}),
		},
		'data-airo-wp-hotspot': 'true',
		'data-airo-wp-hotspot-trigger': trigger,
		'data-airo-wp-hotspot-position': tooltipPosition,
		'data-airo-wp-hotspot-animation': animation,
	});
	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-hotspot__items',
	});

	return (
		<div {...blockProps}>
			<div className="airo-wp-hotspot__image-wrap">
				{imageUrl ? (
					<img
						className="airo-wp-hotspot__image"
						src={imageUrl}
						alt={imageAlt}
					/>
				) : (
					<div className="airo-wp-hotspot__image airo-wp-hotspot__image--empty" />
				)}
				<div {...innerBlocksProps} />
			</div>
		</div>
	);
}
