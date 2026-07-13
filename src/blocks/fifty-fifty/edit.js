/**
 * Fifty Fifty Block - Edit Component
 *
 * Full-width 50/50 split with edge-to-edge media and constrained content.
 *
 * @since 1.5.0
 */

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	MediaReplaceFlow,
	BlockControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	SelectControl,
	FocalPointPicker,
	Button,
	TextControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseCustomUnits as useCustomUnits,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useSelect } from '@wordpress/data';
import { convertPresetToCSSVar } from '../../utils/convert-preset-to-css-var';

/**
 * Fifty Fifty Edit Component
 *
 * @param {Object}   props               Component props
 * @param {Object}   props.attributes    Block attributes
 * @param {Function} props.setAttributes Function to set attributes
 * @param {string}   props.clientId      Block client ID
 * @return {JSX.Element} Edit component
 */
export default function FiftyFiftyEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		mediaPosition,
		mediaId,
		mediaUrl,
		mediaAlt,
		focalPoint,
		minHeight,
		verticalAlignment,
		contentPadding,
	} = attributes;

	// Units for min height control
	const units = useCustomUnits({
		availableUnits: ['px', 'vh', 'vw', 'em', 'rem'],
	});

	// Check if block has inner blocks for appender logic
	const { hasInnerBlocks } = useSelect(
		(select) => {
			const { getBlock } = select(blockEditorStore);
			const block = getBlock(clientId);
			return {
				hasInnerBlocks: block?.innerBlocks?.length > 0,
			};
		},
		[clientId]
	);

	// Map verticalAlignment to CSS align-items
	const alignItemsMap = {
		top: 'flex-start',
		center: 'center',
		bottom: 'flex-end',
	};

	// Build block class name
	const blockClassName = [
		'airo-wp-fifty-fifty',
		`airo-wp-fifty-fifty--media-${mediaPosition}`,
	].join(' ');

	// Block wrapper props
	const blockProps = useBlockProps({
		className: blockClassName,
		style: {
			'--airo-wp-fifty-fifty-min-height': minHeight || undefined,
			'--airo-wp-fifty-fifty-content-justify':
				alignItemsMap[verticalAlignment] || 'center',
			'--airo-wp-fifty-fifty-content-padding':
				convertPresetToCSSVar(contentPadding) || undefined,
		},
	});

	// InnerBlocks props - placed in the content-inner wrapper
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-fifty-fifty__content-inner',
		},
		{
			template: [
				[
					'core/heading',
					{
						level: 2,
						placeholder: __('Add heading…', 'airo-wp'),
					},
				],
				[
					'core/paragraph',
					{
						placeholder: __('Add content…', 'airo-wp'),
					},
				],
			],
			templateLock: false,
			renderAppender: hasInnerBlocks
				? undefined
				: InnerBlocks.ButtonBlockAppender,
		}
	);

	// Media selection handler
	const onSelectMedia = (media) => {
		setAttributes({
			mediaId: media.id,
			mediaUrl: media.url,
			mediaAlt: media.alt || '',
		});
	};

	const onRemoveMedia = () => {
		setAttributes({
			mediaId: 0,
			mediaUrl: '',
			mediaAlt: '',
			focalPoint: { x: 0.5, y: 0.5 },
		});
	};

	// Focal point as object-position (coerce to Number to prevent CSS injection)
	const objectPosition = focalPoint
		? `${Number(focalPoint.x) * 100}% ${Number(focalPoint.y) * 100}%`
		: '50% 50%';

	return (
		<>
			{/* Toolbar: flip media side + replace media */}
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon="image-flip-horizontal"
						label={__('Flip Layout', 'airo-wp')}
						onClick={() =>
							setAttributes({
								mediaPosition:
									mediaPosition === 'left' ? 'right' : 'left',
							})
						}
					/>
				</ToolbarGroup>
				{mediaUrl && (
					<MediaReplaceFlow
						mediaId={mediaId}
						mediaURL={mediaUrl}
						allowedTypes={['image']}
						accept="image/*"
						onSelect={onSelectMedia}
						name={__('Replace Image', 'airo-wp')}
					/>
				)}
			</BlockControls>

			{/* Inspector Controls */}
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							mediaPosition: 'left',
							verticalAlignment: 'center',
							minHeight: '500px',
							mediaId: 0,
							mediaUrl: '',
							mediaAlt: '',
							focalPoint: { x: 0.5, y: 0.5 },
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Media Position', 'airo-wp')}
						hasValue={() => mediaPosition !== 'left'}
						onDeselect={() =>
							setAttributes({ mediaPosition: 'left' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Media Position', 'airo-wp')}
							value={mediaPosition}
							options={[
								{
									label: __('Left', 'airo-wp'),
									value: 'left',
								},
								{
									label: __('Right', 'airo-wp'),
									value: 'right',
								},
							]}
							onChange={(value) =>
								setAttributes({ mediaPosition: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Content Vertical Alignment', 'airo-wp')}
						hasValue={() => verticalAlignment !== 'center'}
						onDeselect={() =>
							setAttributes({ verticalAlignment: 'center' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__(
								'Content Vertical Alignment',
								'airo-wp'
							)}
							value={verticalAlignment}
							options={[
								{
									label: __('Top', 'airo-wp'),
									value: 'top',
								},
								{
									label: __('Center', 'airo-wp'),
									value: 'center',
								},
								{
									label: __('Bottom', 'airo-wp'),
									value: 'bottom',
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
						label={__('Min Height', 'airo-wp')}
						hasValue={() => minHeight !== '500px'}
						onDeselect={() => setAttributes({ minHeight: '500px' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Min Height', 'airo-wp')}
							value={minHeight}
							onChange={(value) =>
								setAttributes({ minHeight: value })
							}
							units={units}
							__next40pxDefaultSize
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Image', 'airo-wp')}
						hasValue={() => !!mediaUrl}
						onDeselect={() =>
							setAttributes({
								mediaId: 0,
								mediaUrl: '',
								mediaAlt: '',
								focalPoint: { x: 0.5, y: 0.5 },
							})
						}
						isShownByDefault
					>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectMedia}
								allowedTypes={['image']}
								value={mediaId}
								render={({ open }) => (
									<>
										{mediaUrl ? (
											<>
												<img
													src={mediaUrl}
													alt={mediaAlt}
													style={{
														width: '100%',
														height: 'auto',
														marginBottom: '8px',
														borderRadius: '4px',
													}}
												/>
												<div
													style={{
														display: 'flex',
														gap: '8px',
														marginBottom: '12px',
													}}
												>
													<Button
														onClick={open}
														variant="secondary"
														style={{ flex: 1 }}
													>
														{__(
															'Replace',
															'airo-wp'
														)}
													</Button>
													<Button
														onClick={onRemoveMedia}
														variant="secondary"
														isDestructive
													>
														{__(
															'Remove',
															'airo-wp'
														)}
													</Button>
												</div>
											</>
										) : (
											<Button
												onClick={open}
												variant="secondary"
												style={{
													width: '100%',
													justifyContent: 'center',
													marginBottom: '12px',
												}}
											>
												{__(
													'Select Image',
													'airo-wp'
												)}
											</Button>
										)}
									</>
								)}
							/>
						</MediaUploadCheck>
					</DsgoInspectorPanel.Item>

					{mediaUrl && (
						<DsgoInspectorPanel.Item
							label={__('Alt Text', 'airo-wp')}
							hasValue={() => mediaAlt !== ''}
							onDeselect={() => setAttributes({ mediaAlt: '' })}
							isShownByDefault
						>
							<TextControl
								label={__('Alt Text', 'airo-wp')}
								value={mediaAlt}
								onChange={(value) =>
									setAttributes({ mediaAlt: value })
								}
								help={__(
									'Describe the image for accessibility.',
									'airo-wp'
								)}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					{mediaUrl && (
						<DsgoInspectorPanel.Item
							label={__('Focal Point', 'airo-wp')}
							hasValue={() =>
								focalPoint?.x !== 0.5 || focalPoint?.y !== 0.5
							}
							onDeselect={() =>
								setAttributes({
									focalPoint: { x: 0.5, y: 0.5 },
								})
							}
							isShownByDefault
						>
							<FocalPointPicker
								__nextHasNoMarginBottom
								label={__('Focal Point', 'airo-wp')}
								url={mediaUrl}
								value={focalPoint}
								onChange={(value) =>
									setAttributes({ focalPoint: value })
								}
								help={__(
									'Click to adjust which part of the image stays visible.',
									'airo-wp'
								)}
							/>
						</DsgoInspectorPanel.Item>
					)}
				</DsgoInspectorPanel>
			</InspectorControls>

			{/* Block Output */}
			<div {...blockProps}>
				<div className="airo-wp-fifty-fifty__media">
					{mediaUrl ? (
						<img
							src={mediaUrl}
							alt={mediaAlt}
							style={{ objectPosition }}
						/>
					) : (
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectMedia}
								allowedTypes={['image']}
								value={mediaId}
								render={({ open }) => (
									<div
										className="airo-wp-fifty-fifty__media-placeholder"
										onClick={open}
										onKeyDown={(e) => {
											if (
												e.key === 'Enter' ||
												e.key === ' '
											) {
												e.preventDefault();
												open();
											}
										}}
										role="button"
										tabIndex={0}
										aria-label={__(
											'Select image',
											'airo-wp'
										)}
									>
										<span className="dashicons dashicons-format-image" />
										<span>
											{__('Select Image', 'airo-wp')}
										</span>
									</div>
								)}
							/>
						</MediaUploadCheck>
					)}
				</div>

				<div className="airo-wp-fifty-fifty__content">
					<div {...innerBlocksProps} />
				</div>
			</div>
		</>
	);
}
