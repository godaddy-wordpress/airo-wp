/**
 * Tab Block - Edit Component
 *
 * Individual tab panel (child block)
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	TextControl,
	SelectControl,
	RangeControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useEffect } from '@wordpress/element';
import { IconPicker } from '../icon/components/IconPicker';
import { useIconDefaults } from '../../hooks';

export default function Edit({ attributes, setAttributes, clientId, context }) {
	const { uniqueId, title, icon, iconPosition, iconStyle, strokeWidth } =
		attributes;

	// Theme-level icon defaults inherited when style is left unset.
	const iconDefaults = useIconDefaults();
	const effectiveStyle = iconStyle || iconDefaults.style;

	// Get context from parent Tabs block
	const activeTab = context['airo-wp/tabs/activeTab'] || 0;
	const tabStyle = context['airo-wp/tabs/tabStyle'] || 'default';

	// Generate unique ID on mount
	useEffect(() => {
		if (!uniqueId) {
			setAttributes({ uniqueId: clientId.substring(0, 8) });
		}
	}, [uniqueId, clientId, setAttributes]);

	// Generate anchor from title if not set
	useEffect(() => {
		if (title && !attributes.anchor) {
			const anchor = title
				.toLowerCase()
				.replace(/[^a-z0-9]+/g, '-')
				.replace(/(^-|-$)/g, '');
			setAttributes({ anchor });
		}
	}, [title, attributes.anchor, setAttributes]);

	// Get this tab's index from parent
	const tabIndex = wp.data
		.select('core/block-editor')
		.getBlockOrder(
			wp.data.select('core/block-editor').getBlockRootClientId(clientId)
		)
		.indexOf(clientId);

	const isActive = tabIndex === activeTab;

	const blockProps = useBlockProps({
		className: `airo-wp-tab airo-wp-tab--${tabStyle} ${isActive ? 'is-active' : 'is-inactive'}`,
		role: 'tabpanel',
		'aria-labelledby': `tab-${uniqueId}`,
		id: `panel-${uniqueId}`,
		'data-tab-active': isActive,
		style: {
			display: isActive ? 'block' : 'none',
		},
	});

	// Use useInnerBlocksProps for proper WordPress integration
	// IMPORTANT: Always show appender when tab is active so users can add content
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-tab__content',
		},
		{
			templateLock: false,
			renderAppender: isActive
				? wp.blockEditor.InnerBlocks.ButtonBlockAppender
				: false,
		}
	);

	const inspector = (
		<InspectorControls>
			<DsgoInspectorPanel
				title={__('Settings', 'airo-wp')}
				panelName="settings"
				panelId={clientId}
				resetAll={() =>
					setAttributes({
						title: 'Tab',
						icon: '',
						iconPosition: 'none',
						iconStyle: undefined,
						strokeWidth: 1.5,
						anchor: '',
					})
				}
			>
				<DsgoInspectorPanel.Item
					label={__('Tab Title', 'airo-wp')}
					hasValue={() => title !== 'Tab'}
					onDeselect={() => setAttributes({ title: 'Tab' })}
					isShownByDefault
				>
					<TextControl
						label={__('Tab Title', 'airo-wp')}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						help={__(
							'The title shown in the tab navigation',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>

				<DsgoInspectorPanel.Item
					label={__('Icon Position', 'airo-wp')}
					hasValue={() => iconPosition !== 'none'}
					onDeselect={() =>
						setAttributes({ iconPosition: 'none', icon: '' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Icon Position', 'airo-wp')}
						value={iconPosition}
						options={[
							{
								label: __('None', 'airo-wp'),
								value: 'none',
							},
							{
								label: __('Left', 'airo-wp'),
								value: 'left',
							},
							{
								label: __('Right', 'airo-wp'),
								value: 'right',
							},
							{
								label: __('Top', 'airo-wp'),
								value: 'top',
							},
						]}
						onChange={(value) =>
							setAttributes({ iconPosition: value })
						}
						help={__(
							'Choose where to display an icon with the tab',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>

				{iconPosition !== 'none' && (
					<DsgoInspectorPanel.Item
						label={__('Tab Icon', 'airo-wp')}
						hasValue={() => icon !== ''}
						onDeselect={() => setAttributes({ icon: '' })}
						isShownByDefault
					>
						<IconPicker
							value={icon}
							onChange={(value) => setAttributes({ icon: value })}
							label={__('Tab Icon', 'airo-wp')}
							help={__(
								'Choose an icon to display with the tab',
								'airo-wp'
							)}
						/>
					</DsgoInspectorPanel.Item>
				)}

				{iconPosition !== 'none' && icon && (
					<DsgoInspectorPanel.Item
						label={__('Icon Style', 'airo-wp')}
						hasValue={() => typeof iconStyle === 'string'}
						onDeselect={() =>
							setAttributes({ iconStyle: undefined })
						}
						isShownByDefault
					>
						<ToggleGroupControl
							label={__('Icon Style', 'airo-wp')}
							value={effectiveStyle}
							onChange={(value) =>
								setAttributes({ iconStyle: value })
							}
							help={
								!iconStyle &&
								sprintf(
									/* translators: %s: inherited icon style (Filled or Outlined). */
									__(
										'Inheriting theme default (%s).',
										'airo-wp'
									),
									iconDefaults.style === 'outlined'
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
				)}

				{iconPosition !== 'none' &&
					icon &&
					effectiveStyle === 'outlined' && (
						<DsgoInspectorPanel.Item
							label={__('Stroke Width', 'airo-wp')}
							hasValue={() => strokeWidth !== 1.5}
							onDeselect={() =>
								setAttributes({ strokeWidth: 1.5 })
							}
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
					label={__('Anchor (URL Hash)', 'airo-wp')}
					hasValue={() => !!attributes.anchor}
					onDeselect={() => setAttributes({ anchor: '' })}
					isShownByDefault
				>
					<TextControl
						label={__('Anchor (URL Hash)', 'airo-wp')}
						value={attributes.anchor}
						onChange={(value) => setAttributes({ anchor: value })}
						help={__(
							'URL-friendly identifier for deep linking',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			</DsgoInspectorPanel>
		</InspectorControls>
	);

	// Don't render inactive tabs at all - improves performance
	if (!isActive) {
		return (
			<>
				{inspector}

				<div {...blockProps}>
					<div className="airo-wp-tab__inactive-notice">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="20"
							height="20"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeWidth="2"
						>
							<circle cx="12" cy="12" r="10" />
							<line x1="12" y1="8" x2="12" y2="12" />
							<line x1="12" y1="16" x2="12.01" y2="16" />
						</svg>
						<span>
							{__(
								'Click the tab above to edit its content',
								'airo-wp'
							)}
						</span>
					</div>
				</div>
			</>
		);
	}

	return (
		<>
			{inspector}

			<div {...blockProps}>
				{/* Use spread props pattern - InnerBlocks will show appender */}
				<div {...innerBlocksProps} />
			</div>
		</>
	);
}
