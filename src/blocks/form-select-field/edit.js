/**
 * Form Select Field Block - Edit Component
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	TextControl,
	ToggleControl,
	SelectControl,
	Button,
	Flex,
	FlexItem,
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

const DEFAULT_OPTIONS = [
	{ label: 'Option 1', value: 'option-1' },
	{ label: 'Option 2', value: 'option-2' },
	{ label: 'Option 3', value: 'option-3' },
];
const DEFAULT_PLACEHOLDER = '-- Select an option --';

// Deep-equality check against DEFAULT_OPTIONS. Options is an array of
// {label, value} pairs; matches the block.json default exactly when the
// author hasn't touched the Options list.
const isDefaultOptions = (opts) =>
	Array.isArray(opts) &&
	opts.length === DEFAULT_OPTIONS.length &&
	opts.every(
		(opt, i) =>
			opt?.label === DEFAULT_OPTIONS[i].label &&
			opt?.value === DEFAULT_OPTIONS[i].value
	);

export default function FormSelectFieldEdit({
	attributes,
	setAttributes,
	clientId,
	context,
}) {
	const {
		fieldName,
		label,
		helpText,
		required,
		defaultValue,
		options,
		placeholder,
		fieldWidth,
	} = attributes;

	// Generate field name from clientId if empty
	useEffect(() => {
		if (!fieldName) {
			setAttributes({ fieldName: `select-${clientId.slice(0, 8)}` });
		}
	}, [fieldName, clientId, setAttributes]);

	// Get context values from parent form
	const fieldBackgroundColor =
		context['airo-wp/form/fieldBackgroundColor'];

	const fieldClasses = classnames(
		'airo-wp-form-field',
		'airo-wp-form-field--select'
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

	// Add new option
	const addOption = () => {
		const newOptions = [
			...options,
			{
				label: `Option ${options.length + 1}`,
				value: `option-${options.length + 1}`,
			},
		];
		setAttributes({ options: newOptions });
	};

	// Remove option
	const removeOption = (index) => {
		const newOptions = options.filter((_, i) => i !== index);
		setAttributes({ options: newOptions });
	};

	// Update option
	const updateOption = (index, field, value) => {
		const newOptions = options.map((option, i) => {
			if (i === index) {
				return { ...option, [field]: value };
			}
			return option;
		});
		setAttributes({ options: newOptions });
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
							label: 'Select Option',
							helpText: '',
							required: false,
							defaultValue: '',
							options: DEFAULT_OPTIONS,
							placeholder: DEFAULT_PLACEHOLDER,
							fieldWidth: '100',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Field Name', 'airo-wp')}
						hasValue={() =>
							!!fieldName &&
							!/^select-[a-z0-9]{1,8}$/i.test(fieldName)
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
						hasValue={() => label !== 'Select Option'}
						onDeselect={() =>
							setAttributes({ label: 'Select Option' })
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
						label={__('Options', 'airo-wp')}
						hasValue={() => !isDefaultOptions(options)}
						onDeselect={() =>
							setAttributes({ options: DEFAULT_OPTIONS })
						}
						isShownByDefault
					>
						{options.map((option, index) => (
							<Flex
								key={index}
								gap={2}
								style={{ marginBottom: '1rem' }}
							>
								<FlexItem isBlock>
									<TextControl
										label={__('Label', 'airo-wp')}
										value={option.label}
										onChange={(value) =>
											updateOption(index, 'label', value)
										}
										__next40pxDefaultSize
										__nextHasNoMarginBottom
									/>
								</FlexItem>
								<FlexItem isBlock>
									<TextControl
										label={__('Value', 'airo-wp')}
										value={option.value}
										onChange={(value) =>
											updateOption(index, 'value', value)
										}
										__next40pxDefaultSize
										__nextHasNoMarginBottom
									/>
								</FlexItem>
								<FlexItem style={{ alignSelf: 'flex-end' }}>
									<Button
										isDestructive
										icon="trash"
										label={__(
											'Remove option',
											'airo-wp'
										)}
										onClick={() => removeOption(index)}
										disabled={options.length === 1}
									/>
								</FlexItem>
							</Flex>
						))}

						<Button variant="secondary" onClick={addOption}>
							{__('Add Option', 'airo-wp')}
						</Button>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Placeholder', 'airo-wp')}
						hasValue={() => placeholder !== DEFAULT_PLACEHOLDER}
						onDeselect={() =>
							setAttributes({ placeholder: DEFAULT_PLACEHOLDER })
						}
						isShownByDefault
					>
						<TextControl
							label={__('Placeholder', 'airo-wp')}
							value={placeholder}
							onChange={(value) =>
								setAttributes({ placeholder: value })
							}
							help={__(
								'Text shown when no option is selected',
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
								'Pre-selected option value',
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

				<select
					id={fieldId}
					className="airo-wp-form-field__select"
					defaultValue={defaultValue || ''}
					aria-describedby={helpText ? `${fieldId}-help` : undefined}
					disabled
				>
					{placeholder && <option value="">{placeholder}</option>}
					{options.map((option, index) => (
						<option key={index} value={option.value}>
							{option.label}
						</option>
					))}
				</select>

				{helpText && (
					<p id={`${fieldId}-help`} className="airo-wp-form-field__help">
						{helpText}
					</p>
				)}
			</div>
		</>
	);
}
