/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { ToggleControl, SelectControl, Notice } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

/**
 * Layout options
 */
const LAYOUT_OPTIONS = [
	{
		label: __('Boxed (Default)', 'airo-wp'),
		value: 'boxed',
	},
	{
		label: __('Inline', 'airo-wp'),
		value: 'inline',
	},
	{
		label: __('Compact', 'airo-wp'),
		value: 'compact',
	},
];

/**
 * Display Panel component
 *
 * Renders DsgoInspectorPanel.Item entries for layout + unit visibility.
 * Meant to be composed inside the Settings DsgoInspectorPanel in
 * countdown-timer/edit.js.
 *
 * @param {Object}   props               - Component properties
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to update attributes
 * @return {JSX.Element} Item fragment
 */
export default function DisplayPanel({ attributes, setAttributes }) {
	const { showDays, showHours, showMinutes, showSeconds, layout } =
		attributes;

	// Check if at least one unit is visible
	const hasVisibleUnit = showDays || showHours || showMinutes || showSeconds;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Layout Style', 'airo-wp')}
				hasValue={() => layout !== 'boxed'}
				onDeselect={() => setAttributes({ layout: 'boxed' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Layout Style', 'airo-wp')}
					value={layout}
					options={LAYOUT_OPTIONS}
					onChange={(newLayout) =>
						setAttributes({ layout: newLayout })
					}
					help={__(
						'Choose how the countdown units are displayed.',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Days', 'airo-wp')}
				hasValue={() => showDays !== true}
				onDeselect={() => setAttributes({ showDays: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Days', 'airo-wp')}
					checked={showDays}
					onChange={(value) => setAttributes({ showDays: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Hours', 'airo-wp')}
				hasValue={() => showHours !== true}
				onDeselect={() => setAttributes({ showHours: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Hours', 'airo-wp')}
					checked={showHours}
					onChange={(value) => setAttributes({ showHours: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Minutes', 'airo-wp')}
				hasValue={() => showMinutes !== true}
				onDeselect={() => setAttributes({ showMinutes: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Minutes', 'airo-wp')}
					checked={showMinutes}
					onChange={(value) => setAttributes({ showMinutes: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Seconds', 'airo-wp')}
				hasValue={() => showSeconds !== true}
				onDeselect={() => setAttributes({ showSeconds: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Seconds', 'airo-wp')}
					checked={showSeconds}
					onChange={(value) => setAttributes({ showSeconds: value })}
					__nextHasNoMarginBottom
				/>
				{!hasVisibleUnit && (
					<Notice status="warning" isDismissible={false}>
						{__(
							'At least one time unit should be visible.',
							'airo-wp'
						)}
					</Notice>
				)}
			</DsgoInspectorPanel.Item>
		</>
	);
}
