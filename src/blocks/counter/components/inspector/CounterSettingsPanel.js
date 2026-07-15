/**
 * Counter Block - Counter Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for counter values, decimals,
 * and prefix/suffix. Meant to be composed inside the Settings
 * DsgoInspectorPanel in counter/edit.js.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { RangeControl, TextControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

export const CounterSettingsPanel = ({
	startValue,
	endValue,
	decimals,
	prefix,
	suffix,
	setAttributes,
}) => {
	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Start Value', 'airo-wp')}
				hasValue={() => startValue !== 0}
				onDeselect={() => setAttributes({ startValue: 0 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Start Value', 'airo-wp')}
					value={startValue}
					onChange={(value) => setAttributes({ startValue: value })}
					min={0}
					max={endValue}
					help={__('Number to count from', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('End Value', 'airo-wp')}
				hasValue={() => endValue !== 100}
				onDeselect={() => setAttributes({ endValue: 100 })}
				isShownByDefault
			>
				<TextControl
					label={__('End Value', 'airo-wp')}
					type="number"
					value={endValue}
					onChange={(value) =>
						setAttributes({ endValue: parseFloat(value) || 0 })
					}
					help={__('Final number to display', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Decimal Places', 'airo-wp')}
				hasValue={() => decimals !== 0}
				onDeselect={() => setAttributes({ decimals: 0 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Decimal Places', 'airo-wp')}
					value={decimals}
					onChange={(value) => setAttributes({ decimals: value })}
					min={0}
					max={3}
					help={__('Number of decimal places', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Prefix', 'airo-wp')}
				hasValue={() => prefix !== ''}
				onDeselect={() => setAttributes({ prefix: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('Prefix', 'airo-wp')}
					value={prefix}
					onChange={(value) => setAttributes({ prefix: value })}
					placeholder="$"
					help={__(
						'Text before number (e.g., "$", "€")',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Suffix', 'airo-wp')}
				hasValue={() => suffix !== ''}
				onDeselect={() => setAttributes({ suffix: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('Suffix', 'airo-wp')}
					value={suffix}
					onChange={(value) => setAttributes({ suffix: value })}
					placeholder="+"
					help={__(
						'Text after number (e.g., "+", "%", "K")',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
};
