/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';

/**
 * Badge Settings Panel Component
 *
 * @param {Object}   props               - Component props
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to set attributes
 * @return {Element} Badge settings panel
 */
export default function BadgeSettingsPanel({ attributes, setAttributes }) {
	const {
		badgeText,
		badgeStyle,
		badgeFloatingPosition,
		badgeInlinePosition,
	} = attributes;

	const styleOptions = [
		{ label: __('Floating (Over Card)', 'airo-wp'), value: 'floating' },
		{ label: __('Inline (In Content)', 'airo-wp'), value: 'inline' },
	];

	const floatingPositionOptions = [
		{ label: __('Top Left', 'airo-wp'), value: 'top-left' },
		{ label: __('Top Right', 'airo-wp'), value: 'top-right' },
		{ label: __('Bottom Left', 'airo-wp'), value: 'bottom-left' },
		{ label: __('Bottom Right', 'airo-wp'), value: 'bottom-right' },
	];

	const inlinePositionOptions = [
		{ label: __('Above Title', 'airo-wp'), value: 'above-title' },
		{ label: __('Below Title', 'airo-wp'), value: 'below-title' },
	];

	return (
		<PanelBody title={__('Badge', 'airo-wp')} initialOpen={false}>
			<TextControl
				label={__('Badge Text', 'airo-wp')}
				value={badgeText}
				onChange={(value) => setAttributes({ badgeText: value })}
				placeholder={__('NEW', 'airo-wp')}
				help={__('Leave empty to hide the badge.', 'airo-wp')}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>

			{badgeText && (
				<>
					<SelectControl
						label={__('Badge Style', 'airo-wp')}
						value={badgeStyle}
						options={styleOptions}
						onChange={(value) =>
							setAttributes({ badgeStyle: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					{badgeStyle === 'floating' && (
						<SelectControl
							label={__('Floating Position', 'airo-wp')}
							value={badgeFloatingPosition}
							options={floatingPositionOptions}
							onChange={(value) =>
								setAttributes({ badgeFloatingPosition: value })
							}
							help={__(
								'Position the badge over the card.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}

					{badgeStyle === 'inline' && (
						<SelectControl
							label={__('Inline Position', 'airo-wp')}
							value={badgeInlinePosition}
							options={inlinePositionOptions}
							onChange={(value) =>
								setAttributes({ badgeInlinePosition: value })
							}
							help={__(
								'Position the badge in the content flow.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}
				</>
			)}
		</PanelBody>
	);
}
