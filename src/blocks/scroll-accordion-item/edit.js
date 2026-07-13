/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './editor.scss';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';

/**
 * Edit component for the Scroll Accordion Item block.
 * Simple container for inner blocks with sticky stacking effect on frontend.
 *
 * @param {Object}   props               - Component props.
 * @param {Object}   props.attributes    - Block attributes.
 * @param {Function} props.setAttributes - Function to update attributes.
 * @param {string}   props.clientId      - Block client ID.
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes, clientId }) {
	const { overlayColor } = attributes;
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// Apply overlay styles when color is set
	const overlayStyles = overlayColor
		? {
				'--airo-wp-overlay-color': convertColorToCSSVar(overlayColor),
				'--airo-wp-overlay-opacity': '0.8',
			}
		: {};

	const blockProps = useBlockProps({
		className: classnames('airo-wp-scroll-accordion-item', {
			'airo-wp-scroll-accordion-item--has-overlay': !!overlayColor,
		}),
		style: {
			alignSelf: 'stretch',
			...overlayStyles,
		},
	});

	const innerBlocksProps = useInnerBlocksProps(blockProps, {
		template: [
			[
				'core/paragraph',
				{
					placeholder: __(
						'Add content for this scroll accordion item…',
						'airo-wp'
					),
				},
			],
		],
		templateLock: false,
	});

	return (
		<>
			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Overlay', 'airo-wp')}
					settings={[
						{
							label: __('Overlay Color', 'airo-wp'),
							colorValue: decodeColorValue(
								overlayColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									overlayColor:
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

			<div {...innerBlocksProps} />
		</>
	);
}
