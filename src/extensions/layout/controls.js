import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { DsgoInspectorPanel } from '../../components/shared/DsgoInspectorPanel';
import { shouldExtendBlock } from '../../utils/should-extend-block';
import { isExtensionEnabled } from '../../utils/is-extension-enabled';
import { support, appliesTo, isContainer, updateLayout } from './utils';
import { labels, groupFields } from './labels';
import LayoutField from './field';

export function useLayoutParent(clientId) {
	return useSelect(
		(select) => {
			const store = select('core/block-editor');
			const parentId = store.getBlockRootClientId?.(clientId);
			return parentId ? store.getBlock(parentId) : null;
		},
		[clientId]
	);
}

/**
 * Reuse each native item's existing Settings panel for composition controls.
 * @param {Object} props Block edit props.
 * @return {Element|null} Layout items for a selected direct container child.
 */
export function ChildLayoutControls(props) {
	const parent = useLayoutParent(props.clientId);
	return isContainer(parent?.name) && props.isSelected ? (
		<LayoutControls {...props} />
	) : null;
}

/**
 * Items render inside each main container's existing Settings panel.
 * @param {Object}   root0               Component props.
 * @param {string}   root0.name          Block name.
 * @param {Object}   root0.attributes    Block attributes.
 * @param {Function} root0.setAttributes Attribute setter.
 * @param {string}   root0.clientId      Editor block id.
 */
export default function LayoutControls({
	name,
	attributes,
	setAttributes,
	clientId,
}) {
	const [device, setDevice] = useState('desktop');
	const [group, setGroup] = useState(isContainer(name) ? 'layout' : 'size');
	const [resets, setResets] = useState({});
	const parent = useLayoutParent(clientId);
	if (
		!support.blocks[name] ||
		!shouldExtendBlock(name) ||
		!isExtensionEnabled('layout')
	) {
		return null;
	}
	const layout = attributes.dsgoLayout || {};
	const values = layout[device] || {};
	const setValue = (field, value) => {
		const next = updateLayout(name, layout, device, field, value);
		if (!next) {
			return false;
		}
		setAttributes({
			dsgoLayout: Object.keys(next).length ? next : undefined,
		});
		return true;
	};
	const inherited = (field) => {
		const previous = Object.keys(support.devices).slice(
			0,
			Object.keys(support.devices).indexOf(device)
		);
		return previous
			.reverse()
			.map((entry) => layout[entry]?.[field])
			.find((value) => value !== undefined);
	};
	const fields = groupFields[group].filter((field) => {
		if (!appliesTo(name, support.properties[field])) {
			return false;
		}
		if (field.startsWith('flex')) {
			return support.blocks[parent?.name]?.role === 'flex';
		}
		if (['gridColumn', 'gridRow', 'gridArea'].includes(field)) {
			return (
				parent?.name === 'airo-wp/grid' &&
				!parent.attributes.matchRowHeights
			);
		}
		return true;
	});
	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Viewport', 'airo-wp')}
				hasValue={() => device !== 'desktop'}
				onDeselect={() => setDevice('desktop')}
				isShownByDefault
			>
				<h3 className="airo-wp-layout-controls__heading">
					{__('Layout overrides', 'airo-wp')}
				</h3>
				<SelectControl
					label={__('Viewport', 'airo-wp')}
					value={device}
					onChange={setDevice}
					options={[
						{
							label: __('Desktop (base)', 'airo-wp'),
							value: 'desktop',
						},
						{
							label: __('Tablet (1024px and below)', 'airo-wp'),
							value: 'tablet',
						},
						{
							label: __('Mobile (767px and below)', 'airo-wp'),
							value: 'mobile',
						},
					]}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					help={
						device === 'desktop'
							? __(
									'Optional. A value here takes priority over the block’s own setting; leave a field empty to keep that setting. Screen reader order stays the same.',
									'airo-wp'
								)
							: __(
									'Empty fields use the value from the larger screen sizes. Screen reader order stays the same.',
									'airo-wp'
								)
					}
				/>
			</DsgoInspectorPanel.Item>
			<DsgoInspectorPanel.Item
				label={__('Property group', 'airo-wp')}
				hasValue={() => false}
				onDeselect={() =>
					setGroup(isContainer(name) ? 'layout' : 'size')
				}
				isShownByDefault
			>
				<SelectControl
					label={__('Property group', 'airo-wp')}
					value={group}
					onChange={setGroup}
					options={[
						...(isContainer(name)
							? [
									{
										label: __(
											'Layout and spacing',
											'airo-wp'
										),
										value: 'layout',
									},
								]
							: []),
						{ label: __('Sizing', 'airo-wp'), value: 'size' },
						{
							label: __('Placement and layering', 'airo-wp'),
							value: 'placement',
						},
					]}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
			{fields.map((field) => (
				<DsgoInspectorPanel.Item
					key={`${device}-${field}-${resets[field] || 0}`}
					label={labels[field]}
					hasValue={() => values[field] !== undefined}
					onDeselect={() => {
						setValue(field, '');
						setResets((previous) => ({
							...previous,
							[field]: (previous[field] || 0) + 1,
						}));
					}}
					isShownByDefault
				>
					<LayoutField
						field={field}
						definition={support.properties[field]}
						value={values[field]}
						inherited={inherited(field)}
						onChange={(value) => setValue(field, value)}
					/>
				</DsgoInspectorPanel.Item>
			))}
		</>
	);
}
