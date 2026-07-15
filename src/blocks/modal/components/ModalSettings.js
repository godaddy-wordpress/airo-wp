/**
 * Modal Settings Panel Component
 *
 * Renders a fragment of DsgoInspectorPanel.Item entries for the modal's
 * core sizing attributes. Meant to be dropped inside the Settings
 * DsgoInspectorPanel in modal/edit.js.
 *
 * @package
 */
/* eslint-disable no-nested-ternary */
import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	TextControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export default function ModalSettings({ attributes, setAttributes }) {
	const { modalId, modalLabel, width, maxWidth, height, maxHeight } =
		attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Accessible Label', 'airo-wp')}
				hasValue={() => modalLabel.trim() !== ''}
				onDeselect={() => setAttributes({ modalLabel: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('Accessible Label', 'airo-wp')}
					value={modalLabel}
					onChange={(value) => setAttributes({ modalLabel: value })}
					placeholder={__('Modal', 'airo-wp')}
					help={__(
						'Describes the modal for screen readers (aria-label). Defaults to "Modal" when left blank.',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Modal ID', 'airo-wp')}
				hasValue={() =>
					modalId !== '' && !/^airo-wp-modal-[a-z0-9]+$/i.test(modalId)
				}
				onDeselect={() => setAttributes({ modalId: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('Modal ID', 'airo-wp')}
					value={modalId}
					onChange={(value) => {
						// Sanitize to valid HTML ID format
						// Only allow alphanumeric, hyphens, and underscores
						const sanitized = value
							.toLowerCase()
							.replace(/[^a-z0-9-_]/gi, '-')
							.replace(/^-+|-+$/g, '') // Remove leading/trailing hyphens
							.replace(/-{2,}/g, '-'); // Replace multiple hyphens with single

						// Ensure it starts with airo-wp-modal- prefix
						const finalId = sanitized.startsWith('airo-wp-modal-')
							? sanitized
							: sanitized
								? `airo-wp-modal-${sanitized}`
								: 'airo-wp-modal-';

						setAttributes({ modalId: finalId });
					}}
					help={__(
						'Unique identifier for this modal. Only letters, numbers, hyphens, and underscores allowed.',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Width', 'airo-wp')}
				hasValue={() => width !== '600px'}
				onDeselect={() => setAttributes({ width: '600px' })}
				isShownByDefault
			>
				<UnitControl
					label={__('Width', 'airo-wp')}
					value={width}
					onChange={(value) => setAttributes({ width: value })}
					units={[
						{ value: 'px', label: 'px' },
						{ value: '%', label: '%' },
						{ value: 'vw', label: 'vw' },
					]}
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Max Width', 'airo-wp')}
				hasValue={() => maxWidth !== '90vw'}
				onDeselect={() => setAttributes({ maxWidth: '90vw' })}
				isShownByDefault
			>
				<UnitControl
					label={__('Max Width', 'airo-wp')}
					value={maxWidth}
					onChange={(value) => setAttributes({ maxWidth: value })}
					units={[
						{ value: 'px', label: 'px' },
						{ value: '%', label: '%' },
						{ value: 'vw', label: 'vw' },
					]}
					__next40pxDefaultSize
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Height', 'airo-wp')}
				hasValue={() => height !== 'auto'}
				onDeselect={() =>
					setAttributes({ height: 'auto', maxHeight: '90vh' })
				}
				isShownByDefault
			>
				<SelectControl
					label={__('Height', 'airo-wp')}
					value={height}
					onChange={(value) => setAttributes({ height: value })}
					options={[
						{ label: __('Auto', 'airo-wp'), value: 'auto' },
						{ label: __('Custom', 'airo-wp'), value: 'custom' },
					]}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{height !== 'auto' && (
				<DsgoInspectorPanel.Item
					label={__('Max Height', 'airo-wp')}
					hasValue={() => maxHeight !== '90vh'}
					onDeselect={() => setAttributes({ maxHeight: '90vh' })}
					isShownByDefault
				>
					<UnitControl
						label={__('Max Height', 'airo-wp')}
						value={maxHeight}
						onChange={(value) =>
							setAttributes({ maxHeight: value })
						}
						units={[
							{ value: 'px', label: 'px' },
							{ value: 'vh', label: 'vh' },
						]}
						__next40pxDefaultSize
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
}
