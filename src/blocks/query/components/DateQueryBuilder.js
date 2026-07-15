/* eslint-disable @wordpress/no-unsafe-wp-apis -- experimental layout/control primitives intentionally used; stable replacements not yet available */
import { __ } from '@wordpress/i18n';
import {
	Button,
	SelectControl,
	TextControl,
	ToggleControl,
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

const COLUMN_OPTIONS = [
	{ value: 'post_date', label: __('Post Date', 'airo-wp') },
	{ value: 'post_modified', label: __('Post Modified', 'airo-wp') },
	{ value: 'post_date_gmt', label: __('Post Date (GMT)', 'airo-wp') },
	{
		value: 'post_modified_gmt',
		label: __('Post Modified (GMT)', 'airo-wp'),
	},
];

const MODE_OPTIONS = [
	{ value: 'after', label: __('After', 'airo-wp') },
	{ value: 'before', label: __('Before', 'airo-wp') },
	{ value: 'between', label: __('Between', 'airo-wp') },
];

const EMPTY_DEFAULT = { relation: 'AND', clauses: [] };

const DEFAULT_CLAUSE = {
	column: 'post_date',
	mode: 'after',
	after: '',
	before: '',
	inclusive: true,
};

export default function DateQueryBuilder({
	attributes,
	setAttributes,
	clientId,
}) {
	// Normalise both `dateQuery` and `dateQuery.clauses` so a malformed import
	// (e.g. `{ relation: 'AND' }` with no clauses) doesn't crash the builder.
	const rawDateQuery = attributes.dateQuery ?? EMPTY_DEFAULT;
	const dateQuery = {
		relation: rawDateQuery.relation ?? 'AND',
		clauses: Array.isArray(rawDateQuery.clauses)
			? rawDateQuery.clauses
			: [],
	};

	const updateClause = (i, patch) => {
		const next = [...dateQuery.clauses];
		next[i] = { ...next[i], ...patch };
		setAttributes({ dateQuery: { ...dateQuery, clauses: next } });
	};

	const removeClause = (i) => {
		setAttributes({
			dateQuery: {
				...dateQuery,
				clauses: dateQuery.clauses.filter((_, idx) => idx !== i),
			},
		});
	};

	const addClause = () => {
		setAttributes({
			dateQuery: {
				...dateQuery,
				clauses: [...dateQuery.clauses, { ...DEFAULT_CLAUSE }],
			},
		});
	};

	return (
		<DsgoInspectorPanel
			title={__('Date filters', 'airo-wp')}
			panelName="settings"
			panelId={clientId}
			resetAll={() => setAttributes({ dateQuery: EMPTY_DEFAULT })}
		>
			<DsgoInspectorPanel.Item
				label={__('Date query', 'airo-wp')}
				hasValue={() => dateQuery.clauses.length > 0}
				onDeselect={() => setAttributes({ dateQuery: EMPTY_DEFAULT })}
				isShownByDefault
			>
				<VStack spacing={3}>
					{dateQuery.clauses.length > 1 && (
						<SelectControl
							label={__('Relation', 'airo-wp')}
							value={dateQuery.relation}
							options={[
								{ value: 'AND', label: 'AND' },
								{ value: 'OR', label: 'OR' },
							]}
							onChange={(v) =>
								setAttributes({
									dateQuery: { ...dateQuery, relation: v },
								})
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}

					{dateQuery.clauses.map((clause, i) => {
						const showAfter =
							clause.mode === 'after' ||
							clause.mode === 'between';
						const showBefore =
							clause.mode === 'before' ||
							clause.mode === 'between';
						return (
							<VStack key={i} spacing={2}>
								<HStack>
									<SelectControl
										label={__('Column', 'airo-wp')}
										value={clause.column}
										options={COLUMN_OPTIONS}
										onChange={(v) =>
											updateClause(i, { column: v })
										}
										__next40pxDefaultSize
										__nextHasNoMarginBottom
									/>
									<SelectControl
										label={__('Mode', 'airo-wp')}
										value={clause.mode}
										options={MODE_OPTIONS}
										onChange={(v) =>
											updateClause(i, { mode: v })
										}
										__next40pxDefaultSize
										__nextHasNoMarginBottom
									/>
								</HStack>
								{showAfter && (
									<TextControl
										label={__('After', 'airo-wp')}
										value={clause.after}
										placeholder="YYYY-MM-DD or -30 days"
										onChange={(v) =>
											updateClause(i, { after: v })
										}
										__next40pxDefaultSize
										__nextHasNoMarginBottom
									/>
								)}
								{showBefore && (
									<TextControl
										label={__('Before', 'airo-wp')}
										value={clause.before}
										placeholder="YYYY-MM-DD or today"
										onChange={(v) =>
											updateClause(i, { before: v })
										}
										__next40pxDefaultSize
										__nextHasNoMarginBottom
									/>
								)}
								<ToggleControl
									label={__('Inclusive', 'airo-wp')}
									checked={clause.inclusive}
									onChange={(v) =>
										updateClause(i, { inclusive: v })
									}
									__nextHasNoMarginBottom
								/>
								<Button
									isDestructive
									variant="tertiary"
									onClick={() => removeClause(i)}
									aria-label={__(
										'Remove date clause',
										'airo-wp'
									)}
									__next40pxDefaultSize
								>
									{__('Remove', 'airo-wp')}
								</Button>
							</VStack>
						);
					})}

					<Button
						variant="secondary"
						onClick={addClause}
						__next40pxDefaultSize
					>
						{__('Add date clause', 'airo-wp')}
					</Button>
				</VStack>
			</DsgoInspectorPanel.Item>
		</DsgoInspectorPanel>
	);
}
