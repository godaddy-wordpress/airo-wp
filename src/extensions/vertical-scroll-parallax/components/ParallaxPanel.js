/**
 * Scroll Parallax - Inspector Panel Component
 *
 * UI controls for configuring scroll parallax effects.
 * Supports vertical (up/down) and horizontal (left/right) movement.
 *
 * @package
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	RangeControl,
	Flex,
	FlexItem,
	Notice,
} from '@wordpress/components';
import {
	DIRECTION_VALUES,
	ROTATION_DIRECTION_VALUES,
	RELATIVE_TO_VALUES,
	DEFAULT_PARALLAX_SETTINGS,
} from '../constants';

/**
 * Parallax Panel Component
 *
 * @param {Object}   props               Component props
 * @param {Object}   props.attributes    Block attributes
 * @param {Function} props.setAttributes Function to update attributes
 * @return {JSX.Element} Panel component
 */
export default function ParallaxPanel({ attributes, setAttributes }) {
	// Translatable options - defined here where __ is available
	const directionOptions = [
		{ label: __('Up', 'airo-wp'), value: DIRECTION_VALUES.UP },
		{ label: __('Down', 'airo-wp'), value: DIRECTION_VALUES.DOWN },
		{ label: __('Left', 'airo-wp'), value: DIRECTION_VALUES.LEFT },
		{ label: __('Right', 'airo-wp'), value: DIRECTION_VALUES.RIGHT },
	];

	const rotateDirectionOptions = [
		{
			label: __('Clockwise', 'airo-wp'),
			value: ROTATION_DIRECTION_VALUES.CW,
		},
		{
			label: __('Counter-Clockwise', 'airo-wp'),
			value: ROTATION_DIRECTION_VALUES.CCW,
		},
	];

	const relativeToOptions = [
		{
			label: __('Viewport', 'airo-wp'),
			value: RELATIVE_TO_VALUES.VIEWPORT,
		},
		{
			label: __('Entire Page', 'airo-wp'),
			value: RELATIVE_TO_VALUES.PAGE,
		},
	];
	const {
		dsgoParallaxEnabled = DEFAULT_PARALLAX_SETTINGS.enabled,
		dsgoParallaxDirection = DEFAULT_PARALLAX_SETTINGS.direction,
		dsgoParallaxSpeed = DEFAULT_PARALLAX_SETTINGS.speed,
		dsgoParallaxViewportStart = DEFAULT_PARALLAX_SETTINGS.viewportStart,
		dsgoParallaxViewportEnd = DEFAULT_PARALLAX_SETTINGS.viewportEnd,
		dsgoParallaxRelativeTo = DEFAULT_PARALLAX_SETTINGS.relativeTo,
		dsgoParallaxDesktop = DEFAULT_PARALLAX_SETTINGS.enableDesktop,
		dsgoParallaxTablet = DEFAULT_PARALLAX_SETTINGS.enableTablet,
		dsgoParallaxMobile = DEFAULT_PARALLAX_SETTINGS.enableMobile,
		dsgoParallaxRotateEnabled = DEFAULT_PARALLAX_SETTINGS.rotateEnabled,
		dsgoParallaxRotateDirection = DEFAULT_PARALLAX_SETTINGS.rotateDirection,
		dsgoParallaxRotateSpeed = DEFAULT_PARALLAX_SETTINGS.rotateSpeed,
	} = attributes;

	return (
		<PanelBody title={__('Scroll Parallax', 'airo-wp')} initialOpen={false}>
			<ToggleControl
				label={__('Enable Scroll Parallax Effect', 'airo-wp')}
				checked={dsgoParallaxEnabled}
				onChange={(value) =>
					setAttributes({ dsgoParallaxEnabled: value })
				}
				__nextHasNoMarginBottom
			/>

			{dsgoParallaxEnabled && (
				<>
					<SelectControl
						label={__('Direction', 'airo-wp')}
						value={dsgoParallaxDirection}
						options={directionOptions}
						onChange={(value) =>
							setAttributes({ dsgoParallaxDirection: value })
						}
						help={__(
							'Direction the element moves while scrolling down.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<RangeControl
						label={__('Speed', 'airo-wp')}
						value={dsgoParallaxSpeed}
						onChange={(value) =>
							setAttributes({ dsgoParallaxSpeed: value })
						}
						min={0}
						max={10}
						step={1}
						marks={[
							{ value: 0, label: __('None', 'airo-wp') },
							{ value: 5, label: '' },
							{ value: 10, label: __('Max', 'airo-wp') },
						]}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<RangeControl
						label={__('Viewport Start (%)', 'airo-wp')}
						value={dsgoParallaxViewportStart}
						onChange={(value) =>
							setAttributes({ dsgoParallaxViewportStart: value })
						}
						min={0}
						max={100}
						step={5}
						help={__(
							'Effect starts when element reaches this viewport position.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<RangeControl
						label={__('Viewport End (%)', 'airo-wp')}
						value={dsgoParallaxViewportEnd}
						onChange={(value) =>
							setAttributes({ dsgoParallaxViewportEnd: value })
						}
						min={0}
						max={100}
						step={5}
						help={__(
							'Effect ends when element reaches this viewport position.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<SelectControl
						label={__('Effects Relative To', 'airo-wp')}
						value={dsgoParallaxRelativeTo}
						options={relativeToOptions}
						onChange={(value) =>
							setAttributes({ dsgoParallaxRelativeTo: value })
						}
						help={__(
							'Calculate scroll position relative to viewport or entire page.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<ToggleControl
						label={__('Enable Rotation', 'airo-wp')}
						checked={dsgoParallaxRotateEnabled}
						onChange={(value) =>
							setAttributes({
								dsgoParallaxRotateEnabled: value,
							})
						}
						help={__(
							'Rotate the element as the user scrolls.',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>

					{dsgoParallaxRotateEnabled && (
						<>
							<SelectControl
								label={__('Rotate Direction', 'airo-wp')}
								value={dsgoParallaxRotateDirection}
								options={rotateDirectionOptions}
								onChange={(value) =>
									setAttributes({
										dsgoParallaxRotateDirection: value,
									})
								}
								help={__(
									'Rotation direction while scrolling down.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>

							<RangeControl
								label={__('Rotation Speed', 'airo-wp')}
								value={dsgoParallaxRotateSpeed}
								onChange={(value) =>
									setAttributes({
										dsgoParallaxRotateSpeed: value,
									})
								}
								min={0}
								max={10}
								step={1}
								marks={[
									{
										value: 0,
										label: __('None', 'airo-wp'),
									},
									{ value: 5, label: '' },
									{
										value: 10,
										label: __('Max', 'airo-wp'),
									},
								]}
								help={__(
									'Speed 10 = 360° full rotation.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</>
					)}

					<p
						style={{
							marginTop: '16px',
							marginBottom: '8px',
							fontWeight: 500,
						}}
					>
						{__('Apply Effects On', 'airo-wp')}
					</p>

					<Flex wrap>
						<FlexItem>
							<ToggleControl
								label={__('Desktop', 'airo-wp')}
								checked={dsgoParallaxDesktop}
								onChange={(value) =>
									setAttributes({
										dsgoParallaxDesktop: value,
									})
								}
								__nextHasNoMarginBottom
							/>
						</FlexItem>
						<FlexItem>
							<ToggleControl
								label={__('Tablet', 'airo-wp')}
								checked={dsgoParallaxTablet}
								onChange={(value) =>
									setAttributes({ dsgoParallaxTablet: value })
								}
								__nextHasNoMarginBottom
							/>
						</FlexItem>
						<FlexItem>
							<ToggleControl
								label={__('Mobile', 'airo-wp')}
								checked={dsgoParallaxMobile}
								onChange={(value) =>
									setAttributes({ dsgoParallaxMobile: value })
								}
								__nextHasNoMarginBottom
							/>
						</FlexItem>
					</Flex>

					{!dsgoParallaxMobile && (
						<Notice status="info" isDismissible={false}>
							{__(
								'Mobile is disabled by default for better performance.',
								'airo-wp'
							)}
						</Notice>
					)}
				</>
			)}
		</PanelBody>
	);
}
