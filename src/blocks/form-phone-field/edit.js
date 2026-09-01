/**
 * Form Phone Field Block - Edit Component
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	TextControl,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useEffect } from '@wordpress/element';
import classnames from 'classnames';
import { convertColorToCSSVar } from '../../utils/convert-preset-to-css-var';
import COUNTRY_CODES from './country-codes';

const FIELD_WIDTH_OPTIONS = [
	{ label: __('Full Width (100%)', 'airo-wp'), value: '100' },
	{ label: __('Half Width (50%)', 'airo-wp'), value: '50' },
	{ label: __('One Third (33%)', 'airo-wp'), value: '33' },
	{ label: __('Two Thirds (66%)', 'airo-wp'), value: '66' },
	{ label: __('One Quarter (25%)', 'airo-wp'), value: '25' },
	{ label: __('Three Quarters (75%)', 'airo-wp'), value: '75' },
];

export default function FormPhoneFieldEdit({
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
		phoneFormat,
		showCountryCode,
		countryCode,
		autoFormat,
		fieldWidth,
	} = attributes;

	// Generate field name from clientId if empty
	useEffect(() => {
		if (!fieldName) {
			setAttributes({ fieldName: `phone-${clientId.slice(0, 8)}` });
		}
	}, [fieldName, clientId, setAttributes]);

	// Get context values from parent form
	const fieldLabelColor = context['airo-wp/form/fieldLabelColor'];
	const fieldBorderColor = context['airo-wp/form/fieldBorderColor'];
	const fieldBackgroundColor = context['airo-wp/form/fieldBackgroundColor'];

	const fieldClasses = classnames(
		'airo-wp-form-field',
		'airo-wp-form-field--phone'
	);

	const fieldStyles = {
		'--airo-wp-field-label-color': convertColorToCSSVar(fieldLabelColor),
		'--airo-wp-field-border-color': convertColorToCSSVar(fieldBorderColor),
		'--airo-wp-form-field-bg': convertColorToCSSVar(fieldBackgroundColor),
	};

	const blockProps = useBlockProps({
		className: fieldClasses,
		style: {
			...fieldStyles,
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

	const fieldId = `field-${fieldName}`;

	// Get pattern based on phone format
	const getPattern = () => {
		switch (phoneFormat) {
			case 'us':
				return '[0-9]{3}-[0-9]{3}-[0-9]{4}';
			case 'international':
				return '\\+[0-9]{1,3}[0-9\\s\\-]{4,14}';
			default:
				return undefined;
		}
	};

	// Get placeholder based on format
	const getPlaceholder = () => {
		if (placeholder) {
			return placeholder;
		}

		switch (phoneFormat) {
			case 'us':
				return '555-123-4567';
			case 'international':
				return '+1 555 123 4567';
			default:
				return '';
		}
	};

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
							label: 'Phone Number',
							placeholder: '',
							helpText: '',
							required: false,
							defaultValue: '',
							phoneFormat: 'any',
							showCountryCode: true,
							countryCode: '+1',
							autoFormat: true,
							fieldWidth: '100',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Field Name', 'airo-wp')}
						hasValue={() =>
							!!fieldName &&
							!/^phone-[a-z0-9]{1,8}$/i.test(fieldName)
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
										/[^a-z0-9_-]/gi,
										''
									),
								})
							}
							help={__(
								'Unique identifier for this field (letters, numbers, hyphens, underscores only)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Label', 'airo-wp')}
						hasValue={() => label !== 'Phone Number'}
						onDeselect={() =>
							setAttributes({ label: 'Phone Number' })
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
						label={__('Required', 'airo-wp')}
						hasValue={() => required !== false}
						onDeselect={() => setAttributes({ required: false })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Required', 'airo-wp')}
							checked={required}
							onChange={(value) =>
								setAttributes({ required: value })
							}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Phone Format', 'airo-wp')}
						hasValue={() => phoneFormat !== 'any'}
						onDeselect={() => setAttributes({ phoneFormat: 'any' })}
						isShownByDefault
					>
						<SelectControl
							label={__('Phone Format', 'airo-wp')}
							value={phoneFormat}
							options={[
								{
									label: __('Any Format', 'airo-wp'),
									value: 'any',
								},
								{
									label: __(
										'US Format (555–123–4567)',
										'airo-wp'
									),
									value: 'us',
								},
								{
									label: __(
										'International (+1 555 123 4567)',
										'airo-wp'
									),
									value: 'international',
								},
							]}
							onChange={(value) =>
								setAttributes({ phoneFormat: value })
							}
							help={__(
								'Choose how phone numbers should be formatted',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Show Country Code Selector', 'airo-wp')}
						hasValue={() => showCountryCode !== true}
						onDeselect={() =>
							setAttributes({ showCountryCode: true })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Show Country Code Selector', 'airo-wp')}
							checked={showCountryCode}
							onChange={(value) =>
								setAttributes({ showCountryCode: value })
							}
							help={__(
								'Display a dropdown for selecting country code',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{showCountryCode && (
						<DsgoInspectorPanel.Item
							label={__('Default Country Code', 'airo-wp')}
							hasValue={() => countryCode !== '+1'}
							onDeselect={() =>
								setAttributes({ countryCode: '+1' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Default Country Code', 'airo-wp')}
								value={countryCode}
								options={COUNTRY_CODES}
								onChange={(value) =>
									setAttributes({ countryCode: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Auto-Format Phone Number', 'airo-wp')}
						hasValue={() => autoFormat !== true}
						onDeselect={() => setAttributes({ autoFormat: true })}
						isShownByDefault
					>
						<ToggleControl
							label={__('Auto-Format Phone Number', 'airo-wp')}
							checked={autoFormat}
							onChange={(value) =>
								setAttributes({ autoFormat: value })
							}
							help={__(
								'Automatically format phone number as user types',
								'airo-wp'
							)}
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
								'Example text shown when field is empty',
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
						<TextControl
							label={__('Help Text', 'airo-wp')}
							value={helpText}
							onChange={(value) =>
								setAttributes({ helpText: value })
							}
							help={__(
								'Additional guidance shown below the field',
								'airo-wp'
							)}
							__next40pxDefaultSize
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
								'Pre-filled value for this field',
								'airo-wp'
							)}
							type="tel"
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
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				<label htmlFor={fieldId} className="airo-wp-form-field__label">
					{label}
					{required && (
						<span
							className="airo-wp-form-field__required"
							aria-label="required"
						>
							*
						</span>
					)}
				</label>

				<div
					className="airo-wp-form-field__phone-wrapper"
					style={{ display: 'flex', gap: '0.5rem' }}
				>
					{showCountryCode && (
						<select
							className="airo-wp-form-field__country-code"
							defaultValue={countryCode}
							disabled
							style={{ minWidth: '85px', flexShrink: 0 }}
						>
							{COUNTRY_CODES.map((code) => (
								<option key={code.value} value={code.value}>
									{code.value}
								</option>
							))}
						</select>
					)}
					<input
						type="tel"
						id={fieldId}
						className="airo-wp-form-field__input"
						placeholder={getPlaceholder()}
						defaultValue={defaultValue || undefined}
						pattern={getPattern()}
						aria-describedby={
							helpText ? `${fieldId}-help` : undefined
						}
						disabled
						style={{ flex: 1 }}
					/>
				</div>

				{helpText && (
					<p
						id={`${fieldId}-help`}
						className="airo-wp-form-field__help"
					>
						{helpText}
					</p>
				)}
			</div>
		</>
	);
}
