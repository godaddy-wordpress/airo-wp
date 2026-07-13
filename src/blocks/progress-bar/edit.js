/**
 * Progress Bar Block - Edit Component
 *
 * Provides a visual progress bar with customizable appearance and animations.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	RangeControl,
	ToggleControl,
	SelectControl,
	TextControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';

/**
 * Edit component for Progress Bar block
 *
 * @param {Object}   props               - Component props
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to update attributes
 * @param {string}   props.clientId      - Block client ID
 * @return {JSX.Element} Edit component
 */
export default function ProgressBarEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		percentage,
		barColor,
		barBackgroundColor,
		height,
		borderRadius,
		showLabel,
		labelText,
		showPercentage,
		labelPosition,
		barStyle,
		animateOnScroll,
		animationDuration,
		stripedAnimation,
	} = attributes;

	// Get theme color palette and gradient settings
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// Calculate bar width (clamped between 0-100)
	const barWidth = Math.min(Math.max(percentage, 0), 100);

	// Resolve chosen colors; omit when unset so the CSS default (shared with
	// the frontend) paints instead of a baked hex — keeps the editor preview
	// matching the saved markup.
	const barFillColor = convertColorToCSSVar(barColor);
	const barTrackColor = convertColorToCSSVar(barBackgroundColor);

	// Build bar fill styles declaratively
	const barFillStyles = {
		width: `${barWidth}%`,
		height: '100%',
		backgroundColor: barFillColor || undefined,
		transition: `width ${animationDuration}s ease-out`,
		borderRadius,
	};

	// Add striped background if enabled
	if (barStyle === 'striped' || barStyle === 'striped-animated') {
		barFillStyles.backgroundImage =
			'linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent)';
		barFillStyles.backgroundSize = '1rem 1rem';
	}

	// Build bar container styles
	const barContainerStyles = {
		width: '100%',
		height,
		backgroundColor: barTrackColor || undefined,
		borderRadius,
		overflow: 'hidden',
		position: 'relative',
	};

	// Build label display text
	const displayText = (() => {
		const parts = [];
		if (showLabel && labelText) {
			parts.push(labelText);
		}
		if (showPercentage) {
			parts.push(`${barWidth}%`);
		}
		return parts.join(' - ') || __('Progress Bar', 'airo-wp');
	})();

	// Get block props
	const blockProps = useBlockProps({
		className: 'airo-wp-progress-bar',
	});

	return (
		<>
			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Color Settings', 'airo-wp')}
					settings={[
						{
							label: __('Bar Color', 'airo-wp'),
							colorValue: decodeColorValue(
								barColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									barColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Background Color', 'airo-wp'),
							colorValue: decodeColorValue(
								barBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									barBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
					]}
					{...colorGradientSettings}
				/>
			</InspectorControls>

			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							percentage: 75,
							height: '20px',
							borderRadius: '4px',
							barStyle: 'solid',
							showLabel: true,
							labelText: '',
							showPercentage: true,
							labelPosition: 'top',
							animateOnScroll: true,
							animationDuration: 1.5,
							stripedAnimation: false,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Percentage', 'airo-wp')}
						hasValue={() => percentage !== 75}
						onDeselect={() => setAttributes({ percentage: 75 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Percentage', 'airo-wp')}
							value={percentage}
							onChange={(value) =>
								setAttributes({ percentage: value })
							}
							min={0}
							max={100}
							step={1}
							help={__(
								'Set the progress percentage (0–100)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Bar Height', 'airo-wp')}
						hasValue={() => height !== '20px'}
						onDeselect={() => setAttributes({ height: '20px' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Bar Height', 'airo-wp')}
							value={height}
							onChange={(value) =>
								setAttributes({ height: value })
							}
							units={[
								{ value: 'px', label: 'px' },
								{ value: 'em', label: 'em' },
								{ value: 'rem', label: 'rem' },
							]}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Border Radius', 'airo-wp')}
						hasValue={() => borderRadius !== '4px'}
						onDeselect={() =>
							setAttributes({ borderRadius: '4px' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Border Radius', 'airo-wp')}
							value={borderRadius}
							onChange={(value) =>
								setAttributes({ borderRadius: value })
							}
							units={[
								{ value: 'px', label: 'px' },
								{ value: 'em', label: 'em' },
								{ value: '%', label: '%' },
							]}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Bar Style', 'airo-wp')}
						hasValue={() => barStyle !== 'solid'}
						onDeselect={() => setAttributes({ barStyle: 'solid' })}
						isShownByDefault
					>
						<ToggleGroupControl
							label={__('Bar Style', 'airo-wp')}
							value={barStyle}
							onChange={(value) =>
								setAttributes({ barStyle: value })
							}
							isBlock
						>
							<ToggleGroupControlOption
								value="solid"
								label={__('Solid', 'airo-wp')}
							/>
							<ToggleGroupControlOption
								value="striped"
								label={__('Striped', 'airo-wp')}
							/>
							<ToggleGroupControlOption
								value="striped-animated"
								label={__('Animated', 'airo-wp')}
							/>
						</ToggleGroupControl>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Show Label', 'airo-wp')}
						hasValue={() => showLabel !== true}
						onDeselect={() => setAttributes({ showLabel: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Label', 'airo-wp')}
							checked={showLabel}
							onChange={(value) =>
								setAttributes({ showLabel: value })
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{showLabel && (
						<DsgoInspectorPanel.Item
							label={__('Label Text', 'airo-wp')}
							hasValue={() => labelText !== ''}
							onDeselect={() => setAttributes({ labelText: '' })}
							isShownByDefault
						>
							<TextControl
								label={__('Label Text', 'airo-wp')}
								value={labelText}
								onChange={(value) =>
									setAttributes({ labelText: value })
								}
								placeholder={__(
									'e.g., Project Progress',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Show Percentage', 'airo-wp')}
						hasValue={() => showPercentage !== true}
						onDeselect={() =>
							setAttributes({ showPercentage: true })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Percentage', 'airo-wp')}
							checked={showPercentage}
							onChange={(value) =>
								setAttributes({ showPercentage: value })
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{(showLabel || showPercentage) && (
						<DsgoInspectorPanel.Item
							label={__('Label Position', 'airo-wp')}
							hasValue={() => labelPosition !== 'top'}
							onDeselect={() =>
								setAttributes({ labelPosition: 'top' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Label Position', 'airo-wp')}
								value={labelPosition}
								options={[
									{
										label: __('Above Bar', 'airo-wp'),
										value: 'top',
									},
									{
										label: __('Inside Bar', 'airo-wp'),
										value: 'inside',
									},
									{
										label: __('Below Bar', 'airo-wp'),
										value: 'bottom',
									},
								]}
								onChange={(value) =>
									setAttributes({ labelPosition: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Animate on Scroll', 'airo-wp')}
						hasValue={() => animateOnScroll !== true}
						onDeselect={() =>
							setAttributes({ animateOnScroll: true })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Animate on Scroll', 'airo-wp')}
							checked={animateOnScroll}
							onChange={(value) =>
								setAttributes({ animateOnScroll: value })
							}
							help={__(
								'Animate the bar when it enters the viewport',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Animation Duration', 'airo-wp')}
						hasValue={() => animationDuration !== 1.5}
						onDeselect={() =>
							setAttributes({ animationDuration: 1.5 })
						}
						isShownByDefault
					>
						<RangeControl
							label={__('Animation Duration', 'airo-wp')}
							value={animationDuration}
							onChange={(value) =>
								setAttributes({ animationDuration: value })
							}
							min={0.5}
							max={5}
							step={0.1}
							help={__('Duration in seconds', 'airo-wp')}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{(barStyle === 'striped' ||
						barStyle === 'striped-animated') && (
						<DsgoInspectorPanel.Item
							label={__('Animate Stripes', 'airo-wp')}
							hasValue={() => stripedAnimation !== false}
							onDeselect={() =>
								setAttributes({ stripedAnimation: false })
							}
							isShownByDefault
						>
							<ToggleControl
								label={__('Animate Stripes', 'airo-wp')}
								checked={
									stripedAnimation ||
									barStyle === 'striped-animated'
								}
								onChange={(value) =>
									setAttributes({ stripedAnimation: value })
								}
								disabled={barStyle === 'striped-animated'}
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				{/* Label Above */}
				{(showLabel || showPercentage) && labelPosition === 'top' && (
					<div className="airo-wp-progress-bar__label airo-wp-progress-bar__label--top">
						{displayText}
					</div>
				)}

				{/* Progress Bar */}
				<div
					className="airo-wp-progress-bar__container"
					style={barContainerStyles}
				>
					<div
						className={`airo-wp-progress-bar__fill ${
							barStyle === 'striped-animated' || stripedAnimation
								? 'airo-wp-progress-bar__fill--animated'
								: ''
						}`}
						style={barFillStyles}
					>
						{/* Label Inside */}
						{(showLabel || showPercentage) &&
							labelPosition === 'inside' && (
								<div className="airo-wp-progress-bar__label airo-wp-progress-bar__label--inside">
									{displayText}
								</div>
							)}
					</div>
				</div>

				{/* Label Below */}
				{(showLabel || showPercentage) &&
					labelPosition === 'bottom' && (
						<div className="airo-wp-progress-bar__label airo-wp-progress-bar__label--bottom">
							{displayText}
						</div>
					)}
			</div>
		</>
	);
}
