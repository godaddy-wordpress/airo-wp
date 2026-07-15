/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, SelectControl, RangeControl } from '@wordpress/components';

/**
 * Layout Settings Panel Component
 *
 * @param {Object}   props               - Component props
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to set attributes
 * @return {Element} Layout settings panel
 */
export default function LayoutSettingsPanel({ attributes, setAttributes }) {
	const { layoutPreset, overlayOpacity, contentAlignment, visualStyle } =
		attributes;

	const layoutOptions = [
		{
			label: __('Standard (Image Top)', 'airo-wp'),
			value: 'standard',
		},
		{
			label: __('Horizontal (Image Left)', 'airo-wp'),
			value: 'horizontal-left',
		},
		{
			label: __('Horizontal (Image Right)', 'airo-wp'),
			value: 'horizontal-right',
		},
		{
			label: __('Background (Image Behind)', 'airo-wp'),
			value: 'background',
		},
		{
			label: __('Minimal (No Image)', 'airo-wp'),
			value: 'minimal',
		},
		{
			label: __('Featured (Large Image)', 'airo-wp'),
			value: 'featured',
		},
	];

	const alignmentOptions = [
		{ label: __('Left', 'airo-wp'), value: 'left' },
		{ label: __('Center', 'airo-wp'), value: 'center' },
		{ label: __('Right', 'airo-wp'), value: 'right' },
	];

	const visualStyleOptions = [
		{ label: __('Default', 'airo-wp'), value: 'default' },
		{ label: __('Outlined', 'airo-wp'), value: 'outlined' },
		{ label: __('Filled', 'airo-wp'), value: 'filled' },
		{ label: __('Shadow', 'airo-wp'), value: 'shadow' },
		{ label: __('Minimal', 'airo-wp'), value: 'minimal' },
	];

	return (
		<PanelBody title={__('Layout', 'airo-wp')} initialOpen={true}>
			<SelectControl
				label={__('Layout Preset', 'airo-wp')}
				value={layoutPreset}
				options={layoutOptions}
				onChange={(value) => setAttributes({ layoutPreset: value })}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>

			<SelectControl
				label={__('Visual Style', 'airo-wp')}
				value={visualStyle}
				options={visualStyleOptions}
				onChange={(value) => setAttributes({ visualStyle: value })}
				help={__(
					'Choose a visual style for the card appearance.',
					'airo-wp'
				)}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>

			{layoutPreset === 'background' && (
				<>
					<RangeControl
						label={__('Overlay Opacity', 'airo-wp')}
						value={overlayOpacity}
						onChange={(value) =>
							setAttributes({ overlayOpacity: value })
						}
						min={0}
						max={100}
						step={5}
						help={__(
							'Darkens the background image to improve text readability.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<SelectControl
						label={__('Content Alignment', 'airo-wp')}
						value={contentAlignment}
						options={alignmentOptions}
						onChange={(value) =>
							setAttributes({ contentAlignment: value })
						}
						help={__(
							'Horizontal alignment for content over background image.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</>
			)}
		</PanelBody>
	);
}
