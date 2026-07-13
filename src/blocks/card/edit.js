/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
} from '@wordpress/block-editor';
import {
	SelectControl,
	RangeControl,
	TextControl,
	ToggleControl,
	Button,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { useBlockColors } from '../../hooks';

/**
 * Edit component for Card block
 *
 * @param {Object}   props               - Component props
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to set attributes
 * @param {string}   props.clientId      - Block client ID
 * @return {Element} Edit component
 */
export default function CardEdit({ attributes, setAttributes, clientId }) {
	const {
		layoutPreset,
		imageUrl,
		imageAlt,
		imageAspectRatio,
		imageCustomAspectRatio,
		imageObjectFit,
		imageFocalPoint,
		badgeText,
		badgeStyle,
		badgeFloatingPosition,
		badgeInlinePosition,
		badgeBackgroundColor,
		badgeTextColor,
		title,
		subtitle,
		bodyText,
		overlayOpacity,
		overlayColor,
		contentAlignment,
		visualStyle,
		borderColor,
		showImage,
		showTitle,
		showSubtitle,
		showBody,
		showBadge,
		showCta,
	} = attributes;

	// Border panel — migrated to useBlockColors hook.
	// colorGradientSettings is returned by the hook (same shape as
	// useMultipleOriginColorsAndGradients) and used by the inline panels below.
	const { settings: borderColorSettings, colorGradientSettings } =
		useBlockColors({
			attributes,
			setAttributes,
			entries: [
				{
					label: __('Border Color', 'airo-wp'),
					attribute: 'borderColor',
				},
			],
		});

	// Build block props with border color
	const blockStyles = {};
	// Only apply custom border color on styles that have borders (not minimal)
	if (borderColor && visualStyle !== 'minimal') {
		blockStyles.borderColor = borderColor;
		// Ensure border exists
		blockStyles.borderWidth = visualStyle === 'outlined' ? '2px' : '1px';
		blockStyles.borderStyle = 'solid';
	}

	const blockProps = useBlockProps({
		className: `airo-wp-card airo-wp-card--${layoutPreset} airo-wp-card--style-${visualStyle}`,
		style: blockStyles,
	});

	// Inner blocks props for CTA area
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-card__cta',
		},
		{
			template: [
				[
					'airo-wp/icon-button',
					{ text: __('Learn More', 'airo-wp') },
				],
			],
			templateLock: false,
			allowedBlocks: ['airo-wp/icon-button'],
		}
	);

	// Calculate image styles
	const imageStyles = {};
	if (imageAspectRatio !== 'original') {
		if (imageAspectRatio === 'custom' && imageCustomAspectRatio) {
			imageStyles.aspectRatio = imageCustomAspectRatio;
		} else if (imageAspectRatio === '16-9') {
			imageStyles.aspectRatio = '16 / 9';
		} else if (imageAspectRatio === '4-3') {
			imageStyles.aspectRatio = '4 / 3';
		} else if (imageAspectRatio === '1-1') {
			imageStyles.aspectRatio = '1 / 1';
		}
	}
	if (imageObjectFit) {
		imageStyles.objectFit = imageObjectFit;
	}
	if (imageObjectFit === 'cover' && imageFocalPoint) {
		imageStyles.objectPosition = `${Number(imageFocalPoint.x) * 100}% ${Number(imageFocalPoint.y) * 100}%`;
	}

	// Calculate badge styles
	const badgeStyles = {};
	if (badgeBackgroundColor) {
		badgeStyles.backgroundColor = badgeBackgroundColor;
	}
	if (badgeTextColor) {
		badgeStyles.color = badgeTextColor;
	}

	// Calculate overlay styles for background layout
	const overlayStyles = {};
	if (layoutPreset === 'background') {
		if (overlayColor) {
			overlayStyles.backgroundColor = overlayColor;
			overlayStyles.opacity = overlayOpacity / 100;
		} else {
			// Use theme contrast color at full opacity, let overlayOpacity control transparency
			overlayStyles.backgroundColor =
				'var(--wp--preset--color--contrast, #000)';
			overlayStyles.opacity = overlayOpacity / 100;
		}
	}

	// Content alignment class
	const contentAlignmentClass = `airo-wp-card__content--${contentAlignment}`;

	// Options for select controls
	const layoutOptions = [
		{ label: __('Standard (Image Top)', 'airo-wp'), value: 'standard' },
		{
			label: __('Horizontal (Image Left)', 'airo-wp'),
			value: 'horizontal-left',
		},
		{
			label: __('Horizontal (Image Right)', 'airo-wp'),
			value: 'horizontal-right',
		},
		{
			label: __('Background (Image Behind)', 'airo-wp'),
			value: 'background',
		},
		{ label: __('Minimal (No Image)', 'airo-wp'), value: 'minimal' },
		{
			label: __('Featured (Large Image)', 'airo-wp'),
			value: 'featured',
		},
	];

	const visualStyleOptions = [
		{ label: __('Default', 'airo-wp'), value: 'default' },
		{ label: __('Outlined', 'airo-wp'), value: 'outlined' },
		{ label: __('Filled', 'airo-wp'), value: 'filled' },
		{ label: __('Shadow', 'airo-wp'), value: 'shadow' },
		{ label: __('Minimal', 'airo-wp'), value: 'minimal' },
	];

	const alignmentOptions = [
		{ label: __('Left', 'airo-wp'), value: 'left' },
		{ label: __('Center', 'airo-wp'), value: 'center' },
		{ label: __('Right', 'airo-wp'), value: 'right' },
	];

	const badgeStyleOptions = [
		{ label: __('Floating (Over Card)', 'airo-wp'), value: 'floating' },
		{ label: __('Inline (In Content)', 'airo-wp'), value: 'inline' },
	];

	const badgeFloatingPositionOptions = [
		{ label: __('Top Left', 'airo-wp'), value: 'top-left' },
		{ label: __('Top Right', 'airo-wp'), value: 'top-right' },
		{ label: __('Bottom Left', 'airo-wp'), value: 'bottom-left' },
		{ label: __('Bottom Right', 'airo-wp'), value: 'bottom-right' },
	];

	const badgeInlinePositionOptions = [
		{ label: __('Above Title', 'airo-wp'), value: 'above-title' },
		{ label: __('Below Title', 'airo-wp'), value: 'below-title' },
	];

	// Render badge
	const renderBadge = () => {
		if (!showBadge || !badgeText) {
			return null;
		}

		const badgeClass =
			badgeStyle === 'floating'
				? `airo-wp-card__badge airo-wp-card__badge--floating airo-wp-card__badge--${badgeFloatingPosition}`
				: `airo-wp-card__badge airo-wp-card__badge--inline airo-wp-card__badge--${badgeInlinePosition}`;

		return (
			<span className={badgeClass} style={badgeStyles}>
				{badgeText}
			</span>
		);
	};

	// Render image
	const renderImage = () => {
		if (!showImage || layoutPreset === 'minimal') {
			return null;
		}

		// Placeholder for background layout
		if (layoutPreset === 'background') {
			if (!imageUrl) {
				return (
					<div className="airo-wp-card__background airo-wp-card__background--placeholder">
						<div className="airo-wp-card__placeholder-content">
							<span className="dashicons dashicons-format-image"></span>
							<span>
								{__('Select background image', 'airo-wp')}
							</span>
						</div>
					</div>
				);
			}
			return (
				<div
					className="airo-wp-card__background"
					style={{ backgroundImage: `url(${imageUrl})` }}
				>
					<div className="airo-wp-card__overlay" style={overlayStyles} />
				</div>
			);
		}

		// Placeholder for standard layouts
		if (!imageUrl) {
			return (
				<div className="airo-wp-card__image-wrapper airo-wp-card__image-wrapper--placeholder">
					<div className="airo-wp-card__placeholder-content">
						<span className="dashicons dashicons-format-image"></span>
						<span>{__('Select image', 'airo-wp')}</span>
					</div>
				</div>
			);
		}

		return (
			<div className="airo-wp-card__image-wrapper">
				<img
					src={imageUrl}
					alt={imageAlt}
					className="airo-wp-card__image"
					style={imageStyles}
				/>
			</div>
		);
	};

	// Render content
	const renderContent = () => (
		<div
			className={`airo-wp-card__content ${layoutPreset === 'background' ? contentAlignmentClass : ''}`}
		>
			{badgeStyle === 'inline' &&
				badgeInlinePosition === 'above-title' &&
				renderBadge()}

			{showTitle && (
				<RichText
					tagName="h3"
					className="airo-wp-card__title"
					value={title}
					onChange={(value) => setAttributes({ title: value })}
					placeholder={__('Card Title…', 'airo-wp')}
				/>
			)}

			{badgeStyle === 'inline' &&
				badgeInlinePosition === 'below-title' &&
				renderBadge()}

			{showSubtitle && (
				<RichText
					tagName="p"
					className="airo-wp-card__subtitle"
					value={subtitle}
					onChange={(value) => setAttributes({ subtitle: value })}
					placeholder={__('Card Subtitle…', 'airo-wp')}
				/>
			)}

			{showBody && (
				<RichText
					tagName="p"
					className="airo-wp-card__body"
					value={bodyText}
					onChange={(value) => setAttributes({ bodyText: value })}
					placeholder={__(
						'Card description goes here…',
						'airo-wp'
					)}
				/>
			)}

			{showCta && <div {...innerBlocksProps} />}
		</div>
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
							layoutPreset: 'standard',
							visualStyle: 'default',
							imageId: 0,
							imageUrl: '',
							imageAlt: '',
							imageAspectRatio: '16-9',
							imageCustomAspectRatio: '',
							imageObjectFit: 'cover',
							imageFocalPoint: { x: 0.5, y: 0.5 },
							overlayOpacity: 80,
							contentAlignment: 'center',
							badgeText: '',
							badgeStyle: 'floating',
							badgeFloatingPosition: 'top-right',
							badgeInlinePosition: 'above-title',
							showImage: true,
							showTitle: true,
							showSubtitle: true,
							showBody: true,
							showBadge: true,
							showCta: true,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Layout Preset', 'airo-wp')}
						hasValue={() => layoutPreset !== 'standard'}
						onDeselect={() =>
							setAttributes({ layoutPreset: 'standard' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Layout Preset', 'airo-wp')}
							value={layoutPreset}
							options={layoutOptions}
							onChange={(value) =>
								setAttributes({ layoutPreset: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Visual Style', 'airo-wp')}
						hasValue={() => visualStyle !== 'default'}
						onDeselect={() =>
							setAttributes({ visualStyle: 'default' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Visual Style', 'airo-wp')}
							value={visualStyle}
							options={visualStyleOptions}
							onChange={(value) =>
								setAttributes({ visualStyle: value })
							}
							help={__(
								'Choose a visual style for the card appearance.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{layoutPreset !== 'minimal' && showImage && (
						<DsgoInspectorPanel.Item
							label={__('Image', 'airo-wp')}
							hasValue={() => !!imageUrl}
							onDeselect={() =>
								setAttributes({
									imageId: 0,
									imageUrl: '',
									imageAlt: '',
									imageFocalPoint: { x: 0.5, y: 0.5 },
								})
							}
							isShownByDefault
						>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={(media) => {
										setAttributes({
											imageId: media.id,
											imageUrl: media.url,
											imageAlt: media.alt || '',
										});
									}}
									allowedTypes={['image']}
									value={imageUrl}
									render={({ open }) => (
										<>
											{imageUrl ? (
												<>
													<img
														src={imageUrl}
														alt={imageAlt}
														style={{
															width: '100%',
															height: 'auto',
															marginBottom: '8px',
															borderRadius: '4px',
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
															onClick={() =>
																setAttributes({
																	imageId: 0,
																	imageUrl:
																		'',
																	imageAlt:
																		'',
																})
															}
															variant="secondary"
															isDestructive
														>
															{__(
																'Remove',
																'airo-wp'
															)}
														</Button>
													</div>
												</>
											) : (
												<Button
													onClick={open}
													variant="secondary"
													style={{
														width: '100%',
														justifyContent:
															'center',
													}}
												>
													{__(
														'Select Image',
														'airo-wp'
													)}
												</Button>
											)}
										</>
									)}
								/>
							</MediaUploadCheck>
						</DsgoInspectorPanel.Item>
					)}

					{layoutPreset !== 'minimal' && showImage && imageUrl && (
						<DsgoInspectorPanel.Item
							label={__('Alt Text', 'airo-wp')}
							hasValue={() => imageAlt !== ''}
							onDeselect={() => setAttributes({ imageAlt: '' })}
							isShownByDefault
						>
							<TextControl
								label={__('Alt Text', 'airo-wp')}
								value={imageAlt}
								onChange={(value) =>
									setAttributes({ imageAlt: value })
								}
								help={__(
									'Describe the image for accessibility.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{layoutPreset !== 'minimal' && showImage && imageUrl && (
						<DsgoInspectorPanel.Item
							label={__('Aspect Ratio', 'airo-wp')}
							hasValue={() => imageAspectRatio !== '16-9'}
							onDeselect={() =>
								setAttributes({
									imageAspectRatio: '16-9',
									imageCustomAspectRatio: '',
								})
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Aspect Ratio', 'airo-wp')}
								value={imageAspectRatio}
								options={[
									{
										label: __('16:9', 'airo-wp'),
										value: '16-9',
									},
									{
										label: __('4:3', 'airo-wp'),
										value: '4-3',
									},
									{
										label: __('1:1', 'airo-wp'),
										value: '1-1',
									},
									{
										label: __('Original', 'airo-wp'),
										value: 'original',
									},
									{
										label: __('Custom', 'airo-wp'),
										value: 'custom',
									},
								]}
								onChange={(value) =>
									setAttributes({
										imageAspectRatio: value,
									})
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{layoutPreset !== 'minimal' &&
						showImage &&
						imageUrl &&
						imageAspectRatio === 'custom' && (
							<DsgoInspectorPanel.Item
								label={__('Custom Aspect Ratio', 'airo-wp')}
								hasValue={() => imageCustomAspectRatio !== ''}
								onDeselect={() =>
									setAttributes({
										imageCustomAspectRatio: '',
									})
								}
								isShownByDefault
							>
								<TextControl
									label={__(
										'Custom Aspect Ratio',
										'airo-wp'
									)}
									value={imageCustomAspectRatio}
									onChange={(value) =>
										setAttributes({
											imageCustomAspectRatio: value,
										})
									}
									placeholder="16 / 9"
									help={__(
										'E.g., "16 / 9" or "2 / 1"',
										'airo-wp'
									)}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							</DsgoInspectorPanel.Item>
						)}

					{layoutPreset !== 'minimal' && showImage && imageUrl && (
						<DsgoInspectorPanel.Item
							label={__('Object Fit', 'airo-wp')}
							hasValue={() => imageObjectFit !== 'cover'}
							onDeselect={() =>
								setAttributes({ imageObjectFit: 'cover' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Object Fit', 'airo-wp')}
								value={imageObjectFit}
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
										label: __('Fill', 'airo-wp'),
										value: 'fill',
									},
									{
										label: __('Scale Down', 'airo-wp'),
										value: 'scale-down',
									},
								]}
								onChange={(value) =>
									setAttributes({
										imageObjectFit: value,
									})
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{layoutPreset === 'background' && (
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
								step={5}
								help={__(
									'Darkens the background image to improve text readability.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{layoutPreset === 'background' && (
						<DsgoInspectorPanel.Item
							label={__('Content Alignment', 'airo-wp')}
							hasValue={() => contentAlignment !== 'center'}
							onDeselect={() =>
								setAttributes({ contentAlignment: 'center' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Content Alignment', 'airo-wp')}
								value={contentAlignment}
								options={alignmentOptions}
								onChange={(value) =>
									setAttributes({ contentAlignment: value })
								}
								help={__(
									'Horizontal alignment for content over background image.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Badge Text', 'airo-wp')}
						hasValue={() => badgeText !== ''}
						onDeselect={() => setAttributes({ badgeText: '' })}
						isShownByDefault
					>
						<TextControl
							label={__('Badge Text', 'airo-wp')}
							value={badgeText}
							onChange={(value) =>
								setAttributes({ badgeText: value })
							}
							placeholder={__('NEW', 'airo-wp')}
							help={__(
								'Leave empty to hide the badge.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{badgeText && (
						<DsgoInspectorPanel.Item
							label={__('Badge Style', 'airo-wp')}
							hasValue={() => badgeStyle !== 'floating'}
							onDeselect={() =>
								setAttributes({ badgeStyle: 'floating' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Badge Style', 'airo-wp')}
								value={badgeStyle}
								options={badgeStyleOptions}
								onChange={(value) =>
									setAttributes({ badgeStyle: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{badgeText && badgeStyle === 'floating' && (
						<DsgoInspectorPanel.Item
							label={__('Floating Position', 'airo-wp')}
							hasValue={() =>
								badgeFloatingPosition !== 'top-right'
							}
							onDeselect={() =>
								setAttributes({
									badgeFloatingPosition: 'top-right',
								})
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Floating Position', 'airo-wp')}
								value={badgeFloatingPosition}
								options={badgeFloatingPositionOptions}
								onChange={(value) =>
									setAttributes({
										badgeFloatingPosition: value,
									})
								}
								help={__(
									'Position the badge over the card.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{badgeText && badgeStyle === 'inline' && (
						<DsgoInspectorPanel.Item
							label={__('Inline Position', 'airo-wp')}
							hasValue={() =>
								badgeInlinePosition !== 'above-title'
							}
							onDeselect={() =>
								setAttributes({
									badgeInlinePosition: 'above-title',
								})
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Inline Position', 'airo-wp')}
								value={badgeInlinePosition}
								options={badgeInlinePositionOptions}
								onChange={(value) =>
									setAttributes({
										badgeInlinePosition: value,
									})
								}
								help={__(
									'Position the badge in the content flow.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{layoutPreset !== 'minimal' && (
						<DsgoInspectorPanel.Item
							label={__('Show Image', 'airo-wp')}
							hasValue={() => showImage !== true}
							onDeselect={() =>
								setAttributes({ showImage: true })
							}
							isShownByDefault
						>
							<ToggleControl
								label={__('Show Image', 'airo-wp')}
								checked={showImage}
								onChange={(value) =>
									setAttributes({ showImage: value })
								}
								help={__(
									'Display the card image.',
									'airo-wp'
								)}
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Show Title', 'airo-wp')}
						hasValue={() => showTitle !== true}
						onDeselect={() => setAttributes({ showTitle: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Title', 'airo-wp')}
							checked={showTitle}
							onChange={(value) =>
								setAttributes({ showTitle: value })
							}
							help={__('Display the card title.', 'airo-wp')}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Show Subtitle', 'airo-wp')}
						hasValue={() => showSubtitle !== true}
						onDeselect={() => setAttributes({ showSubtitle: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Subtitle', 'airo-wp')}
							checked={showSubtitle}
							onChange={(value) =>
								setAttributes({ showSubtitle: value })
							}
							help={__(
								'Display the card subtitle.',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Show Body Text', 'airo-wp')}
						hasValue={() => showBody !== true}
						onDeselect={() => setAttributes({ showBody: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Body Text', 'airo-wp')}
							checked={showBody}
							onChange={(value) =>
								setAttributes({ showBody: value })
							}
							help={__(
								'Display the card body text.',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Show Badge', 'airo-wp')}
						hasValue={() => showBadge !== true}
						onDeselect={() => setAttributes({ showBadge: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Badge', 'airo-wp')}
							checked={showBadge}
							onChange={(value) =>
								setAttributes({ showBadge: value })
							}
							help={__(
								'Display the badge element.',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Show CTA Button', 'airo-wp')}
						hasValue={() => showCta !== true}
						onDeselect={() => setAttributes({ showCta: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show CTA Button', 'airo-wp')}
							checked={showCta}
							onChange={(value) =>
								setAttributes({ showCta: value })
							}
							help={__(
								'Display the call-to-action button.',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Border', 'airo-wp')}
					settings={borderColorSettings}
					{...colorGradientSettings}
				/>

				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Badge Colors', 'airo-wp')}
					settings={[
						{
							label: __('Badge Background', 'airo-wp'),
							colorValue: decodeColorValue(
								badgeBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									badgeBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Badge Text', 'airo-wp'),
							colorValue: decodeColorValue(
								badgeTextColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									badgeTextColor:
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

				{layoutPreset === 'background' && (
					<ColorGradientSettingsDropdown
						panelId={clientId}
						title={__('Overlay Color', 'airo-wp')}
						settings={[
							{
								label: __('Overlay', 'airo-wp'),
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
				)}
			</InspectorControls>

			<div {...blockProps}>
				{badgeStyle === 'floating' && renderBadge()}
				{layoutPreset === 'background' && renderImage()}

				<div className="airo-wp-card__inner">
					{layoutPreset !== 'background' && renderImage()}
					{renderContent()}
				</div>
			</div>
		</>
	);
}
