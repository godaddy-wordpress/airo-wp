/**
 * Tabs Block - Edit Component
 *
 * Parent block that manages tab navigation and panels
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	BlockControls,
	InspectorControls,
	useInnerBlocksProps,
	store as blockEditorStore,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	SelectControl,
	ToggleControl,
	RangeControl,
	Button,
	Tooltip,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { createBlock, cloneBlock } from '@wordpress/blocks';
import { copy, trash, plus } from '@wordpress/icons';
import { useSelect, useDispatch } from '@wordpress/data';
import { useCallback, useEffect, useRef } from '@wordpress/element';
import { getIcon } from '../icon/utils/svg-icons';
import { useUniqueBlockId, useIconDefaults } from '../../hooks';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import TabsPlaceholder from './components/TabsPlaceholder';
import useTablistKeyboard from '../../hooks/useTablistKeyboard';
import DsgoChildToolbar from '../../components/shared/DsgoChildToolbar';

const ALLOWED_BLOCKS = ['airo-wp/tab'];

export default function Edit({ attributes, setAttributes, clientId }) {
	const {
		uniqueId,
		orientation,
		activeTab,
		alignment,
		mobileBreakpoint,
		mobileMode,
		enableDeepLinking,
		gap,
		tabStyle,
		tabColor,
		tabBackgroundColor,
		tabContentBackgroundColor,
		activeTabColor,
		activeTabBackgroundColor,
		tabBorderColor,
		tabHoverColor,
		tabHoverBackgroundColor,
		showNavBorder,
	} = attributes;

	// Get theme color palette and gradient settings
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	useUniqueBlockId({
		clientId,
		attributeName: 'uniqueId',
		value: uniqueId,
		setAttributes,
	});

	// Theme-level icon defaults inherited by child tabs whose iconStyle is unset.
	const iconDefaults = useIconDefaults();

	// Get inner blocks (tabs)
	const { innerBlocks } = useSelect(
		(select) => {
			const { getBlock } = select(blockEditorStore);
			return {
				innerBlocks: getBlock(clientId)?.innerBlocks || [],
			};
		},
		[clientId]
	);

	const { insertBlock, removeBlock, updateBlockAttributes } =
		useDispatch(blockEditorStore);

	useEffect(() => {
		// Keep the active tab index valid when authors remove every child and
		// then reseed via the placeholder, or when malformed content restores
		// with an out-of-range index.
		if (innerBlocks.length === 0) {
			if (activeTab !== 0) {
				setAttributes({ activeTab: 0 });
			}
			return;
		}

		if (activeTab < 0 || activeTab >= innerBlocks.length) {
			setAttributes({ activeTab: 0 });
		}
	}, [activeTab, innerBlocks.length, setAttributes]);

	// Handle tab chip click — only switch which tab is active. We intentionally
	// do NOT call selectBlock() on the child Tab, so the Gutenberg inline
	// toolbar stays anchored to the Tabs parent (above the whole block) instead
	// of hovering between the nav and the panel. Authors who want to edit a
	// specific Tab's attributes can click into its panel content below.
	const handleTabClick = (index) => {
		setAttributes({ activeTab: index });
	};

	const handleTitleChange = (tab, value) => {
		updateBlockAttributes(tab.clientId, { title: value });
	};

	const handleAddTab = () => {
		const tabCount = innerBlocks.length;
		const newTab = createBlock('airo-wp/tab', {
			title: sprintf(
				/* translators: %d: tab number */
				__('Tab %d', 'airo-wp'),
				tabCount + 1
			),
		});
		// updateSelection: false — keep the Tabs parent selected so the inline
		// toolbar doesn't jump to the new child Tab and overlap the nav.
		insertBlock(newTab, tabCount, clientId, false);
		setAttributes({ activeTab: tabCount });
	};

	const handleDuplicateTab = (tab, index) => {
		// cloneBlock produces a deep clone with fresh clientIds at every level.
		// Reset uniqueId so the duplicated tab regenerates its own via the
		// child block's onMount effect.
		const clone = cloneBlock(tab, { uniqueId: '' });
		insertBlock(clone, index + 1, clientId, false);
		setAttributes({ activeTab: index + 1 });
	};

	const handleRemoveTab = (tab, index) => {
		if (innerBlocks.length <= 1) {
			return;
		}
		removeBlock(tab.clientId, false);
		// If we removed the active tab (or an earlier one), keep a valid index.
		if (index <= activeTab) {
			const next = Math.max(0, activeTab - 1);
			setAttributes({ activeTab: next });
		}
	};

	// Shared tablist keyboard navigation — same ArrowLeft/Right/Home/End
	// behavior across tabs, slider, accordion, etc.
	// Ref anchors the focus lookup to the editor canvas's iframed document.
	// `document.querySelector` here resolves to the WP admin wrapper and
	// misses the tabs rendered inside the canvas iframe.
	const navRef = useRef(null);
	const focusTabByIndex = useCallback((newIndex) => {
		const tabButton = navRef.current?.querySelector(
			`[data-tab-index="${newIndex}"]`
		);
		if (tabButton) {
			tabButton.focus();
		}
	}, []);
	const handleKeyDown = useTablistKeyboard({
		itemCount: innerBlocks.length,
		orientation,
		onIndexChange: (newIndex) => setAttributes({ activeTab: newIndex }),
		focusItem: focusTabByIndex,
	});

	const blockProps = useBlockProps({
		className: `airo-wp-tabs airo-wp-tabs-${uniqueId} airo-wp-tabs--${orientation} airo-wp-tabs--${tabStyle} airo-wp-tabs--align-${alignment}${showNavBorder ? ' airo-wp-tabs--show-nav-border' : ''}`,
		style: {
			'--airo-wp-tabs-gap': gap,
			...(tabColor && {
				'--airo-wp-tab-color': convertColorToCSSVar(tabColor),
			}),
			...(tabBackgroundColor && {
				'--airo-wp-tab-bg': convertColorToCSSVar(tabBackgroundColor),
			}),
			...(tabContentBackgroundColor && {
				'--airo-wp-tab-content-bg': convertColorToCSSVar(
					tabContentBackgroundColor
				),
			}),
			...(activeTabColor && {
				'--airo-wp-tab-color-active': convertColorToCSSVar(activeTabColor),
			}),
			...(activeTabBackgroundColor && {
				'--airo-wp-tab-bg-active': convertColorToCSSVar(
					activeTabBackgroundColor
				),
			}),
			...(tabBorderColor && {
				'--airo-wp-tab-border-color': convertColorToCSSVar(tabBorderColor),
			}),
			...(tabHoverColor && {
				'--airo-wp-tab-color-hover': convertColorToCSSVar(tabHoverColor),
			}),
			...(tabHoverBackgroundColor && {
				'--airo-wp-tab-bg-hover': convertColorToCSSVar(
					tabHoverBackgroundColor
				),
			}),
		},
	});

	// Use useInnerBlocksProps for tab panels. Initial seeding happens via
	// TabsPlaceholder so authors pick a starter layout instead of landing on
	// a generic three-tab template.
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-tabs__panels',
		},
		{
			allowedBlocks: ALLOWED_BLOCKS,
			orientation: orientation === 'vertical' ? 'vertical' : 'horizontal',
		}
	);

	if (innerBlocks.length === 0) {
		return (
			<div {...blockProps}>
				<TabsPlaceholder
					clientId={clientId}
					setAttributes={setAttributes}
				/>
			</div>
		);
	}

	return (
		<>
			<BlockControls>
				<DsgoChildToolbar
					parentClientId={clientId}
					childBlockName="airo-wp/tab"
					activeIndex={activeTab}
					onActiveIndexChange={(index) =>
						setAttributes({ activeTab: index })
					}
					childAttributes={{
						title: sprintf(
							/* translators: %d: tab number */
							__('Tab %d', 'airo-wp'),
							innerBlocks.length + 1
						),
					}}
					cloneAttributeOverrides={{ uniqueId: '' }}
					addLabel={__('Add tab', 'airo-wp')}
					duplicateLabel={__('Duplicate tab', 'airo-wp')}
					removeLabel={__('Remove tab', 'airo-wp')}
					movePrevLabel={
						orientation === 'vertical'
							? __('Move tab up', 'airo-wp')
							: __('Move tab left', 'airo-wp')
					}
					moveNextLabel={
						orientation === 'vertical'
							? __('Move tab down', 'airo-wp')
							: __('Move tab right', 'airo-wp')
					}
					orientation={orientation}
				/>
			</BlockControls>

			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Tab Colors', 'airo-wp')}
					settings={[
						{
							label: __('Tab Text', 'airo-wp'),
							colorValue: decodeColorValue(
								tabColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									tabColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Tab Background', 'airo-wp'),
							colorValue: decodeColorValue(
								tabBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									tabBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Tab Text Hover', 'airo-wp'),
							colorValue: decodeColorValue(
								tabHoverColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									tabHoverColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Tab Background Hover', 'airo-wp'),
							colorValue: decodeColorValue(
								tabHoverBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									tabHoverBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Active Tab Text', 'airo-wp'),
							colorValue: decodeColorValue(
								activeTabColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									activeTabColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Active Tab Background', 'airo-wp'),
							colorValue: decodeColorValue(
								activeTabBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									activeTabBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Tab Border', 'airo-wp'),
							colorValue: decodeColorValue(
								tabBorderColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									tabBorderColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Tab Content Background', 'airo-wp'),
							colorValue: decodeColorValue(
								tabContentBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									tabContentBackgroundColor:
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

			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							orientation: 'horizontal',
							tabStyle: 'default',
							alignment: 'left',
							gap: '8px',
							showNavBorder: false,
							mobileBreakpoint: 768,
							mobileMode: 'accordion',
							enableDeepLinking: false,
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Orientation', 'airo-wp')}
						hasValue={() => orientation !== 'horizontal'}
						onDeselect={() =>
							setAttributes({ orientation: 'horizontal' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Orientation', 'airo-wp')}
							value={orientation}
							options={[
								{
									label: __('Horizontal', 'airo-wp'),
									value: 'horizontal',
								},
								{
									label: __('Vertical', 'airo-wp'),
									value: 'vertical',
								},
							]}
							onChange={(value) =>
								setAttributes({ orientation: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Tab Style', 'airo-wp')}
						hasValue={() => tabStyle !== 'default'}
						onDeselect={() =>
							setAttributes({ tabStyle: 'default' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Tab Style', 'airo-wp')}
							value={tabStyle}
							options={[
								{
									label: __('Default', 'airo-wp'),
									value: 'default',
								},
								{
									label: __('Pills', 'airo-wp'),
									value: 'pills',
								},
								{
									label: __('Underline', 'airo-wp'),
									value: 'underline',
								},
								{
									label: __('Minimal', 'airo-wp'),
									value: 'minimal',
								},
							]}
							onChange={(value) =>
								setAttributes({ tabStyle: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{orientation === 'horizontal' && (
						<DsgoInspectorPanel.Item
							label={__('Alignment', 'airo-wp')}
							hasValue={() => alignment !== 'left'}
							onDeselect={() =>
								setAttributes({ alignment: 'left' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Alignment', 'airo-wp')}
								value={alignment}
								options={[
									{
										label: __('Left', 'airo-wp'),
										value: 'left',
									},
									{
										label: __('Center', 'airo-wp'),
										value: 'center',
									},
									{
										label: __('Right', 'airo-wp'),
										value: 'right',
									},
									{
										label: __('Justified', 'airo-wp'),
										value: 'justified',
									},
								]}
								onChange={(value) =>
									setAttributes({ alignment: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Gap Between Tabs', 'airo-wp')}
						hasValue={() => gap !== '8px'}
						onDeselect={() => setAttributes({ gap: '8px' })}
						isShownByDefault
					>
						<RangeControl
							label={__('Gap Between Tabs', 'airo-wp')}
							value={parseInt(gap)}
							onChange={(value) =>
								setAttributes({ gap: `${value}px` })
							}
							min={0}
							max={40}
							step={1}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Show Border Below Tabs', 'airo-wp')}
						hasValue={() => showNavBorder !== false}
						onDeselect={() =>
							setAttributes({ showNavBorder: false })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Border Below Tabs', 'airo-wp')}
							checked={showNavBorder}
							onChange={(value) =>
								setAttributes({ showNavBorder: value })
							}
							help={__(
								'Add a divider line between tab navigation and content',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Mobile Breakpoint (px)', 'airo-wp')}
						hasValue={() => mobileBreakpoint !== 768}
						onDeselect={() =>
							setAttributes({ mobileBreakpoint: 768 })
						}
						isShownByDefault
					>
						<RangeControl
							label={__('Mobile Breakpoint (px)', 'airo-wp')}
							value={mobileBreakpoint}
							onChange={(value) =>
								setAttributes({ mobileBreakpoint: value })
							}
							min={320}
							max={1024}
							step={1}
							help={__(
								'Screen width below which mobile mode activates',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Mobile Mode', 'airo-wp')}
						hasValue={() => mobileMode !== 'accordion'}
						onDeselect={() =>
							setAttributes({ mobileMode: 'accordion' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Mobile Mode', 'airo-wp')}
							value={mobileMode}
							options={[
								{
									label: __('Accordion', 'airo-wp'),
									value: 'accordion',
								},
								{
									label: __('Dropdown', 'airo-wp'),
									value: 'dropdown',
								},
								{
									label: __(
										'Tabs (Scrollable)',
										'airo-wp'
									),
									value: 'tabs',
								},
							]}
							onChange={(value) =>
								setAttributes({ mobileMode: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Enable Deep Linking', 'airo-wp')}
						hasValue={() => enableDeepLinking !== false}
						onDeselect={() =>
							setAttributes({ enableDeepLinking: false })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Enable Deep Linking', 'airo-wp')}
							checked={enableDeepLinking}
							onChange={(value) =>
								setAttributes({ enableDeepLinking: value })
							}
							help={__(
								'Allow tabs to be accessed via URL hash (e.g., #tab-name)',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				{/* Tab Navigation. The tablist + the editor-only "Add tab"
				    button are wrapped in a single flex row so the add
				    control reads as adjacent to the tabs without sitting
				    inside role="tablist". */}
				<div className="airo-wp-tabs__nav-row">
					<div
						ref={navRef}
						className="airo-wp-tabs__nav"
						role="tablist"
						aria-label={__('Tabs', 'airo-wp')}
					>
						{innerBlocks.map((block, index) => {
							const {
								title,
								icon,
								iconPosition,
								iconStyle,
								strokeWidth,
								uniqueId: tabId,
							} = block.attributes;
							const isActive = index === activeTab;
							const effectiveIconStyle =
								iconStyle || iconDefaults.style;

							const placeholderLabel = sprintf(
								/* translators: %d: tab number */
								__('Tab %d', 'airo-wp'),
								index + 1
							);
							return (
								<div
									key={block.clientId}
									className={`airo-wp-tabs__tab airo-wp-tabs__tab--editor ${
										isActive ? 'is-active' : ''
									} ${
										icon
											? `has-icon has-icon-${iconPosition}`
											: ''
									}`}
									id={`tab-${tabId}`}
									role="tab"
									aria-selected={isActive}
									aria-controls={`panel-${tabId}`}
									tabIndex={isActive ? 0 : -1}
									data-tab-index={index}
									onClick={(e) => {
										// Ignore clicks routed through the inline
										// edit input or an action button — they
										// handle their own focus/click behavior.
										if (
											e.target.closest(
												'.airo-wp-tabs__tab-title--editor, .airo-wp-tabs__tab-actions'
											)
										) {
											return;
										}
										handleTabClick(index);
									}}
									onKeyDown={(e) => handleKeyDown(e, index)}
								>
									{icon && iconPosition === 'left' && (
										<span className="airo-wp-tabs__tab-icon">
											{getIcon(
												icon,
												effectiveIconStyle,
												strokeWidth
											)}
										</span>
									)}

									{icon && iconPosition === 'top' && (
										<span className="airo-wp-tabs__tab-icon-top">
											{getIcon(
												icon,
												effectiveIconStyle,
												strokeWidth
											)}
										</span>
									)}

									<input
										type="text"
										className="airo-wp-tabs__tab-title airo-wp-tabs__tab-title--editor"
										value={title || ''}
										placeholder={placeholderLabel}
										onFocus={() => handleTabClick(index)}
										onChange={(e) =>
											handleTitleChange(
												block,
												e.target.value
											)
										}
										onKeyDown={(e) => {
											// Don't let navigation keys from the
											// title input bubble up and trigger
											// the tab's arrow-key navigation —
											// those should move the text caret.
											if (
												[
													'ArrowLeft',
													'ArrowRight',
													'ArrowUp',
													'ArrowDown',
													'Home',
													'End',
												].includes(e.key)
											) {
												e.stopPropagation();
											}
										}}
										aria-label={__(
											'Tab title',
											'airo-wp'
										)}
									/>

									{icon && iconPosition === 'right' && (
										<span className="airo-wp-tabs__tab-icon">
											{getIcon(
												icon,
												effectiveIconStyle,
												strokeWidth
											)}
										</span>
									)}

									<span className="airo-wp-tabs__tab-actions">
										<Tooltip
											text={__(
												'Duplicate tab',
												'airo-wp'
											)}
										>
											<Button
												size="small"
												icon={copy}
												label={__(
													'Duplicate tab',
													'airo-wp'
												)}
												onClick={() =>
													handleDuplicateTab(
														block,
														index
													)
												}
											/>
										</Tooltip>
										<Tooltip
											text={__(
												'Remove tab',
												'airo-wp'
											)}
										>
											<Button
												size="small"
												icon={trash}
												isDestructive
												label={__(
													'Remove tab',
													'airo-wp'
												)}
												onClick={() =>
													handleRemoveTab(
														block,
														index
													)
												}
											/>
										</Tooltip>
									</span>
								</div>
							);
						})}
					</div>
					{/* "Add tab" sits outside the tablist so it doesn't violate the
				    ARIA tab pattern (a tablist should only contain role="tab"
				    children). */}
					<Button
						size="small"
						icon={plus}
						className="airo-wp-tabs__add-tab"
						onClick={handleAddTab}
					>
						{__('Add tab', 'airo-wp')}
					</Button>
				</div>

				{/* Tab Panels - Use spread props pattern */}
				<div {...innerBlocksProps} />
			</div>
		</>
	);
}
