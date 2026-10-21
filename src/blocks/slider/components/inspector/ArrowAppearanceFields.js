/**
 * Slider — arrow appearance fields.
 *
 * Style, placement, size, and padding for the prev/next arrows. Only
 * rendered while arrows are shown — whether to show them lives in
 * NavigationFields in the Settings panel. Composed inside the Style
 * DsgoInspectorPanel in StylePanel.js.
 */

import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

/**
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element|null} Arrow appearance fields, or null while arrows
 *                             are hidden.
 */
export default function ArrowAppearanceFields({ attributes, setAttributes }) {
	const {
		showArrows,
		arrowStyle,
		arrowPosition,
		arrowVerticalPosition,
		arrowSize,
		arrowPadding,
	} = attributes;

	if (!showArrows) {
		return null;
	}

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Arrow Style', 'airo-wp')}
				hasValue={() => arrowStyle !== 'default'}
				onDeselect={() => setAttributes({ arrowStyle: 'default' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Arrow Style', 'airo-wp')}
					value={arrowStyle}
					options={[
						{
							label: __('Default', 'airo-wp'),
							value: 'default',
						},
						{ label: __('Circle', 'airo-wp'), value: 'circle' },
						{ label: __('Square', 'airo-wp'), value: 'square' },
						{
							label: __('Minimal', 'airo-wp'),
							value: 'minimal',
						},
					]}
					onChange={(value) => setAttributes({ arrowStyle: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Arrow Position (Horizontal)', 'airo-wp')}
				hasValue={() => arrowPosition !== 'sides'}
				onDeselect={() => setAttributes({ arrowPosition: 'sides' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Arrow Position (Horizontal)', 'airo-wp')}
					value={arrowPosition}
					options={[
						{ label: __('Sides', 'airo-wp'), value: 'sides' },
						{ label: __('Inside', 'airo-wp'), value: 'inside' },
						{
							label: __('Outside', 'airo-wp'),
							value: 'outside',
						},
					]}
					onChange={(value) =>
						setAttributes({ arrowPosition: value })
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Arrow Position (Vertical)', 'airo-wp')}
				hasValue={() => arrowVerticalPosition !== 'center'}
				onDeselect={() =>
					setAttributes({ arrowVerticalPosition: 'center' })
				}
				isShownByDefault
			>
				<SelectControl
					label={__('Arrow Position (Vertical)', 'airo-wp')}
					value={arrowVerticalPosition}
					options={[
						{ label: __('Top', 'airo-wp'), value: 'top' },
						{ label: __('Center', 'airo-wp'), value: 'center' },
						{ label: __('Bottom', 'airo-wp'), value: 'bottom' },
					]}
					onChange={(value) =>
						setAttributes({ arrowVerticalPosition: value })
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Arrow Size', 'airo-wp')}
				hasValue={() => arrowSize !== '24px'}
				onDeselect={() => setAttributes({ arrowSize: '24px' })}
				isShownByDefault
			>
				<UnitControl
					label={__('Arrow Size', 'airo-wp')}
					value={arrowSize}
					onChange={(value) => setAttributes({ arrowSize: value })}
					units={[
						{ value: 'px', label: 'px' },
						{ value: 'rem', label: 'rem' },
						{ value: 'em', label: 'em' },
					]}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Arrow Padding', 'airo-wp')}
				hasValue={() => arrowPadding !== ''}
				onDeselect={() => setAttributes({ arrowPadding: '' })}
				isShownByDefault
			>
				<UnitControl
					label={__('Arrow Padding', 'airo-wp')}
					value={arrowPadding}
					onChange={(value) => setAttributes({ arrowPadding: value })}
					units={[
						{ value: 'px', label: 'px' },
						{ value: 'rem', label: 'rem' },
						{ value: 'em', label: 'em' },
					]}
					help={__('Inner spacing of the arrow button', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}
