/**
 * Modal Trigger Block - Editor Component
 *
 * The block root is a plain block-level "justification wrapper" (`.airo-wp-justify`)
 * that core's constrained layout caps at the content column. The visible button
 * (always a `div` in the editor, to preserve editability) shrink-wraps inside it.
 * Visual supports are re-derived with the hook variants of the block-support
 * helpers so the editor canvas matches the frontend save() output.
 *
 * @package
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	RichText,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseBorderProps as useBorderProps,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseColorProps as useColorProps,
	getTypographyClassesAndStyles,
} from '@wordpress/block-editor';
import {
	SelectControl,
	Notice,
	RangeControl,
	ToggleControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import clsx from 'clsx';
import { DsgoInspectorPanel } from '../../components/shared';
import DsgoJustificationToolbar from '../../components/shared/DsgoJustificationToolbar';
import { useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { getIcon } from '../icon/utils/svg-icons';
import { IconPicker } from '../icon/components/IconPicker';
import { useIconDefaults } from '../../hooks';
import { getJustificationClass } from '../../utils/justification';

/**
 * Recursively find modal blocks in a block tree
 *
 * @param {Array} blocks - Blocks to search
 * @param {Array} result - Accumulator array
 * @return {Array} Array of modal info objects
 */
function findModals(blocks, result = []) {
	for (const block of blocks) {
		if (block.name === 'airo-wp/modal') {
			result.push({
				id: block.attributes.modalId || '',
				clientId: block.clientId,
			});
		}
		if (block.innerBlocks?.length) {
			findModals(block.innerBlocks, result);
		}
	}
	return result;
}

export default function ModalTriggerEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		targetModalId,
		text,
		buttonStyle,
		justification,
		fullWidth,
		icon,
		iconPosition,
		iconStyle,
		strokeWidth,
		iconSize,
		iconGap,
		style,
	} = attributes;

	// Theme-level icon defaults inherited when size/style are left unset.
	const iconDefaults = useIconDefaults({
		sizeKey: 'modalTrigger',
		sizeFallback: 20,
	});
	const effectiveStyle = iconStyle || iconDefaults.style;
	const effectiveSize =
		typeof iconSize === 'number' ? iconSize : iconDefaults.size;

	// Get all blocks from the editor — getBlocks returns a stable reference
	// when blocks haven't changed, so useSelect won't cause re-renders
	const allBlocks = useSelect(
		(select) => select('core/block-editor').getBlocks(),
		[]
	);

	// Derive modal list outside useSelect so we don't create new arrays
	// inside the selector. useMemo ensures stable reference.
	const modals = useMemo(() => findModals(allBlocks), [allBlocks]);

	// Create options for the select control
	const modalOptions = [
		{ label: __('Select a modal…', 'airo-wp'), value: '' },
		...modals.map((modal) => ({
			label: modal.id || __('(Unnamed Modal)', 'airo-wp'),
			value: modal.id,
		})),
	];

	// block.json skip-serializes border and typography off the wrapper, so
	// useBlockProps() below no longer carries them — there is nothing to
	// neutralise. The visible button is the inner element, so re-derive the
	// same classes/styles with the official block-support helpers (identical
	// to how core/button applies them to its inner link) and apply them there
	// instead, mirroring save.js so the editor canvas matches the frontend.
	const border = useBorderProps(attributes);
	const colors = useColorProps(attributes);
	const typography = getTypographyClassesAndStyles(attributes);

	// Extract padding - WordPress stores it in style.spacing.padding
	const paddingValue = style?.spacing?.padding;

	const hasIcon = iconPosition !== 'none' && !!icon;

	const buttonStyles = {
		...border.style,
		...colors.style,
		...typography.style,
		...(hasIcon && iconGap && { gap: iconGap }),
		...(paddingValue?.top !== undefined && {
			paddingTop: paddingValue.top,
		}),
		...(paddingValue?.right !== undefined && {
			paddingRight: paddingValue.right,
		}),
		...(paddingValue?.bottom !== undefined && {
			paddingBottom: paddingValue.bottom,
		}),
		...(paddingValue?.left !== undefined && {
			paddingLeft: paddingValue.left,
		}),
	};

	// Calculate icon wrapper styles. Preview uses the effective (possibly
	// inherited) size so it always shows a size in the editor.
	const iconWrapperStyles = {
		display: 'flex',
		alignItems: 'center',
		justifyContent: 'center',
		width: `${effectiveSize}px`,
		height: `${effectiveSize}px`,
		flexShrink: 0,
	};

	const buttonClasses = clsx(
		'airo-wp-modal-trigger',
		`airo-wp-modal-trigger--${buttonStyle}`,
		'wp-block-button',
		'wp-block-button__link',
		'wp-element-button',
		border.className,
		colors.className,
		typography.className,
		fullWidth && 'airo-wp-modal-trigger--full-width',
		iconPosition === 'end' && 'airo-wp-modal-trigger--icon-end'
	);

	const blockProps = useBlockProps({
		className: clsx(
			'airo-wp-justify',
			getJustificationClass(justification)
		),
	});

	return (
		<>
			<DsgoJustificationToolbar
				value={justification}
				onChange={(value) => setAttributes({ justification: value })}
			/>

			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							targetModalId: '',
							buttonStyle: 'fill',
							icon: '',
							iconPosition: 'none',
							iconStyle: undefined,
							strokeWidth: 1.5,
							iconSize: undefined,
							iconGap: '8px',
							fullWidth: false,
						})
					}
				>
					{modals.length === 0 && (
						<Notice status="warning" isDismissible={false}>
							{__(
								'No modal blocks found on this page. Add a Modal block first.',
								'airo-wp'
							)}
						</Notice>
					)}

					<DsgoInspectorPanel.Item
						label={__('Target Modal', 'airo-wp')}
						hasValue={() => targetModalId !== ''}
						onDeselect={() => setAttributes({ targetModalId: '' })}
						isShownByDefault
					>
						<SelectControl
							label={__('Target Modal', 'airo-wp')}
							value={targetModalId}
							options={modalOptions}
							onChange={(value) =>
								setAttributes({ targetModalId: value })
							}
							help={__(
								'Select which modal this button should open.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Button Style', 'airo-wp')}
						hasValue={() => buttonStyle !== 'fill'}
						onDeselect={() =>
							setAttributes({ buttonStyle: 'fill' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Button Style', 'airo-wp')}
							value={buttonStyle}
							onChange={(value) =>
								setAttributes({ buttonStyle: value })
							}
							options={[
								{
									label: __('Fill', 'airo-wp'),
									value: 'fill',
								},
								{
									label: __('Outline', 'airo-wp'),
									value: 'outline',
								},
								{
									label: __('Link', 'airo-wp'),
									value: 'link',
								},
							]}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Icon', 'airo-wp')}
						hasValue={() => icon !== ''}
						onDeselect={() =>
							setAttributes({ icon: '', iconPosition: 'none' })
						}
						isShownByDefault
					>
						<IconPicker
							label={__('Icon', 'airo-wp')}
							value={icon}
							onChange={(value) => {
								setAttributes({ icon: value });
								// If icon is selected and position is none, default to start
								if (value && iconPosition === 'none') {
									setAttributes({ iconPosition: 'start' });
								}
								// If icon is cleared, set position to none
								if (!value) {
									setAttributes({ iconPosition: 'none' });
								}
							}}
						/>
					</DsgoInspectorPanel.Item>

					{icon && (
						<DsgoInspectorPanel.Item
							label={__('Icon Position', 'airo-wp')}
							hasValue={() => iconPosition !== 'none'}
							onDeselect={() =>
								setAttributes({ iconPosition: 'none' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Icon Position', 'airo-wp')}
								value={iconPosition}
								options={[
									{
										label: __('Start', 'airo-wp'),
										value: 'start',
									},
									{
										label: __('End', 'airo-wp'),
										value: 'end',
									},
									{
										label: __('None', 'airo-wp'),
										value: 'none',
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

					{icon && iconPosition !== 'none' && (
						<DsgoInspectorPanel.Item
							label={__('Icon Style', 'airo-wp')}
							hasValue={() => typeof iconStyle === 'string'}
							onDeselect={() =>
								setAttributes({ iconStyle: undefined })
							}
							isShownByDefault
						>
							<ToggleGroupControl
								label={__('Icon Style', 'airo-wp')}
								value={effectiveStyle}
								onChange={(value) =>
									setAttributes({ iconStyle: value })
								}
								help={
									!iconStyle &&
									sprintf(
										/* translators: %s: inherited icon style (Filled or Outlined). */
										__(
											'Inheriting theme default (%s).',
											'airo-wp'
										),
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

					{icon &&
						iconPosition !== 'none' &&
						effectiveStyle === 'outlined' && (
							<DsgoInspectorPanel.Item
								label={__('Stroke Width', 'airo-wp')}
								hasValue={() => strokeWidth !== 1.5}
								onDeselect={() =>
									setAttributes({ strokeWidth: 1.5 })
								}
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

					{icon && iconPosition !== 'none' && (
						<DsgoInspectorPanel.Item
							label={__('Icon Size (px)', 'airo-wp')}
							hasValue={() => typeof iconSize === 'number'}
							onDeselect={() =>
								setAttributes({ iconSize: undefined })
							}
							isShownByDefault
						>
							<RangeControl
								label={__('Icon Size (px)', 'airo-wp')}
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
								step={1}
								allowReset
								placeholder={iconDefaults.size}
								help={
									typeof iconSize !== 'number' &&
									sprintf(
										/* translators: %d: inherited icon size in pixels. */
										__(
											'Inheriting theme default (%dpx).',
											'airo-wp'
										),
										iconDefaults.size
									)
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{icon && iconPosition !== 'none' && (
						<DsgoInspectorPanel.Item
							label={__('Icon Gap', 'airo-wp')}
							hasValue={() => iconGap !== '8px'}
							onDeselect={() => setAttributes({ iconGap: '8px' })}
							isShownByDefault
						>
							<UnitControl
								label={__('Icon Gap', 'airo-wp')}
								value={iconGap}
								onChange={(value) =>
									setAttributes({ iconGap: value })
								}
								units={[
									{ value: 'px', label: 'px' },
									{ value: 'em', label: 'em' },
									{ value: 'rem', label: 'rem' },
								]}
								__next40pxDefaultSize
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Full width', 'airo-wp')}
						hasValue={() => !!fullWidth}
						onDeselect={() => setAttributes({ fullWidth: false })}
						isShownByDefault
					>
						<ToggleControl
							__nextHasNoMarginBottom
							label={__('Full width', 'airo-wp')}
							checked={!!fullWidth}
							onChange={(value) =>
								setAttributes({ fullWidth: value })
							}
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				<div className={buttonClasses} style={buttonStyles}>
					{icon && iconPosition !== 'none' && (
						<span
							className="airo-wp-modal-trigger__icon"
							style={iconWrapperStyles}
						>
							{getIcon(icon, effectiveStyle, strokeWidth)}
						</span>
					)}
					<RichText
						tagName="span"
						value={text}
						onChange={(value) => setAttributes({ text: value })}
						placeholder={__('Button text…', 'airo-wp')}
						allowedFormats={['core/bold', 'core/italic']}
						className="airo-wp-modal-trigger__text"
					/>
				</div>
			</div>
		</>
	);
}
