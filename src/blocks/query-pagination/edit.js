import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import InfiniteScrollControls from './components/InfiniteScrollControls';

/**
 * Canvas preview for the pagination block in the editor.
 * Extracted to avoid nested ternary expressions (no-nested-ternary rule).
 *
 * @param {Object}  root0
 * @param {string}  root0.effectiveKind         The resolved pagination kind.
 * @param {boolean} root0.showPrevNext          Whether to show prev/next arrows.
 * @param {string}  root0.labelLoadMore         Label for the load-more button.
 * @param {string}  root0.buttonLabelWhenPaused Label for the infinite-scroll pause button.
 */
function PaginationPreview({
	effectiveKind,
	showPrevNext,
	labelLoadMore,
	buttonLabelWhenPaused,
}) {
	if (effectiveKind === 'infinite') {
		return (
			<div className="airo-wp-query-pagination--infinite is-editor-preview">
				<button
					type="button"
					className="airo-wp-query-pagination__loadmore wp-element-button"
					disabled
				>
					{buttonLabelWhenPaused || __('Load more', 'airo-wp')}
				</button>
				<div
					className="airo-wp-query-pagination__sentinel"
					aria-hidden="true"
				/>
			</div>
		);
	}
	if (effectiveKind === 'loadmore') {
		return (
			<button
				type="button"
				className="airo-wp-query-pagination__loadmore"
				disabled
			>
				{labelLoadMore || __('Load more', 'airo-wp')}
			</button>
		);
	}
	return (
		<span className="airo-wp-query-pagination__preview">
			{showPrevNext ? '\u2190 ' : ''}1 2 3{showPrevNext ? ' \u2192' : ''}
		</span>
	);
}

const DEFAULTS = {
	mode: 'numbered',
	paginationKind: 'numbered',
	labelLoadMore: '',
	labelLoading: '',
	showPrevNext: true,
	autoPauseAfter: 3,
	sentinelOffsetPx: 200,
	buttonLabelWhenPaused: 'Load more',
	alignment: 'left',
};

const ALIGNMENT_OPTIONS = [
	{ value: 'left', label: __('Left', 'airo-wp') },
	{ value: 'center', label: __('Center', 'airo-wp') },
	{ value: 'right', label: __('Right', 'airo-wp') },
];

export default function QueryPaginationEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		mode,
		paginationKind,
		labelLoadMore,
		labelLoading,
		showPrevNext,
		buttonLabelWhenPaused,
		alignment,
	} = attributes;

	// Determine the effective kind: paginationKind takes precedence when set
	// to a non-default value; fall back to mode for backwards compatibility.
	const effectiveKind = paginationKind !== 'numbered' ? paginationKind : mode;

	const blockProps = useBlockProps({
		className: `airo-wp-query-pagination is-editor is-align-${
			alignment || 'left'
		}`,
	});

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() => setAttributes(DEFAULTS)}
				>
					<DsgoInspectorPanel.Item
						label={__('Mode', 'airo-wp')}
						hasValue={() => mode !== 'numbered'}
						onDeselect={() => setAttributes({ mode: 'numbered' })}
						isShownByDefault
					>
						<SelectControl
							label={__('Mode', 'airo-wp')}
							value={mode}
							options={[
								{
									value: 'numbered',
									label: __('Numbered', 'airo-wp'),
								},
								{
									value: 'loadmore',
									label: __('Load more', 'airo-wp'),
								},
							]}
							onChange={(v) => setAttributes({ mode: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{mode === 'numbered' && paginationKind !== 'infinite' && (
						<DsgoInspectorPanel.Item
							label={__('Show prev/next arrows', 'airo-wp')}
							hasValue={() => showPrevNext !== true}
							onDeselect={() =>
								setAttributes({ showPrevNext: true })
							}
						>
							<ToggleControl
								label={__('Show prev/next arrows', 'airo-wp')}
								checked={!!showPrevNext}
								onChange={(v) =>
									setAttributes({ showPrevNext: !!v })
								}
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{mode === 'loadmore' && paginationKind !== 'infinite' && (
						<>
							<DsgoInspectorPanel.Item
								label={__('Load more label', 'airo-wp')}
								hasValue={() => labelLoadMore !== ''}
								onDeselect={() =>
									setAttributes({ labelLoadMore: '' })
								}
								isShownByDefault
							>
								<TextControl
									label={__(
										'Load more button label',
										'airo-wp'
									)}
									value={labelLoadMore}
									onChange={(v) =>
										setAttributes({
											labelLoadMore: v,
										})
									}
									placeholder={__('Load more', 'airo-wp')}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							</DsgoInspectorPanel.Item>
							<DsgoInspectorPanel.Item
								label={__('Loading label', 'airo-wp')}
								hasValue={() => labelLoading !== ''}
								onDeselect={() =>
									setAttributes({ labelLoading: '' })
								}
							>
								<TextControl
									label={__('Loading state label', 'airo-wp')}
									value={labelLoading}
									onChange={(v) =>
										setAttributes({
											labelLoading: v,
										})
									}
									placeholder={__('Loading\u2026', 'airo-wp')}
									__next40pxDefaultSize
									__nextHasNoMarginBottom
								/>
							</DsgoInspectorPanel.Item>
						</>
					)}

					{paginationKind === 'infinite' && (
						<InfiniteScrollControls
							attributes={attributes}
							setAttributes={setAttributes}
							panelId={clientId}
						/>
					)}

					<DsgoInspectorPanel.Item
						label={__('Alignment', 'airo-wp')}
						hasValue={() => (alignment || 'left') !== 'left'}
						onDeselect={() => setAttributes({ alignment: 'left' })}
						isShownByDefault
					>
						<SelectControl
							label={__('Alignment', 'airo-wp')}
							value={alignment || 'left'}
							options={ALIGNMENT_OPTIONS}
							onChange={(v) => setAttributes({ alignment: v })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				<PaginationPreview
					effectiveKind={effectiveKind}
					showPrevNext={showPrevNext}
					labelLoadMore={labelLoadMore}
					buttonLabelWhenPaused={buttonLabelWhenPaused}
				/>
			</div>
		</>
	);
}
