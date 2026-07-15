import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	RangeControl,
	SelectControl,
	ToggleControl,
	Button,
	Notice,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis -- no stable export in @wordpress/components
	__experimentalUnitControl as UnitControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis -- no stable export in @wordpress/components
	__experimentalHStack as HStack,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { plus, close } from '@wordpress/icons';

export default function ScrollMarqueeEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		rows,
		scrollSpeed,
		imageHeight,
		imageWidth,
		objectFit,
		gap,
		rowGap,
	} = attributes;
	const borderRadius = attributes.style?.border?.radius;

	const imageInlineStyle = {
		height: imageHeight,
		width: imageWidth,
		objectFit,
		borderRadius,
	};

	// Performance: Calculate total images across all rows
	const totalImages = rows.reduce(
		(sum, row) => sum + (row.images?.length || 0),
		0
	);
	const showPerformanceWarning = totalImages > 20;

	const blockProps = useBlockProps({
		className: 'airo-wp-scroll-marquee',
		style: {
			'--airo-wp-marquee-gap': gap,
			'--airo-wp-marquee-row-gap': rowGap,
			'--airo-wp-marquee-image-height': imageHeight,
			'--airo-wp-marquee-image-width': imageWidth,
			'--airo-wp-marquee-object-fit': objectFit,
		},
	});

	const addRow = () => {
		const newRows = [
			...rows,
			{ images: [], direction: rows.length % 2 === 0 ? 'left' : 'right' },
		];
		setAttributes({ rows: newRows });
	};

	const removeRow = (rowIndex) => {
		const newRows = rows.filter((_, index) => index !== rowIndex);
		setAttributes({ rows: newRows });
	};

	const toggleRowDirection = (rowIndex) => {
		const newRows = [...rows];
		newRows[rowIndex].direction =
			newRows[rowIndex].direction === 'left' ? 'right' : 'left';
		setAttributes({ rows: newRows });
	};

	const onSelectImages = (rowIndex, images) => {
		const newRows = [...rows];
		// MediaUpload with 'value' prop pre-selects existing images,
		// so 'images' param contains ALL selected images (existing + new)
		// We just need to map them to our format
		newRows[rowIndex].images = images.map((img) => ({
			id: img.id,
			url: img.url,
			alt: img.alt || '',
		}));
		setAttributes({ rows: newRows });
	};

	const removeImage = (rowIndex, imageIndex) => {
		const newRows = [...rows];
		newRows[rowIndex].images = newRows[rowIndex].images.filter(
			(_, index) => index !== imageIndex
		);
		setAttributes({ rows: newRows });
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
							scrollSpeed: 0.5,
							imageHeight: '200px',
							imageWidth: 'auto',
							objectFit: 'cover',
							gap: '20px',
							rowGap: '20px',
						})
					}
				>
					{showPerformanceWarning ? (
						<Notice status="warning" isDismissible={false}>
							{__('You have', 'airo-wp')}
							<strong>{totalImages}</strong>
							{__(
								'images. For best performance, consider using fewer images (20 or less) or optimizing image sizes. Each image is duplicated 6 times for smooth infinite scrolling.',
								'airo-wp'
							)}
						</Notice>
					) : (
						<Notice status="success" isDismissible={false}>
							{__('Total images:', 'airo-wp')}
							<strong>{totalImages}</strong>
							{__(
								'(duplicated 6x for infinite scroll)',
								'airo-wp'
							)}
						</Notice>
					)}

					<DsgoInspectorPanel.Item
						label={__('Scroll Speed', 'airo-wp')}
						hasValue={() => scrollSpeed !== 0.5}
						onDeselect={() => setAttributes({ scrollSpeed: 0.5 })}
						isShownByDefault
					>
						<RangeControl
							label={__('Scroll Speed', 'airo-wp')}
							value={scrollSpeed}
							onChange={(value) =>
								setAttributes({ scrollSpeed: value })
							}
							min={0.1}
							max={2}
							step={0.1}
							help={__(
								'Controls how fast images move based on scroll',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Image Height', 'airo-wp')}
						hasValue={() => imageHeight !== '200px'}
						onDeselect={() =>
							setAttributes({ imageHeight: '200px' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Image Height', 'airo-wp')}
							value={imageHeight}
							onChange={(value) =>
								setAttributes({ imageHeight: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Image Width', 'airo-wp')}
						hasValue={() => imageWidth !== 'auto'}
						onDeselect={() => setAttributes({ imageWidth: 'auto' })}
						isShownByDefault
					>
						<ToggleControl
							label={__(
								'Auto width (from aspect ratio)',
								'airo-wp'
							)}
							checked={imageWidth === 'auto'}
							onChange={(isAuto) =>
								setAttributes({
									imageWidth: isAuto ? 'auto' : '300px',
								})
							}
							help={__(
								'Let each image keep its natural aspect ratio, sizing its width from the height.',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
						{imageWidth !== 'auto' && (
							<UnitControl
								label={__('Image Width', 'airo-wp')}
								value={imageWidth}
								onChange={(value) =>
									setAttributes({ imageWidth: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						)}
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Image Fit', 'airo-wp')}
						hasValue={() => objectFit !== 'cover'}
						onDeselect={() => setAttributes({ objectFit: 'cover' })}
						isShownByDefault
					>
						<SelectControl
							label={__('Image Fit', 'airo-wp')}
							value={objectFit}
							options={[
								{
									label: __('Cover', 'airo-wp'),
									value: 'cover',
								},
								{
									label: __('Contain', 'airo-wp'),
									value: 'contain',
								},
								{
									label: __('Fill', 'airo-wp'),
									value: 'fill',
								},
								{
									label: __('Scale down', 'airo-wp'),
									value: 'scale-down',
								},
							]}
							onChange={(value) =>
								setAttributes({ objectFit: value })
							}
							help={__(
								'How each image fills its box. Cover crops to fill; Contain fits the whole image and may letterbox.',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Gap Between Images', 'airo-wp')}
						hasValue={() => gap !== '20px'}
						onDeselect={() => setAttributes({ gap: '20px' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Gap Between Images', 'airo-wp')}
							value={gap}
							onChange={(value) => setAttributes({ gap: value })}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Gap Between Rows', 'airo-wp')}
						hasValue={() => rowGap !== '20px'}
						onDeselect={() => setAttributes({ rowGap: '20px' })}
						isShownByDefault
					>
						<UnitControl
							label={__('Gap Between Rows', 'airo-wp')}
							value={rowGap}
							onChange={(value) =>
								setAttributes({ rowGap: value })
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				{rows.map((row, rowIndex) => (
					<div
						key={rowIndex}
						className="airo-wp-scroll-marquee__row"
						data-direction={row.direction}
					>
						<div className="airo-wp-scroll-marquee__row-controls">
							<HStack justify="space-between" spacing={3}>
								<div className="airo-wp-scroll-marquee__direction-control">
									<Button
										variant="secondary"
										size="small"
										onClick={() =>
											toggleRowDirection(rowIndex)
										}
										style={{ minWidth: '120px' }}
									>
										{row.direction === 'left'
											? '← Scroll Left'
											: 'Scroll Right →'}
									</Button>
								</div>
								<Button
									icon={close}
									label={__('Remove Row', 'airo-wp')}
									onClick={() => removeRow(rowIndex)}
									isDestructive
									size="small"
								/>
							</HStack>
						</div>

						<div className="airo-wp-scroll-marquee__track">
							<div className="airo-wp-scroll-marquee__track-segment">
								{row.images.map((image, imageIndex) => (
									<div
										key={imageIndex}
										className="airo-wp-scroll-marquee__image-wrapper"
									>
										<img
											src={image.url}
											alt={image.alt}
											className="airo-wp-scroll-marquee__image"
											style={imageInlineStyle}
										/>
										<Button
											icon={close}
											label={__(
												'Remove Image',
												'airo-wp'
											)}
											onClick={() =>
												removeImage(
													rowIndex,
													imageIndex
												)
											}
											className="airo-wp-scroll-marquee__remove-image"
											isDestructive
											size="small"
										/>
									</div>
								))}

								<MediaUploadCheck>
									<MediaUpload
										onSelect={(images) =>
											onSelectImages(rowIndex, images)
										}
										allowedTypes={['image']}
										multiple={true}
										value={row.images.map((img) => img.id)}
										render={({ open }) => (
											<Button
												icon={plus}
												onClick={open}
												className="airo-wp-scroll-marquee__add-images"
												variant="secondary"
											>
												{row.images.length === 0
													? __(
															'Add Images',
															'airo-wp'
														)
													: __(
															'Add More Images',
															'airo-wp'
														)}
											</Button>
										)}
									/>
								</MediaUploadCheck>
							</div>
						</div>
					</div>
				))}

				<Button
					icon={plus}
					onClick={addRow}
					className="airo-wp-scroll-marquee__add-row"
					variant="primary"
				>
					{__('Add Row', 'airo-wp')}
				</Button>
			</div>
		</>
	);
}
