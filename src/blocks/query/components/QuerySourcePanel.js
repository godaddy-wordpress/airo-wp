/* eslint-disable @wordpress/no-unsafe-wp-apis -- experimental layout/control primitives intentionally used; stable replacements not yet available */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import {
	SelectControl,
	RangeControl,
	TextControl,
	__experimentalNumberControl as NumberControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';
import TemplateIO from './TemplateIO';

export const GROUP_BY_OPTIONS = [
	{ value: 'none', label: __('None', 'airo-wp') },
	{ value: 'taxonomy', label: __('Taxonomy', 'airo-wp') },
	{ value: 'meta', label: __('Meta field', 'airo-wp') },
	{ value: 'date', label: __('Date', 'airo-wp') },
];

export const DATE_PRECISION_OPTIONS = [
	{ value: 'Y', label: __('Year', 'airo-wp') },
	{ value: 'Y-M', label: __('Year + Month', 'airo-wp') },
	{ value: 'Y-M-D', label: __('Year + Month + Day', 'airo-wp') },
];

const SOURCES = [
	{ value: 'posts', label: __('Posts', 'airo-wp') },
	{ value: 'users', label: __('Users', 'airo-wp') },
	{ value: 'terms', label: __('Terms', 'airo-wp') },
	{ value: 'manual', label: __('Manual picks', 'airo-wp') },
	{ value: 'current', label: __('Current archive', 'airo-wp') },
	{
		value: 'relationship',
		label: __('Related items (field-driven)', 'airo-wp'),
	},
];

const RELATIONSHIP_FALLBACK_OPTIONS = [
	{ value: 'empty', label: __('Render no items', 'airo-wp') },
	{ value: 'all', label: __('Fall back to all posts', 'airo-wp') },
	{ value: 'parent', label: __('Render the parent item', 'airo-wp') },
];

const ORDER_BY_OPTIONS = [
	{ value: 'date', label: __('Date', 'airo-wp') },
	{ value: 'title', label: __('Title', 'airo-wp') },
	{ value: 'menu_order', label: __('Menu order', 'airo-wp') },
	{ value: 'rand', label: __('Random', 'airo-wp') },
	{ value: 'comment_count', label: __('Comment count', 'airo-wp') },
	{ value: 'meta_value', label: __('Meta value (text)', 'airo-wp') },
	{
		value: 'meta_value_num',
		label: __('Meta value (numeric)', 'airo-wp'),
	},
];

const ORDER_OPTIONS = [
	{ value: 'DESC', label: __('Descending', 'airo-wp') },
	{ value: 'ASC', label: __('Ascending', 'airo-wp') },
];

export default function QuerySourcePanel({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		source,
		postType,
		perPage,
		offset,
		orderBy,
		orderByMetaKey,
		order,
		relationshipField,
		relationshipFallback,
	} = attributes;

	const postTypes = useSelect(
		(select) => select(coreStore).getPostTypes({ per_page: -1 }) || [],
		[]
	);

	const postTypeOptions = (postTypes || [])
		.filter((pt) => pt && pt.viewable)
		.map((pt) => ({
			label: pt.labels?.singular_name || pt.slug,
			value: pt.slug,
		}));

	const showPostType = source === 'posts';
	const showRelationship = source === 'relationship';
	const showMetaKey = ['meta_value', 'meta_value_num'].includes(orderBy);

	return (
		<DsgoInspectorPanel
			title={__('Settings', 'airo-wp')}
			panelName="settings"
			panelId={clientId}
			resetAll={() =>
				setAttributes({
					source: 'posts',
					postType: 'post',
					perPage: 6,
					offset: 0,
					orderBy: 'date',
					orderByMetaKey: '',
					order: 'DESC',
				})
			}
		>
			<DsgoInspectorPanel.Item
				label={__('Source', 'airo-wp')}
				hasValue={() => source !== 'posts'}
				onDeselect={() => setAttributes({ source: 'posts' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Source', 'airo-wp')}
					value={source}
					options={SOURCES}
					onChange={(value) => setAttributes({ source: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{showPostType && (
				<DsgoInspectorPanel.Item
					label={__('Post type', 'airo-wp')}
					hasValue={() => postType !== 'post'}
					onDeselect={() => setAttributes({ postType: 'post' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Post type', 'airo-wp')}
						value={postType}
						options={postTypeOptions}
						onChange={(value) => setAttributes({ postType: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{showRelationship && (
				<DsgoInspectorPanel.Item
					label={__('Relationship field', 'airo-wp')}
					hasValue={() => (relationshipField || '') !== ''}
					onDeselect={() => setAttributes({ relationshipField: '' })}
					isShownByDefault
				>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('Relationship field', 'airo-wp')}
						help={__(
							'Meta key or ACF field on the parent item that holds the related post IDs.',
							'airo-wp'
						)}
						value={relationshipField || ''}
						onChange={(v) =>
							setAttributes({ relationshipField: v })
						}
					/>
				</DsgoInspectorPanel.Item>
			)}

			{showRelationship && (
				<DsgoInspectorPanel.Item
					label={__('When no related items', 'airo-wp')}
					hasValue={() =>
						(relationshipFallback || 'empty') !== 'empty'
					}
					onDeselect={() =>
						setAttributes({ relationshipFallback: 'empty' })
					}
					isShownByDefault
				>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={__('When no related items', 'airo-wp')}
						value={relationshipFallback || 'empty'}
						onChange={(v) =>
							setAttributes({ relationshipFallback: v })
						}
						options={RELATIONSHIP_FALLBACK_OPTIONS}
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Items per page', 'airo-wp')}
				hasValue={() => perPage !== 6}
				onDeselect={() => setAttributes({ perPage: 6 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Items per page', 'airo-wp')}
					value={perPage}
					min={1}
					max={48}
					onChange={(value) => setAttributes({ perPage: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Offset', 'airo-wp')}
				hasValue={() => offset !== 0}
				onDeselect={() => setAttributes({ offset: 0 })}
				isShownByDefault
			>
				<NumberControl
					label={__('Offset', 'airo-wp')}
					value={offset}
					min={0}
					onChange={(value) =>
						setAttributes({ offset: Number(value) || 0 })
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Order by', 'airo-wp')}
				hasValue={() => orderBy !== 'date'}
				onDeselect={() => setAttributes({ orderBy: 'date' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Order by', 'airo-wp')}
					value={orderBy}
					options={ORDER_BY_OPTIONS}
					onChange={(value) => setAttributes({ orderBy: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{showMetaKey && (
				<DsgoInspectorPanel.Item
					label={__('Order by meta key', 'airo-wp')}
					hasValue={() => orderByMetaKey !== ''}
					onDeselect={() => setAttributes({ orderByMetaKey: '' })}
					isShownByDefault
				>
					<TextControl
						label={__('Meta key', 'airo-wp')}
						value={orderByMetaKey}
						onChange={(value) =>
							setAttributes({
								orderByMetaKey: String(value || ''),
							})
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Order direction', 'airo-wp')}
				hasValue={() => order !== 'DESC'}
				onDeselect={() => setAttributes({ order: 'DESC' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Order direction', 'airo-wp')}
					value={order}
					options={ORDER_OPTIONS}
					onChange={(value) => setAttributes({ order: value })}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Template I/O', 'airo-wp')}
				hasValue={() => false}
				onDeselect={() => {}}
				isShownByDefault
			>
				<TemplateIO clientId={clientId} attributes={attributes} />
			</DsgoInspectorPanel.Item>
		</DsgoInspectorPanel>
	);
}
