/**
 * Display Options Inspector Panel
 *
 * Renders DsgoInspectorPanel.Item entries for the product showcase
 * display toggles + image size. Meant to be composed inside the
 * Settings DsgoInspectorPanel in product-showcase-hero/edit.js.
 *
 * @since 2.1.0
 */

import { __ } from '@wordpress/i18n';
import { SelectControl, ToggleControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export default function DisplayOptionsPanel({ attributes, setAttributes }) {
	const {
		showPrice,
		showRating,
		showStockStatus,
		showSaleBadge,
		showShortDescription,
		showAddToCart,
		showVariations,
		imageSize,
	} = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Show Price', 'airo-wp')}
				hasValue={() => showPrice !== true}
				onDeselect={() => setAttributes({ showPrice: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Price', 'airo-wp')}
					checked={showPrice}
					onChange={(value) => setAttributes({ showPrice: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Rating', 'airo-wp')}
				hasValue={() => showRating !== true}
				onDeselect={() => setAttributes({ showRating: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Rating', 'airo-wp')}
					checked={showRating}
					onChange={(value) => setAttributes({ showRating: value })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Stock Status', 'airo-wp')}
				hasValue={() => showStockStatus !== true}
				onDeselect={() => setAttributes({ showStockStatus: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Stock Status', 'airo-wp')}
					checked={showStockStatus}
					onChange={(value) =>
						setAttributes({ showStockStatus: value })
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Sale Badge', 'airo-wp')}
				hasValue={() => showSaleBadge !== true}
				onDeselect={() => setAttributes({ showSaleBadge: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Sale Badge', 'airo-wp')}
					checked={showSaleBadge}
					onChange={(value) =>
						setAttributes({ showSaleBadge: value })
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Short Description', 'airo-wp')}
				hasValue={() => showShortDescription !== false}
				onDeselect={() =>
					setAttributes({ showShortDescription: false })
				}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Short Description', 'airo-wp')}
					checked={showShortDescription}
					onChange={(value) =>
						setAttributes({ showShortDescription: value })
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Add to Cart', 'airo-wp')}
				hasValue={() => showAddToCart !== true}
				onDeselect={() => setAttributes({ showAddToCart: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Add to Cart', 'airo-wp')}
					checked={showAddToCart}
					onChange={(value) =>
						setAttributes({ showAddToCart: value })
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show Variations', 'airo-wp')}
				hasValue={() => showVariations !== true}
				onDeselect={() => setAttributes({ showVariations: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Variations', 'airo-wp')}
					checked={showVariations}
					onChange={(value) =>
						setAttributes({ showVariations: value })
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Image Size', 'airo-wp')}
				hasValue={() => imageSize !== 'large'}
				onDeselect={() => setAttributes({ imageSize: 'large' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Image Size', 'airo-wp')}
					value={imageSize}
					options={[
						{
							label: __('Medium', 'airo-wp'),
							value: 'medium',
						},
						{
							label: __('Large', 'airo-wp'),
							value: 'large',
						},
						{
							label: __('Full', 'airo-wp'),
							value: 'full',
						},
					]}
					onChange={(value) => setAttributes({ imageSize: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}
