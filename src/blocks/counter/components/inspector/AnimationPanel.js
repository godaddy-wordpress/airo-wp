/**
 * Counter Block - Animation Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for animation override.
 * Meant to be composed inside the Settings DsgoInspectorPanel in
 * counter/edit.js.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
	RangeControl,
	SelectControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

export const AnimationPanel = ({
	overrideAnimation,
	customDuration,
	customDelay,
	customEasing,
	context,
	setAttributes,
}) => {
	// Get parent settings from context (with fallback defaults)
	const parentDuration =
		context?.['airo-wp/counterGroup/animationDuration'] || 2;
	const parentDelay = context?.['airo-wp/counterGroup/animationDelay'] || 0;
	const parentEasing =
		context?.['airo-wp/counterGroup/animationEasing'] || 'easeOutQuad';

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Override Parent Animation', 'airo-wp')}
				hasValue={() => overrideAnimation !== false}
				onDeselect={() => setAttributes({ overrideAnimation: false })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Override Parent Animation', 'airo-wp')}
					checked={overrideAnimation}
					onChange={(value) =>
						setAttributes({ overrideAnimation: value })
					}
					help={__(
						'Use custom animation settings instead of parent settings',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
				{!overrideAnimation && (
					<div
						style={{
							padding: '12px',
							background: '#f0f0f0',
							borderRadius: '4px',
							marginTop: '12px',
						}}
					>
						<p
							style={{
								margin: 0,
								fontSize: '12px',
								color: '#666',
							}}
						>
							<strong>
								{__('Using parent settings:', 'airo-wp')}
							</strong>
							<br />
							{__('Duration:', 'airo-wp')} {parentDuration}s
							<br />
							{__('Delay:', 'airo-wp')} {parentDelay}s
							<br />
							{__('Easing:', 'airo-wp')} {parentEasing}
						</p>
					</div>
				)}
			</DsgoInspectorPanel.Item>

			{overrideAnimation && (
				<DsgoInspectorPanel.Item
					label={__('Animation Duration (seconds)', 'airo-wp')}
					hasValue={() => customDuration !== 2}
					onDeselect={() => setAttributes({ customDuration: 2 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Animation Duration (seconds)', 'airo-wp')}
						value={customDuration}
						onChange={(value) =>
							setAttributes({ customDuration: value })
						}
						min={0.5}
						max={5}
						step={0.1}
						help={__(
							'How long the counting animation takes',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{overrideAnimation && (
				<DsgoInspectorPanel.Item
					label={__('Animation Delay (seconds)', 'airo-wp')}
					hasValue={() => customDelay !== 0}
					onDeselect={() => setAttributes({ customDelay: 0 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Animation Delay (seconds)', 'airo-wp')}
						value={customDelay}
						onChange={(value) =>
							setAttributes({ customDelay: value })
						}
						min={0}
						max={2}
						step={0.1}
						help={__('Delay before animation starts', 'airo-wp')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{overrideAnimation && (
				<DsgoInspectorPanel.Item
					label={__('Easing Function', 'airo-wp')}
					hasValue={() => customEasing !== 'easeOutQuad'}
					onDeselect={() =>
						setAttributes({ customEasing: 'easeOutQuad' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Easing Function', 'airo-wp')}
						value={customEasing}
						options={[
							{
								label: __('Ease Out Quad', 'airo-wp'),
								value: 'easeOutQuad',
							},
							{
								label: __('Ease Out Cubic', 'airo-wp'),
								value: 'easeOutCubic',
							},
							{
								label: __('Ease In Out', 'airo-wp'),
								value: 'easeInOutQuad',
							},
							{
								label: __('Linear', 'airo-wp'),
								value: 'linear',
							},
						]}
						onChange={(value) =>
							setAttributes({ customEasing: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
};
