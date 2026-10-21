/**
 * Slider — dot appearance fields.
 *
 * Style and placement for the pagination dots. Only rendered while dots are
 * shown — whether to show them lives in NavigationFields in the Settings
 * panel. Composed inside the Style DsgoInspectorPanel in StylePanel.js.
 */

import { __ } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

/**
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element|null} Dot appearance fields, or null while dots are
 *                             hidden.
 */
export default function DotAppearanceFields({ attributes, setAttributes }) {
	const { showDots, dotStyle, dotPosition } = attributes;

	if (!showDots) {
		return null;
	}

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Dot Style', 'airo-wp')}
				hasValue={() => dotStyle !== 'default'}
				onDeselect={() => setAttributes({ dotStyle: 'default' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Dot Style', 'airo-wp')}
					value={dotStyle}
					options={[
						{
							label: __('Default', 'airo-wp'),
							value: 'default',
						},
						{ label: __('Lines', 'airo-wp'), value: 'lines' },
						{
							label: __('Squares', 'airo-wp'),
							value: 'squares',
						},
					]}
					onChange={(value) => setAttributes({ dotStyle: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Dot Position', 'airo-wp')}
				hasValue={() => dotPosition !== 'inside'}
				onDeselect={() => setAttributes({ dotPosition: 'inside' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Dot Position', 'airo-wp')}
					value={dotPosition}
					options={[
						{ label: __('Inside', 'airo-wp'), value: 'inside' },
						{
							label: __('Outside', 'airo-wp'),
							value: 'outside',
						},
					]}
					onChange={(value) => setAttributes({ dotPosition: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}
