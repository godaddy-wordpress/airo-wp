/**
 * Form Text Field Block - Editor Component
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	TextControl,
	TextareaControl,
	ToggleControl,
	RangeControl,
	SelectControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useEffect } from '@wordpress/element';
import classnames from 'classnames';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';

const FIELD_WIDTH_OPTIONS = [
	{ label: __('Full Width (100%)', 'airo-wp'), value: '100' },
	{ label: __('Half Width (50%)', 'airo-wp'), value: '50' },
	{ label: __('One Third (33%)', 'airo-wp'), value: '33' },
	{ label: __('Two Thirds (66%)', 'airo-wp'), value: '66' },
	{ label: __('One Quarter (25%)', 'airo-wp'), value: '25' },
	{ label: __('Three Quarters (75%)', 'airo-wp'), value: '75' },
];

export default function FormTextFieldEdit({
	attributes,
	setAttributes,
	clientId,
	context,
}) {
	const {
		fieldName,
		label,
		placeholder,
		helpText,
		required,
		defaultValue,
		minLength,
		maxLength,
		validation,
		validationPattern,
		validationMessage,
		fieldWidth,
	} = attributes;

	// Generate unique field name on mount if not set
	useEffect(() => {
		if (!fieldName) {
			setAttributes({ fieldName: `field_${clientId.substring(0, 8)}` });
		}
	}, [fieldName, clientId, setAttributes]);

	// Get context values from parent form
	const fieldLabelColor = context['airo-wp/form/fieldLabelColor'];
	const fieldBorderColor = context['airo-wp/form/fieldBorderColor'];
	const fieldBackgroundColor =
		context['airo-wp/form/fieldBackgroundColor'];

	const fieldClasses = classnames('airo-wp-form-field', 'airo-wp-form-field--text');

	const fieldStyles = {
		'--airo-wp-field-label-color': convertColorToCSSVar(fieldLabelColor),
		'--airo-wp-field-border-color': convertColorToCSSVar(fieldBorderColor),
		'--airo-wp-form-field-bg': convertColorToCSSVar(fieldBackgroundColor),
	};

	const blockProps = useBlockProps({
		className: fieldClasses,
		style: {
			...fieldStyles,
			// Use flex-basis with calc to account for gap between fields
			flexBasis:
				fieldWidth === '100'
					? '100%'
					: `calc(${fieldWidth}% - var(--airo-wp-form-field-spacing, 1.5rem) / 2)`,
			maxWidth:
				fieldWidth === '100'
					? '100%'
					: `calc(${fieldWidth}% - var(--airo-wp-form-field-spacing, 1.5rem) / 2)`,
		},
	});

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							fieldName: '',
							label: 'Text Field',
							placeholder: '',
							helpText: '',
							required: false,
							defaultValue: '',
							minLength: 0,
							maxLength: 0,
							validation: 'none',
							validationPattern: '',
							validationMessage: '',
							fieldWidth: '100',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Field Name', 'airo-wp')}
						hasValue={() =>
							!!fieldName &&
							!/^field_[a-z0-9]{1,8}$/i.test(fieldName)
						}
						onDeselect={() => setAttributes({ fieldName: '' })}
						isShownByDefault
					>
						<TextControl
							label={__('Field Name', 'airo-wp')}
							value={fieldName}
							onChange={(value) =>
								setAttributes({
									fieldName: value.replace(
										/[^a-z0-9_]/g,
										'_'
									),
								})
							}
							help={__(
								'Unique identifier for this field (letters, numbers, underscores)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Label', 'airo-wp')}
						hasValue={() => label !== 'Text Field'}
						onDeselect={() =>
							setAttributes({ label: 'Text Field' })
						}
						isShownByDefault
					>
						<TextControl
							label={__('Label', 'airo-wp')}
							value={label}
							onChange={(value) =>
								setAttributes({ label: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Required Field', 'airo-wp')}
						hasValue={() => required !== false}
						onDeselect={() => setAttributes({ required: false })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Required Field', 'airo-wp')}
							checked={required}
							onChange={(value) =>
								setAttributes({ required: value })
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Placeholder', 'airo-wp')}
						hasValue={() => placeholder !== ''}
						onDeselect={() => setAttributes({ placeholder: '' })}
						isShownByDefault
					>
						<TextControl
							label={__('Placeholder', 'airo-wp')}
							value={placeholder}
							onChange={(value) =>
								setAttributes({ placeholder: value })
							}
							help={__(
								'Hint text shown inside the field',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Help Text', 'airo-wp')}
						hasValue={() => helpText !== ''}
						onDeselect={() => setAttributes({ helpText: '' })}
						isShownByDefault
					>
						<TextareaControl
							label={__('Help Text', 'airo-wp')}
							value={helpText}
							onChange={(value) =>
								setAttributes({ helpText: value })
							}
							help={__(
								'Additional guidance shown below the field',
								'airo-wp'
							)}
							rows={2}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Default Value', 'airo-wp')}
						hasValue={() => defaultValue !== ''}
						onDeselect={() => setAttributes({ defaultValue: '' })}
						isShownByDefault
					>
						<TextControl
							label={__('Default Value', 'airo-wp')}
							value={defaultValue}
							onChange={(value) =>
								setAttributes({ defaultValue: value })
							}
							help={__(
								'Pre-fill this field with a default value',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Field Width', 'airo-wp')}
						hasValue={() => fieldWidth !== '100'}
						onDeselect={() => setAttributes({ fieldWidth: '100' })}
						isShownByDefault
					>
						<SelectControl
							label={__('Field Width', 'airo-wp')}
							value={fieldWidth}
							options={FIELD_WIDTH_OPTIONS}
							onChange={(value) =>
								setAttributes({ fieldWidth: value })
							}
							help={__(
								'Set field width to create multi-column layouts',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Minimum Length', 'airo-wp')}
						hasValue={() => minLength !== 0}
						onDeselect={() => setAttributes({ minLength: 0 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Minimum Length', 'airo-wp')}
							value={minLength}
							onChange={(value) =>
								setAttributes({ minLength: value })
							}
							min={0}
							max={500}
							help={__(
								'Minimum number of characters required (0 = no minimum)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Maximum Length', 'airo-wp')}
						hasValue={() => maxLength !== 0}
						onDeselect={() => setAttributes({ maxLength: 0 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Maximum Length', 'airo-wp')}
							value={maxLength}
							onChange={(value) =>
								setAttributes({ maxLength: value })
							}
							min={0}
							max={500}
							help={__(
								'Maximum number of characters allowed (0 = no maximum)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Validation Type', 'airo-wp')}
						hasValue={() => validation !== 'none'}
						onDeselect={() =>
							setAttributes({
								validation: 'none',
								validationPattern: '',
								validationMessage: '',
							})
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Validation Type', 'airo-wp')}
							value={validation}
							options={[
								{
									label: __('None', 'airo-wp'),
									value: 'none',
								},
								{
									label: __('Letters Only', 'airo-wp'),
									value: 'letters',
								},
								{
									label: __('Numbers Only', 'airo-wp'),
									value: 'numbers',
								},
								{
									label: __('Alphanumeric', 'airo-wp'),
									value: 'alphanumeric',
								},
								{
									label: __('Custom Pattern', 'airo-wp'),
									value: 'custom',
								},
							]}
							onChange={(value) =>
								setAttributes({ validation: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{validation === 'custom' && (
						<DsgoInspectorPanel.Item
							label={__('Custom Pattern (Regex)', 'airo-wp')}
							hasValue={() => validationPattern !== ''}
							onDeselect={() =>
								setAttributes({ validationPattern: '' })
							}
							isShownByDefault
						>
							<TextControl
								label={__(
									'Custom Pattern (Regex)',
									'airo-wp'
								)}
								value={validationPattern}
								onChange={(value) =>
									setAttributes({ validationPattern: value })
								}
								help={__(
									'Regular expression for validation',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{validation === 'custom' && (
						<DsgoInspectorPanel.Item
							label={__('Validation Message', 'airo-wp')}
							hasValue={() => validationMessage !== ''}
							onDeselect={() =>
								setAttributes({ validationMessage: '' })
							}
							isShownByDefault
						>
							<TextControl
								label={__('Validation Message', 'airo-wp')}
								value={validationMessage}
								onChange={(value) =>
									setAttributes({ validationMessage: value })
								}
								help={__(
									'Message shown when validation fails',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				<label
					htmlFor={`field-${clientId}`}
					className="airo-wp-form-field__label"
				>
					{label}
					{required && (
						<span className="airo-wp-form-field__required">*</span>
					)}
				</label>

				<input
					type="text"
					id={`field-${clientId}`}
					className="airo-wp-form-field__input"
					placeholder={placeholder}
					disabled
				/>

				{helpText && (
					<p className="airo-wp-form-field__help">{helpText}</p>
				)}
			</div>
		</>
	);
}
