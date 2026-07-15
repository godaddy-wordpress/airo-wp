/**
 * Counter Block - Icon Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for icon show/hide, type, and
 * position. Meant to be composed inside the Settings DsgoInspectorPanel
 * in counter/edit.js.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { ToggleControl, SelectControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

export const IconSettingsPanel = ({
	showIcon,
	icon,
	iconPosition,
	setAttributes,
}) => {
	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Show Icon', 'airo-wp')}
				hasValue={() => showIcon !== false}
				onDeselect={() => setAttributes({ showIcon: false })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Show Icon', 'airo-wp')}
					checked={showIcon}
					onChange={(value) => setAttributes({ showIcon: value })}
					help={
						showIcon
							? __('Icon is displayed', 'airo-wp')
							: __('No icon displayed', 'airo-wp')
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{showIcon && (
				<DsgoInspectorPanel.Item
					label={__('Icon', 'airo-wp')}
					hasValue={() => icon !== 'star'}
					onDeselect={() => setAttributes({ icon: 'star' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Icon', 'airo-wp')}
						value={icon}
						options={[
							{ label: __('Star', 'airo-wp'), value: 'star' },
							{
								label: __('Trophy', 'airo-wp'),
								value: 'trophy',
							},
							{
								label: __('Heart', 'airo-wp'),
								value: 'heart',
							},
							{
								label: __('Check', 'airo-wp'),
								value: 'check',
							},
							{
								label: __('Dollar', 'airo-wp'),
								value: 'dollar',
							},
							{
								label: __('Users', 'airo-wp'),
								value: 'users',
							},
							{
								label: __('Chart', 'airo-wp'),
								value: 'chart',
							},
							{
								label: __('Rocket', 'airo-wp'),
								value: 'rocket',
							},
						]}
						onChange={(value) => setAttributes({ icon: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{showIcon && (
				<DsgoInspectorPanel.Item
					label={__('Icon Position', 'airo-wp')}
					hasValue={() => iconPosition !== 'top'}
					onDeselect={() => setAttributes({ iconPosition: 'top' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Icon Position', 'airo-wp')}
						value={iconPosition}
						options={[
							{ label: __('Top', 'airo-wp'), value: 'top' },
							{ label: __('Left', 'airo-wp'), value: 'left' },
							{
								label: __('Right', 'airo-wp'),
								value: 'right',
							},
						]}
						onChange={(value) =>
							setAttributes({ iconPosition: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
};
