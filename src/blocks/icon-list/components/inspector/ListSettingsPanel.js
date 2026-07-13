/**
 * Icon List - List Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for icon-list layout and icon
 * attributes. Meant to be composed inside the Settings DsgoInspectorPanel
 * in icon-list/edit.js.
 *
 * @since 1.0.0
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	SelectControl,
	RangeControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

export const ListSettingsPanel = ({
	layout,
	iconSize,
	iconStyle,
	strokeWidth,
	effectiveStyle,
	iconDefaults,
	gap,
	iconPosition,
	columns,
	columnMinWidth,
	alignment,
	iconVerticalAlignment,
	setAttributes,
}) => {
	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Layout', 'airo-wp')}
				hasValue={() => layout !== 'vertical'}
				onDeselect={() => setAttributes({ layout: 'vertical' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Layout', 'airo-wp')}
					value={layout}
					options={[
						{
							label: __('Vertical', 'airo-wp'),
							value: 'vertical',
						},
						{
							label: __('Horizontal', 'airo-wp'),
							value: 'horizontal',
						},
						{ label: __('Grid', 'airo-wp'), value: 'grid' },
					]}
					onChange={(value) => setAttributes({ layout: value })}
					help={__(
						'Choose how list items are arranged',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{(layout === 'vertical' || layout === 'horizontal') && (
				<DsgoInspectorPanel.Item
					label={__('Alignment', 'airo-wp')}
					hasValue={() => alignment !== 'left'}
					onDeselect={() => setAttributes({ alignment: 'left' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Alignment', 'airo-wp')}
						value={alignment}
						options={[
							{ label: __('Left', 'airo-wp'), value: 'left' },
							{
								label: __('Center', 'airo-wp'),
								value: 'center',
							},
							{
								label: __('Right', 'airo-wp'),
								value: 'right',
							},
						]}
						onChange={(value) =>
							setAttributes({ alignment: value })
						}
						help={
							layout === 'vertical'
								? __(
										'Align list items horizontally',
										'airo-wp'
									)
								: __(
										'Distribute items horizontally',
										'airo-wp'
									)
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{layout === 'grid' && (
				<DsgoInspectorPanel.Item
					label={__('Columns', 'airo-wp')}
					hasValue={() => columns !== 1}
					onDeselect={() => setAttributes({ columns: 1 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Columns', 'airo-wp')}
						value={columns}
						onChange={(value) => setAttributes({ columns: value })}
						min={1}
						max={4}
						help={__(
							'Number of columns in grid layout',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{layout === 'grid' && (
				<DsgoInspectorPanel.Item
					label={__('Column Min Width', 'airo-wp')}
					hasValue={() => columnMinWidth !== ''}
					onDeselect={() => setAttributes({ columnMinWidth: '' })}
					isShownByDefault
				>
					<UnitControl
						label={__('Column Min Width', 'airo-wp')}
						value={columnMinWidth}
						onChange={(value) =>
							setAttributes({ columnMinWidth: value || '' })
						}
						units={[
							{ value: 'px', label: 'px' },
							{ value: 'em', label: 'em' },
							{ value: 'rem', label: 'rem' },
						]}
						isResetValueOnUnitChange
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						help={__(
							'When set, the grid auto-fits as many columns as fit at this minimum width, overriding the fixed column count.',
							'airo-wp'
						)}
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Icon Position', 'airo-wp')}
				hasValue={() => iconPosition !== 'left'}
				onDeselect={() => setAttributes({ iconPosition: 'left' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Icon Position', 'airo-wp')}
					value={iconPosition}
					options={[
						{ label: __('Left', 'airo-wp'), value: 'left' },
						{ label: __('Right', 'airo-wp'), value: 'right' },
						{ label: __('Top', 'airo-wp'), value: 'top' },
					]}
					onChange={(value) => setAttributes({ iconPosition: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{iconPosition !== 'top' && (
				<DsgoInspectorPanel.Item
					label={__('Icon Vertical Alignment', 'airo-wp')}
					hasValue={() => iconVerticalAlignment !== 'top'}
					onDeselect={() =>
						setAttributes({ iconVerticalAlignment: 'top' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Vertical Alignment', 'airo-wp')}
						value={iconVerticalAlignment}
						options={[
							{
								label: __('Top', 'airo-wp'),
								value: 'top',
							},
							{
								label: __('Center', 'airo-wp'),
								value: 'center',
							},
						]}
						onChange={(value) =>
							setAttributes({ iconVerticalAlignment: value })
						}
						help={__(
							'Vertically align the icon with the text content',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Icon Size', 'airo-wp')}
				hasValue={() => typeof iconSize === 'number'}
				onDeselect={() => setAttributes({ iconSize: undefined })}
				isShownByDefault
			>
				<RangeControl
					label={__('Icon Size', 'airo-wp')}
					value={iconSize}
					onChange={(value) =>
						setAttributes({
							iconSize:
								typeof value === 'number' ? value : undefined,
						})
					}
					min={16}
					max={128}
					allowReset
					placeholder={iconDefaults?.size}
					help={
						typeof iconSize !== 'number'
							? sprintf(
									/* translators: %d: inherited icon size in pixels. */
									__(
										'Inheriting theme default (%dpx).',
										'airo-wp'
									),
									iconDefaults?.size
								)
							: __(
									'Default icon size for all items',
									'airo-wp'
								)
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Icon Style', 'airo-wp')}
				hasValue={() => typeof iconStyle === 'string'}
				onDeselect={() => setAttributes({ iconStyle: undefined })}
				isShownByDefault
			>
				<ToggleGroupControl
					label={__('Icon Style', 'airo-wp')}
					value={effectiveStyle}
					onChange={(value) => setAttributes({ iconStyle: value })}
					help={
						!iconStyle &&
						sprintf(
							/* translators: %s: inherited icon style (Filled or Outlined). */
							__('Inheriting theme default (%s).', 'airo-wp'),
							iconDefaults?.style === 'outlined'
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

			{effectiveStyle === 'outlined' && (
				<DsgoInspectorPanel.Item
					label={__('Stroke Width', 'airo-wp')}
					hasValue={() => strokeWidth !== 1.5}
					onDeselect={() => setAttributes({ strokeWidth: 1.5 })}
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
				label={__('Gap', 'airo-wp')}
				hasValue={() => gap !== '24px'}
				onDeselect={() => setAttributes({ gap: '24px' })}
				isShownByDefault
			>
				<UnitControl
					label={__('Gap', 'airo-wp')}
					value={gap}
					onChange={(value) => setAttributes({ gap: value })}
					units={[
						{ value: 'px', label: 'px' },
						{ value: 'em', label: 'em' },
						{ value: 'rem', label: 'rem' },
					]}
					help={__('Space between list items', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
};
