import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { useUniqueBlockId } from '../../hooks';
import HotspotItemInspector from './components/HotspotItemInspector';

const clampCoordinate = (value) =>
	Math.max(0, Math.min(100, Number(value) || 0));

export default function HotspotItemEdit({
	attributes,
	setAttributes,
	clientId,
	context,
}) {
	const [isTooltipOpen, setTooltipOpen] = useState(false);
	const {
		uniqueId,
		x,
		y,
		label,
		icon,
		tooltip,
		tooltipPosition,
		tooltipWidth,
		trigger,
		animation,
		sequenceOrder,
		originX,
		originY,
	} = attributes;
	useUniqueBlockId({
		clientId,
		attributeName: 'uniqueId',
		value: uniqueId,
		setAttributes,
		prefix: 'hotspot-',
	});
	const effectivePosition =
		tooltipPosition === 'inherit'
			? context['airo-wp/hotspot/tooltipPosition'] || 'top'
			: tooltipPosition;
	const effectiveWidth =
		typeof tooltipWidth === 'number'
			? tooltipWidth
			: context['airo-wp/hotspot/tooltipWidth'] || 240;
	const effectiveAnimation =
		animation === 'inherit'
			? context.airowp_hotspot_animation || 'none'
			: animation;
	const effectiveTrigger =
		trigger === 'inherit'
			? context.airowp_hotspot_trigger || 'click'
			: trigger;
	const markerAccessibleLabel =
		icon || !label || label === '+' ? __('Hotspot', 'airo-wp') : undefined;
	const blockProps = useBlockProps({
		className: `airo-wp-hotspot-item airo-wp-hotspot-item--position-${effectivePosition} airo-wp-hotspot-item--animation-${effectiveAnimation} airo-wp-hotspot-item--origin-x-${originX} airo-wp-hotspot-item--origin-y-${originY}`,
		style: {
			'--airo-wp-hotspot-x': `${clampCoordinate(x)}%`,
			'--airo-wp-hotspot-y': `${clampCoordinate(y)}%`,
			'--airo-wp-hotspot-tooltip-width': `${effectiveWidth}px`,
			'--airo-wp-hotspot-sequence-order': String(sequenceOrder),
			'--airo-wp-hotspot-origin-x': originX,
			'--airo-wp-hotspot-origin-y': originY,
		},
		'data-airo-wp-hotspot-item-editor': clientId,
		'data-airo-wp-hotspot-trigger': effectiveTrigger,
	});

	return (
		<>
			<InspectorControls>
				<HotspotItemInspector
					attributes={attributes}
					setAttributes={setAttributes}
					clientId={clientId}
				/>
			</InspectorControls>
			<div {...blockProps}>
				<button
					className="airo-wp-hotspot-item__marker"
					type="button"
					aria-label={markerAccessibleLabel}
					aria-expanded={isTooltipOpen}
					onClick={(event) => {
						event.preventDefault();
						setTooltipOpen((isOpen) => !isOpen);
					}}
				>
					{icon || label || '+'}
				</button>
				<RichText
					tagName="span"
					className={`airo-wp-hotspot-item__tooltip${
						isTooltipOpen ? ' is-open' : ''
					}`}
					value={tooltip}
					onChange={(value) => setAttributes({ tooltip: value })}
					placeholder={__('Describe this hotspot…', 'airo-wp')}
				/>
			</div>
		</>
	);
}
