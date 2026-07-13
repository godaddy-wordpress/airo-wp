import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

const ATTR_DEFAULTS = {
	search: '',
	bindSearchTo: '',
	excludeCurrent: false,
	ignoreSticky: true,
	manualIds: [],
};

export default function AdvancedPanel({ attributes, setAttributes, clientId }) {
	const {
		source,
		search,
		bindSearchTo,
		excludeCurrent,
		ignoreSticky,
		manualIds,
	} = attributes;

	const manualIdsAsText = Array.isArray(manualIds)
		? manualIds.join(', ')
		: '';

	return (
		<DsgoInspectorPanel
			title={__('Advanced query', 'airo-wp')}
			panelName="settings"
			panelId={clientId}
			resetAll={() => setAttributes(ATTR_DEFAULTS)}
		>
			<DsgoInspectorPanel.Item
				label={__('Search', 'airo-wp')}
				hasValue={() => search !== ''}
				onDeselect={() => setAttributes({ search: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('Search text', 'airo-wp')}
					help={__(
						'Limit results by keyword (like WP search).',
						'airo-wp'
					)}
					value={search}
					onChange={(v) => setAttributes({ search: v })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Bind search to URL param', 'airo-wp')}
				hasValue={() => bindSearchTo !== ''}
				onDeselect={() => setAttributes({ bindSearchTo: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('URL parameter name', 'airo-wp')}
					help={__(
						'Overrides the static search with ?<param>=\u2026 at render time. Leave blank to ignore.',
						'airo-wp'
					)}
					value={bindSearchTo}
					onChange={(v) => setAttributes({ bindSearchTo: v })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Exclude current post', 'airo-wp')}
				hasValue={() => excludeCurrent !== false}
				onDeselect={() => setAttributes({ excludeCurrent: false })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Exclude current post', 'airo-wp')}
					checked={!!excludeCurrent}
					onChange={(v) => setAttributes({ excludeCurrent: !!v })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Ignore sticky', 'airo-wp')}
				hasValue={() => ignoreSticky !== true}
				onDeselect={() => setAttributes({ ignoreSticky: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Ignore sticky posts', 'airo-wp')}
					checked={!!ignoreSticky}
					onChange={(v) => setAttributes({ ignoreSticky: !!v })}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{source === 'manual' && (
				<DsgoInspectorPanel.Item
					label={__('Manual IDs', 'airo-wp')}
					hasValue={() =>
						Array.isArray(manualIds) && manualIds.length > 0
					}
					onDeselect={() => setAttributes({ manualIds: [] })}
					isShownByDefault
				>
					<TextareaControl
						label={__(
							'Manual post IDs (comma-separated)',
							'airo-wp'
						)}
						value={manualIdsAsText}
						onChange={(v) => {
							const ids = String(v || '')
								.split(',')
								.map((s) => parseInt(s.trim(), 10))
								.filter((n) => Number.isInteger(n) && n > 0);
							setAttributes({ manualIds: ids });
						}}
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</DsgoInspectorPanel>
	);
}
