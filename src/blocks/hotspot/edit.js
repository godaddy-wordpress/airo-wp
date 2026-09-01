import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	InspectorControls,
	store as blockEditorStore,
	useBlockProps,
	useInnerBlocksProps,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
} from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { seen, unseen } from '@wordpress/icons';
import DsgoChildToolbar from '../../components/shared/DsgoChildToolbar';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { useBlockColors } from '../../hooks';
import { HOTSPOT_ITEM_DUPLICATE_OVERRIDES } from '../hotspot-item/constants';
import { getSafeHotspotColor } from '../hotspot-item/utils';
import HotspotCanvas from './components/HotspotCanvas';
import HotspotInspector from './components/HotspotInspector';

const ALLOWED_BLOCKS = ['airo-wp/hotspot-item'];
const TEMPLATE = [
	['airo-wp/hotspot-item', { label: '+', x: 32, y: 42 }],
	['airo-wp/hotspot-item', { label: '+', x: 68, y: 60 }],
];

export default function HotspotEdit({ attributes, setAttributes, clientId }) {
	const [showOnlySelected, setShowOnlySelected] = useState(false);
	const {
		imageUrl,
		imageAlt,
		tooltipWidth,
		sequenceDuration,
		markerColor,
		markerBackgroundColor,
		tooltipBackgroundColor,
		tooltipTextColor,
	} = attributes;
	// Select the child order and only the selected child. `getBlock(clientId)`
	// rebuilds the whole subtree on every store change and returns a new
	// `innerBlocks` array each time, re-rendering this block for edits that
	// have nothing to do with it.
	const { itemClientIds, selectedBlockId, selectedItem } = useSelect(
		(select) => {
			const editor = select(blockEditorStore);
			const order = editor.getBlockOrder(clientId);
			const selected = editor.getSelectedBlockClientId();

			return {
				itemClientIds: order,
				selectedBlockId: selected,
				selectedItem: order.includes(selected)
					? editor.getBlock(selected)
					: null,
			};
		},
		[clientId]
	);
	const { updateBlockAttributes } = useDispatch(blockEditorStore);
	const { settings: colorSettings, colorGradientSettings } = useBlockColors({
		attributes,
		setAttributes,
		entries: [
			{
				label: __('Marker color', 'airo-wp'),
				attribute: 'markerColor',
			},
			{
				label: __('Marker background', 'airo-wp'),
				attribute: 'markerBackgroundColor',
			},
			{
				label: __('Tooltip background', 'airo-wp'),
				attribute: 'tooltipBackgroundColor',
			},
			{
				label: __('Tooltip text color', 'airo-wp'),
				attribute: 'tooltipTextColor',
			},
		],
	});
	const selectedIndex = selectedItem
		? itemClientIds.indexOf(selectedBlockId)
		: -1;
	const blockProps = useBlockProps({
		className: `airo-wp-hotspot airo-wp-hotspot--position-${attributes.tooltipPosition} airo-wp-hotspot--animation-${attributes.animation}${
			showOnlySelected && selectedItem
				? ' airo-wp-hotspot--editor-selected-only'
				: ''
		}`,
		style: {
			'--airo-wp-hotspot-tooltip-width': `${tooltipWidth}px`,
			'--airo-wp-hotspot-sequence-duration': `${sequenceDuration}ms`,
			...(getSafeHotspotColor(markerColor) && {
				'--airo-wp-hotspot-marker-color': convertColorToCSSVar(
					getSafeHotspotColor(markerColor)
				),
			}),
			...(getSafeHotspotColor(markerBackgroundColor) && {
				'--airo-wp-hotspot-marker-background': convertColorToCSSVar(
					getSafeHotspotColor(markerBackgroundColor)
				),
			}),
			...(getSafeHotspotColor(tooltipBackgroundColor) && {
				'--airo-wp-hotspot-tooltip-background': convertColorToCSSVar(
					getSafeHotspotColor(tooltipBackgroundColor)
				),
			}),
			...(getSafeHotspotColor(tooltipTextColor) && {
				'--airo-wp-hotspot-tooltip-color': convertColorToCSSVar(
					getSafeHotspotColor(tooltipTextColor)
				),
			}),
		},
	});
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'airo-wp-hotspot__items' },
		{ allowedBlocks: ALLOWED_BLOCKS, template: TEMPLATE }
	);
	const updateCoordinates = (item, coordinates) =>
		updateBlockAttributes(item.clientId, coordinates);

	return (
		<>
			<BlockControls group="block">
				<ToolbarButton
					icon={showOnlySelected ? seen : unseen}
					label={
						showOnlySelected
							? __('Show all hotspots', 'airo-wp')
							: __('Show only selected hotspot', 'airo-wp')
					}
					onClick={() => setShowOnlySelected((current) => !current)}
					isPressed={showOnlySelected}
					disabled={!selectedItem}
					showTooltip
				/>
				<DsgoChildToolbar
					parentClientId={clientId}
					childBlockName="airo-wp/hotspot-item"
					activeIndex={selectedIndex}
					cloneAttributeOverrides={HOTSPOT_ITEM_DUPLICATE_OVERRIDES}
					addLabel={__('Add hotspot', 'airo-wp')}
					duplicateLabel={__('Duplicate hotspot', 'airo-wp')}
					removeLabel={__('Remove hotspot', 'airo-wp')}
					orientation="vertical"
				/>
			</BlockControls>
			<InspectorControls>
				<HotspotInspector
					attributes={attributes}
					setAttributes={setAttributes}
					clientId={clientId}
				/>
			</InspectorControls>
			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Colors', 'airo-wp')}
					settings={colorSettings}
					{...colorGradientSettings}
				/>
			</InspectorControls>
			<div {...blockProps}>
				<HotspotCanvas
					imageUrl={imageUrl}
					imageAlt={imageAlt}
					innerBlocksProps={innerBlocksProps}
					selectedItem={selectedItem}
					onCoordinateChange={updateCoordinates}
				/>
			</div>
		</>
	);
}
