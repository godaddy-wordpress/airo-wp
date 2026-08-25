/**
 * Form Number Field Block - Edit Component
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	TextControl,
	ToggleControl,
	SelectControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis -- NumberControl is stable in practice
	__experimentalNumberControl as NumberControl,
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

export default function FormNumberFieldEdit({
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
		min,
		max,
		step,
		allowDecimals,
		fieldWidth,
	} = attributes;

	// Generate field name from clientId if empty
	useEffect(() => {
		if (!fieldName) {
			setAttributes({ fieldName: `number-${clientId.slice(0, 8)}` });
		}
	}, [fieldName, clientId, setAttributes]);

	// Get context values from parent form
	const fieldBackgroundColor = context['airo-wp/form/fieldBackgroundColor'];

	const fieldClasses = classnames(
		'airo-wp-form-field',
		'airo-wp-form-field--number'
	);

	const fieldStyles = {
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
							label: 'Number',
							placeholder: '',
							helpText: '',
							required: false,
							defaultValue: '',
							min: null,
							max: null,
							step: 1,
							allowDecimals: false,
							fieldWidth: '100',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Field Name', 'airo-wp')}
						hasValue={() =>
							!!fieldName &&
							!/^number-[a-z0-9]{1,8}$/i.test(fieldName)
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
						hasValue={() => label !== 'Number'}
						onDeselect={() => setAttributes({ label: 'Number' })}
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
						label={__('Minimum Value', 'airo-wp')}
						hasValue={() => min !== null}
						onDeselect={() => setAttributes({ min: null })}
						isShownByDefault
					>
						<NumberControl
							label={__('Minimum Value', 'airo-wp')}
							value={min !== null ? min : ''}
							onChange={(value) =>
								setAttributes({
									min:
										value !== '' ? parseFloat(value) : null,
								})
							}
							help={__(
								'Minimum allowed value (leave empty for no minimum)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Maximum Value', 'airo-wp')}
						hasValue={() => max !== null}
						onDeselect={() => setAttributes({ max: null })}
						isShownByDefault
					>
						<NumberControl
							label={__('Maximum Value', 'airo-wp')}
							value={max !== null ? max : ''}
							onChange={(value) =>
								setAttributes({
									max:
										value !== '' ? parseFloat(value) : null,
								})
							}
							help={__(
								'Maximum allowed value (leave empty for no maximum)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Allow Decimals', 'airo-wp')}
						hasValue={() => allowDecimals !== false}
						onDeselect={() =>
							setAttributes({ allowDecimals: false, step: 1 })
						}
						isShownByDefault
					>
						<ToggleControl
							label={__('Allow Decimals', 'airo-wp')}
							checked={allowDecimals}
							onChange={(value) => {
								setAttributes({ allowDecimals: value });
								if (!value && step < 1) {
									setAttributes({ step: 1 });
								}
							}}
							help={__(
								'Allow decimal numbers instead of integers only',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Step', 'airo-wp')}
						hasValue={() => step !== 1}
						onDeselect={() => setAttributes({ step: 1 })}
						isShownByDefault
					>
						<NumberControl
							label={__('Step', 'airo-wp')}
							value={step}
							onChange={(value) =>
								setAttributes({ step: parseFloat(value) || 1 })
							}
							help={__(
								'Increment/decrement step value',
								'airo-wp'
							)}
							min={0.01}
							__next40pxDefaultSize
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
							type="number"
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

				<input
					type="number"
					id={fieldId}
					className="airo-wp-form-field__input"
					placeholder={placeholder || undefined}
					defaultValue={defaultValue || undefined}
					min={min !== null ? min : undefined}
					max={max !== null ? max : undefined}
					step={allowDecimals ? step : 1}
					aria-describedby={helpText ? `${fieldId}-help` : undefined}
					disabled
				/>

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
