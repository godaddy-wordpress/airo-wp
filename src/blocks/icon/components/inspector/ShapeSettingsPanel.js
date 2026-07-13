/**
 * Icon Block - Shape Settings Panel Component
 *
 * Provides controls for icon background shape and padding.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { PanelBody, SelectControl, RangeControl } from '@wordpress/components';

/**
 * Shape Settings Panel - Controls for icon background shape.
 *
 * Allows selecting a background shape (none, circle, square, rounded)
 * and adjusting padding when a shape is selected.
 *
 * @param {Object}   props               - Component props
 * @param {string}   props.shape         - Background shape
 * @param {number}   props.shapePadding  - Shape padding in pixels
 * @param {Function} props.setAttributes - Function to update block attributes
 * @return {JSX.Element} Shape Settings Panel component
 */
export const ShapeSettingsPanel = ({ shape, shapePadding, setAttributes }) => {
	return (
		<PanelBody
			title={__('Shape & Background', 'airo-wp')}
			initialOpen={false}
		>
			<SelectControl
				label={__('Background Shape', 'airo-wp')}
				value={shape}
				options={[
					{ label: __('None', 'airo-wp'), value: 'none' },
					{ label: __('Circle', 'airo-wp'), value: 'circle' },
					{ label: __('Square', 'airo-wp'), value: 'square' },
					{
						label: __('Rounded Square', 'airo-wp'),
						value: 'rounded',
					},
				]}
				onChange={(value) => setAttributes({ shape: value })}
				help={
					shape === 'none'
						? __(
								'Select a shape to add a background behind the icon.',
								'airo-wp'
							)
						: __(
								'The background color is set in the "Color" panel below.',
								'airo-wp'
							)
				}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>

			{shape !== 'none' && (
				<>
					<RangeControl
						label={__('Padding', 'airo-wp')}
						value={shapePadding}
						onChange={(value) =>
							setAttributes({ shapePadding: value })
						}
						min={0}
						max={64}
						step={2}
						help={__(
							'Space between the icon and the shape edge.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<p className="components-base-control__help">
						{__(
							'💡 Tip: Set the background color using the "Color" panel, and adjust border radius using the "Border" panel.',
							'airo-wp'
						)}
					</p>
				</>
			)}
		</PanelBody>
	);
};
