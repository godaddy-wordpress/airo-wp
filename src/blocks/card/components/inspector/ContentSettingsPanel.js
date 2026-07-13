/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Content Settings Panel Component
 * Controls which content elements are visible
 *
 * @param {Object}   props               - Component props
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to set attributes
 * @return {Element} Content settings panel
 */
export default function ContentSettingsPanel({ attributes, setAttributes }) {
	const {
		showImage,
		showTitle,
		showSubtitle,
		showBody,
		showBadge,
		showCta,
		layoutPreset,
	} = attributes;

	return (
		<PanelBody
			title={__('Content Elements', 'airo-wp')}
			initialOpen={false}
		>
			{layoutPreset !== 'minimal' && (
				<ToggleControl
					label={__('Show Image', 'airo-wp')}
					checked={showImage}
					onChange={(value) => setAttributes({ showImage: value })}
					help={__('Display the card image.', 'airo-wp')}
					__nextHasNoMarginBottom
				/>
			)}

			<ToggleControl
				label={__('Show Title', 'airo-wp')}
				checked={showTitle}
				onChange={(value) => setAttributes({ showTitle: value })}
				help={__('Display the card title.', 'airo-wp')}
				__nextHasNoMarginBottom
			/>

			<ToggleControl
				label={__('Show Subtitle', 'airo-wp')}
				checked={showSubtitle}
				onChange={(value) => setAttributes({ showSubtitle: value })}
				help={__('Display the card subtitle.', 'airo-wp')}
				__nextHasNoMarginBottom
			/>

			<ToggleControl
				label={__('Show Body Text', 'airo-wp')}
				checked={showBody}
				onChange={(value) => setAttributes({ showBody: value })}
				help={__('Display the card body text.', 'airo-wp')}
				__nextHasNoMarginBottom
			/>

			<ToggleControl
				label={__('Show Badge', 'airo-wp')}
				checked={showBadge}
				onChange={(value) => setAttributes({ showBadge: value })}
				help={__('Display the badge element.', 'airo-wp')}
				__nextHasNoMarginBottom
			/>

			<ToggleControl
				label={__('Show CTA Button', 'airo-wp')}
				checked={showCta}
				onChange={(value) => setAttributes({ showCta: value })}
				help={__('Display the call-to-action button.', 'airo-wp')}
				__nextHasNoMarginBottom
			/>
		</PanelBody>
	);
}
