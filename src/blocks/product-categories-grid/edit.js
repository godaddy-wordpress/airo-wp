/**
 * Product Categories Grid Block - Edit Component
 *
 * Displays WooCommerce product categories in a responsive visual grid.
 * Fetches categories from the WC Store API and provides controls for
 * filtering, layout, and display options.
 *
 * @since 2.1.0
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
} from '@wordpress/block-editor';
import {
	Placeholder,
	Spinner,
	Notice,
	ToggleControl,
	RangeControl,
	ButtonGroup,
	Button,
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';

import GridPreview from './components/GridPreview';
import CategoryPicker from './components/CategoryPicker';
import CategoryList from './components/CategoryList';
import useProductCategories from './hooks/useProductCategories';

/**
 * Image aspect ratio options for the Layout panel.
 *
 * @type {Array<{label: string, value: string}>}
 */
const ASPECT_RATIO_OPTIONS = [
	{ label: '1:1', value: '1/1' },
	{ label: '3:4', value: '3/4' },
	{ label: '4:3', value: '4/3' },
	{ label: '16:9', value: '16/9' },
];

/**
 * Product Categories Grid Edit Component
 *
 * @param {Object}   props               Component props
 * @param {Object}   props.attributes    Block attributes
 * @param {Function} props.setAttributes Function to update block attributes
 * @param {string}   props.clientId      Block client ID
 * @return {JSX.Element} Edit component
 */
export default function ProductCategoriesGridEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		categorySource,
		columns,
		showProductCount,
		showEmpty,
		imageAspectRatio,
		overlayPosition,
	} = attributes;

	const {
		isLoading,
		error,
		filteredCategories,
		excludeIds,
		manualCategories,
		manualFeaturedIds,
		categoryNames,
		handleCategorySelect,
	} = useProductCategories(attributes, setAttributes);

	const blockProps = useBlockProps({
		className: 'airo-wp-product-categories-grid-wrapper',
	});

	return (
		<>
			{/* ── Toolbar ──────────────────────────────────────────────── */}
			<BlockControls>
				<ToolbarGroup>
					{[2, 3, 4, 5].map((count) => (
						<ToolbarButton
							key={count}
							label={sprintf(
								/* translators: %d: number of columns */
								__('%d Columns', 'airo-wp'),
								count
							)}
							isActive={columns === count}
							onClick={() => setAttributes({ columns: count })}
						>
							{count}
						</ToolbarButton>
					))}
				</ToolbarGroup>
			</BlockControls>

			{/* ── Sidebar ───────────────────────────────────────────────── */}
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							categorySource: 'all',
							selectedCategories: [],
							excludeCategories: [],
							columns: 3,
							showProductCount: true,
							showEmpty: false,
							imageAspectRatio: '3/4',
							overlayPosition: 'bottom-left',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Category Source', 'airo-wp')}
						hasValue={() => categorySource !== 'all'}
						onDeselect={() =>
							setAttributes({ categorySource: 'all' })
						}
						isShownByDefault
					>
						<ButtonGroup>
							<Button
								isPressed={categorySource === 'all'}
								onClick={() =>
									setAttributes({ categorySource: 'all' })
								}
								__next40pxDefaultSize
							>
								{__('All Categories', 'airo-wp')}
							</Button>
							<Button
								isPressed={categorySource === 'manual'}
								onClick={() =>
									setAttributes({
										categorySource: 'manual',
									})
								}
								__next40pxDefaultSize
							>
								{__('Manual', 'airo-wp')}
							</Button>
						</ButtonGroup>
					</DsgoInspectorPanel.Item>

					{categorySource === 'all' && (
						<DsgoInspectorPanel.Item
							label={__('Show Empty Categories', 'airo-wp')}
							hasValue={() => showEmpty !== false}
							onDeselect={() =>
								setAttributes({ showEmpty: false })
							}
							isShownByDefault
						>
							<ToggleControl
								label={__(
									'Show Empty Categories',
									'airo-wp'
								)}
								checked={showEmpty}
								onChange={(value) =>
									setAttributes({ showEmpty: value })
								}
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{categorySource === 'manual' && (
						<DsgoInspectorPanel.Item
							label={__('Manual Categories', 'airo-wp')}
							hasValue={() =>
								attributes.selectedCategories.length > 0
							}
							onDeselect={() =>
								setAttributes({ selectedCategories: [] })
							}
							isShownByDefault
						>
							<CategoryPicker
								onSelect={handleCategorySelect}
								excludeIds={excludeIds}
							/>
							<CategoryList
								selectedCategories={
									attributes.selectedCategories
								}
								categoryNames={categoryNames}
								onChange={(newList) =>
									setAttributes({
										selectedCategories: newList,
									})
								}
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Show Product Count', 'airo-wp')}
						hasValue={() => showProductCount !== true}
						onDeselect={() =>
							setAttributes({ showProductCount: true })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Product Count', 'airo-wp')}
							checked={showProductCount}
							onChange={(value) =>
								setAttributes({ showProductCount: value })
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Text Position', 'airo-wp')}
						hasValue={() => overlayPosition !== 'bottom-left'}
						onDeselect={() =>
							setAttributes({ overlayPosition: 'bottom-left' })
						}
						isShownByDefault
					>
						<p
							className="airo-wp-product-categories-grid__aspect-ratio-label"
							id="airo-wp-pcg-overlay-position-label"
						>
							{__('Text Position', 'airo-wp')}
						</p>
						<ButtonGroup aria-labelledby="airo-wp-pcg-overlay-position-label">
							<Button
								isPressed={overlayPosition === 'bottom-left'}
								onClick={() =>
									setAttributes({
										overlayPosition: 'bottom-left',
									})
								}
								__next40pxDefaultSize
							>
								{__('Bottom Left', 'airo-wp')}
							</Button>
							<Button
								isPressed={overlayPosition === 'center'}
								onClick={() =>
									setAttributes({
										overlayPosition: 'center',
									})
								}
								__next40pxDefaultSize
							>
								{__('Center', 'airo-wp')}
							</Button>
						</ButtonGroup>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Columns', 'airo-wp')}
						hasValue={() => columns !== 3}
						onDeselect={() => setAttributes({ columns: 3 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Columns', 'airo-wp')}
							value={columns}
							onChange={(value) =>
								setAttributes({ columns: value })
							}
							min={2}
							max={5}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Image Aspect Ratio', 'airo-wp')}
						hasValue={() => imageAspectRatio !== '3/4'}
						onDeselect={() =>
							setAttributes({ imageAspectRatio: '3/4' })
						}
						isShownByDefault
					>
						<p
							className="airo-wp-product-categories-grid__aspect-ratio-label"
							id="airo-wp-pcg-aspect-ratio-label"
						>
							{__('Image Aspect Ratio', 'airo-wp')}
						</p>
						<ButtonGroup aria-labelledby="airo-wp-pcg-aspect-ratio-label">
							{ASPECT_RATIO_OPTIONS.map((option) => (
								<Button
									key={option.value}
									isPressed={
										imageAspectRatio === option.value
									}
									onClick={() =>
										setAttributes({
											imageAspectRatio: option.value,
										})
									}
									__next40pxDefaultSize
								>
									{option.label}
								</Button>
							))}
						</ButtonGroup>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			{/* ── Canvas ────────────────────────────────────────────────── */}
			<div {...blockProps}>
				{isLoading && (
					<Placeholder
						icon="category"
						label={__('Product Categories Grid', 'airo-wp')}
					>
						<Spinner />
					</Placeholder>
				)}

				{!isLoading && error && (
					<Placeholder
						icon="category"
						label={__('Product Categories Grid', 'airo-wp')}
					>
						<Notice status="error" isDismissible={false}>
							{error}
						</Notice>
					</Placeholder>
				)}

				{!isLoading &&
					!error &&
					categorySource === 'manual' &&
					manualCategories.length > 0 && (
						<GridPreview
							categories={manualCategories}
							attributes={attributes}
							featuredIds={manualFeaturedIds}
						/>
					)}

				{!isLoading &&
					!error &&
					categorySource === 'manual' &&
					manualCategories.length === 0 && (
						<Placeholder
							icon="category"
							label={__('Product Categories Grid', 'airo-wp')}
							instructions={__(
								'Search and select categories in the sidebar to build your grid.',
								'airo-wp'
							)}
						/>
					)}

				{!isLoading &&
					!error &&
					categorySource === 'all' &&
					filteredCategories.length === 0 && (
						<Placeholder
							icon="category"
							label={__('Product Categories Grid', 'airo-wp')}
							instructions={__(
								'No categories found. Add product categories in WooCommerce, or enable "Show Empty Categories" above.',
								'airo-wp'
							)}
						/>
					)}

				{!isLoading &&
					!error &&
					categorySource === 'all' &&
					filteredCategories.length > 0 && (
						<GridPreview
							categories={filteredCategories}
							attributes={attributes}
							featuredIds={[]}
						/>
					)}
			</div>
		</>
	);
}
