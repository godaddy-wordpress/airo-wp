import { RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { getSafeHotspotUrl } from './utils';

const clampCoordinate = (value) =>
	Math.max(
		0,
		Math.min(100, Number.isFinite(Number(value)) ? Number(value) : 50)
	);

export default function HotspotItemSave({ attributes }) {
	const {
		uniqueId,
		x,
		y,
		originX,
		originY,
		label,
		icon,
		url,
		tooltip,
		tooltipPosition,
		tooltipWidth,
		trigger,
		animation,
		sequenceOrder,
	} = attributes;
	const markerId = `airo-wp-hotspot-marker-${uniqueId || 'item'}`;
	const tooltipId = `airo-wp-hotspot-tooltip-${uniqueId || 'item'}`;
	const safeUrl = getSafeHotspotUrl(url);
	const markerAccessibleLabel =
		icon || !label || label === '+' ? __('Hotspot', 'airo-wp') : undefined;
	const isLinkedMarker = !!safeUrl;
	const markerProps = {
		className: 'airo-wp-hotspot-item__marker',
		id: markerId,
		...(!isLinkedMarker &&
			trigger === 'click' && {
				'aria-expanded': 'false',
				'aria-controls': tooltipId,
			}),
		...(isLinkedMarker || trigger === 'hover'
			? { 'aria-describedby': tooltipId }
			: {}),
		'aria-label': markerAccessibleLabel,
		'data-airo-wp-hotspot-marker': 'true',
	};
	const blockProps = useBlockProps.save({
		className: `airo-wp-hotspot-item airo-wp-hotspot-item--position-${tooltipPosition} airo-wp-hotspot-item--animation-${animation} airo-wp-hotspot-item--origin-x-${originX} airo-wp-hotspot-item--origin-y-${originY}`,
		style: {
			'--airo-wp-hotspot-x': `${clampCoordinate(x)}%`,
			'--airo-wp-hotspot-y': `${clampCoordinate(y)}%`,
			...(typeof tooltipWidth === 'number' && {
				'--airo-wp-hotspot-tooltip-width': `${tooltipWidth}px`,
			}),
			'--airo-wp-hotspot-sequence-order': String(sequenceOrder),
			'--airo-wp-hotspot-origin-x': originX,
			'--airo-wp-hotspot-origin-y': originY,
		},
		'data-airo-wp-hotspot-item': 'true',
		'data-airo-wp-hotspot-trigger':
			trigger === 'inherit' ? undefined : trigger,
	});
	const markerContent = icon || label || '+';

	return (
		<div {...blockProps}>
			{safeUrl ? (
				<a {...markerProps} href={safeUrl}>
					{markerContent}
				</a>
			) : (
				<button {...markerProps} type="button">
					{markerContent}
				</button>
			)}
			<div
				className="airo-wp-hotspot-item__tooltip"
				id={tooltipId}
				role="tooltip"
				data-airo-wp-hotspot-tooltip="true"
				hidden
				aria-hidden="true"
			>
				<RichText.Content tagName="span" value={tooltip} />
			</div>
		</div>
	);
}
