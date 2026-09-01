/**
 * Divider Block - Edit Component
 *
 * Visual separator with multiple style options including
 * solid, dashed, gradient, and decorative patterns.
 *
 * @since 1.0.0
 */

import { __, sprintf } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	SelectControl,
	RangeControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { getIcon, IconPicker } from '../shared/icon-utils';
import { useIconDefaults } from '../../hooks';

/**
 * Divider Edit Component
 *
 * @param {Object}   props               - Component props
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to update attributes
 * @param {string}   props.clientId      - Block client ID
 * @return {JSX.Element} Divider block edit component
 */
export default function DividerEdit({ attributes, setAttributes, clientId }) {
	const { dividerStyle, width, thickness, iconName, iconStyle, strokeWidth } =
		attributes;

	// Theme-level icon defaults inherited when style is left unset.
	const iconDefaults = useIconDefaults();
	const effectiveStyle = iconStyle || iconDefaults.style;

	// Block wrapper props - Block Supports automatically applies color styles
	const blockProps = useBlockProps({
		className: `airo-wp-divider airo-wp-divider--${dividerStyle}`,
	});

	// Divider container styles
	const containerStyle = {
		width: `${width}%`,
	};

	// Divider line styles
	const lineStyle = {
		height: `${thickness}px`,
	};

	return (
		<>
			{/* ========================================
			     INSPECTOR CONTROLS - SETTINGS TAB
			    ======================================== */}
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							dividerStyle: 'solid',
							width: 100,
							thickness: 2,
							iconName: 'star',
							iconStyle: undefined,
							strokeWidth: 1.5,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Style', 'airo-wp')}
						hasValue={() => dividerStyle !== 'solid'}
						onDeselect={() =>
							setAttributes({ dividerStyle: 'solid' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Style', 'airo-wp')}
							value={dividerStyle}
							options={[
								{
									label: __('Solid', 'airo-wp'),
									value: 'solid',
								},
								{
									label: __('Dashed', 'airo-wp'),
									value: 'dashed',
								},
								{
									label: __('Dotted', 'airo-wp'),
									value: 'dotted',
								},
								{
									label: __('Double', 'airo-wp'),
									value: 'double',
								},
								{
									label: __('Gradient Fade', 'airo-wp'),
									value: 'gradient',
								},
								{
									label: __('Dots Pattern', 'airo-wp'),
									value: 'dots',
								},
								{
									label: __('Wave Pattern', 'airo-wp'),
									value: 'wave',
								},
								{
									label: __('Icon Centered', 'airo-wp'),
									value: 'icon',
								},
							]}
							onChange={(value) =>
								setAttributes({ dividerStyle: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{dividerStyle === 'icon' && (
						<DsgoInspectorPanel.Item
							label={__('Icon', 'airo-wp')}
							hasValue={() => iconName !== 'star'}
							onDeselect={() =>
								setAttributes({ iconName: 'star' })
							}
							isShownByDefault
						>
							<IconPicker
								label={__('Icon', 'airo-wp')}
								value={iconName}
								onChange={(value) =>
									setAttributes({ iconName: value })
								}
							/>
						</DsgoInspectorPanel.Item>
					)}

					{dividerStyle === 'icon' && (
						<DsgoInspectorPanel.Item
							label={__('Icon Style', 'airo-wp')}
							hasValue={() => typeof iconStyle === 'string'}
							onDeselect={() =>
								setAttributes({ iconStyle: undefined })
							}
							isShownByDefault
						>
							<ToggleGroupControl
								label={__('Icon Style', 'airo-wp')}
								value={effectiveStyle}
								onChange={(value) =>
									setAttributes({ iconStyle: value })
								}
								help={
									!iconStyle &&
									sprintf(
										/* translators: %s: inherited icon style (Filled or Outlined). */
										__(
											'Inheriting theme default (%s).',
											'airo-wp'
										),
										iconDefaults.style === 'outlined'
											? __('Outlined', 'airo-wp')
											: __('Filled', 'airo-wp')
									)
								}
								isBlock
								__nextHasNoMarginBottom
							>
								<ToggleGroupControlOption
									value="filled"
									label={__('Filled', 'airo-wp')}
								/>
								<ToggleGroupControlOption
									value="outlined"
									label={__('Outlined', 'airo-wp')}
								/>
							</ToggleGroupControl>
						</DsgoInspectorPanel.Item>
					)}

					{dividerStyle === 'icon' &&
						effectiveStyle === 'outlined' && (
							<DsgoInspectorPanel.Item
								label={__('Stroke Width', 'airo-wp')}
								hasValue={() => strokeWidth !== 1.5}
								onDeselect={() =>
									setAttributes({ strokeWidth: 1.5 })
								}
								isShownByDefault
							>
								<RangeControl
									label={__('Stroke Width', 'airo-wp')}
									value={strokeWidth}
									onChange={(value) =>
										setAttributes({ strokeWidth: value })
									}
									min={0.5}
									max={4}
									step={0.5}
									help={__(
										'Thinner strokes work better for detailed icons',
										'airo-wp'
									)}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							</DsgoInspectorPanel.Item>
						)}

					<DsgoInspectorPanel.Item
						label={__('Width (%)', 'airo-wp')}
						hasValue={() => width !== 100}
						onDeselect={() => setAttributes({ width: 100 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Width (%)', 'airo-wp')}
							value={width}
							onChange={(value) =>
								setAttributes({ width: value })
							}
							min={10}
							max={100}
							step={5}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{dividerStyle !== 'icon' && (
						<DsgoInspectorPanel.Item
							label={__('Thickness (px)', 'airo-wp')}
							hasValue={() => thickness !== 2}
							onDeselect={() => setAttributes({ thickness: 2 })}
							isShownByDefault
						>
							<RangeControl
								label={__('Thickness (px)', 'airo-wp')}
								value={thickness}
								onChange={(value) =>
									setAttributes({ thickness: value })
								}
								min={1}
								max={20}
								step={1}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			{/* ========================================
			     BLOCK CONTENT
			    ======================================== */}
			<div {...blockProps}>
				<div
					className="airo-wp-divider__container"
					style={containerStyle}
				>
					{dividerStyle === 'icon' ? (
						<div className="airo-wp-divider__icon-wrapper">
							<span
								className="airo-wp-divider__line airo-wp-divider__line--left"
								style={lineStyle}
							/>
							<span className="airo-wp-divider__icon">
								{getIcon(iconName, effectiveStyle, strokeWidth)}
							</span>
							<span
								className="airo-wp-divider__line airo-wp-divider__line--right"
								style={lineStyle}
							/>
						</div>
					) : (
						<div
							className="airo-wp-divider__line"
							style={lineStyle}
						/>
					)}
				</div>
			</div>
		</>
	);
}
