/**
 * Blobs Block - Editor Component
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	SelectControl,
	RangeControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import classnames from 'classnames';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { hasExplicitString } from '../../utils/has-explicit-value';

export default function BlobsEdit({ attributes, setAttributes, clientId }) {
	const wrapperRef = useRef(null);
	const {
		blobShape,
		blobAnimation,
		animationDuration,
		animationEasing,
		size,
		height,
		maxWidth,
		enableOverlay,
		overlayColor,
		overlayOpacity,
	} = attributes;

	// Derive unit-specific min/max for the blob size control
	const sizeUnit = size ? size.replace(/[\d.]+/, '') : 'px';
	const sizeConstraints = {
		px: { min: 50, max: 800 },
		'%': { min: 20, max: 200 },
		vw: { min: 10, max: 100 },
		vh: { min: 10, max: 100 },
	};
	const { min: sizeMin, max: sizeMax } =
		sizeConstraints[sizeUnit] || sizeConstraints.px;

	// Derive unit-specific min/max for the blob height control
	const heightUnit = height ? height.replace(/[\d.]+/, '') : 'px';
	const { min: heightMin, max: heightMax } =
		sizeConstraints[heightUnit] || sizeConstraints.px;

	// Get theme color palette and gradient settings
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// Calculate classes
	const blobClasses = classnames('airo-wp-blobs', {
		[`airo-wp-blobs--${blobShape}`]: blobShape,
		[`airo-wp-blobs--${blobAnimation}`]:
			blobAnimation && blobAnimation !== 'none',
	});

	// Apply animation settings as CSS custom properties
	const customStyles = {
		'--airo-wp-blob-size': size,
		...(height ? { '--airo-wp-blob-height': height } : {}),
		'--airo-wp-blob-animation-duration': animationDuration,
		'--airo-wp-blob-animation-easing': animationEasing,
	};

	// Optional max-width constraint on the wrapper - MUST MATCH save.js.
	// Only add the class + kit-controllable custom property when the author
	// sets an explicit maxWidth; the stylesheet owns the actual max-width and
	// centering margins.
	const hasMaxWidth = hasExplicitString(maxWidth);

	// Get block props with our wrapper class
	const blockProps = useBlockProps({
		className: classnames('airo-wp-blobs-wrapper', {
			'airo-wp-has-max-width': hasMaxWidth,
		}),
		ref: wrapperRef,
		...(hasMaxWidth && {
			style: { '--airo-wp-blob-max-width': maxWidth },
		}),
	});

	// Transfer background styles from wrapper to blob in editor
	useEffect(() => {
		if (!wrapperRef.current) {
			return;
		}

		const wrapper = wrapperRef.current;
		const blob = wrapper.querySelector('.airo-wp-blobs');
		if (!blob) {
			return;
		}

		// WordPress sets inline styles on the wrapper
		// We need to read inline styles directly because our CSS has `background: none !important;`
		const inlineStyle = wrapper.style;

		// Transfer background image
		if (
			inlineStyle.backgroundImage &&
			inlineStyle.backgroundImage !== 'none'
		) {
			blob.style.setProperty(
				'background-image',
				inlineStyle.backgroundImage
			);
		} else {
			blob.style.removeProperty('background-image');
		}

		// Transfer background size
		if (
			inlineStyle.backgroundSize &&
			inlineStyle.backgroundSize !== 'auto'
		) {
			blob.style.setProperty(
				'background-size',
				inlineStyle.backgroundSize
			);
		}

		// Transfer background position
		if (inlineStyle.backgroundPosition) {
			blob.style.setProperty(
				'background-position',
				inlineStyle.backgroundPosition
			);
		}

		// Transfer background repeat
		if (
			inlineStyle.backgroundRepeat &&
			inlineStyle.backgroundRepeat !== 'repeat'
		) {
			blob.style.setProperty(
				'background-repeat',
				inlineStyle.backgroundRepeat
			);
		}

		// Transfer background attachment
		if (
			inlineStyle.backgroundAttachment &&
			inlineStyle.backgroundAttachment !== 'scroll'
		) {
			blob.style.setProperty(
				'background-attachment',
				inlineStyle.backgroundAttachment
			);
		}

		// Transfer WordPress background color classes from wrapper to blob
		// so WordPress's own CSS applies the color to the blob shape directly
		const bgClasses = Array.from(wrapper.classList).filter(
			(c) =>
				c.match(/^has-.*-background-color$/) || c === 'has-background'
		);
		bgClasses.forEach((cls) => {
			wrapper.classList.remove(cls);
			blob.classList.add(cls);
		});

		// Transfer inline background color (custom non-preset colors)
		if (inlineStyle.backgroundColor) {
			blob.style.setProperty(
				'background-color',
				inlineStyle.backgroundColor
			);
		} else if (bgClasses.length === 0) {
			// Apply default color if no user color is set
			const defaultColor =
				window
					.getComputedStyle(document.documentElement)
					.getPropertyValue('--wp--preset--color--accent-2') ||
				'#2563eb';
			blob.style.setProperty('background-color', defaultColor.trim());
		}
	}); // Run on every render to catch style changes

	// Inner blocks for content inside blob
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-blobs__content',
		},
		{
			template: [
				[
					'core/heading',
					{
						level: 2,
						placeholder: __('Add title…', 'airo-wp'),
						textAlign: 'center',
					},
				],
				[
					'core/paragraph',
					{
						placeholder: __('Add description…', 'airo-wp'),
						align: 'center',
					},
				],
			],
		}
	);

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							blobShape: 'shape-1',
							blobAnimation: 'none',
							animationDuration: '8s',
							animationEasing: 'ease-in-out',
							size: '300px',
							height: '',
							maxWidth: undefined,
							overlayOpacity: 80,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Blob Shape', 'airo-wp')}
						hasValue={() => blobShape !== 'shape-1'}
						onDeselect={() =>
							setAttributes({ blobShape: 'shape-1' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Blob Shape', 'airo-wp')}
							value={blobShape}
							options={[
								{
									label: __('Classic Blob', 'airo-wp'),
									value: 'shape-1',
								},
								{
									label: __('Amoeba', 'airo-wp'),
									value: 'shape-2',
								},
								{
									label: __('Pebble', 'airo-wp'),
									value: 'shape-3',
								},
								{
									label: __('Splash', 'airo-wp'),
									value: 'shape-4',
								},
								{
									label: __('Drop', 'airo-wp'),
									value: 'shape-5',
								},
								{
									label: __('Cloud', 'airo-wp'),
									value: 'shape-6',
								},
							]}
							onChange={(value) =>
								setAttributes({ blobShape: value })
							}
							help={__(
								'Choose the organic shape style',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Width', 'airo-wp')}
						hasValue={() => size !== '300px'}
						onDeselect={() => setAttributes({ size: '300px' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Width', 'airo-wp')}
							value={size}
							onChange={(value) =>
								setAttributes({ size: value || '300px' })
							}
							units={[
								{ value: 'px', label: 'px', default: 300 },
								{ value: '%', label: '%', default: 100 },
								{ value: 'vw', label: 'vw', default: 30 },
								{ value: 'vh', label: 'vh', default: 30 },
							]}
							min={sizeMin}
							max={sizeMax}
							help={__('Width of the blob shape', 'airo-wp')}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Height', 'airo-wp')}
						hasValue={() => height !== ''}
						onDeselect={() => setAttributes({ height: '' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Height', 'airo-wp')}
							value={height}
							onChange={(value) =>
								setAttributes({ height: value })
							}
							units={[
								{ value: 'px', label: 'px', default: 300 },
								{ value: '%', label: '%', default: 100 },
								{ value: 'vw', label: 'vw', default: 30 },
								{ value: 'vh', label: 'vh', default: 30 },
							]}
							min={heightMin}
							max={heightMax}
							placeholder={size}
							help={__(
								'Height of the blob shape. Defaults to width if empty.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Max Width', 'airo-wp')}
						hasValue={() => !!maxWidth}
						onDeselect={() =>
							setAttributes({ maxWidth: undefined })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Max Width', 'airo-wp')}
							value={maxWidth || ''}
							onChange={(value) =>
								setAttributes({
									maxWidth: value || undefined,
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 800 },
								{ value: '%', label: '%', default: 100 },
								{ value: 'vw', label: 'vw', default: 60 },
								{ value: 'rem', label: 'rem', default: 50 },
							]}
							help={__(
								'Constrain the blob wrapper width and center it. Leave empty for no constraint.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Animation Type', 'airo-wp')}
						hasValue={() => blobAnimation !== 'none'}
						onDeselect={() =>
							setAttributes({ blobAnimation: 'none' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Animation Type', 'airo-wp')}
							value={blobAnimation}
							options={[
								{
									label: __('None', 'airo-wp'),
									value: 'none',
								},
								{
									label: __('Float', 'airo-wp'),
									value: 'float',
								},
								{
									label: __('Pulse', 'airo-wp'),
									value: 'pulse',
								},
								{
									label: __('Spin', 'airo-wp'),
									value: 'spin',
								},
								{
									label: __('Morph Style 1', 'airo-wp'),
									value: 'morph-1',
								},
								{
									label: __('Morph Style 2', 'airo-wp'),
									value: 'morph-2',
								},
							]}
							onChange={(value) =>
								setAttributes({ blobAnimation: value })
							}
							help={__(
								'Float/Pulse/Spin keep your shape. Morph changes the shape itself.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Animation Duration', 'airo-wp')}
						hasValue={() => animationDuration !== '8s'}
						onDeselect={() =>
							setAttributes({ animationDuration: '8s' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Animation Duration', 'airo-wp')}
							value={animationDuration}
							onChange={(value) =>
								setAttributes({
									animationDuration: value || '8s',
								})
							}
							units={[
								{ value: 's', label: 's', default: 8 },
								{ value: 'ms', label: 'ms', default: 8000 },
							]}
							min={1}
							max={30}
							help={__(
								'How long one animation cycle takes',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Animation Easing', 'airo-wp')}
						hasValue={() => animationEasing !== 'ease-in-out'}
						onDeselect={() =>
							setAttributes({ animationEasing: 'ease-in-out' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Animation Easing', 'airo-wp')}
							value={animationEasing}
							options={[
								{
									label: __('Linear', 'airo-wp'),
									value: 'linear',
								},
								{
									label: __('Ease', 'airo-wp'),
									value: 'ease',
								},
								{
									label: __('Ease In', 'airo-wp'),
									value: 'ease-in',
								},
								{
									label: __('Ease Out', 'airo-wp'),
									value: 'ease-out',
								},
								{
									label: __('Ease In Out', 'airo-wp'),
									value: 'ease-in-out',
								},
							]}
							onChange={(value) =>
								setAttributes({ animationEasing: value })
							}
							help={__('Animation timing function', 'airo-wp')}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{enableOverlay && (
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
									setAttributes({ overlayOpacity: value })
								}
								min={0}
								max={100}
								help={__(
									'Overlay transparency (0 = transparent, 100 = opaque)',
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
					title={__('Overlay Color', 'airo-wp')}
					settings={[
						{
							label: __('Overlay Color', 'airo-wp'),
							colorValue: decodeColorValue(
								overlayColor,
								colorGradientSettings
							),
							onColorChange: (color) => {
								// Auto-enable overlay when user sets a color
								if (color) {
									setAttributes({
										overlayColor: encodeColorValue(
											color,
											colorGradientSettings
										),
										enableOverlay: true,
									});
								} else {
									// Disable overlay when color is cleared
									setAttributes({
										overlayColor: '',
										enableOverlay: false,
									});
								}
							},
							enableAlpha: true,
							clearable: true,
						},
					]}
					{...colorGradientSettings}
				/>
			</InspectorControls>

			<div {...blockProps}>
				<div className={blobClasses} style={customStyles}>
					{enableOverlay && (
						<div
							className="airo-wp-blobs__overlay"
							style={{
								backgroundColor:
									convertColorToCSSVar(overlayColor),
								opacity: overlayOpacity / 100,
							}}
						/>
					)}
					<div className="airo-wp-blobs__shape">
						<div {...innerBlocksProps} />
					</div>
				</div>
			</div>
		</>
	);
}
