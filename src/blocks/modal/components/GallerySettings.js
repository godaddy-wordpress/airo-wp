/**
 * Gallery Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for the modal's gallery
 * attributes, meant to be composed inside the Settings panel in
 * modal/edit.js.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import {
	TextControl,
	RangeControl,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export default function GallerySettings({ attributes, setAttributes }) {
	const {
		galleryGroupId,
		galleryIndex,
		showGalleryNavigation,
		navigationStyle,
		navigationPosition,
	} = attributes;

	const isGalleryEnabled = !!galleryGroupId;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Gallery Group ID', 'airo-wp')}
				hasValue={() => galleryGroupId !== ''}
				onDeselect={() =>
					setAttributes({
						galleryGroupId: '',
						galleryIndex: 0,
						showGalleryNavigation: true,
					})
				}
				isShownByDefault
			>
				<TextControl
					label={__('Gallery Group ID', 'airo-wp')}
					value={galleryGroupId}
					onChange={(value) =>
						setAttributes({ galleryGroupId: value })
					}
					help={__(
						'Enter a group ID to link this modal with others (e.g., "product-gallery"). Leave empty to disable gallery navigation.',
						'airo-wp'
					)}
					placeholder="e.g., product-gallery"
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{isGalleryEnabled && (
				<DsgoInspectorPanel.Item
					label={__('Gallery Index', 'airo-wp')}
					hasValue={() => galleryIndex !== 0}
					onDeselect={() => setAttributes({ galleryIndex: 0 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Gallery Index', 'airo-wp')}
						value={galleryIndex}
						onChange={(value) =>
							setAttributes({ galleryIndex: value })
						}
						min={0}
						max={50}
						help={__(
							'Position of this modal in the gallery sequence (0-based).',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{isGalleryEnabled && (
				<DsgoInspectorPanel.Item
					label={__('Show Gallery Navigation', 'airo-wp')}
					hasValue={() => showGalleryNavigation !== true}
					onDeselect={() =>
						setAttributes({ showGalleryNavigation: true })
					}
					isShownByDefault
				>
					<ToggleControl
						label={__('Show Navigation', 'airo-wp')}
						checked={showGalleryNavigation}
						onChange={(value) =>
							setAttributes({ showGalleryNavigation: value })
						}
						help={__(
							'Display previous/next navigation buttons.',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{isGalleryEnabled && showGalleryNavigation && (
				<DsgoInspectorPanel.Item
					label={__('Navigation Style', 'airo-wp')}
					hasValue={() => navigationStyle !== 'arrows'}
					onDeselect={() =>
						setAttributes({ navigationStyle: 'arrows' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Navigation Style', 'airo-wp')}
						value={navigationStyle}
						options={[
							{
								label: __('Arrows', 'airo-wp'),
								value: 'arrows',
							},
							{
								label: __('Chevrons', 'airo-wp'),
								value: 'chevrons',
							},
							{
								label: __('Text', 'airo-wp'),
								value: 'text',
							},
						]}
						onChange={(value) =>
							setAttributes({ navigationStyle: value })
						}
						help={__(
							'Choose how navigation buttons appear.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{isGalleryEnabled && showGalleryNavigation && (
				<DsgoInspectorPanel.Item
					label={__('Navigation Position', 'airo-wp')}
					hasValue={() => navigationPosition !== 'sides'}
					onDeselect={() =>
						setAttributes({ navigationPosition: 'sides' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Navigation Position', 'airo-wp')}
						value={navigationPosition}
						options={[
							{
								label: __('Sides', 'airo-wp'),
								value: 'sides',
							},
							{
								label: __('Bottom', 'airo-wp'),
								value: 'bottom',
							},
							{
								label: __('Top', 'airo-wp'),
								value: 'top',
							},
						]}
						onChange={(value) =>
							setAttributes({ navigationPosition: value })
						}
						help={__('Position of navigation buttons.', 'airo-wp')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
}
