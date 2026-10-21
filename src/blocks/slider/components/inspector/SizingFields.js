/**
 * Slider — sizing fields.
 *
 * Height (fixed aspect ratio or minimum height) and the gap between slides.
 * Composed inside the Style DsgoInspectorPanel in StylePanel.js.
 */

import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	ToggleControl,
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

/**
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @return {JSX.Element} Sizing fields.
 */
export default function SizingFields({ attributes, setAttributes }) {
	const { useAspectRatio, aspectRatio, height, gap } = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Use Aspect Ratio', 'airo-wp')}
				hasValue={() => useAspectRatio !== false}
				onDeselect={() => setAttributes({ useAspectRatio: false })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Use Aspect Ratio', 'airo-wp')}
					checked={useAspectRatio}
					onChange={(value) =>
						setAttributes({ useAspectRatio: value })
					}
					help={
						useAspectRatio
							? __('Slider uses aspect ratio', 'airo-wp')
							: __(
									'Slider uses minimum height — content can grow taller',
									'airo-wp'
								)
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{useAspectRatio ? (
				<DsgoInspectorPanel.Item
					label={__('Aspect Ratio', 'airo-wp')}
					hasValue={() => aspectRatio !== '16/9'}
					onDeselect={() => setAttributes({ aspectRatio: '16/9' })}
					isShownByDefault
				>
					<SelectControl
						label={__('Aspect Ratio', 'airo-wp')}
						value={aspectRatio}
						options={[
							{ label: '16:9', value: '16/9' },
							{ label: '4:3', value: '4/3' },
							{ label: '21:9', value: '21/9' },
							{ label: '1:1', value: '1/1' },
							{ label: '3:2', value: '3/2' },
						]}
						onChange={(value) =>
							setAttributes({ aspectRatio: value })
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			) : (
				<DsgoInspectorPanel.Item
					label={__('Min Height', 'airo-wp')}
					hasValue={() => height !== ''}
					onDeselect={() => setAttributes({ height: '' })}
					isShownByDefault
				>
					<UnitControl
						label={__('Min Height', 'airo-wp')}
						value={height}
						onChange={(value) =>
							setAttributes({ height: value || '' })
						}
						units={[
							{ value: 'px', label: 'px', default: 500 },
							{ value: 'vh', label: 'vh', default: 50 },
							{ value: 'rem', label: 'rem', default: 30 },
						]}
						min={100}
						max={1000}
						help={
							!height
								? __(
										'No height set — slider fits its content',
										'airo-wp'
									)
								: __(
										'Slider will be at least this tall',
										'airo-wp'
									)
						}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Gap Between Slides', 'airo-wp')}
				hasValue={() => gap !== '20px'}
				onDeselect={() => setAttributes({ gap: '20px' })}
				isShownByDefault
			>
				<UnitControl
					label={__('Gap Between Slides', 'airo-wp')}
					value={gap}
					onChange={(value) =>
						setAttributes({ gap: value || '20px' })
					}
					units={[
						{ value: 'px', label: 'px', default: 20 },
						{ value: 'rem', label: 'rem', default: 1.25 },
					]}
					min={0}
					max={64}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}
