import { __ } from '@wordpress/i18n';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import {
	Button,
	RangeControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared/DsgoInspectorPanel';

export default function HotspotInspector({
	attributes,
	setAttributes,
	clientId,
}) {
	const {
		imageId,
		imageUrl,
		imageAlt,
		trigger,
		tooltipPosition,
		tooltipWidth,
		animation,
		sequenceDuration,
	} = attributes;
	const selectImage = (media) =>
		setAttributes({
			imageId: media.id,
			imageUrl: media.url,
			imageAlt: media.alt || '',
		});

	return (
		<>
			<DsgoInspectorPanel
				title={__('Settings', 'airo-wp')}
				panelName="settings"
				panelId={clientId}
				resetAll={() =>
					setAttributes({
						imageId: undefined,
						imageUrl: '',
						imageAlt: '',
						trigger: 'click',
						tooltipPosition: 'top',
						tooltipWidth: 240,
						animation: 'pulse',
						sequenceDuration: 0,
					})
				}
			>
				<DsgoInspectorPanel.Item
					label={__('Image', 'airo-wp')}
					hasValue={() => !!imageUrl}
					onDeselect={() =>
						setAttributes({
							imageId: undefined,
							imageUrl: '',
							imageAlt: '',
						})
					}
					isShownByDefault
				>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={selectImage}
							allowedTypes={['image']}
							value={imageId}
							render={({ open }) => (
								<Button variant="secondary" onClick={open}>
									{imageUrl
										? __('Replace image', 'airo-wp')
										: __('Select image', 'airo-wp')}
								</Button>
							)}
						/>
					</MediaUploadCheck>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Alternative text', 'airo-wp')}
					hasValue={() => !!imageAlt}
					onDeselect={() => setAttributes({ imageAlt: '' })}
					isShownByDefault
				>
					<TextControl
						label={__('Alternative text', 'airo-wp')}
						value={imageAlt}
						onChange={(value) => setAttributes({ imageAlt: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Trigger', 'airo-wp')}
					hasValue={() => trigger !== 'click'}
					onDeselect={() => setAttributes({ trigger: 'click' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Trigger', 'airo-wp')}
						value={trigger}
						options={[
							{
								label: __('Click', 'airo-wp'),
								value: 'click',
							},
							{
								label: __('Hover', 'airo-wp'),
								value: 'hover',
							},
						]}
						onChange={(value) => setAttributes({ trigger: value })}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Tooltip position', 'airo-wp')}
					hasValue={() => tooltipPosition !== 'top'}
					onDeselect={() => setAttributes({ tooltipPosition: 'top' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Tooltip position', 'airo-wp')}
						value={tooltipPosition}
						options={['top', 'right', 'bottom', 'left'].map(
							(value) => ({
								label: value[0].toUpperCase() + value.slice(1),
								value,
							})
						)}
						onChange={(value) =>
							setAttributes({ tooltipPosition: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Tooltip width', 'airo-wp')}
					hasValue={() => tooltipWidth !== 240}
					onDeselect={() => setAttributes({ tooltipWidth: 240 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Tooltip width', 'airo-wp')}
						value={tooltipWidth}
						onChange={(value) =>
							setAttributes({ tooltipWidth: value })
						}
						min={120}
						max={600}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			</DsgoInspectorPanel>
			<DsgoInspectorPanel
				title={__('Style', 'airo-wp')}
				panelName="style"
				panelId={clientId}
				resetAll={() =>
					setAttributes({
						animation: 'pulse',
						sequenceDuration: 0,
					})
				}
			>
				<DsgoInspectorPanel.Item
					label={__('Marker animation', 'airo-wp')}
					hasValue={() => animation !== 'pulse'}
					onDeselect={() => setAttributes({ animation: 'pulse' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Marker animation', 'airo-wp')}
						value={animation}
						options={[
							{
								label: __('Pulse', 'airo-wp'),
								value: 'pulse',
							},
							{
								label: __('Scale', 'airo-wp'),
								value: 'scale',
							},
							{
								label: __('Fade', 'airo-wp'),
								value: 'fade',
							},
							{ label: __('None', 'airo-wp'), value: 'none' },
						]}
						onChange={(value) =>
							setAttributes({ animation: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
				<DsgoInspectorPanel.Item
					label={__('Sequence duration', 'airo-wp')}
					hasValue={() => sequenceDuration !== 0}
					onDeselect={() => setAttributes({ sequenceDuration: 0 })}
					isShownByDefault
				>
					<RangeControl
						label={__('Sequence duration', 'airo-wp')}
						value={sequenceDuration}
						onChange={(value) =>
							setAttributes({ sequenceDuration: value })
						}
						min={0}
						max={3000}
						step={100}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			</DsgoInspectorPanel>
		</>
	);
}
