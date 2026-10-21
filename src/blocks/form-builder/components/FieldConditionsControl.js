/**
 * Form Builder — "Show this field when…" rule editor.
 *
 * @since 2.10.0
 */

import { __, sprintf } from '@wordpress/i18n';
import {
	Button,
	Notice,
	SelectControl,
	TextControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

const VALUELESS = ['empty', 'not_empty'];

/**
 * @param {string} type Source field type.
 * @return {Array<{value: string, label: string}>} Operators for that type.
 */
export function opsForType(type) {
	if (type === 'checkbox') {
		return [
			{ value: 'not_empty', label: __('is checked', 'airo-wp') },
			{ value: 'empty', label: __('is not checked', 'airo-wp') },
		];
	}
	const is = [
		{ value: 'is', label: __('is', 'airo-wp') },
		{ value: 'is_not', label: __('is not', 'airo-wp') },
	];
	const empties = [
		{ value: 'empty', label: __('is empty', 'airo-wp') },
		{ value: 'not_empty', label: __('is not empty', 'airo-wp') },
	];
	if (type === 'select') {
		return [...is, ...empties];
	}
	if (['number', 'date', 'time'].includes(type)) {
		return [
			...is,
			{ value: 'gt', label: __('is greater than', 'airo-wp') },
			{ value: 'lt', label: __('is less than', 'airo-wp') },
			...empties,
		];
	}
	return [
		...is,
		{ value: 'contains', label: __('contains', 'airo-wp') },
		...empties,
	];
}

const INPUT_TYPE = { number: 'number', date: 'date', time: 'time' };

export default function FieldConditionsControl({
	value,
	onChange,
	fields,
	currentName,
}) {
	const operator = value?.operator === 'OR' ? 'OR' : 'AND';
	const rules = Array.isArray(value?.rules) ? value.rules : [];
	const sources = fields.filter(
		(field) => field.name && field.name !== currentName
	);

	const commit = (nextRules, nextOperator = operator) =>
		onChange(
			nextRules.length
				? { operator: nextOperator, rules: nextRules }
				: null
		);

	const updateRule = (index, patch) =>
		commit(
			rules.map((rule, i) => (i === index ? { ...rule, ...patch } : rule))
		);

	const changeSource = (index, fieldName) => {
		const source = sources.find((field) => field.name === fieldName);
		const ops = opsForType(source?.type).map((op) => op.value);
		const current = rules[index];
		// A value typed for one field rarely means anything for another.
		updateRule(index, {
			field: fieldName,
			op: ops.includes(current.op) ? current.op : ops[0],
			value: '',
		});
	};

	return (
		<div className="airo-wp-field-conditions">
			<p className="airo-wp-field-conditions__intro">
				{__('Show this field when:', 'airo-wp')}
			</p>
			{rules.length > 1 && (
				<ToggleGroupControl
					label={__('Match', 'airo-wp')}
					value={operator}
					onChange={(next) => commit(rules, next)}
					isBlock
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				>
					<ToggleGroupControlOption
						value="AND"
						label={__('All rules', 'airo-wp')}
					/>
					<ToggleGroupControlOption
						value="OR"
						label={__('Any rule', 'airo-wp')}
					/>
				</ToggleGroupControl>
			)}
			{rules.map((rule, index) => {
				const source = sources.find(
					(field) => field.name === rule.field
				);
				const ops = opsForType(source?.type);
				return (
					<div
						className="airo-wp-field-conditions__rule"
						key={index}
						role="group"
						aria-label={sprintf(
							/* translators: %d: rule number, starting at 1 */
							__('Rule %d', 'airo-wp'),
							index + 1
						)}
					>
						{rule.field && !source && (
							<Notice
								status="warning"
								isDismissible={false}
								spokenMessage={null}
							>
								{__(
									'This field no longer exists, so the rule is ignored.',
									'airo-wp'
								)}
							</Notice>
						)}
						<SelectControl
							label={__('Field', 'airo-wp')}
							value={rule.field}
							options={[
								{
									value: '',
									label: __('Select a field…', 'airo-wp'),
								},
								...sources.map((field) => ({
									value: field.name,
									label: field.label,
								})),
							]}
							onChange={(next) => changeSource(index, next)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<SelectControl
							label={__('Condition', 'airo-wp')}
							value={rule.op}
							options={ops}
							onChange={(op) =>
								updateRule(index, {
									op,
									value: VALUELESS.includes(op)
										? ''
										: rule.value,
								})
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						{!VALUELESS.includes(rule.op) &&
							source?.type === 'select' && (
								<SelectControl
									label={__('Value', 'airo-wp')}
									value={rule.value}
									options={[
										{
											value: '',
											label: __('Select…', 'airo-wp'),
										},
										...source.options.map((option) => ({
											value: option.value,
											label: option.label || option.value,
										})),
									]}
									onChange={(next) =>
										updateRule(index, { value: next })
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							)}
						{!VALUELESS.includes(rule.op) &&
							source?.type !== 'select' && (
								<TextControl
									label={__('Value', 'airo-wp')}
									type={INPUT_TYPE[source?.type] || 'text'}
									value={rule.value}
									onChange={(next) =>
										updateRule(index, { value: next })
									}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							)}
						<Button
							variant="tertiary"
							isDestructive
							onClick={() =>
								commit(rules.filter((_, i) => i !== index))
							}
							label={__('Remove rule', 'airo-wp')}
						>
							{__('Remove rule', 'airo-wp')}
						</Button>
					</div>
				);
			})}
			<Button
				variant="secondary"
				onClick={() =>
					commit([...rules, { field: '', op: 'is', value: '' }])
				}
				__next40pxDefaultSize
			>
				{__('Add rule', 'airo-wp')}
			</Button>
		</div>
	);
}
