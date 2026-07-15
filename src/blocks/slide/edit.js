import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	SelectControl,
	RangeControl,
	Button,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import classnames from 'classnames';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import {
	convertColorToCSSVar,
	convertPresetToCSSVar,
} from '../../utils/convert-preset-to-css-var';

export default function SlideEdit({
	attributes,
	setAttributes,
	context,
	clientId,
}) {
	const {
		backgroundImage,
		backgroundSize,
		backgroundPosition,
		backgroundRepeat,
		overlayColor,
		overlayOpacity,
		contentVerticalAlign,
		contentHorizontalAlign,
		minHeight,
	} = attributes;

	// Get context from parent slider
	const styleVariation = context['airo-wp/slider/styleVariation'];

	// Get theme color palette and gradient settings
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// Declaratively calculate classes
	const slideClasses = classnames('airo-wp-slide', {
		'airo-wp-slide--has-background': backgroundImage?.url,
		'airo-wp-slide--has-overlay': overlayColor, // Show overlay if color is set
		[`airo-wp-slide--${styleVariation}`]: styleVariation,
	});

	// Background image styles
	const backgroundStyles = backgroundImage?.url
		? {
				backgroundImage: `url(${backgroundImage.url})`,
				backgroundSize,
				backgroundPosition,
				backgroundRepeat,
			}
		: {};

	// Overlay styles - only apply if overlayColor is set
	const overlayStyles = overlayColor
		? {
				'--airo-wp-slide-overlay-color':
					convertColorToCSSVar(overlayColor),
				'--airo-wp-slide-overlay-opacity': String(overlayOpacity / 100),
			}
		: {};

	// Content alignment styles
	const alignmentStyles = {
		'--airo-wp-slide-content-vertical-align': contentVerticalAlign,
		'--airo-wp-slide-content-horizontal-align': contentHorizontalAlign,
	};

	// Min height override
	const heightStyles = minHeight ? { minHeight } : {};

	// Block wrapper props
	const blockProps = useBlockProps({
		className: slideClasses,
		style: {
			...backgroundStyles,
			...overlayStyles,
			...alignmentStyles,
			...heightStyles,
		},
		role: 'group',
		'aria-roledescription': 'slide',
	});

	// Pass user-set block gap to the content wrapper
	const blockGap = convertPresetToCSSVar(attributes.style?.spacing?.blockGap);
	const contentStyle = blockGap ? { gap: blockGap } : {};

	// Inner blocks configuration - Allow any blocks
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-slide__content',
			style: contentStyle,
		},
		{
			template: [
				[
					'core/heading',
					{
						level: 2,
						placeholder: __('Add slide title…', 'airo-wp'),
						textAlign: 'center',
					},
				],
				[
					'core/paragraph',
					{
						placeholder: __('Add slide content…', 'airo-wp'),
						align: 'center',
					},
				],
			],
			templateLock: false,
		}
	);

	// Background image handler
	const onSelectImage = (media) => {
		setAttributes({
			backgroundImage: {
				id: media.id,
				url: media.url,
				alt: media.alt || '',
			},
		});
	};

	const onRemoveImage = () => {
		setAttributes({
			backgroundImage: {
				id: 0,
				url: '',
				alt: '',
			},
		});
	};

	return (
		<>
			{/* Color controls - appear in STYLES tab */}
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
				/>
			</InspectorControls>

			{/* Other controls - appear in SETTINGS tab */}
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							backgroundImage: { url: '', id: 0, alt: '' },
							backgroundSize: 'cover',
							backgroundPosition: 'center center',
							overlayOpacity: 80,
							contentVerticalAlign: 'center',
							contentHorizontalAlign: 'center',
							minHeight: '',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Background Image', 'airo-wp')}
						hasValue={() => !!backgroundImage?.url}
						onDeselect={() =>
							setAttributes({
								backgroundImage: { url: '', id: 0, alt: '' },
							})
						}
						isShownByDefault
					>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectImage}
								allowedTypes={['image']}
								value={backgroundImage?.id}
								render={({ open }) => (
									<>
										{!backgroundImage?.url ? (
											<Button
												onClick={open}
												variant="secondary"
												style={{
													width: '100%',
													marginBottom: '12px',
												}}
											>
												{__(
													'Select Background Image',
													'airo-wp'
												)}
											</Button>
										) : (
											<div
												style={{ marginBottom: '12px' }}
											>
												<img
													src={backgroundImage.url}
													alt={backgroundImage.alt}
													style={{
														width: '100%',
														height: 'auto',
														borderRadius: '4px',
														marginBottom: '8px',
													}}
												/>
												<div
													style={{
														display: 'flex',
														gap: '8px',
													}}
												>
													<Button
														onClick={open}
														variant="secondary"
														style={{ flex: 1 }}
													>
														{__(
															'Replace Image',
															'airo-wp'
														)}
													</Button>
													<Button
														onClick={onRemoveImage}
														variant="secondary"
														isDestructive
														style={{ flex: 1 }}
													>
														{__(
															'Remove Image',
															'airo-wp'
														)}
													</Button>
												</div>
											</div>
										)}
									</>
								)}
							/>
						</MediaUploadCheck>
					</DsgoInspectorPanel.Item>

					{backgroundImage?.url && (
						<DsgoInspectorPanel.Item
							label={__('Background Size', 'airo-wp')}
							hasValue={() => backgroundSize !== 'cover'}
							onDeselect={() =>
								setAttributes({ backgroundSize: 'cover' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Background Size', 'airo-wp')}
								value={backgroundSize}
								options={[
									{
										label: __('Cover', 'airo-wp'),
										value: 'cover',
									},
									{
										label: __('Contain', 'airo-wp'),
										value: 'contain',
									},
									{
										label: __('Auto', 'airo-wp'),
										value: 'auto',
									},
								]}
								onChange={(value) =>
									setAttributes({ backgroundSize: value })
								}
								help={__(
									'How the background image fills the slide',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{backgroundImage?.url && (
						<DsgoInspectorPanel.Item
							label={__('Background Position', 'airo-wp')}
							hasValue={() =>
								backgroundPosition !== 'center center'
							}
							onDeselect={() =>
								setAttributes({
									backgroundPosition: 'center center',
								})
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Background Position', 'airo-wp')}
								value={backgroundPosition}
								options={[
									{
										label: __(
											'Center Center',
											'airo-wp'
										),
										value: 'center center',
									},
									{
										label: __('Top Center', 'airo-wp'),
										value: 'top center',
									},
									{
										label: __(
											'Bottom Center',
											'airo-wp'
										),
										value: 'bottom center',
									},
									{
										label: __('Left Center', 'airo-wp'),
										value: 'left center',
									},
									{
										label: __(
											'Right Center',
											'airo-wp'
										),
										value: 'right center',
									},
									{
										label: __('Top Left', 'airo-wp'),
										value: 'top left',
									},
									{
										label: __('Top Right', 'airo-wp'),
										value: 'top right',
									},
									{
										label: __('Bottom Left', 'airo-wp'),
										value: 'bottom left',
									},
									{
										label: __(
											'Bottom Right',
											'airo-wp'
										),
										value: 'bottom right',
									},
								]}
								onChange={(value) =>
									setAttributes({ backgroundPosition: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{backgroundImage?.url && overlayColor && (
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
										overlayOpacity: value,
									})
								}
								min={0}
								max={100}
								help={__(
									'Set overlay color in the Styles tab to enable overlay',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Vertical Alignment', 'airo-wp')}
						hasValue={() => contentVerticalAlign !== 'center'}
						onDeselect={() =>
							setAttributes({ contentVerticalAlign: 'center' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Vertical Alignment', 'airo-wp')}
							value={contentVerticalAlign}
							options={[
								{
									label: __('Top', 'airo-wp'),
									value: 'flex-start',
								},
								{
									label: __('Center', 'airo-wp'),
									value: 'center',
								},
								{
									label: __('Bottom', 'airo-wp'),
									value: 'flex-end',
								},
							]}
							onChange={(value) =>
								setAttributes({ contentVerticalAlign: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Horizontal Alignment', 'airo-wp')}
						hasValue={() => contentHorizontalAlign !== 'center'}
						onDeselect={() =>
							setAttributes({ contentHorizontalAlign: 'center' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Horizontal Alignment', 'airo-wp')}
							value={contentHorizontalAlign}
							options={[
								{
									label: __('Left', 'airo-wp'),
									value: 'flex-start',
								},
								{
									label: __('Center', 'airo-wp'),
									value: 'center',
								},
								{
									label: __('Right', 'airo-wp'),
									value: 'flex-end',
								},
							]}
							onChange={(value) =>
								setAttributes({ contentHorizontalAlign: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Min Height Override', 'airo-wp')}
						hasValue={() => minHeight !== ''}
						onDeselect={() => setAttributes({ minHeight: '' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Min Height Override', 'airo-wp')}
							value={minHeight}
							onChange={(value) =>
								setAttributes({ minHeight: value })
							}
							units={[
								{ value: 'px', label: 'px', default: 0 },
								{ value: 'vh', label: 'vh', default: 0 },
							]}
							help={__(
								'Override slider height for this slide only (leave empty to use slider settings)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				{overlayColor && (
					<div
						className="airo-wp-slide__overlay"
						style={{
							backgroundColor: convertColorToCSSVar(overlayColor),
							opacity: overlayOpacity / 100,
						}}
					/>
				)}
				<div {...innerBlocksProps} />
			</div>
		</>
	);
}
