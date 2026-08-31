import { __ } from '@wordpress/i18n';
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
	ToggleControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import classnames from 'classnames';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';

export default function TimelineEdit({ attributes, setAttributes, clientId }) {
	const {
		orientation,
		layout,
		lineColor,
		lineThickness,
		connectorStyle,
		markerStyle,
		markerSize,
		markerColor,
		markerBorderColor,
		itemSpacing,
		animateOnScroll,
		animationDuration,
		staggerDelay,
	} = attributes;

	// Get theme color palette
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// CSS custom properties for styling
	const customStyles = {
		'--airo-wp-timeline-line-color':
			lineColor || 'var(--wp--preset--color--contrast, #e5e7eb)',
		'--airo-wp-timeline-line-thickness': `${lineThickness}px`,
		'--airo-wp-timeline-connector-style': connectorStyle,
		'--airo-wp-timeline-marker-size': `${markerSize}px`,
		'--airo-wp-timeline-marker-color':
			markerColor || 'var(--wp--preset--color--primary, #2563eb)',
		'--airo-wp-timeline-marker-border-color':
			markerBorderColor ||
			markerColor ||
			'var(--wp--preset--color--primary, #2563eb)',
		'--airo-wp-timeline-item-spacing': itemSpacing,
		'--airo-wp-timeline-animation-duration': `${animationDuration}ms`,
	};

	// Build class names
	const timelineClasses = classnames('airo-wp-timeline', {
		[`airo-wp-timeline--${orientation}`]: orientation,
		[`airo-wp-timeline--layout-${layout}`]: layout,
		[`airo-wp-timeline--marker-${markerStyle}`]: markerStyle,
		'airo-wp-timeline--animate': animateOnScroll,
	});

	const blockProps = useBlockProps({
		className: timelineClasses,
		style: customStyles,
	});

	// Inner blocks configuration
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-timeline__items',
		},
		{
			allowedBlocks: ['airo-wp/timeline-item'],
			template: [
				[
					'airo-wp/timeline-item',
					{
						date: __('2020', 'airo-wp'),
						title: __('First Milestone', 'airo-wp'),
					},
				],
				[
					'airo-wp/timeline-item',
					{
						date: __('2022', 'airo-wp'),
						title: __('Second Milestone', 'airo-wp'),
					},
				],
				[
					'airo-wp/timeline-item',
					{
						date: __('2024', 'airo-wp'),
						title: __('Third Milestone', 'airo-wp'),
					},
				],
			],
			orientation:
				orientation === 'horizontal' ? 'horizontal' : 'vertical',
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
							orientation: 'vertical',
							layout: 'alternating',
							lineThickness: 2,
							connectorStyle: 'solid',
							markerStyle: 'circle',
							markerSize: 16,
							itemSpacing: '2rem',
							animateOnScroll: true,
							animationDuration: 600,
							staggerDelay: 100,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Orientation', 'airo-wp')}
						hasValue={() => orientation !== 'vertical'}
						onDeselect={() =>
							setAttributes({ orientation: 'vertical' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Orientation', 'airo-wp')}
							value={orientation}
							options={[
								{
									label: __('Vertical', 'airo-wp'),
									value: 'vertical',
								},
								{
									label: __('Horizontal', 'airo-wp'),
									value: 'horizontal',
								},
							]}
							onChange={(value) =>
								setAttributes({ orientation: value })
							}
							help={__(
								'Horizontal timelines automatically switch to vertical on mobile devices.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{orientation === 'vertical' && (
						<DsgoInspectorPanel.Item
							label={__('Content Layout', 'airo-wp')}
							hasValue={() => layout !== 'alternating'}
							onDeselect={() =>
								setAttributes({ layout: 'alternating' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Content Layout', 'airo-wp')}
								value={layout}
								options={[
									{
										label: __(
											'Alternating Sides',
											'airo-wp'
										),
										value: 'alternating',
									},
									{
										label: __('Right Side Only', 'airo-wp'),
										value: 'right',
									},
								]}
								onChange={(value) =>
									setAttributes({ layout: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Item Spacing', 'airo-wp')}
						hasValue={() => itemSpacing !== '2rem'}
						onDeselect={() =>
							setAttributes({ itemSpacing: '2rem' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Item Spacing', 'airo-wp')}
							value={itemSpacing}
							onChange={(value) =>
								setAttributes({ itemSpacing: value ?? '2rem' })
							}
							units={[
								{ value: 'px', label: 'px', default: 32 },
								{ value: 'rem', label: 'rem', default: 2 },
								{ value: 'em', label: 'em', default: 2 },
							]}
							min={0}
							max={200}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Line Thickness', 'airo-wp')}
						hasValue={() => lineThickness !== 2}
						onDeselect={() => setAttributes({ lineThickness: 2 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Line Thickness', 'airo-wp')}
							value={lineThickness}
							onChange={(value) =>
								setAttributes({ lineThickness: value })
							}
							min={1}
							max={8}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Connector Style', 'airo-wp')}
						hasValue={() => connectorStyle !== 'solid'}
						onDeselect={() =>
							setAttributes({ connectorStyle: 'solid' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Connector Style', 'airo-wp')}
							value={connectorStyle}
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
							]}
							onChange={(value) =>
								setAttributes({ connectorStyle: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Marker Shape', 'airo-wp')}
						hasValue={() => markerStyle !== 'circle'}
						onDeselect={() =>
							setAttributes({ markerStyle: 'circle' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Marker Shape', 'airo-wp')}
							value={markerStyle}
							options={[
								{
									label: __('Circle', 'airo-wp'),
									value: 'circle',
								},
								{
									label: __('Square', 'airo-wp'),
									value: 'square',
								},
								{
									label: __('Diamond', 'airo-wp'),
									value: 'diamond',
								},
							]}
							onChange={(value) =>
								setAttributes({ markerStyle: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Marker Size', 'airo-wp')}
						hasValue={() => markerSize !== 16}
						onDeselect={() => setAttributes({ markerSize: 16 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Marker Size', 'airo-wp')}
							value={markerSize}
							onChange={(value) =>
								setAttributes({ markerSize: value })
							}
							min={8}
							max={48}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

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
							help={
								animateOnScroll
									? __(
											'Items will fade in as they scroll into view',
											'airo-wp'
										)
									: __(
											'All items will be visible immediately',
											'airo-wp'
										)
							}
							checked={animateOnScroll}
							onChange={(value) =>
								setAttributes({ animateOnScroll: value })
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{animateOnScroll && (
						<DsgoInspectorPanel.Item
							label={__('Animation Duration (ms)', 'airo-wp')}
							hasValue={() => animationDuration !== 600}
							onDeselect={() =>
								setAttributes({ animationDuration: 600 })
							}
							isShownByDefault
						>
							<RangeControl
								label={__('Animation Duration (ms)', 'airo-wp')}
								value={animationDuration}
								onChange={(value) =>
									setAttributes({ animationDuration: value })
								}
								min={100}
								max={2000}
								step={50}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{animateOnScroll && (
						<DsgoInspectorPanel.Item
							label={__('Stagger Delay (ms)', 'airo-wp')}
							hasValue={() => staggerDelay !== 100}
							onDeselect={() =>
								setAttributes({ staggerDelay: 100 })
							}
							isShownByDefault
						>
							<RangeControl
								label={__('Stagger Delay (ms)', 'airo-wp')}
								value={staggerDelay}
								onChange={(value) =>
									setAttributes({ staggerDelay: value })
								}
								min={0}
								max={500}
								step={25}
								help={__(
									'Delay between each item animation',
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
					settings={[
						{
							label: __('Line Color', 'airo-wp'),
							colorValue: decodeColorValue(
								lineColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									lineColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Marker Fill', 'airo-wp'),
							colorValue: decodeColorValue(
								markerColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									markerColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Marker Border', 'airo-wp'),
							colorValue: decodeColorValue(
								markerBorderColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									markerBorderColor:
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

			<div {...blockProps}>
				<div className="airo-wp-timeline__line" aria-hidden="true" />
				<div {...innerBlocksProps} />
			</div>
		</>
	);
}
