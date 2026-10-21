/**
 * Row Block - Edit Component
 *
 * Flexible horizontal layouts with wrapping.
 * Leverages WordPress's native flex layout system.
 *
 * @since 1.0.0
 */

import { __, sprintf } from '@wordpress/i18n';
import { getAlignItemsValue } from './alignment';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
	store as blockEditorStore,
	useSettings,
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	ToggleControl,
	SelectControl,
	__experimentalUnitControl as UnitControl,
	__experimentalUseCustomUnits as useCustomUnits,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import LayoutControls from '../../extensions/layout/controls';
import { useFixedOrientation } from '../../hooks';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import {
	convertPresetToCSSVar,
	convertColorToCSSVar,
} from '../../utils/convert-preset-to-css-var';
import { getOverlayOpacity } from '../../utils/overlay-opacity';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import {
	hasOverlayStyleClass,
	hoverVariationClasses,
} from '../../utils/style-variation-classes';

/**
 * Row Container Edit Component
 *
 * @param {Object}   props               Component props
 * @param {Object}   props.attributes    Block attributes
 * @param {Function} props.setAttributes Function to update attributes
 * @param {string}   props.clientId      Block client ID
 * @return {JSX.Element} Edit component
 */
export default function RowEdit({ attributes, setAttributes, clientId }) {
	const {
		align,
		className,
		tagName = 'div',
		constrainWidth,
		contentWidth,
		overlayColor,
		hoverBackgroundColor,
		hoverTextColor,
		hoverIconBackgroundColor,
		hoverButtonBackgroundColor,
		mobileStack,
		layout,
	} = attributes;

	// Auto-migrate old blocks that use className for alignment
	useEffect(() => {
		if (!align && className) {
			let newAlign;
			if (className.includes('alignfull')) {
				newAlign = 'full';
			} else if (className.includes('alignwide')) {
				newAlign = 'wide';
			}

			if (newAlign) {
				const cleanClassName = className
					.split(' ')
					.filter((cls) => cls !== 'alignfull' && cls !== 'alignwide')
					.join(' ')
					.trim();

				setAttributes({
					align: newAlign,
					className: cleanClassName || undefined,
				});
			}
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, []); // Only run once on mount

	// Get theme settings (WP 6.5+)
	const [themeContentSize] = useSettings('layout.contentSize');

	// Get theme color palette and gradient settings
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// Get available spacing units
	const units = useCustomUnits({
		availableUnits: ['px', 'em', 'rem', 'vh', 'vw', '%'],
	});

	// A Row is always horizontal; the orientation toggle is hidden.
	useFixedOrientation(layout, 'horizontal', setAttributes);

	// Get inner blocks to determine if container is empty
	const hasInnerBlocks = useSelect(
		(select) =>
			select(blockEditorStore).getBlock(clientId)?.innerBlocks?.length >
			0,
		[clientId]
	);

	// Block wrapper props - outer div stays full width (must match save.js EXACTLY)
	const hasOverlay = !!overlayColor || hasOverlayStyleClass(className);
	const blockClassName = [
		'airo-wp-flex',
		mobileStack && 'airo-wp-flex--mobile-stack',
		hasOverlay && 'airo-wp-flex--has-overlay',
		...hoverVariationClasses(className, 'airo-wp-flex'),
	]
		.filter(Boolean)
		.join(' ');

	const TagName = tagName || 'div';
	const blockProps = useBlockProps({
		className: blockClassName,
		style: {
			...(hoverBackgroundColor && {
				'--airo-wp-hover-bg-color':
					convertColorToCSSVar(hoverBackgroundColor),
			}),
			...(hoverTextColor && {
				'--airo-wp-hover-text-color':
					convertColorToCSSVar(hoverTextColor),
			}),
			...(hoverIconBackgroundColor && {
				'--airo-wp-parent-hover-icon-bg': convertColorToCSSVar(
					hoverIconBackgroundColor
				),
			}),
			...(hoverButtonBackgroundColor && {
				'--airo-wp-parent-hover-button-bg': convertColorToCSSVar(
					hoverButtonBackgroundColor
				),
			}),
			...(overlayColor && {
				'--airo-wp-overlay-color': convertColorToCSSVar(overlayColor),
				'--airo-wp-overlay-opacity': getOverlayOpacity(
					overlayColor,
					attributes.overlayOpacity
				),
			}),
		},
	});

	// Extract gap AFTER creating blockProps, so we can move it to inner div instead (must match save.js EXACTLY)
	// WordPress layout support stores gap in attributes.style.spacing.blockGap
	// Convert from WordPress preset format (var:preset|spacing|md) to CSS var (var(--wp--preset--spacing--md))
	const rawGapValue = attributes.style?.spacing?.blockGap;
	const gapValue = convertPresetToCSSVar(rawGapValue);

	// Remove gap from outer div's inline styles - it should only be on inner div
	// This prevents WordPress from applying gap to the wrong element
	if (blockProps.style?.gap) {
		delete blockProps.style.gap;
	}

	// Share the saved flex layout. Restore explicit alignment after WordPress merges
	// its editor styles: vertical rows otherwise reinterpret justification as alignment.
	const innerStyle = {
		display: 'flex',
		// Apply layout justifyContent to inner div where flex children are
		justifyContent: layout?.justifyContent || 'left',
		// Apply flex-wrap from layout support
		// Fallback must match block.json supports.layout.default.flexWrap ("nowrap").
		// Using "wrap" here made the editor preview disagree with the toggle state
		// and caused children to appear stacked on fresh rows.
		flexWrap: layout?.flexWrap || 'nowrap',
		// Apply gap from blockProps or attributes
		...(gapValue && { gap: gapValue }),
	};

	// Apply width constraints if enabled
	// Use custom contentWidth if set, otherwise fallback to theme's contentSize
	if (constrainWidth) {
		innerStyle.maxWidth = contentWidth || themeContentSize || '1140px';
		innerStyle.marginLeft = 'auto';
		innerStyle.marginRight = 'auto';
	}

	// Merge inner blocks props
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-flex__inner',
			style: innerStyle,
		},
		{
			templateLock: false,
			renderAppender: hasInnerBlocks
				? undefined
				: InnerBlocks.ButtonBlockAppender,
		}
	);

	const alignItems = getAlignItemsValue(layout?.verticalAlignment);
	if (alignItems) {
		innerBlocksProps.style = { ...innerBlocksProps.style, alignItems };
	}

	return (
		<>
			<InspectorControls group="advanced">
				<SelectControl
					label={__('HTML Element', 'airo-wp')}
					value={tagName}
					options={[
						{
							label: __('Default (<div>)', 'airo-wp'),
							value: 'div',
						},
						{ label: '<section>', value: 'section' },
						{ label: '<article>', value: 'article' },
						{ label: '<aside>', value: 'aside' },
						{ label: '<header>', value: 'header' },
						{ label: '<footer>', value: 'footer' },
						{ label: '<main>', value: 'main' },
					]}
					onChange={(value) => setAttributes({ tagName: value })}
					help={__(
						'Choose the HTML element for this block. Use semantic elements when appropriate for better accessibility.',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</InspectorControls>

			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							mobileStack: false,
							constrainWidth: false,
							contentWidth: '',
							dsgoLayout: undefined,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Stack on Mobile', 'airo-wp')}
						hasValue={() => mobileStack !== false}
						onDeselect={() => setAttributes({ mobileStack: false })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Stack on Mobile', 'airo-wp')}
							checked={mobileStack}
							onChange={(value) =>
								setAttributes({ mobileStack: value })
							}
							help={
								mobileStack
									? __(
											'Items will stack vertically on mobile devices',
											'airo-wp'
										)
									: __(
											'Items maintain flex layout on all devices',
											'airo-wp'
										)
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Constrain Inner Width', 'airo-wp')}
						hasValue={() => constrainWidth !== false}
						onDeselect={() =>
							setAttributes({
								constrainWidth: false,
								contentWidth: '',
							})
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Constrain Inner Width', 'airo-wp')}
							checked={constrainWidth}
							onChange={(value) =>
								setAttributes({ constrainWidth: value })
							}
							help={
								constrainWidth
									? __(
											'Inner content is constrained to max width',
											'airo-wp'
										)
									: __(
											'Inner content spans full container width',
											'airo-wp'
										)
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{constrainWidth && (
						<DsgoInspectorPanel.Item
							label={__('Max Content Width', 'airo-wp')}
							hasValue={() => contentWidth !== ''}
							onDeselect={() =>
								setAttributes({ contentWidth: '' })
							}
							isShownByDefault
						>
							<UnitControl
								label={__('Max Content Width', 'airo-wp')}
								value={contentWidth}
								onChange={(value) =>
									setAttributes({ contentWidth: value })
								}
								placeholder={
									themeContentSize ||
									__('Theme default', 'airo-wp')
								}
								units={units}
								__unstableInputWidth="80px"
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								help={
									!contentWidth && themeContentSize
										? sprintf(
												/* translators: %s: theme content size value */
												__(
													'Using theme default: %s',
													'airo-wp'
												),
												themeContentSize
											)
										: ''
								}
							/>
						</DsgoInspectorPanel.Item>
					)}
					<LayoutControls
						name="airo-wp/row"
						attributes={attributes}
						setAttributes={setAttributes}
						clientId={clientId}
					/>
				</DsgoInspectorPanel>
			</InspectorControls>

			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Hover Settings', 'airo-wp')}
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
						{
							label: __('Hover Background Color', 'airo-wp'),
							colorValue: decodeColorValue(
								hoverBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									hoverBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Hover Text Color', 'airo-wp'),
							colorValue: decodeColorValue(
								hoverTextColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									hoverTextColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						// Only show icon background control if hover background is set
						...(hoverBackgroundColor
							? [
									{
										label: __(
											'Hover Icon Background Color',
											'airo-wp'
										),
										colorValue: decodeColorValue(
											hoverIconBackgroundColor,
											colorGradientSettings
										),
										onColorChange: (color) =>
											setAttributes({
												hoverIconBackgroundColor:
													encodeColorValue(
														color,
														colorGradientSettings
													) || '',
											}),
										enableAlpha: true,
										clearable: true,
									},
								]
							: []),
						// Only show button background control if hover background is set
						...(hoverBackgroundColor
							? [
									{
										label: __(
											'Hover Button Background Color',
											'airo-wp'
										),
										colorValue: decodeColorValue(
											hoverButtonBackgroundColor,
											colorGradientSettings
										),
										onColorChange: (color) =>
											setAttributes({
												hoverButtonBackgroundColor:
													encodeColorValue(
														color,
														colorGradientSettings
													) || '',
											}),
										enableAlpha: true,
										clearable: true,
									},
								]
							: []),
					]}
					{...colorGradientSettings}
				/>
			</InspectorControls>

			<TagName {...blockProps}>
				<div {...innerBlocksProps} />
			</TagName>
		</>
	);
}
