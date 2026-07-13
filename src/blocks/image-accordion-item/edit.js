import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	BlockControls,
	MediaReplaceFlow,
} from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { SelectControl, ToolbarGroup } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import classnames from 'classnames';

export default function ImageAccordionItemEdit({
	attributes,
	setAttributes,
	context,
	clientId,
	isSelected,
}) {
	const { uniqueId, verticalAlignment, horizontalAlignment, style } =
		attributes;

	// Expand when this item (or any of its inner blocks) is selected so the
	// inline toolbar and content editing stay stable — hover-based expansion
	// collapsed the item when the cursor moved to the toolbar.
	const hasChildSelected = useSelect(
		(select) =>
			select('core/block-editor').hasSelectedInnerBlock(clientId, true),
		[clientId]
	);
	const isExpanded = isSelected || hasChildSelected;

	const backgroundImage = style?.background?.backgroundImage;
	const mediaUrl = backgroundImage?.url || '';
	const mediaId = backgroundImage?.id;

	const onSelectMedia = (media) => {
		if (!media?.url) {
			return;
		}
		setAttributes({
			style: {
				...(style || {}),
				background: {
					...(style?.background || {}),
					backgroundImage: {
						url: media.url,
						id: media.id,
						source: 'file',
					},
				},
			},
		});
	};

	const onRemoveMedia = () => {
		setAttributes({
			style: {
				...(style || {}),
				background: {
					...(style?.background || {}),
					backgroundImage: undefined,
				},
			},
		});
	};

	// Only the on/off state is read from context here; it toggles the
	// `--has-overlay` class. The scrim's color/opacity are NOT re-emitted on the
	// item — they inherit down the DOM from the parent accordion's
	// `--airo-wp-image-accordion-overlay-*` custom properties (set inline by the
	// parent's edit.js when explicit) and fall through to the theme token /
	// literal in style.scss. Re-defaulting them here (e.g. `|| 40`) would pin a
	// value that outranks the theme token and desync the editor from the frontend.
	const enableOverlay =
		context['airo-wp/imageAccordion/enableOverlay'] !== undefined
			? context['airo-wp/imageAccordion/enableOverlay']
			: true;

	// Generate unique ID for accessibility
	useEffect(() => {
		if (!uniqueId) {
			setAttributes({
				uniqueId: `image-accordion-item-${Math.random().toString(36).substr(2, 9)}`,
			});
		}
	}, [uniqueId, setAttributes]);

	// Declaratively calculate classes
	const itemClasses = classnames('airo-wp-image-accordion-item', {
		'airo-wp-image-accordion-item--has-overlay': enableOverlay,
		'is-expanded': isExpanded,
	});

	// Only alignment is written inline; the overlay scrim inherits from the
	// parent accordion's custom properties (see the enableOverlay comment above).
	const blockProps = useBlockProps({
		className: itemClasses,
		style: {
			'--airo-wp-vertical-alignment': verticalAlignment,
			'--airo-wp-horizontal-alignment': horizontalAlignment,
		},
	});

	// Inner blocks for item content
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-image-accordion-item__content',
		},
		{
			template: [
				[
					'core/heading',
					{
						level: 2,
						placeholder: __('Add title…', 'airo-wp'),
					},
				],
				[
					'core/paragraph',
					{
						placeholder: __('Add description…', 'airo-wp'),
					},
				],
			],
		}
	);

	return (
		<>
			<BlockControls group="other">
				<ToolbarGroup>
					<MediaReplaceFlow
						mediaId={mediaId}
						mediaURL={mediaUrl}
						allowedTypes={['image']}
						accept="image/*"
						onSelect={onSelectMedia}
						onReset={mediaUrl ? onRemoveMedia : undefined}
						name={
							mediaUrl
								? __('Replace', 'airo-wp')
								: __('Add image', 'airo-wp')
						}
					/>
				</ToolbarGroup>
			</BlockControls>

			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							verticalAlignment: 'center',
							horizontalAlignment: 'center',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Vertical Alignment', 'airo-wp')}
						hasValue={() => verticalAlignment !== 'center'}
						onDeselect={() =>
							setAttributes({ verticalAlignment: 'center' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Vertical Alignment', 'airo-wp')}
							value={verticalAlignment}
							options={[
								{
									label: __('Top', 'airo-wp'),
									value: 'flex-start',
								},
								{
									label: __('Center', 'airo-wp'),
									value: 'center',
								},
								{
									label: __('Bottom', 'airo-wp'),
									value: 'flex-end',
								},
							]}
							onChange={(value) =>
								setAttributes({ verticalAlignment: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Horizontal Alignment', 'airo-wp')}
						hasValue={() => horizontalAlignment !== 'center'}
						onDeselect={() =>
							setAttributes({ horizontalAlignment: 'center' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Horizontal Alignment', 'airo-wp')}
							value={horizontalAlignment}
							options={[
								{
									label: __('Left', 'airo-wp'),
									value: 'flex-start',
								},
								{
									label: __('Center', 'airo-wp'),
									value: 'center',
								},
								{
									label: __('Right', 'airo-wp'),
									value: 'flex-end',
								},
							]}
							onChange={(value) =>
								setAttributes({ horizontalAlignment: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				<div {...innerBlocksProps} />
			</div>
		</>
	);
}
