/**
 * Form Builder Block - Editor Component
 *
 * @since 1.0.0
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	useSettings,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import {
	Notice,
	TextControl,
	TextareaControl,
	ToggleControl,
	RangeControl,
	SelectControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useEffect, useMemo } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import classnames from 'classnames';
import { useUniqueBlockId } from '../../hooks';
import {
	encodeColorValue,
	decodeColorValue,
} from '../../utils/encode-color-value';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { validateCSSLength } from '../../utils/css-generator';
import FormBuilderPlaceholder from './components/FormBuilderPlaceholder';

// Blocks that Gutenberg identifies as form fields for the reply-to dropdown.
const EMAILABLE_FIELD_BLOCKS = new Set([
	'airo-wp/form-text-field',
	'airo-wp/form-email-field',
	'airo-wp/form-textarea-field',
	'airo-wp/form-number-field',
	'airo-wp/form-phone-field',
	'airo-wp/form-url-field',
	'airo-wp/form-date-field',
	'airo-wp/form-time-field',
	'airo-wp/form-select-field',
	'airo-wp/form-checkbox-field',
	'airo-wp/form-hidden-field',
]);

export default function FormBuilderEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		formId,
		submitButtonText,
		submitButtonAlignment,
		submitButtonPosition,
		ajaxSubmit,
		successMessage,
		errorMessage,
		fieldSpacing,
		inputHeight,
		inputPadding,
		fieldLabelColor,
		fieldBorderColor,
		fieldBackgroundColor,
		fieldBorderRadius,
		submitButtonColor,
		submitButtonBackgroundColor,
		submitButtonPaddingVertical,
		submitButtonPaddingHorizontal,
		submitButtonFontSize,
		submitButtonHeight,
		submitButtonHoverColor,
		submitButtonHoverBackgroundColor,
		enableHoneypot,
		enableRateLimit,
		rateLimitCount,
		rateLimitWindow,
		enableTurnstile,
		enableEmail,
		emailTo,
		emailSubject,
		emailFromName,
		emailFromEmail,
		emailReplyTo,
		emailBody,
		redirectUrl,
	} = attributes;

	// Get theme color palette and gradient settings
	const colorGradientSettings = useMultipleOriginColorsAndGradients();

	// Read the site-wide default hover animation from theme.json custom settings.
	const [themeDefaultHover] = useSettings(
		'custom.airowp.defaultIconButtonHover'
	);

	// Resolve the effective animation for editor preview.
	// Form submit always uses the site-wide default — no per-block override.
	// Validate against allowlist to match backend (class-plugin.php ALLOWED_HOVER_ANIMATIONS).
	const ALLOWED_ANIMATIONS = [
		'fill-diagonal',
		'zoom-in',
		'slide-left',
		'slide-right',
		'slide-down',
		'slide-up',
		'border-pulse',
		'border-glow',
		'lift',
		'shrink',
	];
	const adminDefault = themeDefaultHover || 'none';
	const isValidAnimation = ALLOWED_ANIMATIONS.includes(adminDefault);
	const effectiveAnimation =
		isValidAnimation && adminDefault !== 'none' ? adminDefault : null;

	// Build animation class for the submit button
	const submitAnimationClass =
		effectiveAnimation && effectiveAnimation !== 'none'
			? ` airo-wp-form__submit--${effectiveAnimation}`
			: '';

	useUniqueBlockId({
		clientId,
		attributeName: 'formId',
		value: formId,
		setAttributes,
	});

	// Track child count so we can show the template chooser on first insert,
	// and build the reply-to dropdown options from the actual form fields.
	//
	// Return the store's already-memoized `getBlocks` array directly rather than
	// a freshly-derived object/array: useSelect compares its mapSelect result by
	// reference, so building `.filter().map()` output inside the selector makes
	// it unstable on every call (the "useSelect returns different values" dev
	// warning). Derive the dropdown options in a useMemo keyed on that stable
	// array instead.
	// `getBlocks` always returns an array (stable `[]` for an unknown clientId),
	// so `|| []` short-circuits to that same reference and never reintroduces the
	// instability — it's kept purely as a defensive guard.
	const childBlocks = useSelect(
		(select) => select('core/block-editor').getBlocks(clientId) || [],
		[clientId]
	);
	const hasInnerBlocks = childBlocks.length > 0;
	const replyToFieldOptions = useMemo(
		() =>
			childBlocks
				.filter((child) => EMAILABLE_FIELD_BLOCKS.has(child.name))
				.map((child) => ({
					name: child.attributes?.fieldName || '',
					label: child.attributes?.label || '',
				}))
				.filter((field) => !!field.name),
		[childBlocks]
	);

	// Keep hasFields in sync with the actual child field count so save.js can
	// suppress the submit button/form wrapper when no template is picked.
	useEffect(() => {
		if (attributes.hasFields !== hasInnerBlocks) {
			setAttributes({ hasFields: hasInnerBlocks });
		}
	}, [hasInnerBlocks, attributes.hasFields, setAttributes]);

	// Calculate classes
	const formClasses = classnames('airo-wp-form-builder', {
		[`airo-wp-form-builder--align-${submitButtonAlignment}`]:
			submitButtonAlignment && submitButtonPosition === 'below',
		'airo-wp-form-builder--button-inline': submitButtonPosition === 'inline',
	});

	// Apply form settings as CSS custom properties - MUST MATCH save.js.
	// Spacing/sizing tokens are only written when explicitly set; otherwise they
	// inherit the theme.json custom properties defined in style.scss.
	const formStyles = {
		...(fieldSpacing && { '--airo-wp-form-field-spacing': fieldSpacing }),
		...(inputHeight && { '--airo-wp-form-input-height': inputHeight }),
		...(inputPadding && { '--airo-wp-form-input-padding': inputPadding }),
		'--airo-wp-form-label-color': convertColorToCSSVar(fieldLabelColor),
		'--airo-wp-form-border-color': convertColorToCSSVar(fieldBorderColor),
		'--airo-wp-form-field-bg': convertColorToCSSVar(fieldBackgroundColor),
		'--airo-wp-form-border-radius': validateCSSLength(fieldBorderRadius),
		// Button colors now applied as inline styles on button element
	};

	// Submit button style - MUST MATCH save.js. Sizing is only written when
	// explicitly set; otherwise the button inherits the theme's global button
	// styles via wp-element-button.
	const submitButtonStyle = {
		...(submitButtonColor && {
			color: convertColorToCSSVar(submitButtonColor),
		}),
		...(submitButtonBackgroundColor && {
			backgroundColor: convertColorToCSSVar(submitButtonBackgroundColor),
		}),
		...(submitButtonHeight && { minHeight: submitButtonHeight }),
		...(submitButtonPaddingVertical && {
			paddingTop: submitButtonPaddingVertical,
			paddingBottom: submitButtonPaddingVertical,
		}),
		...(submitButtonPaddingHorizontal && {
			paddingLeft: submitButtonPaddingHorizontal,
			paddingRight: submitButtonPaddingHorizontal,
		}),
		...(submitButtonFontSize && { fontSize: submitButtonFontSize }),
		...(submitButtonHoverBackgroundColor && {
			'--airo-wp-button-hover-bg': convertColorToCSSVar(
				submitButtonHoverBackgroundColor
			),
		}),
		...(submitButtonHoverColor && {
			'--airo-wp-button-hover-color': convertColorToCSSVar(
				submitButtonHoverColor
			),
		}),
	};

	const blockProps = useBlockProps({
		className: formClasses,
		style: formStyles,
		'data-form-id': formId,
	});

	// Inner blocks for form fields. The first-insert template chooser seeds
	// innerBlocks itself, so we omit a default template here — otherwise the
	// placeholder would never show.
	// Extract children so we can add button inside fields container.
	const { children, ...innerBlocksPropsWithoutChildren } =
		useInnerBlocksProps(
			{
				className: 'airo-wp-form__fields',
			},
			{
				allowedBlocks: [
					'airo-wp/form-text-field',
					'airo-wp/form-email-field',
					'airo-wp/form-textarea-field',
					'airo-wp/form-number-field',
					'airo-wp/form-phone-field',
					'airo-wp/form-url-field',
					'airo-wp/form-date-field',
					'airo-wp/form-time-field',
					'airo-wp/form-select-field',
					'airo-wp/form-checkbox-field',
					'airo-wp/form-hidden-field',
				],
				orientation: 'vertical',
			}
		);

	// Show the template chooser when the form is empty.
	if (!hasInnerBlocks) {
		return (
			<div {...blockProps}>
				<FormBuilderPlaceholder
					clientId={clientId}
					setAttributes={setAttributes}
				/>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							submitButtonText: 'Submit',
							submitButtonAlignment: 'left',
							submitButtonPosition: 'below',
							ajaxSubmit: true,
							successMessage:
								'Thank you! Your form has been submitted successfully.',
							errorMessage:
								'There was an error submitting the form. Please try again.',
							redirectUrl: '',
							fieldSpacing: '',
							inputHeight: '',
							inputPadding: '',
							fieldBorderRadius: '',
							submitButtonHeight: '',
							submitButtonPaddingVertical: '',
							submitButtonPaddingHorizontal: '',
							submitButtonFontSize: '',
							enableHoneypot: true,
							enableRateLimit: true,
							rateLimitCount: 3,
							rateLimitWindow: 60,
							enableTurnstile: false,
							enableEmail: true,
							emailTo: '',
							emailSubject: 'New Form Submission',
							emailFromName: '',
							emailFromEmail: '',
							emailReplyTo: '',
							emailBody: '',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('AJAX Submit', 'airo-wp')}
						hasValue={() => ajaxSubmit !== true}
						onDeselect={() => setAttributes({ ajaxSubmit: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('AJAX Submit', 'airo-wp')}
							checked={ajaxSubmit}
							onChange={(value) =>
								setAttributes({ ajaxSubmit: value })
							}
							help={__(
								'Submit form without page reload',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Submit Button Text', 'airo-wp')}
						hasValue={() => submitButtonText !== 'Submit'}
						onDeselect={() =>
							setAttributes({ submitButtonText: 'Submit' })
						}
						isShownByDefault
					>
						<TextControl
							label={__('Submit Button Text', 'airo-wp')}
							value={submitButtonText}
							onChange={(value) =>
								setAttributes({ submitButtonText: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Button Position', 'airo-wp')}
						hasValue={() => submitButtonPosition !== 'below'}
						onDeselect={() =>
							setAttributes({ submitButtonPosition: 'below' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Button Position', 'airo-wp')}
							value={submitButtonPosition}
							options={[
								{
									label: __('Below fields', 'airo-wp'),
									value: 'below',
								},
								{
									label: __(
										'Inline with last field',
										'airo-wp'
									),
									value: 'inline',
								},
							]}
							onChange={(value) =>
								setAttributes({ submitButtonPosition: value })
							}
							help={__(
								'Place button below all fields or inline with the last field (useful for subscribe forms)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{submitButtonPosition === 'below' && (
						<DsgoInspectorPanel.Item
							label={__('Button Alignment', 'airo-wp')}
							hasValue={() => submitButtonAlignment !== 'left'}
							onDeselect={() =>
								setAttributes({ submitButtonAlignment: 'left' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Button Alignment', 'airo-wp')}
								value={submitButtonAlignment}
								options={[
									{
										label: __('Left', 'airo-wp'),
										value: 'left',
									},
									{
										label: __('Center', 'airo-wp'),
										value: 'center',
									},
									{
										label: __('Right', 'airo-wp'),
										value: 'right',
									},
								]}
								onChange={(value) =>
									setAttributes({
										submitButtonAlignment: value,
									})
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Button Height', 'airo-wp')}
						hasValue={() => submitButtonHeight !== ''}
						onDeselect={() =>
							setAttributes({ submitButtonHeight: '' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Button Height', 'airo-wp')}
							value={submitButtonHeight}
							onChange={(value) =>
								setAttributes({
									submitButtonHeight: value || '',
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 44 },
								{ value: 'rem', label: 'rem', default: 2.75 },
								{ value: 'em', label: 'em', default: 2.75 },
							]}
							min={28}
							max={200}
							help={__(
								'Minimum height for submit button',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Button Padding (Vertical)', 'airo-wp')}
						hasValue={() => submitButtonPaddingVertical !== ''}
						onDeselect={() =>
							setAttributes({
								submitButtonPaddingVertical: '',
							})
						}
						isShownByDefault
					>
						<UnitControl
							label={__(
								'Button Padding (Vertical)',
								'airo-wp'
							)}
							value={submitButtonPaddingVertical}
							onChange={(value) =>
								setAttributes({
									submitButtonPaddingVertical: value || '',
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 12 },
								{ value: 'rem', label: 'rem', default: 0.75 },
								{ value: 'em', label: 'em', default: 0.75 },
							]}
							min={0}
							max={50}
							help={__(
								'Top and bottom padding for button',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Button Padding (Horizontal)', 'airo-wp')}
						hasValue={() => submitButtonPaddingHorizontal !== ''}
						onDeselect={() =>
							setAttributes({
								submitButtonPaddingHorizontal: '',
							})
						}
						isShownByDefault
					>
						<UnitControl
							label={__(
								'Button Padding (Horizontal)',
								'airo-wp'
							)}
							value={submitButtonPaddingHorizontal}
							onChange={(value) =>
								setAttributes({
									submitButtonPaddingHorizontal: value || '',
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 32 },
								{ value: 'rem', label: 'rem', default: 2 },
								{ value: 'em', label: 'em', default: 2 },
							]}
							min={0}
							max={100}
							help={__(
								'Left and right padding for button',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Button Font Size', 'airo-wp')}
						hasValue={() => submitButtonFontSize !== ''}
						onDeselect={() =>
							setAttributes({ submitButtonFontSize: '' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Button Font Size', 'airo-wp')}
							value={submitButtonFontSize}
							onChange={(value) =>
								setAttributes({
									submitButtonFontSize: value || '',
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 16 },
								{ value: 'rem', label: 'rem', default: 1 },
								{ value: 'em', label: 'em', default: 1 },
							]}
							min={10}
							max={100}
							help={__(
								'Font size for button text (leave empty to inherit)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Field Spacing', 'airo-wp')}
						hasValue={() => fieldSpacing !== ''}
						onDeselect={() => setAttributes({ fieldSpacing: '' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Field Spacing', 'airo-wp')}
							value={fieldSpacing}
							onChange={(value) =>
								setAttributes({
									fieldSpacing: value || '',
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 24 },
								{ value: 'rem', label: 'rem', default: 1.5 },
								{ value: 'em', label: 'em', default: 1.5 },
							]}
							min={0}
							max={100}
							help={__(
								'Space between form fields',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Input Height', 'airo-wp')}
						hasValue={() => inputHeight !== ''}
						onDeselect={() => setAttributes({ inputHeight: '' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Input Height', 'airo-wp')}
							value={inputHeight}
							onChange={(value) =>
								setAttributes({ inputHeight: value || '' })
							}
							units={[
								{ value: 'px', label: 'px', default: 44 },
								{ value: 'rem', label: 'rem', default: 2.75 },
								{ value: 'em', label: 'em', default: 2.75 },
							]}
							min={28}
							max={200}
							help={__(
								'Minimum height for input fields',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Input Padding', 'airo-wp')}
						hasValue={() => inputPadding !== ''}
						onDeselect={() => setAttributes({ inputPadding: '' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Input Padding', 'airo-wp')}
							value={inputPadding}
							onChange={(value) =>
								setAttributes({
									inputPadding: value || '',
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 12 },
								{ value: 'rem', label: 'rem', default: 0.75 },
								{ value: 'em', label: 'em', default: 0.75 },
							]}
							min={0}
							max={50}
							help={__(
								'Padding inside input fields',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Field Border Radius', 'airo-wp')}
						hasValue={() => fieldBorderRadius !== ''}
						onDeselect={() =>
							setAttributes({ fieldBorderRadius: '' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Border Radius', 'airo-wp')}
							value={fieldBorderRadius}
							onChange={(value) =>
								setAttributes({
									fieldBorderRadius: value || '',
								})
							}
							units={[
								{ value: 'px', label: 'px', default: 6 },
								{ value: 'rem', label: 'rem', default: 0.375 },
								{ value: 'em', label: 'em', default: 0.375 },
							]}
							min={0}
							max={100}
							help={__(
								'Border radius for input fields',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Success Message', 'airo-wp')}
						hasValue={() =>
							successMessage !==
							'Thank you! Your form has been submitted successfully.'
						}
						onDeselect={() =>
							setAttributes({
								successMessage:
									'Thank you! Your form has been submitted successfully.',
							})
						}
						isShownByDefault
					>
						<TextareaControl
							label={__('Success Message', 'airo-wp')}
							value={successMessage}
							onChange={(value) =>
								setAttributes({ successMessage: value })
							}
							help={__(
								'Message shown after successful submission',
								'airo-wp'
							)}
							rows={3}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Error Message', 'airo-wp')}
						hasValue={() =>
							errorMessage !==
							'There was an error submitting the form. Please try again.'
						}
						onDeselect={() =>
							setAttributes({
								errorMessage:
									'There was an error submitting the form. Please try again.',
							})
						}
						isShownByDefault
					>
						<TextareaControl
							label={__('Error Message', 'airo-wp')}
							value={errorMessage}
							onChange={(value) =>
								setAttributes({ errorMessage: value })
							}
							help={__(
								'Message shown if submission fails',
								'airo-wp'
							)}
							rows={3}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Redirect URL', 'airo-wp')}
						hasValue={() => redirectUrl !== ''}
						onDeselect={() => setAttributes({ redirectUrl: '' })}
						isShownByDefault
					>
						<TextControl
							label={__('Redirect URL', 'airo-wp')}
							value={redirectUrl}
							onChange={(value) =>
								setAttributes({ redirectUrl: value })
							}
							type="url"
							placeholder="https://example.com/thank-you"
							help={__(
								'Redirect to this URL after successful submission. Leave empty to show the success message instead. Requires AJAX Submit to be enabled.',
								'airo-wp'
							)}
							disabled={!ajaxSubmit}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Enable Honeypot', 'airo-wp')}
						hasValue={() => enableHoneypot !== true}
						onDeselect={() =>
							setAttributes({ enableHoneypot: true })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Enable Honeypot', 'airo-wp')}
							checked={enableHoneypot}
							onChange={(value) =>
								setAttributes({ enableHoneypot: value })
							}
							help={__(
								'Invisible field to catch spam bots',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Enable Rate Limiting', 'airo-wp')}
						hasValue={() => enableRateLimit !== true}
						onDeselect={() =>
							setAttributes({ enableRateLimit: true })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Enable Rate Limiting', 'airo-wp')}
							checked={enableRateLimit}
							onChange={(value) =>
								setAttributes({ enableRateLimit: value })
							}
							help={__(
								'Limit submissions per IP address',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{enableRateLimit && (
						<DsgoInspectorPanel.Item
							label={__('Max Submissions', 'airo-wp')}
							hasValue={() => rateLimitCount !== 3}
							onDeselect={() =>
								setAttributes({ rateLimitCount: 3 })
							}
							isShownByDefault
						>
							<RangeControl
								label={__('Max Submissions', 'airo-wp')}
								value={rateLimitCount}
								onChange={(value) =>
									setAttributes({ rateLimitCount: value })
								}
								min={1}
								max={10}
								help={__(
									'Maximum submissions allowed per time window',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{enableRateLimit && (
						<DsgoInspectorPanel.Item
							label={__(
								'Rate Limit Time Window (seconds)',
								'airo-wp'
							)}
							hasValue={() => rateLimitWindow !== 60}
							onDeselect={() =>
								setAttributes({ rateLimitWindow: 60 })
							}
							isShownByDefault
						>
							<RangeControl
								label={__(
									'Time Window (seconds)',
									'airo-wp'
								)}
								value={rateLimitWindow}
								onChange={(value) =>
									setAttributes({ rateLimitWindow: value })
								}
								min={30}
								max={300}
								step={30}
								help={__(
									'Time period for rate limiting',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Enable Cloudflare Turnstile', 'airo-wp')}
						hasValue={() => enableTurnstile !== false}
						onDeselect={() =>
							setAttributes({ enableTurnstile: false })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__(
								'Enable Cloudflare Turnstile',
								'airo-wp'
							)}
							checked={enableTurnstile}
							onChange={(value) =>
								setAttributes({ enableTurnstile: value })
							}
							help={__(
								'Privacy-friendly CAPTCHA alternative',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
						{enableTurnstile && (
							<p className="airo-wp-form-builder__turnstile-note">
								{__(
									'Configure your Turnstile keys in',
									'airo-wp'
								)}{' '}
								<a
									href={
										window.designSetGoAdmin?.adminUrl +
										'admin.php?page=airo-wp-settings'
									}
									target="_blank"
									rel="noopener noreferrer"
									aria-label={__(
										'Settings → Integrations (opens in new tab)',
										'airo-wp'
									)}
								>
									{__(
										'Settings → Integrations',
										'airo-wp'
									)}
								</a>
								.{' '}
								{__(
									'Widget mode (Managed, Non-interactive, Invisible) is configured in your Cloudflare dashboard.',
									'airo-wp'
								)}
							</p>
						)}
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Enable Email Notifications', 'airo-wp')}
						hasValue={() => enableEmail !== true}
						onDeselect={() => setAttributes({ enableEmail: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__(
								'Enable Email Notifications',
								'airo-wp'
							)}
							checked={enableEmail}
							onChange={(value) =>
								setAttributes({ enableEmail: value })
							}
							help={__(
								'Send email when form is submitted',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
						{enableEmail && (
							<Notice
								status="info"
								isDismissible={false}
								className="airo-wp-form__smtp-notice"
							>
								{__(
									'Emails are sent using wp_mail(). For reliable delivery, consider using an SMTP plugin like WP Mail SMTP.',
									'airo-wp'
								)}
							</Notice>
						)}
					</DsgoInspectorPanel.Item>

					{enableEmail && (
						<DsgoInspectorPanel.Item
							label={__('Recipient Email', 'airo-wp')}
							hasValue={() => emailTo !== ''}
							onDeselect={() => setAttributes({ emailTo: '' })}
							isShownByDefault
						>
							<TextControl
								label={__('Recipient Email', 'airo-wp')}
								value={emailTo}
								onChange={(value) =>
									setAttributes({ emailTo: value })
								}
								type="email"
								placeholder="admin@example.com"
								help={__(
									'Leave empty to use the site admin email address',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{enableEmail && (
						<DsgoInspectorPanel.Item
							label={__('Email Subject', 'airo-wp')}
							hasValue={() =>
								emailSubject !== 'New Form Submission'
							}
							onDeselect={() =>
								setAttributes({
									emailSubject: 'New Form Submission',
								})
							}
							isShownByDefault
						>
							<TextControl
								label={__('Email Subject', 'airo-wp')}
								value={emailSubject}
								onChange={(value) =>
									setAttributes({ emailSubject: value })
								}
								help={__(
									'Subject line for notification emails. Use {field_name} for dynamic values.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{enableEmail && (
						<DsgoInspectorPanel.Item
							label={__('From Name', 'airo-wp')}
							hasValue={() => emailFromName !== ''}
							onDeselect={() =>
								setAttributes({ emailFromName: '' })
							}
							isShownByDefault
						>
							<TextControl
								label={__('From Name', 'airo-wp')}
								value={emailFromName}
								onChange={(value) =>
									setAttributes({ emailFromName: value })
								}
								placeholder={__('Site Name', 'airo-wp')}
								help={__(
									'Name shown as email sender (leave empty for site name)',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{enableEmail && (
						<DsgoInspectorPanel.Item
							label={__('From Email', 'airo-wp')}
							hasValue={() => emailFromEmail !== ''}
							onDeselect={() =>
								setAttributes({ emailFromEmail: '' })
							}
							isShownByDefault
						>
							<TextControl
								label={__('From Email', 'airo-wp')}
								value={emailFromEmail}
								onChange={(value) =>
									setAttributes({ emailFromEmail: value })
								}
								type="email"
								placeholder="wordpress@example.com"
								help={__(
									'Email address shown as sender (leave empty for wordpress@yourdomain.com)',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{enableEmail && (
						<DsgoInspectorPanel.Item
							label={__('Reply-To Field', 'airo-wp')}
							hasValue={() => emailReplyTo !== ''}
							onDeselect={() =>
								setAttributes({ emailReplyTo: '' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Reply-To Field', 'airo-wp')}
								value={emailReplyTo || ''}
								options={[
									{
										label: __(
											'— None (use From address) —',
											'airo-wp'
										),
										value: '',
									},
									...replyToFieldOptions.map((field) => ({
										label: field.label
											? `${field.label} (${field.name})`
											: field.name,
										value: field.name,
									})),
									// If the saved value references a field that
									// no longer exists, keep it as a selectable
									// option so nothing silently changes behavior.
									...(emailReplyTo &&
									!replyToFieldOptions.some(
										(field) => field.name === emailReplyTo
									)
										? [
												{
													label: sprintf(
														/* translators: %s: missing field name */
														__(
															'%s (field not found)',
															'airo-wp'
														),
														emailReplyTo
													),
													value: emailReplyTo,
												},
											]
										: []),
								]}
								onChange={(value) =>
									setAttributes({ emailReplyTo: value })
								}
								help={__(
									'Submission from this form field is used as the reply-to address on notification emails.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{enableEmail && (
						<DsgoInspectorPanel.Item
							label={__('Email Body Template', 'airo-wp')}
							hasValue={() => emailBody !== ''}
							onDeselect={() => setAttributes({ emailBody: '' })}
							isShownByDefault
						>
							<TextareaControl
								label={__('Email Body Template', 'airo-wp')}
								value={emailBody}
								onChange={(value) =>
									setAttributes({ emailBody: value })
								}
								placeholder={
									__('New form submission:', 'airo-wp') +
									'\n\n{all_fields}\n\n' +
									__(
										'Submitted from: {page_url}',
										'airo-wp'
									)
								}
								help={__(
									'Email content template. Use {field_name} for specific fields or {all_fields} for all submitted data.',
									'airo-wp'
								)}
								rows={5}
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			<InspectorControls group="color">
				<ColorGradientSettingsDropdown
					panelId={clientId}
					title={__('Form Colors', 'airo-wp')}
					settings={[
						{
							label: __('Label Color', 'airo-wp'),
							colorValue: decodeColorValue(
								fieldLabelColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									fieldLabelColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Border Color', 'airo-wp'),
							colorValue: decodeColorValue(
								fieldBorderColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									fieldBorderColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Field Background', 'airo-wp'),
							colorValue: decodeColorValue(
								fieldBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									fieldBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Button Text Color', 'airo-wp'),
							colorValue: decodeColorValue(
								submitButtonColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									submitButtonColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Button Background Color', 'airo-wp'),
							colorValue: decodeColorValue(
								submitButtonBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									submitButtonBackgroundColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __('Button Hover Text Color', 'airo-wp'),
							colorValue: decodeColorValue(
								submitButtonHoverColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									submitButtonHoverColor:
										encodeColorValue(
											color,
											colorGradientSettings
										) || '',
								}),
							enableAlpha: true,
							clearable: true,
						},
						{
							label: __(
								'Button Hover Background Color',
								'airo-wp'
							),
							colorValue: decodeColorValue(
								submitButtonHoverBackgroundColor,
								colorGradientSettings
							),
							onColorChange: (color) =>
								setAttributes({
									submitButtonHoverBackgroundColor:
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

			<div {...blockProps}>
				<div {...innerBlocksPropsWithoutChildren}>
					{children}
					{submitButtonPosition === 'inline' && (
						<button
							type="button"
							className={`airo-wp-form__submit airo-wp-form__submit--inline wp-element-button${submitAnimationClass}`}
							disabled
							style={submitButtonStyle}
						>
							{submitButtonText}
						</button>
					)}
				</div>

				{submitButtonPosition === 'below' && (
					<div className="airo-wp-form__footer">
						<button
							type="button"
							className={`airo-wp-form__submit wp-element-button${submitAnimationClass}`}
							disabled
							style={submitButtonStyle}
						>
							{submitButtonText}
						</button>
					</div>
				)}
			</div>
		</>
	);
}
