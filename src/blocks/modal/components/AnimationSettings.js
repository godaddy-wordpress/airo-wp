/**
 * Animation Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for the modal's animation
 * attributes, meant to be composed inside the Settings panel in
 * modal/edit.js.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { RangeControl, SelectControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export default function AnimationSettings({ attributes, setAttributes }) {
	const { animationType, animationDuration } = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Animation Type', 'airo-wp')}
				hasValue={() => animationType !== 'fade'}
				onDeselect={() => setAttributes({ animationType: 'fade' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Animation Type', 'airo-wp')}
					value={animationType}
					onChange={(value) =>
						setAttributes({ animationType: value })
					}
					options={[
						{ label: __('Fade', 'airo-wp'), value: 'fade' },
						{
							label: __('Slide Up', 'airo-wp'),
							value: 'slide-up',
						},
						{
							label: __('Slide Down', 'airo-wp'),
							value: 'slide-down',
						},
						{ label: __('Zoom In', 'airo-wp'), value: 'zoom' },
						{ label: __('None', 'airo-wp'), value: 'none' },
					]}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Animation Duration (ms)', 'airo-wp')}
				hasValue={() => animationDuration !== 300}
				onDeselect={() => setAttributes({ animationDuration: 300 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Animation Duration (ms)', 'airo-wp')}
					value={animationDuration}
					onChange={(value) =>
						setAttributes({ animationDuration: value })
					}
					min={0}
					max={1000}
					step={50}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}
