/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	SelectControl,
	Button,
	FocalPointPicker,
	TextControl,
} from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';

/**
 * Image Settings Panel Component
 *
 * @param {Object}   props               - Component props
 * @param {Object}   props.attributes    - Block attributes
 * @param {Function} props.setAttributes - Function to set attributes
 * @return {Element} Image settings panel
 */
export default function ImageSettingsPanel({ attributes, setAttributes }) {
	const {
		imageId,
		imageUrl,
		imageAlt,
		imageAspectRatio,
		imageCustomAspectRatio,
		imageObjectFit,
		imageFocalPoint,
		layoutPreset,
	} = attributes;

	// Don't show image settings for minimal layout
	if (layoutPreset === 'minimal') {
		return null;
	}

	const aspectRatioOptions = [
		{ label: __('16:9 (Landscape)', 'airo-wp'), value: '16-9' },
		{ label: __('4:3 (Standard)', 'airo-wp'), value: '4-3' },
		{ label: __('1:1 (Square)', 'airo-wp'), value: '1-1' },
		{ label: __('Original', 'airo-wp'), value: 'original' },
		{ label: __('Custom', 'airo-wp'), value: 'custom' },
	];

	const objectFitOptions = [
		{ label: __('Cover (Fill Space)', 'airo-wp'), value: 'cover' },
		{ label: __('Contain (Fit Inside)', 'airo-wp'), value: 'contain' },
		{ label: __('Fill (Stretch)', 'airo-wp'), value: 'fill' },
		{ label: __('Scale Down', 'airo-wp'), value: 'scale-down' },
	];

	return (
		<PanelBody title={__('Image', 'airo-wp')} initialOpen={true}>
			<MediaUploadCheck>
				<MediaUpload
					onSelect={(media) =>
						setAttributes({
							imageId: media.id,
							imageUrl: media.url,
							imageAlt: media.alt || '',
						})
					}
					allowedTypes={['image']}
					value={imageId}
					render={({ open }) => (
						<div className="airo-wp-card-image-upload">
							{imageUrl ? (
								<>
									<img
										src={imageUrl}
										alt={imageAlt}
										style={{
											width: '100%',
											height: 'auto',
											marginBottom: '12px',
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
										>
											{__('Replace Image', 'airo-wp')}
										</Button>
										<Button
											onClick={() =>
												setAttributes({
													imageId: 0,
													imageUrl: '',
													imageAlt: '',
												})
											}
											variant="secondary"
											isDestructive
										>
											{__('Remove', 'airo-wp')}
										</Button>
									</div>
								</>
							) : (
								<Button
									onClick={open}
									variant="primary"
									style={{ marginBottom: '12px' }}
								>
									{__('Select Image', 'airo-wp')}
								</Button>
							)}
						</div>
					)}
				/>
			</MediaUploadCheck>

			{imageUrl && (
				<>
					<TextControl
						label={__('Alt Text', 'airo-wp')}
						value={imageAlt}
						onChange={(value) => setAttributes({ imageAlt: value })}
						help={__(
							'Describe the image for screen readers and SEO.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<SelectControl
						label={__('Aspect Ratio', 'airo-wp')}
						value={imageAspectRatio}
						options={aspectRatioOptions}
						onChange={(value) =>
							setAttributes({ imageAspectRatio: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					{imageAspectRatio === 'custom' && (
						<TextControl
							label={__('Custom Aspect Ratio', 'airo-wp')}
							value={imageCustomAspectRatio}
							onChange={(value) =>
								setAttributes({ imageCustomAspectRatio: value })
							}
							placeholder="16 / 9"
							help={__(
								'Enter a CSS aspect-ratio value (e.g., "16 / 9" or "1.5").',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					)}

					<SelectControl
						label={__('Object Fit', 'airo-wp')}
						value={imageObjectFit}
						options={objectFitOptions}
						onChange={(value) =>
							setAttributes({ imageObjectFit: value })
						}
						help={__(
							'How the image should fit within its container.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					{imageObjectFit === 'cover' && (
						<FocalPointPicker
							label={__('Focal Point', 'airo-wp')}
							url={imageUrl}
							value={imageFocalPoint}
							onChange={(value) =>
								setAttributes({ imageFocalPoint: value })
							}
							help={__(
								'Click to adjust which part of the image is visible when cropped.',
								'airo-wp'
							)}
						/>
					)}
				</>
			)}
		</PanelBody>
	);
}
