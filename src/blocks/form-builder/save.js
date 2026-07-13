/**
 * Form Builder Block - Save Component
 *
 * @since 1.0.0
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import { validateCSSLength } from '../../utils/css-generator';

export default function FormBuilderSave({ attributes }) {
	const {
		formId,
		hasFields,
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
		enableTurnstile,
		redirectUrl,
	} = attributes;

	// If the author never picked a template (so the form has no fields),
	// render nothing instead of a lonely submit button.
	if (!hasFields) {
		return null;
	}

	// Same classes as edit.js - MUST MATCH
	const formClasses = classnames('airo-wp-form-builder', {
		[`airo-wp-form-builder--align-${submitButtonAlignment}`]:
			submitButtonAlignment && submitButtonPosition === 'below',
		'airo-wp-form-builder--button-inline': submitButtonPosition === 'inline',
	});

	// Apply form settings as CSS custom properties - MUST MATCH edit.js.
	// Spacing/sizing tokens are only written when the author set an explicit
	// value; when omitted they fall back to the theme.json custom properties
	// (--wp--custom--airo-wp--form--*) defined in style.scss, so a pattern
	// can drop them to inherit the theme.
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

	// Submit button style - MUST MATCH edit.js. Sizing (height/padding/font) is
	// only written when explicitly set; otherwise the button inherits the
	// theme's global button styles via the wp-element-button class.
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

	const blockProps = useBlockProps.save({
		className: formClasses,
		style: formStyles,
		'data-form-id': formId,
		'data-ajax-submit': ajaxSubmit,
		'data-success-message': successMessage,
		'data-error-message': errorMessage,
		'data-submit-text': submitButtonText,
		...(enableTurnstile && {
			'data-airo-wp-turnstile': 'true',
		}),
		...(redirectUrl && {
			'data-redirect-url': redirectUrl,
		}),
	});

	// Extract children from innerBlocksProps so we can add button inside fields container
	const { children, ...innerBlocksPropsWithoutChildren } =
		useInnerBlocksProps.save({
			className: 'airo-wp-form__fields',
		});

	return (
		<div {...blockProps}>
			<form className="airo-wp-form" method="post" noValidate>
				<div {...innerBlocksPropsWithoutChildren}>
					{children}
					{submitButtonPosition === 'inline' && (
						<button
							type="submit"
							className="airo-wp-form__submit airo-wp-form__submit--inline wp-element-button"
							style={submitButtonStyle}
						>
							{submitButtonText}
						</button>
					)}
				</div>

				{enableHoneypot && (
					<input
						type="text"
						name="dsg_website"
						value=""
						tabIndex="-1"
						autoComplete="off"
						aria-hidden="true"
						style={{
							position: 'absolute',
							left: '-9999px',
							width: '1px',
							height: '1px',
							overflow: 'hidden',
						}}
					/>
				)}

				<input type="hidden" name="dsg_form_id" value={formId} />

				{/* Timestamp added via JavaScript in view.js to avoid validation errors */}

				{/* Turnstile widget container - rendered by JS */}
				{enableTurnstile && (
					<div
						className="airo-wp-turnstile-widget"
						data-airo-wp-turnstile-container="true"
					/>
				)}

				{submitButtonPosition === 'below' && (
					<div className="airo-wp-form__footer">
						<button
							type="submit"
							className="airo-wp-form__submit wp-element-button"
							style={submitButtonStyle}
						>
							{submitButtonText}
						</button>
					</div>
				)}

				<div
					className="airo-wp-form__message"
					role="status"
					aria-live="polite"
					aria-atomic="true"
					style={{ display: 'none' }}
				/>
			</form>
		</div>
	);
}
