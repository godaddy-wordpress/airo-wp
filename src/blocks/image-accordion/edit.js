import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	ToggleControl,
	SelectControl,
	RangeControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import classnames from 'classnames';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import {
	hasExplicitString,
	hasExplicitNumber,
} from '../../utils/has-explicit-value';
import ImageAccordionPlaceholder from './components/ImageAccordionPlaceholder';

export default function ImageAccordionEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		height,
		gap,
		expandedRatio,
		transitionDuration,
		enableOverlay,
		overlayColor,
		overlayOpacity,
		overlayOpacityExpanded,
		triggerType,
		defaultExpanded,
	} = attributes;

	// Get theme color palette and gradient settings
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// Single subscription powers both the placeholder gate and the
	// default-expanded picker so Gutenberg only tracks one subscriber for
	// this block's inner-block list. Only the (store-memoized) innerBlocks
	// reference is selected here — the itemOptions array is derived below
	// via useMemo so useSelect's own return value stays referentially
	// stable when nothing relevant changed.
	const children = useSelect(
		(select) =>
			select(blockEditorStore).getBlock(clientId)?.innerBlocks || [],
		[clientId]
	);
	const hasInnerBlocks = children.length > 0;

	const itemOptions = useMemo(() => {
		const options = [
			{
				label: __('None (no item expanded)', 'airo-wp'),
				value: '0',
			},
		];
		children.forEach((child, index) => {
			const heading = child.innerBlocks?.find(
				(inner) => inner.name === 'core/heading'
			);
			const raw = heading?.attributes?.content ?? '';
			// DOMParser handles malformed/unterminated tags safely
			// (unlike a naive regex).
			const parsed = new window.DOMParser().parseFromString(
				String(raw),
				'text/html'
			);
			const text = (parsed.body.textContent || '').trim().slice(0, 40);
			const label = text
				? sprintf(
						/* translators: %1$d: item position, %2$s: item title */
						__('Item %1$d: %2$s', 'airo-wp'),
						index + 1,
						text
					)
				: sprintf(
						/* translators: %d: item position */
						__('Item %d', 'airo-wp'),
						index + 1
					);
			options.push({ label, value: String(index + 1) });
		});
		return options;
	}, [children]);

	// Declaratively calculate classes based on attributes
	const accordionClasses = classnames('airo-wp-image-accordion', {
		'airo-wp-image-accordion--hover': triggerType === 'hover',
		'airo-wp-image-accordion--click': triggerType === 'click',
	});

	// Height and gap are written inline ONLY when the author sets an explicit
	// value (parity with save.js). Left unset they are omitted so the stylesheet
	// default owns them and the editor preview reflects the theme token / literal
	// fallback rather than a baked-in magic number.
	const hasExplicitHeight = hasExplicitString(height);
	const hasExplicitGap = hasExplicitString(gap);

	// Overlay props are written inline ONLY when the author set them (parity with
	// save.js). Left unset they are omitted so the editor preview inherits the
	// same parent-var → theme-token → literal cascade the frontend uses, rather
	// than pinning a magic number that would outrank the theme token.
	const hasExplicitColor = hasExplicitString(overlayColor);
	const hasExplicitOpacity = hasExplicitNumber(overlayOpacity);
	const hasExplicitOpacityExpanded = hasExplicitNumber(
		overlayOpacityExpanded
	);

	// Apply settings as CSS custom properties for consistent styling
	// Note: Unitless values must be strings to prevent React from adding 'px'
	const customStyles = {
		...(hasExplicitHeight && {
			'--airo-wp-image-accordion-height': height,
		}),
		...(hasExplicitGap && { '--airo-wp-image-accordion-gap': gap }),
		'--airo-wp-image-accordion-expanded-ratio': String(expandedRatio), // Unitless
		'--airo-wp-image-accordion-transition': transitionDuration,
		...(hasExplicitColor && {
			'--airo-wp-image-accordion-overlay-color':
				convertColorToCSSVar(overlayColor),
		}),
		...(hasExplicitOpacity && {
			'--airo-wp-image-accordion-overlay-opacity': String(
				overlayOpacity / 100
			), // Unitless
		}),
		...(hasExplicitOpacityExpanded && {
			'--airo-wp-image-accordion-overlay-opacity-expanded': String(
				overlayOpacityExpanded / 100
			), // Unitless
		}),
	};

	// Block wrapper props
	const blockProps = useBlockProps({
		className: accordionClasses,
		style: customStyles,
	});

	// Inner blocks configuration - ONLY allow image-accordion-item children.
	// Initial seeding is handled by ImageAccordionPlaceholder so authors pick
	// a starter layout instead of landing on a generic three-item template.
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-image-accordion__items',
		},
		{
			allowedBlocks: ['airo-wp/image-accordion-item'],
			orientation: 'vertical', // Always vertical in editor for easier editing
		}
	);

	if (!hasInnerBlocks) {
		return (
			<div {...blockProps}>
				<ImageAccordionPlaceholder
					clientId={clientId}
					setAttributes={setAttributes}
				/>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							height: undefined,
							gap: undefined,
							expandedRatio: 3,
							transitionDuration: '0.5s',
							triggerType: 'hover',
							defaultExpanded: 0,
							enableOverlay: true,
							overlayColor: undefined,
							overlayOpacity: undefined,
							overlayOpacityExpanded: undefined,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Height', 'airo-wp')}
						hasValue={() => !!height}
						onDeselect={() => setAttributes({ height: undefined })}
						isShownByDefault
					>
						<UnitControl
							label={__('Height', 'airo-wp')}
							value={height}
							onChange={(value) =>
								setAttributes({ height: value || undefined })
							}
							units={[
								{ value: 'px', label: 'px', default: 500 },
								{ value: 'vh', label: 'vh', default: 50 },
								{ value: 'rem', label: 'rem', default: 30 },
							]}
							min={200}
							max={1000}
							help={__(
								'Fixed height for the accordion',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Gap Between Items', 'airo-wp')}
						hasValue={() => !!gap}
						onDeselect={() => setAttributes({ gap: undefined })}
						isShownByDefault
					>
						<UnitControl
							label={__('Gap Between Items', 'airo-wp')}
							value={gap}
							onChange={(value) =>
								setAttributes({ gap: value || undefined })
							}
							units={[
								{ value: 'px', label: 'px', default: 4 },
								{ value: 'rem', label: 'rem', default: 0.25 },
							]}
							min={0}
							max={32}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Trigger Type', 'airo-wp')}
						hasValue={() => triggerType !== 'hover'}
						onDeselect={() =>
							setAttributes({ triggerType: 'hover' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Trigger Type', 'airo-wp')}
							value={triggerType}
							options={[
								{
									label: __('Hover (Desktop)', 'airo-wp'),
									value: 'hover',
								},
								{
									label: __('Click/Tap', 'airo-wp'),
									value: 'click',
								},
							]}
							onChange={(value) =>
								setAttributes({ triggerType: value })
							}
							help={__(
								'Hover is automatically replaced with click on mobile',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Default Expanded Item', 'airo-wp')}
						hasValue={() => defaultExpanded !== 0}
						onDeselect={() => setAttributes({ defaultExpanded: 0 })}
						isShownByDefault
					>
						<SelectControl
							label={__('Default Expanded Item', 'airo-wp')}
							value={String(defaultExpanded)}
							options={itemOptions}
							onChange={(value) =>
								setAttributes({
									defaultExpanded: parseInt(value, 10) || 0,
								})
							}
							help={__(
								'Which item is expanded when the page loads',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Expanded Ratio', 'airo-wp')}
						hasValue={() => expandedRatio !== 3}
						onDeselect={() => setAttributes({ expandedRatio: 3 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Expanded Ratio', 'airo-wp')}
							value={expandedRatio}
							onChange={(value) =>
								setAttributes({ expandedRatio: value })
							}
							min={2}
							max={5}
							step={0.5}
							help={__(
								'How much larger the expanded item becomes (others stay normal size)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Transition Duration', 'airo-wp')}
						hasValue={() => transitionDuration !== '0.5s'}
						onDeselect={() =>
							setAttributes({ transitionDuration: '0.5s' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Transition Duration', 'airo-wp')}
							value={transitionDuration}
							onChange={(value) =>
								setAttributes({
									transitionDuration: value || '0.5s',
								})
							}
							units={[
								{ value: 's', label: 's', default: 0.5 },
								{ value: 'ms', label: 'ms', default: 500 },
							]}
							min={0.1}
							max={2}
							help={__(
								'Speed of expansion/collapse animation',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Enable Overlay', 'airo-wp')}
						hasValue={() => enableOverlay !== true}
						onDeselect={() =>
							setAttributes({ enableOverlay: true })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Enable Overlay', 'airo-wp')}
							checked={enableOverlay}
							onChange={(value) =>
								setAttributes({ enableOverlay: value })
							}
							help={
								enableOverlay
									? __(
											'Overlay applied to all items',
											'airo-wp'
										)
									: __('No overlay on items', 'airo-wp')
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{enableOverlay && (
						<DsgoInspectorPanel.Item
							label={__('Overlay Opacity (Default)', 'airo-wp')}
							hasValue={() => overlayOpacity !== undefined}
							onDeselect={() =>
								setAttributes({ overlayOpacity: undefined })
							}
							isShownByDefault
						>
							<RangeControl
								label={__(
									'Overlay Opacity (Default)',
									'airo-wp'
								)}
								value={overlayOpacity ?? 40}
								onChange={(value) =>
									setAttributes({ overlayOpacity: value })
								}
								min={0}
								max={100}
								help={__(
									'Opacity when collapsed. Reset (⋮) to inherit the theme default.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{enableOverlay && (
						<DsgoInspectorPanel.Item
							label={__('Overlay Opacity (Expanded)', 'airo-wp')}
							hasValue={() =>
								overlayOpacityExpanded !== undefined
							}
							onDeselect={() =>
								setAttributes({
									overlayOpacityExpanded: undefined,
								})
							}
							isShownByDefault
						>
							<RangeControl
								label={__(
									'Overlay Opacity (Expanded)',
									'airo-wp'
								)}
								value={overlayOpacityExpanded ?? 20}
								onChange={(value) =>
									setAttributes({
										overlayOpacityExpanded: value,
									})
								}
								min={0}
								max={100}
								help={__(
									'Opacity when expanded. Reset (⋮) to inherit the theme default.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			{enableOverlay && (
				<InspectorControls group="color">
					<ColorGradientSettingsDropdown
						panelId={clientId}
						title={__('Overlay Color', 'airo-wp')}
						settings={[
							{
								label: __('Color', 'airo-wp'),
								colorValue: decodeColorValue(
									overlayColor,
									colorGradientSettings
								),
								onColorChange: (value) =>
									setAttributes({
										overlayColor: encodeColorValue(
											value,
											colorGradientSettings
										),
									}),
								clearable: true,
								enableAlpha: true,
							},
						]}
						{...colorGradientSettings}
					/>
				</InspectorControls>
			)}

			<div {...blockProps}>
				<div {...innerBlocksProps} />
			</div>
		</>
	);
}
