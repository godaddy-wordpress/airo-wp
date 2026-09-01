/**
 * Chart Block - Value format controls
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { ToggleControl, TextControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

/**
 * Prefix, suffix, and thousands grouping for every value the chart prints.
 *
 * The affixes reach the bar/point labels, the y-axis ticks, and the
 * screen-reader data table. A donut's slice labels are a share of the total
 * rather than the author's value, so they are formatted without these and the
 * suffix help says so.
 *
 * @param {Object}   props                Component props.
 * @param {string}   props.chartType      Current chart type.
 * @param {string}   props.valuePrefix    Prefix attribute.
 * @param {string}   props.valueSuffix    Suffix attribute.
 * @param {boolean}  props.groupThousands Grouping attribute.
 * @param {Function} props.setAttributes  Block attribute setter.
 * @return {Element} Inspector items.
 */
export function ValueFormatControls({
	chartType,
	valuePrefix,
	valueSuffix,
	groupThousands,
	setAttributes,
}) {
	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Value prefix', 'airo-wp')}
				hasValue={() => '' !== valuePrefix}
				onDeselect={() => setAttributes({ valuePrefix: '' })}
				isShownByDefault
			>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Value prefix', 'airo-wp')}
					value={valuePrefix}
					onChange={(value) => setAttributes({ valuePrefix: value })}
					help={__(
						'Goes in front of every value, e.g. $. Include a space if you want one.',
						'airo-wp'
					)}
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Value suffix', 'airo-wp')}
				hasValue={() => '' !== valueSuffix}
				onDeselect={() => setAttributes({ valueSuffix: '' })}
				isShownByDefault
			>
				<TextControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={__('Value suffix', 'airo-wp')}
					value={valueSuffix}
					onChange={(value) => setAttributes({ valueSuffix: value })}
					help={
						'donut' === chartType
							? __(
									'Goes after every value in the data table. Donut slice labels always show a share of the total.',
									'airo-wp'
								)
							: __(
									'Goes after every value, e.g. %. Include a space if you want one.',
									'airo-wp'
								)
					}
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Group thousands', 'airo-wp')}
				hasValue={() => true === groupThousands}
				onDeselect={() => setAttributes({ groupThousands: false })}
				isShownByDefault
			>
				<ToggleControl
					__nextHasNoMarginBottom
					label={__('Group thousands', 'airo-wp')}
					checked={groupThousands}
					onChange={(value) =>
						setAttributes({ groupThousands: value })
					}
					help={__(
						'Separates large numbers, e.g. 1,234,567.',
						'airo-wp'
					)}
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}
