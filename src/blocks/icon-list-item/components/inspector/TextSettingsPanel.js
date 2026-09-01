/**
 * Icon List Item - Text Settings Panel Component
 *
 * Provides controls for title and description HTML tag selection.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * Text Settings Panel Component
 *
 * @param {Object}   props                - Component props
 * @param {string}   props.titleTag       - Current title HTML tag
 * @param {string}   props.descriptionTag - Current description HTML tag
 * @param {Function} props.setAttributes  - Function to update attributes
 * @return {JSX.Element} Text Settings Panel component
 */
export const TextSettingsPanel = ({
	titleTag,
	descriptionTag,
	setAttributes,
}) => {
	return (
		<PanelBody title={__('Text Settings', 'airo-wp')} initialOpen={false}>
			<SelectControl
				label={__('Title Tag', 'airo-wp')}
				value={titleTag}
				options={[
					{ label: __('Heading 2 (H2)', 'airo-wp'), value: 'h2' },
					{ label: __('Heading 3 (H3)', 'airo-wp'), value: 'h3' },
					{ label: __('Heading 4 (H4)', 'airo-wp'), value: 'h4' },
					{ label: __('Heading 5 (H5)', 'airo-wp'), value: 'h5' },
					{ label: __('Heading 6 (H6)', 'airo-wp'), value: 'h6' },
					{ label: __('Paragraph (P)', 'airo-wp'), value: 'p' },
					{ label: __('Div', 'airo-wp'), value: 'div' },
				]}
				onChange={(value) => setAttributes({ titleTag: value })}
				help={__('HTML element for the title', 'airo-wp')}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>

			<SelectControl
				label={__('Description Tag', 'airo-wp')}
				value={descriptionTag}
				options={[
					{ label: __('Paragraph (P)', 'airo-wp'), value: 'p' },
					{ label: __('Span', 'airo-wp'), value: 'span' },
					{ label: __('Div', 'airo-wp'), value: 'div' },
				]}
				onChange={(value) => setAttributes({ descriptionTag: value })}
				help={__('HTML element for the description', 'airo-wp')}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
		</PanelBody>
	);
};
