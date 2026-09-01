import { __ } from '@wordpress/i18n';
import {
	RangeControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared/DsgoInspectorPanel';
import { getSafeHotspotUrl } from '../utils';

const INHERIT_OPTIONS = [
	{ label: __('Inherit from Hotspot', 'airo-wp'), value: 'inherit' },
];

export default function HotspotItemInspector({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		label,
		icon,
		url,
		tooltipPosition,
		tooltipWidth,
		trigger,
		animation,
		sequenceOrder,
		originX,
		originY,
	} = attributes;
	return (
		<>
			<DsgoInspectorPanel
				title={__('Settings', 'airo-wp')}
				panelName="settings"
				panelId={clientId}
				resetAll={() =>
					setAttributes({
						label: '+',
						icon: '',
						url: '',
						tooltipPosition: 'inherit',
						tooltipWidth: undefined,
						trigger: 'inherit',
					})
				}
			>
				<DsgoInspectorPanel.Item
					label={__('Label', 'airo-wp')}
					hasValue={() => label !== '+'}
					onDeselect={() => setAttributes({ label: '+' })}
					isShownByDefault
				>
					<TextControl
						label={__('Label', 'airo-wp')}
						value={label}
						onChange={(value) => setAttributes({ label: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Icon text', 'airo-wp')}
					hasValue={() => !!icon}
					onDeselect={() => setAttributes({ icon: '' })}
					isShownByDefault
				>
					<TextControl
						label={__('Icon text', 'airo-wp')}
						value={icon}
						onChange={(value) => setAttributes({ icon: value })}
						help={__(
							'Optional symbol shown instead of the label.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Link', 'airo-wp')}
					hasValue={() => !!url}
					onDeselect={() => setAttributes({ url: '' })}
					isShownByDefault
				>
					<TextControl
						type="url"
						label={__('Link', 'airo-wp')}
						value={url}
						// Store the raw value. Sanitizing per keystroke wipes
						// the field at the intermediate `http:` and `http://`
						// states, which makes an http link untypable by hand.
						// `save()` applies getSafeHotspotUrl at render time, so
						// an unusable value is never emitted as markup.
						onChange={(value) => setAttributes({ url: value })}
						help={
							url && !getSafeHotspotUrl(url)
								? __(
										'Only https, http, mailto, and tel links are used. This link will be ignored.',
										'airo-wp'
									)
								: undefined
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Trigger override', 'airo-wp')}
					hasValue={() => trigger !== 'inherit'}
					onDeselect={() => setAttributes({ trigger: 'inherit' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Trigger override', 'airo-wp')}
						value={trigger}
						options={[
							...INHERIT_OPTIONS,
							{
								label: __('Click', 'airo-wp'),
								value: 'click',
							},
							{
								label: __('Hover', 'airo-wp'),
								value: 'hover',
							},
						]}
						onChange={(value) => setAttributes({ trigger: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Tooltip position', 'airo-wp')}
					hasValue={() => tooltipPosition !== 'inherit'}
					onDeselect={() =>
						setAttributes({ tooltipPosition: 'inherit' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Tooltip position', 'airo-wp')}
						value={tooltipPosition}
						options={[
							...INHERIT_OPTIONS,
							...['top', 'right', 'bottom', 'left'].map(
								(value) => ({
									label:
										value[0].toUpperCase() + value.slice(1),
									value,
								})
							),
						]}
						onChange={(value) =>
							setAttributes({ tooltipPosition: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Tooltip width', 'airo-wp')}
					hasValue={() => typeof tooltipWidth === 'number'}
					onDeselect={() =>
						setAttributes({ tooltipWidth: undefined })
					}
					isShownByDefault
				>
					<RangeControl
						label={__('Tooltip width', 'airo-wp')}
						value={tooltipWidth}
						onChange={(value) =>
							setAttributes({ tooltipWidth: value })
						}
						min={120}
						max={600}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			</DsgoInspectorPanel>
			<DsgoInspectorPanel
				title={__('Style', 'airo-wp')}
				panelName="style"
				panelId={clientId}
				resetAll={() =>
					setAttributes({
						animation: 'inherit',
						sequenceOrder: 0,
						originX: 'center',
						originY: 'center',
					})
				}
			>
				<DsgoInspectorPanel.Item
					label={__('Marker animation', 'airo-wp')}
					hasValue={() => animation !== 'inherit'}
					onDeselect={() => setAttributes({ animation: 'inherit' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Marker animation', 'airo-wp')}
						value={animation}
						options={[
							...INHERIT_OPTIONS,
							{
								label: __('Pulse', 'airo-wp'),
								value: 'pulse',
							},
							{
								label: __('Scale', 'airo-wp'),
								value: 'scale',
							},
							{
								label: __('Fade', 'airo-wp'),
								value: 'fade',
							},
							{ label: __('None', 'airo-wp'), value: 'none' },
						]}
						onChange={(value) =>
							setAttributes({ animation: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Sequence order', 'airo-wp')}
					hasValue={() => sequenceOrder !== 0}
					onDeselect={() => setAttributes({ sequenceOrder: 0 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Sequence order', 'airo-wp')}
						value={sequenceOrder}
						onChange={(value) =>
							setAttributes({ sequenceOrder: value })
						}
						min={0}
						max={20}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Horizontal origin', 'airo-wp')}
					hasValue={() => originX !== 'center'}
					onDeselect={() => setAttributes({ originX: 'center' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Horizontal origin', 'airo-wp')}
						value={originX}
						options={['left', 'center', 'right'].map((value) => ({
							label: value[0].toUpperCase() + value.slice(1),
							value,
						}))}
						onChange={(value) => setAttributes({ originX: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Vertical origin', 'airo-wp')}
					hasValue={() => originY !== 'center'}
					onDeselect={() => setAttributes({ originY: 'center' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Vertical origin', 'airo-wp')}
						value={originY}
						options={['top', 'center', 'bottom'].map((value) => ({
							label: value[0].toUpperCase() + value.slice(1),
							value,
						}))}
						onChange={(value) => setAttributes({ originY: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			</DsgoInspectorPanel>
			<DsgoInspectorPanel
				title={__('Advanced', 'airo-wp')}
				panelName="advanced"
				panelId={clientId}
				resetAll={() => {}}
			/>
		</>
	);
}
