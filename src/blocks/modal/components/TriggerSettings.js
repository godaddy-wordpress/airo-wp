/**
 * Auto-Trigger Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for the modal's auto-trigger
 * attributes, meant to be composed inside the Settings panel in
 * modal/edit.js.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	RangeControl,
	ToggleControl,
	Notice,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export default function TriggerSettings({ attributes, setAttributes }) {
	const {
		autoTriggerType,
		autoTriggerDelay,
		autoTriggerFrequency,
		cookieDuration,
		exitIntentSensitivity,
		exitIntentMinTime,
		exitIntentExcludeMobile,
		scrollDepth,
		scrollDirection,
		timeOnPage,
	} = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Auto Trigger Type', 'airo-wp')}
				hasValue={() => autoTriggerType !== 'none'}
				onDeselect={() => setAttributes({ autoTriggerType: 'none' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Trigger Type', 'airo-wp')}
					value={autoTriggerType}
					options={[
						{ label: __('None', 'airo-wp'), value: 'none' },
						{
							label: __('Page Load', 'airo-wp'),
							value: 'pageLoad',
						},
						{
							label: __('Exit Intent', 'airo-wp'),
							value: 'exitIntent',
						},
						{
							label: __('Scroll Depth', 'airo-wp'),
							value: 'scroll',
						},
						{
							label: __('Time on Page', 'airo-wp'),
							value: 'time',
						},
					]}
					onChange={(value) =>
						setAttributes({ autoTriggerType: value })
					}
					help={__(
						'Automatically open the modal based on user behavior.',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
				{autoTriggerType !== 'none' && (
					<Notice status="info" isDismissible={false}>
						{__(
							'Auto-triggers are disabled in the editor. They will work on the frontend.',
							'airo-wp'
						)}
					</Notice>
				)}
			</DsgoInspectorPanel.Item>

			{autoTriggerType !== 'none' && (
				<DsgoInspectorPanel.Item
					label={__('Trigger Frequency', 'airo-wp')}
					hasValue={() => autoTriggerFrequency !== 'always'}
					onDeselect={() =>
						setAttributes({ autoTriggerFrequency: 'always' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Frequency', 'airo-wp')}
						value={autoTriggerFrequency}
						options={[
							{
								label: __('Every Visit', 'airo-wp'),
								value: 'always',
							},
							{
								label: __('Once per Session', 'airo-wp'),
								value: 'session',
							},
							{
								label: __('Once per User', 'airo-wp'),
								value: 'once',
							},
						]}
						onChange={(value) =>
							setAttributes({ autoTriggerFrequency: value })
						}
						help={__(
							'How often the modal should automatically open.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType !== 'none' && autoTriggerFrequency === 'once' && (
				<DsgoInspectorPanel.Item
					label={__('Cookie Duration (days)', 'airo-wp')}
					hasValue={() => cookieDuration !== 7}
					onDeselect={() => setAttributes({ cookieDuration: 7 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Cookie Duration (days)', 'airo-wp')}
						value={cookieDuration}
						onChange={(value) =>
							setAttributes({ cookieDuration: value })
						}
						min={1}
						max={365}
						help={__(
							'How long to remember that the user has seen this modal.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType === 'pageLoad' && (
				<DsgoInspectorPanel.Item
					label={__('Page Load Delay (s)', 'airo-wp')}
					hasValue={() => autoTriggerDelay !== 0}
					onDeselect={() => setAttributes({ autoTriggerDelay: 0 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Delay (seconds)', 'airo-wp')}
						value={autoTriggerDelay}
						onChange={(value) =>
							setAttributes({ autoTriggerDelay: value })
						}
						min={0}
						max={300}
						step={1}
						help={__(
							'Wait time before opening the modal after page loads.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType === 'exitIntent' && (
				<DsgoInspectorPanel.Item
					label={__('Exit Intent Sensitivity', 'airo-wp')}
					hasValue={() => exitIntentSensitivity !== 'medium'}
					onDeselect={() =>
						setAttributes({ exitIntentSensitivity: 'medium' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Sensitivity', 'airo-wp')}
						value={exitIntentSensitivity}
						options={[
							{
								label: __('Low', 'airo-wp'),
								value: 'low',
							},
							{
								label: __('Medium', 'airo-wp'),
								value: 'medium',
							},
							{
								label: __('High', 'airo-wp'),
								value: 'high',
							},
						]}
						onChange={(value) =>
							setAttributes({
								exitIntentSensitivity: value,
							})
						}
						help={__(
							'How close to the top edge triggers exit intent.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType === 'exitIntent' && (
				<DsgoInspectorPanel.Item
					label={__('Exit Intent Minimum Time (s)', 'airo-wp')}
					hasValue={() => exitIntentMinTime !== 5}
					onDeselect={() => setAttributes({ exitIntentMinTime: 5 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Minimum Time (seconds)', 'airo-wp')}
						value={exitIntentMinTime}
						onChange={(value) =>
							setAttributes({ exitIntentMinTime: value })
						}
						min={0}
						max={300}
						help={__(
							'Minimum time on page before exit intent can trigger.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType === 'exitIntent' && (
				<DsgoInspectorPanel.Item
					label={__('Exclude Mobile Devices', 'airo-wp')}
					hasValue={() => exitIntentExcludeMobile !== true}
					onDeselect={() =>
						setAttributes({ exitIntentExcludeMobile: true })
					}
					isShownByDefault
				>
					<ToggleControl
						label={__('Exclude Mobile Devices', 'airo-wp')}
						checked={exitIntentExcludeMobile}
						onChange={(value) =>
							setAttributes({
								exitIntentExcludeMobile: value,
							})
						}
						help={__(
							"Don't trigger exit intent on mobile devices.",
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType === 'scroll' && (
				<DsgoInspectorPanel.Item
					label={__('Scroll Depth (%)', 'airo-wp')}
					hasValue={() => scrollDepth !== 50}
					onDeselect={() => setAttributes({ scrollDepth: 50 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Scroll Depth (%)', 'airo-wp')}
						value={scrollDepth}
						onChange={(value) =>
							setAttributes({ scrollDepth: value })
						}
						min={0}
						max={100}
						help={__(
							'Percentage of page scrolled before modal opens.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType === 'scroll' && (
				<DsgoInspectorPanel.Item
					label={__('Scroll Direction', 'airo-wp')}
					hasValue={() => scrollDirection !== 'down'}
					onDeselect={() =>
						setAttributes({ scrollDirection: 'down' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Scroll Direction', 'airo-wp')}
						value={scrollDirection}
						options={[
							{
								label: __('Down Only', 'airo-wp'),
								value: 'down',
							},
							{
								label: __('Up or Down', 'airo-wp'),
								value: 'both',
							},
						]}
						onChange={(value) =>
							setAttributes({ scrollDirection: value })
						}
						help={__(
							'Trigger only when scrolling down or in any direction.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{autoTriggerType === 'time' && (
				<DsgoInspectorPanel.Item
					label={__('Time on Page (s)', 'airo-wp')}
					hasValue={() => timeOnPage !== 30}
					onDeselect={() => setAttributes({ timeOnPage: 30 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Time on Page (seconds)', 'airo-wp')}
						value={timeOnPage}
						onChange={(value) =>
							setAttributes({ timeOnPage: value })
						}
						min={0}
						max={300}
						help={__(
							'Seconds on page before modal opens.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
}
