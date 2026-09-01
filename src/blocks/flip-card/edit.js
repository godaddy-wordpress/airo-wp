/**
 * Flip Card Block - Edit Component
 *
 * Interactive card that flips to reveal content on the back.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	SelectControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useSelect } from '@wordpress/data';
import FlipCardPlaceholder from './components/FlipCardPlaceholder';

/**
 * Flip Card Edit Component
 *
 * Uses a template with two airo-wp/flip-card-face children (one per
 * side). Each face can contain any blocks the author wants to add.
 *
 * @param {Object}   props               Component props
 * @param {Object}   props.attributes    Block attributes
 * @param {Function} props.setAttributes Function to update attributes
 * @param {string}   props.clientId      Block client ID
 * @return {JSX.Element} Edit component
 */
export default function FlipCardEdit({ attributes, setAttributes, clientId }) {
	const { flipTrigger, flipEffect, flipDirection, flipDuration } = attributes;

	// Cap child faces at two (one front + one back). airo-wp/flip-card-face
	// is the canonical child block; flip-card-front / flip-card-back are
	// legacy siblings kept for content already in the wild — they count
	// toward the same two-face budget. `hasInnerBlocks` gates the first-
	// insert placeholder (Theme 1).
	const { allowedBlocks, hasInnerBlocks } = useSelect(
		(select) => {
			const { getBlock } = select(blockEditorStore);
			const block = getBlock(clientId);
			const children = block?.innerBlocks || [];
			const legacyCount = children.filter(
				(child) =>
					child.name === 'airo-wp/flip-card-front' ||
					child.name === 'airo-wp/flip-card-back'
			).length;
			const faceCount = children.filter(
				(child) => child.name === 'airo-wp/flip-card-face'
			).length;
			// Empty array hides the inserter entirely — flip card is at capacity.
			const allowed =
				legacyCount + faceCount >= 2 ? [] : ['airo-wp/flip-card-face'];
			return {
				allowedBlocks: allowed,
				hasInnerBlocks: children.length > 0,
			};
		},
		[clientId]
	);

	// Block wrapper props
	const blockProps = useBlockProps({
		className: 'airo-wp-flip-card',
		style: {
			'--airo-wp-flip-duration': flipDuration,
			width: '100%',
		},
	});

	// Inner blocks configuration. Initial seeding is handled by
	// FlipCardPlaceholder so authors pick a starter layout instead of
	// landing on two empty faces. The `template` here is a safety net for
	// "Attempt Recovery" on validation errors — it fires only when there
	// are no children, which normally is caught by hasInnerBlocks above.
	// templateLock is false so authors can delete a face and re-add it.
	const innerBlocksProps = useInnerBlocksProps(
		{
			className: 'airo-wp-flip-card__container',
		},
		{
			allowedBlocks,
			template: [
				['airo-wp/flip-card-face', { side: 'front' }],
				['airo-wp/flip-card-face', { side: 'back' }],
			],
			templateLock: false,
			orientation: 'vertical',
		}
	);

	if (!hasInnerBlocks) {
		return (
			<div {...blockProps}>
				<FlipCardPlaceholder
					clientId={clientId}
					setAttributes={setAttributes}
				/>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							flipTrigger: 'hover',
							flipEffect: 'flip',
							flipDirection: 'horizontal',
							flipDuration: '0.6s',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Flip Trigger', 'airo-wp')}
						hasValue={() => flipTrigger !== 'hover'}
						onDeselect={() =>
							setAttributes({ flipTrigger: 'hover' })
						}
						isShownByDefault
					>
						<SelectControl
							label={__('Flip Trigger', 'airo-wp')}
							value={flipTrigger}
							options={[
								{
									label: __('Hover', 'airo-wp'),
									value: 'hover',
								},
								{
									label: __('Click', 'airo-wp'),
									value: 'click',
								},
							]}
							onChange={(value) =>
								setAttributes({ flipTrigger: value })
							}
							help={
								flipTrigger === 'hover'
									? __(
											'Card flips when hovering over it',
											'airo-wp'
										)
									: __(
											'Card flips when clicking on it',
											'airo-wp'
										)
							}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Flip Effect', 'airo-wp')}
						hasValue={() => flipEffect !== 'flip'}
						onDeselect={() => setAttributes({ flipEffect: 'flip' })}
						isShownByDefault
					>
						<SelectControl
							label={__('Flip Effect', 'airo-wp')}
							value={flipEffect}
							options={[
								{
									label: __('Flip (3D Rotation)', 'airo-wp'),
									value: 'flip',
								},
								{
									label: __('Fade', 'airo-wp'),
									value: 'fade',
								},
								{
									label: __('Slide', 'airo-wp'),
									value: 'slide',
								},
								{
									label: __('Zoom', 'airo-wp'),
									value: 'zoom',
								},
							]}
							onChange={(value) =>
								setAttributes({ flipEffect: value })
							}
							help={__(
								'Choose the transition animation style',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					{flipEffect === 'flip' && (
						<DsgoInspectorPanel.Item
							label={__('Flip Direction', 'airo-wp')}
							hasValue={() => flipDirection !== 'horizontal'}
							onDeselect={() =>
								setAttributes({ flipDirection: 'horizontal' })
							}
							isShownByDefault
						>
							<SelectControl
								label={__('Flip Direction', 'airo-wp')}
								value={flipDirection}
								options={[
									{
										label: __('Horizontal', 'airo-wp'),
										value: 'horizontal',
									},
									{
										label: __('Vertical', 'airo-wp'),
										value: 'vertical',
									},
								]}
								onChange={(value) =>
									setAttributes({ flipDirection: value })
								}
								help={
									flipDirection === 'horizontal'
										? __(
												'Card flips left to right',
												'airo-wp'
											)
										: __(
												'Card flips top to bottom',
												'airo-wp'
											)
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</DsgoInspectorPanel.Item>
					)}

					<DsgoInspectorPanel.Item
						label={__('Flip Duration', 'airo-wp')}
						hasValue={() => flipDuration !== '0.6s'}
						onDeselect={() =>
							setAttributes({ flipDuration: '0.6s' })
						}
						isShownByDefault
					>
						<UnitControl
							label={__('Flip Duration', 'airo-wp')}
							value={flipDuration}
							onChange={(value) =>
								setAttributes({ flipDuration: value || '0.6s' })
							}
							units={[
								{ value: 's', label: 's', default: 0.6 },
								{ value: 'ms', label: 'ms', default: 600 },
							]}
							min={0.1}
							max={3}
							step={0.1}
							help={__('Speed of the flip animation', 'airo-wp')}
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
