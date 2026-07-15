import { __ } from '@wordpress/i18n';
import {
	RangeControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';

import { DsgoInspectorPanel } from '../../../components/shared';
import { GROUP_BY_OPTIONS, DATE_PRECISION_OPTIONS } from './QuerySourcePanel';

export const RESULTS_DEFAULTS = {
	tagName: 'ul',
	itemTagName: 'li',
	columns: 1,
	columnsTablet: 0,
	columnsMobile: 0,
	firstItemColumnSpan: 1,
	firstItemRowSpan: 1,
	groupBy: null,
};

/**
 * Shared "Results layout" inspector panel used by both the parent
 * airo-wp/query proxy and the child airo-wp/query-results own inspector.
 *
 * Both callers read and write the same block attributes (the child's), so a
 * change in either panel is immediately reflected in the other.
 *
 * @param {Object}   root0
 * @param {Object}   root0.attributes      The query-results block's attributes.
 * @param {Function} root0.set             Partial-attribute setter (maps to setAttributes or updateBlockAttributes).
 * @param {string}   root0.panelId         clientId for ToolsPanel reset-state scoping.
 * @param {Array}    root0.taxonomyOptions [{value, label}] list from core-data.
 */
export default function ResultsLayoutControls({
	attributes,
	set,
	panelId,
	taxonomyOptions,
}) {
	const a = { ...RESULTS_DEFAULTS, ...attributes };
	const groupByField = a.groupBy?.field || 'none';

	const handleGroupByFieldChange = (value) => {
		if (value === 'none') {
			set({ groupBy: null });
		} else {
			set({ groupBy: { field: value, key: '' } });
		}
	};

	return (
		<DsgoInspectorPanel
			title={__('Results layout', 'airo-wp')}
			panelName="settings"
			panelId={panelId}
			resetAll={() => set(RESULTS_DEFAULTS)}
		>
			<DsgoInspectorPanel.Item
				label={__('Columns', 'airo-wp')}
				hasValue={() => a.columns !== RESULTS_DEFAULTS.columns}
				onDeselect={() => set({ columns: RESULTS_DEFAULTS.columns })}
				isShownByDefault
			>
				<RangeControl
					label={__('Columns', 'airo-wp')}
					value={a.columns || 1}
					min={1}
					max={6}
					onChange={(v) => set({ columns: v })}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Columns (tablet)', 'airo-wp')}
				hasValue={() =>
					a.columnsTablet !== RESULTS_DEFAULTS.columnsTablet
				}
				onDeselect={() =>
					set({ columnsTablet: RESULTS_DEFAULTS.columnsTablet })
				}
				isShownByDefault
			>
				<RangeControl
					label={__('Columns (tablet)', 'airo-wp')}
					help={__(
						'0 inherits the desktop column count.',
						'airo-wp'
					)}
					value={a.columnsTablet || 0}
					min={0}
					max={6}
					onChange={(v) => set({ columnsTablet: v })}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Columns (mobile)', 'airo-wp')}
				hasValue={() =>
					a.columnsMobile !== RESULTS_DEFAULTS.columnsMobile
				}
				onDeselect={() =>
					set({ columnsMobile: RESULTS_DEFAULTS.columnsMobile })
				}
				isShownByDefault
			>
				<RangeControl
					label={__('Columns (mobile)', 'airo-wp')}
					value={a.columnsMobile || 1}
					min={1}
					max={3}
					onChange={(v) => set({ columnsMobile: v })}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('First item column span', 'airo-wp')}
				hasValue={() =>
					a.firstItemColumnSpan !==
					RESULTS_DEFAULTS.firstItemColumnSpan
				}
				onDeselect={() =>
					set({
						firstItemColumnSpan:
							RESULTS_DEFAULTS.firstItemColumnSpan,
					})
				}
				isShownByDefault
			>
				<RangeControl
					label={__('First item column span', 'airo-wp')}
					help={__(
						'Make the first result a featured callout by spanning extra columns. 1 = no span. Capped at the current column count on smaller screens.',
						'airo-wp'
					)}
					value={a.firstItemColumnSpan || 1}
					min={1}
					max={Math.max(1, a.columns || 1)}
					onChange={(v) => set({ firstItemColumnSpan: v })}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('First item row span', 'airo-wp')}
				hasValue={() =>
					a.firstItemRowSpan !== RESULTS_DEFAULTS.firstItemRowSpan
				}
				onDeselect={() =>
					set({ firstItemRowSpan: RESULTS_DEFAULTS.firstItemRowSpan })
				}
				isShownByDefault
			>
				<RangeControl
					label={__('First item row span', 'airo-wp')}
					help={__(
						'Extra height for the featured first item (like a Pinterest hero). 1 = no span.',
						'airo-wp'
					)}
					value={a.firstItemRowSpan || 1}
					min={1}
					max={4}
					onChange={(v) => set({ firstItemRowSpan: v })}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('List tag', 'airo-wp')}
				hasValue={() => a.tagName !== RESULTS_DEFAULTS.tagName}
				onDeselect={() => set({ tagName: RESULTS_DEFAULTS.tagName })}
				isShownByDefault
			>
				<SelectControl
					label={__('List tag', 'airo-wp')}
					value={a.tagName || 'ul'}
					options={[
						{ label: 'ul', value: 'ul' },
						{ label: 'ol', value: 'ol' },
						{ label: 'div', value: 'div' },
					]}
					onChange={(v) => set({ tagName: v })}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Item tag', 'airo-wp')}
				hasValue={() => a.itemTagName !== RESULTS_DEFAULTS.itemTagName}
				onDeselect={() =>
					set({ itemTagName: RESULTS_DEFAULTS.itemTagName })
				}
				isShownByDefault
			>
				<SelectControl
					label={__('Item tag', 'airo-wp')}
					value={a.itemTagName || 'li'}
					options={[
						{ label: 'li', value: 'li' },
						{ label: 'div', value: 'div' },
						{ label: 'article', value: 'article' },
					]}
					onChange={(v) => set({ itemTagName: v })}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Group by', 'airo-wp')}
				hasValue={() => groupByField !== 'none'}
				onDeselect={() => set({ groupBy: null })}
				isShownByDefault
			>
				<SelectControl
					label={__('Group by', 'airo-wp')}
					value={groupByField}
					options={GROUP_BY_OPTIONS}
					onChange={handleGroupByFieldChange}
					help={__(
						'Grouped output requires a Query group header block inside the results template.',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			{groupByField === 'taxonomy' && (
				<DsgoInspectorPanel.Item
					label={__('Group taxonomy', 'airo-wp')}
					hasValue={() => !!a.groupBy?.key}
					onDeselect={() =>
						set({ groupBy: { ...a.groupBy, key: '' } })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Group taxonomy', 'airo-wp')}
						value={a.groupBy?.key || ''}
						options={[
							{
								value: '',
								label: __('— Select —', 'airo-wp'),
							},
							...(taxonomyOptions || []),
						]}
						onChange={(v) =>
							set({ groupBy: { ...a.groupBy, key: v } })
						}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
				</DsgoInspectorPanel.Item>
			)}

			{groupByField === 'meta' && (
				<DsgoInspectorPanel.Item
					label={__('Group meta key', 'airo-wp')}
					hasValue={() => !!a.groupBy?.key}
					onDeselect={() =>
						set({ groupBy: { ...a.groupBy, key: '' } })
					}
					isShownByDefault
				>
					<TextControl
						label={__('Group meta key', 'airo-wp')}
						value={a.groupBy?.key || ''}
						onChange={(v) =>
							set({ groupBy: { ...a.groupBy, key: v } })
						}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
				</DsgoInspectorPanel.Item>
			)}

			{groupByField === 'date' && (
				<DsgoInspectorPanel.Item
					label={__('Date precision', 'airo-wp')}
					hasValue={() => (a.groupBy?.key || 'Y') !== 'Y'}
					onDeselect={() =>
						set({ groupBy: { ...a.groupBy, key: 'Y' } })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Date precision', 'airo-wp')}
						value={a.groupBy?.key || 'Y'}
						options={DATE_PRECISION_OPTIONS}
						onChange={(v) =>
							set({ groupBy: { ...a.groupBy, key: v } })
						}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
				</DsgoInspectorPanel.Item>
			)}
		</DsgoInspectorPanel>
	);
}
