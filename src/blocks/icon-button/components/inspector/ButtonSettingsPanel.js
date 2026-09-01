/**
 * Icon Button - Button Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for icon-button icon,
 * animation, and modal-close attributes. Meant to be composed inside
 * the Settings DsgoInspectorPanel in icon-button/edit.js.
 *
 * @since 1.0.0
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	SelectControl,
	RangeControl,
	ToggleControl,
	TextControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';
import { IconPicker } from '../../../icon/components/IconPicker';

const ANIMATION_LABELS = {
	none: __('None', 'airo-wp'),
	'fill-diagonal': __('Fill Diagonal', 'airo-wp'),
	'zoom-in': __('Zoom In', 'airo-wp'),
	'slide-left': __('Slide Left', 'airo-wp'),
	'slide-right': __('Slide Right', 'airo-wp'),
	'slide-down': __('Slide Down', 'airo-wp'),
	'slide-up': __('Slide Up', 'airo-wp'),
	'border-pulse': __('Border Pulse', 'airo-wp'),
	'border-glow': __('Border Glow', 'airo-wp'),
	lift: __('Lift', 'airo-wp'),
	shrink: __('Shrink', 'airo-wp'),
};

export const ButtonSettingsPanel = ({
	icon,
	iconPosition,
	iconStyle,
	strokeWidth,
	iconSize,
	iconGap,
	iconDefaults,
	hoverAnimation,
	adminDefaultHover,
	modalCloseId,
	isInsideModal,
	setAttributes,
}) => {
	const effectiveStyle = iconStyle || iconDefaults.style;
	const adminDefault = adminDefaultHover || 'none';
	const defaultLabel =
		adminDefault !== 'none'
			? sprintf(
					/* translators: %s: animation name */
					__('Default (%s)', 'airo-wp'),
					ANIMATION_LABELS[adminDefault] || adminDefault
				)
			: __('Default (None)', 'airo-wp');

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Hover Animation', 'airo-wp')}
				hasValue={() => hoverAnimation !== 'none'}
				onDeselect={() => setAttributes({ hoverAnimation: 'none' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Hover Animation', 'airo-wp')}
					value={hoverAnimation}
					options={[
						{
							label: defaultLabel,
							value: 'none',
						},
						{
							label: __('None (No Animation)', 'airo-wp'),
							value: 'explicit-none',
						},
						{
							label: __('Fill Diagonal', 'airo-wp'),
							value: 'fill-diagonal',
						},
						{
							label: __('Zoom In', 'airo-wp'),
							value: 'zoom-in',
						},
						{
							label: __('Slide Left', 'airo-wp'),
							value: 'slide-left',
						},
						{
							label: __('Slide Right', 'airo-wp'),
							value: 'slide-right',
						},
						{
							label: __('Slide Down', 'airo-wp'),
							value: 'slide-down',
						},
						{
							label: __('Slide Up', 'airo-wp'),
							value: 'slide-up',
						},
						{
							label: __('Border Pulse', 'airo-wp'),
							value: 'border-pulse',
						},
						{
							label: __('Border Glow', 'airo-wp'),
							value: 'border-glow',
						},
						{
							label: __('Lift', 'airo-wp'),
							value: 'lift',
						},
						{
							label: __('Shrink', 'airo-wp'),
							value: 'shrink',
						},
					]}
					onChange={(value) =>
						setAttributes({
							hoverAnimation: value,
						})
					}
					help={__(
						'Choose a hover animation. "Default" uses the site-wide setting from Settings > Animations.',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Icon Position', 'airo-wp')}
				hasValue={() => iconPosition !== 'start'}
				onDeselect={() => setAttributes({ iconPosition: 'start' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Icon Position', 'airo-wp')}
					value={iconPosition}
					options={[
						{ label: __('Start', 'airo-wp'), value: 'start' },
						{ label: __('End', 'airo-wp'), value: 'end' },
						{ label: __('None', 'airo-wp'), value: 'none' },
					]}
					onChange={(value) => setAttributes({ iconPosition: value })}
					help={__('Position of icon relative to text', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{iconPosition !== 'none' && (
				<DsgoInspectorPanel.Item
					label={__('Icon', 'airo-wp')}
					hasValue={() => icon !== 'lightbulb'}
					onDeselect={() => setAttributes({ icon: 'lightbulb' })}
					isShownByDefault
				>
					<IconPicker
						value={icon}
						onChange={(value) => setAttributes({ icon: value })}
					/>
				</DsgoInspectorPanel.Item>
			)}

			{iconPosition !== 'none' && (
				<DsgoInspectorPanel.Item
					label={__('Style', 'airo-wp')}
					hasValue={() => typeof iconStyle === 'string'}
					onDeselect={() => setAttributes({ iconStyle: undefined })}
					isShownByDefault
				>
					<ToggleGroupControl
						label={__('Style', 'airo-wp')}
						value={effectiveStyle}
						onChange={(value) =>
							setAttributes({ iconStyle: value })
						}
						help={
							!iconStyle &&
							sprintf(
								/* translators: %s: inherited icon style (Filled or Outlined). */
								__('Inheriting theme default (%s).', 'airo-wp'),
								iconDefaults.style === 'outlined'
									? __('Outlined', 'airo-wp')
									: __('Filled', 'airo-wp')
							)
						}
						isBlock
						__nextHasNoMarginBottom
					>
						<ToggleGroupControlOption
							value="filled"
							label={__('Filled', 'airo-wp')}
						/>
						<ToggleGroupControlOption
							value="outlined"
							label={__('Outlined', 'airo-wp')}
						/>
					</ToggleGroupControl>
				</DsgoInspectorPanel.Item>
			)}

			{iconPosition !== 'none' && effectiveStyle === 'outlined' && (
				<DsgoInspectorPanel.Item
					label={__('Stroke Width', 'airo-wp')}
					hasValue={() => strokeWidth !== 1.5}
					onDeselect={() => setAttributes({ strokeWidth: 1.5 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Stroke Width', 'airo-wp')}
						value={strokeWidth}
						onChange={(value) =>
							setAttributes({ strokeWidth: value })
						}
						min={0.5}
						max={4}
						step={0.5}
						help={__(
							'Thinner strokes work better for detailed icons',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{iconPosition !== 'none' && (
				<DsgoInspectorPanel.Item
					label={__('Icon Size', 'airo-wp')}
					hasValue={() => typeof iconSize === 'number'}
					onDeselect={() => setAttributes({ iconSize: undefined })}
					isShownByDefault
				>
					<RangeControl
						label={__('Icon Size', 'airo-wp')}
						value={iconSize}
						onChange={(value) =>
							setAttributes({
								iconSize:
									typeof value === 'number'
										? value
										: undefined,
							})
						}
						min={12}
						max={48}
						allowReset
						placeholder={iconDefaults.size}
						help={
							typeof iconSize !== 'number'
								? sprintf(
										/* translators: %d: inherited icon size in pixels. */
										__(
											'Inheriting theme default (%dpx).',
											'airo-wp'
										),
										iconDefaults.size
									)
								: __('Icon size in pixels', 'airo-wp')
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{iconPosition !== 'none' && (
				<DsgoInspectorPanel.Item
					label={__('Icon Gap', 'airo-wp')}
					hasValue={() => iconGap !== undefined && iconGap !== ''}
					onDeselect={() => setAttributes({ iconGap: undefined })}
					isShownByDefault
				>
					<UnitControl
						label={__('Icon Gap', 'airo-wp')}
						value={iconGap}
						// Static placeholder = the literal fallback in the
						// stylesheet chain. It won't reflect a kit override of
						// --airo-wp-icon-button-gap / the theme token (a known
						// limitation of static placeholders); the empty field
						// always means "inherit the themed default".
						placeholder="8px"
						onChange={(value) => setAttributes({ iconGap: value })}
						units={[
							{ value: 'px', label: 'px' },
							{ value: 'em', label: 'em' },
							{ value: 'rem', label: 'rem' },
						]}
						help={__('Space between icon and text', 'airo-wp')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Close modal on click', 'airo-wp')}
				hasValue={() => !!modalCloseId}
				onDeselect={() => setAttributes({ modalCloseId: '' })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Close modal on click', 'airo-wp')}
					checked={!!modalCloseId}
					onChange={(value) =>
						setAttributes({
							modalCloseId: value ? 'true' : '',
						})
					}
					help={
						isInsideModal
							? __(
									'Close the parent modal when this button is clicked',
									'airo-wp'
								)
							: __(
									'Close a modal when this button is clicked (enter modal ID below)',
									'airo-wp'
								)
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{modalCloseId && !isInsideModal && (
				<DsgoInspectorPanel.Item
					label={__('Modal ID', 'airo-wp')}
					hasValue={() => !!modalCloseId && modalCloseId !== 'true'}
					onDeselect={() => setAttributes({ modalCloseId: 'true' })}
					isShownByDefault
				>
					<TextControl
						label={__('Modal ID', 'airo-wp')}
						value={modalCloseId === 'true' ? '' : modalCloseId}
						onChange={(value) =>
							setAttributes({
								modalCloseId: value || 'true',
							})
						}
						placeholder={__('Enter modal ID', 'airo-wp')}
						help={__(
							'Enter the ID of the modal to close',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
};
