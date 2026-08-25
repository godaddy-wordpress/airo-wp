/**
 * Chart Block - Edit
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	SelectControl,
	RangeControl,
	ToggleControl,
	TextControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { DsgoInspectorPanel } from '../../components/shared';
import { DataEditor } from './components/DataEditor';
import { SeriesColors } from './components/SeriesColors';
import { stripWrapperAttributes } from './utils/strip-wrapper-attributes';

const TYPES = [
	{ value: 'bar', label: __('Bar', 'airo-wp') },
	{ value: 'line', label: __('Line', 'airo-wp') },
	{ value: 'donut', label: __('Donut', 'airo-wp') },
];

const SOURCES = [
	{ value: 'manual', label: __('Enter data', 'airo-wp') },
	{ value: 'meta', label: __('Post meta field', 'airo-wp') },
];

// Meta-sourced rows are only known to the server, so offer a fixed set of
// palette slots rather than hiding the colour controls entirely.
const META_SERIES_SLOTS = 6;

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		chartType,
		data,
		dataSource,
		metaKey,
		height,
		showLegend,
		showGrid,
		showValues,
		palette,
		label,
	} = attributes;
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
				>
					<DsgoInspectorPanel.Item
						label={__('Chart type', 'airo-wp')}
						hasValue={() => 'bar' !== chartType}
						onDeselect={() => setAttributes({ chartType: 'bar' })}
						isShownByDefault
					>
						<SelectControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={__('Chart type', 'airo-wp')}
							value={chartType}
							options={TYPES}
							onChange={(value) =>
								setAttributes({ chartType: value })
							}
							help={
								'donut' === chartType
									? __(
											'Slices are shares of a total, so rows of zero or less are left out.',
											'airo-wp'
										)
									: undefined
							}
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Description', 'airo-wp')}
						hasValue={() => !!label}
						onDeselect={() => setAttributes({ label: '' })}
						isShownByDefault
					>
						<TextControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={__('Description', 'airo-wp')}
							value={label}
							onChange={(value) =>
								setAttributes({ label: value })
							}
							help={__(
								'Read by screen readers as the data table caption.',
								'airo-wp'
							)}
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Data source', 'airo-wp')}
						hasValue={() => 'manual' !== dataSource}
						onDeselect={() =>
							setAttributes({ dataSource: 'manual' })
						}
						isShownByDefault
					>
						<SelectControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={__('Data source', 'airo-wp')}
							value={dataSource}
							options={SOURCES}
							onChange={(value) =>
								setAttributes({ dataSource: value })
							}
						/>
					</DsgoInspectorPanel.Item>

					{'meta' === dataSource && (
						<DsgoInspectorPanel.Item
							label={__('Meta key', 'airo-wp')}
							hasValue={() => !!metaKey}
							onDeselect={() => setAttributes({ metaKey: '' })}
							isShownByDefault
						>
							<TextControl
								__next40pxDefaultSize
								__nextHasNoMarginBottom
								label={__('Meta key', 'airo-wp')}
								value={metaKey}
								onChange={(value) =>
									setAttributes({ metaKey: value })
								}
								help={__(
									'The field must hold a JSON array of {label, value} objects.',
									'airo-wp'
								)}
							/>
						</DsgoInspectorPanel.Item>
					)}

					{'manual' === dataSource && (
						<DsgoInspectorPanel.Item
							label={__('Data', 'airo-wp')}
							hasValue={() => !!data?.length}
							onDeselect={() => setAttributes({ data: [] })}
							isShownByDefault
						>
							<DataEditor
								value={data}
								onChange={(value) =>
									setAttributes({ data: value })
								}
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>

				<DsgoInspectorPanel
					title={__('Style', 'airo-wp')}
					panelName="style"
					panelId={clientId}
				>
					<DsgoInspectorPanel.Item
						label={__('Height', 'airo-wp')}
						hasValue={() => 240 !== height}
						onDeselect={() => setAttributes({ height: 240 })}
						isShownByDefault
					>
						<RangeControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={__('Height', 'airo-wp')}
							value={height}
							min={80}
							max={800}
							step={10}
							onChange={(value) =>
								setAttributes({ height: value })
							}
						/>
					</DsgoInspectorPanel.Item>

					{/* A donut has no axis to label, so its legend is the only
					    sighted route from a slice to its category. */}
					{'donut' !== chartType && (
						<DsgoInspectorPanel.Item
							label={__('Legend', 'airo-wp')}
							hasValue={() => true !== showLegend}
							onDeselect={() =>
								setAttributes({ showLegend: true })
							}
							isShownByDefault
						>
							<ToggleControl
								__nextHasNoMarginBottom
								label={__('Show legend', 'airo-wp')}
								checked={showLegend}
								onChange={(value) =>
									setAttributes({ showLegend: value })
								}
								help={__(
									'Category names stay on the axis either way.',
									'airo-wp'
								)}
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Values', 'airo-wp')}
						hasValue={() => true !== showValues}
						onDeselect={() => setAttributes({ showValues: true })}
						isShownByDefault
					>
						<ToggleControl
							__nextHasNoMarginBottom
							label={__('Show values', 'airo-wp')}
							checked={showValues}
							onChange={(value) =>
								setAttributes({ showValues: value })
							}
							help={
								'donut' === chartType
									? __(
											'Labels each slice with its share of the total.',
											'airo-wp'
										)
									: __(
											'Labels each bar or point with its value.',
											'airo-wp'
										)
							}
						/>
					</DsgoInspectorPanel.Item>

					{'donut' !== chartType && (
						<DsgoInspectorPanel.Item
							label={__('Grid', 'airo-wp')}
							hasValue={() => true !== showGrid}
							onDeselect={() => setAttributes({ showGrid: true })}
							isShownByDefault
						>
							<ToggleControl
								__nextHasNoMarginBottom
								label={__('Show grid', 'airo-wp')}
								checked={showGrid}
								onChange={(value) =>
									setAttributes({ showGrid: value })
								}
								help={__(
									'Draws horizontal gridlines and axis labels.',
									'airo-wp'
								)}
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			<SeriesColors
				rows={
					'meta' === dataSource
						? Array.from({ length: META_SERIES_SLOTS }, () => ({}))
						: data
				}
				palette={palette}
				clientId={clientId}
				onChange={(value) => setAttributes({ palette: value })}
			/>

			<div {...blockProps}>
				<ServerSideRender
					block="airo-wp/chart"
					attributes={stripWrapperAttributes(attributes)}
					EmptyResponsePlaceholder={() => (
						<p>
							{__(
								'Add at least one data row to preview the chart.',
								'airo-wp'
							)}
						</p>
					)}
				/>
			</div>
		</>
	);
}
