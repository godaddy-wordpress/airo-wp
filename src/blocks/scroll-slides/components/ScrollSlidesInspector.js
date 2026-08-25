/**
 * Scroll Slides Inspector Controls
 *
 * Settings panel and color controls for the Scroll Slides block.
 */

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	InspectorControls,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	RangeControl,
	ToggleControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { DsgoInspectorPanel } from '../../../components/shared';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../../utils/encode-color-value';

export default function ScrollSlidesInspector({
	attributes,
	setAttributes,
	clientId,
	themeContentSize,
}) {
	const {
		minHeight,
		maxHeight,
		constrainWidth,
		contentWidth,
		overlayColor,
		overlayOpacity,
		navColor,
		navActiveColor,
	} = attributes;

	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							minHeight: '100vh',
							maxHeight: '900px',
							constrainWidth: true,
							contentWidth: '',
							overlayOpacity: 80,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Minimum Height', 'airo-wp')}
						hasValue={() => minHeight !== '100vh'}
						onDeselect={() => setAttributes({ minHeight: '100vh' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Minimum Height', 'airo-wp')}
							value={minHeight}
							onChange={(value) =>
								setAttributes({ minHeight: value })
							}
							units={[
								{ value: 'vh', label: 'vh' },
								{ value: 'px', label: 'px' },
								{ value: 'rem', label: 'rem' },
								{ value: '%', label: '%' },
							]}
							__next40pxDefaultSize
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Maximum Height', 'airo-wp')}
						hasValue={() => maxHeight !== '900px'}
						onDeselect={() => setAttributes({ maxHeight: '900px' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Maximum Height', 'airo-wp')}
							value={maxHeight}
							onChange={(value) =>
								setAttributes({ maxHeight: value })
							}
							help={__(
								'Caps the section height on tall monitors',
								'airo-wp'
							)}
							units={[
								{ value: 'px', label: 'px' },
								{ value: 'vh', label: 'vh' },
								{ value: 'rem', label: 'rem' },
							]}
							__next40pxDefaultSize
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Constrain Content Width', 'airo-wp')}
						hasValue={() => constrainWidth !== true}
						onDeselect={() =>
							setAttributes({
								constrainWidth: true,
								contentWidth: '',
							})
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Constrain Content Width', 'airo-wp')}
							checked={constrainWidth}
							onChange={(value) =>
								setAttributes({ constrainWidth: value })
							}
							help={
								constrainWidth
									? __(
											'Content respects theme content width',
											'airo-wp'
										)
									: __('Content fills full width', 'airo-wp')
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{constrainWidth && (
						<DsgoInspectorPanel.Item
							label={__('Content Width', 'airo-wp')}
							hasValue={() => contentWidth !== ''}
							onDeselect={() =>
								setAttributes({ contentWidth: '' })
							}
							isShownByDefault
						>
							<UnitControl
								label={__('Content Width', 'airo-wp')}
								value={contentWidth}
								onChange={(value) =>
									setAttributes({ contentWidth: value })
								}
								placeholder={
									themeContentSize ||
									__('Theme default', 'airo-wp')
								}
								help={
									!contentWidth && themeContentSize
										? sprintf(
												/* translators: %s: theme content size */
												__(
													'Using theme default: %s',
													'airo-wp'
												),
												themeContentSize
											)
										: undefined
								}
								units={[
									{ value: 'px', label: 'px' },
									{ value: 'rem', label: 'rem' },
									{ value: '%', label: '%' },
									{ value: 'vw', label: 'vw' },
								]}
								__next40pxDefaultSize
							/>
						</DsgoInspectorPanel.Item>
					)}

					{overlayColor && (
						<DsgoInspectorPanel.Item
							label={__('Overlay Opacity', 'airo-wp')}
							hasValue={() => overlayOpacity !== 80}
							onDeselect={() =>
								setAttributes({ overlayOpacity: 80 })
							}
							isShownByDefault
						>
							<RangeControl
								label={__('Overlay Opacity', 'airo-wp')}
								value={overlayOpacity}
								onChange={(value) =>
									setAttributes({
										overlayOpacity:
											value === undefined ? 80 : value,
									})
								}
								min={0}
								max={100}
								step={1}
								help={__(
									'Opacity of the color overlay above each slide background.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Navigation', 'airo-wp')}
					settings={[
						{
							label: __('Navigation Title Color', 'airo-wp'),
							colorValue: decodeColorValue(
								navColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									navColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Active Title Color', 'airo-wp'),
							colorValue: decodeColorValue(
								navActiveColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									navActiveColor:
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
					__experimentalIsRenderedInSidebar
				/>
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Overlay', 'airo-wp')}
					settings={[
						{
							label: __('Overlay Color', 'airo-wp'),
							colorValue: decodeColorValue(
								overlayColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									overlayColor:
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
					__experimentalIsRenderedInSidebar
				/>
			</InspectorControls>
		</>
	);
}
