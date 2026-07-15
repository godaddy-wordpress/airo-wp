/**
 * Form Hidden Field Block - Edit Component
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { TextControl, Notice } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../components/shared';
import { useEffect } from '@wordpress/element';

export default function FormHiddenFieldEdit({
	attributes,
	setAttributes,
	clientId,
}) {
	const { fieldName, value } = attributes;

	// Generate field name from clientId if empty
	useEffect(() => {
		if (!fieldName) {
			setAttributes({ fieldName: `hidden-${clientId.slice(0, 8)}` });
		}
	}, [fieldName, clientId, setAttributes]);

	const blockProps = useBlockProps({
		className: 'airo-wp-form-field airo-wp-form-field--hidden',
	});

	return (
		<>
			<InspectorControls>
				<DsgoInspectorPanel
					title={__('Settings', 'airo-wp')}
					panelName="settings"
					panelId={clientId}
					resetAll={() =>
						setAttributes({
							fieldName: '',
							value: '',
						})
					}
				>
					<DsgoInspectorPanel.Item
						label={__('Field Name', 'airo-wp')}
						hasValue={() =>
							!!fieldName &&
							!/^hidden-[a-z0-9]{1,8}$/i.test(fieldName)
						}
						onDeselect={() => setAttributes({ fieldName: '' })}
						isShownByDefault
					>
						<TextControl
							label={__('Field Name', 'airo-wp')}
							value={fieldName}
							onChange={(newValue) =>
								setAttributes({
									fieldName: newValue.replace(
										/[^a-z0-9_-]/gi,
										''
									),
								})
							}
							help={__(
								'Unique identifier for this field (letters, numbers, hyphens, underscores only)',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>

					<DsgoInspectorPanel.Item
						label={__('Value', 'airo-wp')}
						hasValue={() => value !== ''}
						onDeselect={() => setAttributes({ value: '' })}
						isShownByDefault
					>
						<TextControl
							label={__('Value', 'airo-wp')}
							value={value}
							onChange={(newValue) =>
								setAttributes({ value: newValue })
							}
							help={__(
								'The hidden value to be submitted with the form',
								'airo-wp'
							)}
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
					</DsgoInspectorPanel.Item>
				</DsgoInspectorPanel>
			</InspectorControls>

			<div {...blockProps}>
				<Notice status="info" isDismissible={false}>
					<strong>{__('Hidden Field:', 'airo-wp')}</strong>{' '}
					{fieldName} = {value || __('(empty)', 'airo-wp')}
				</Notice>
			</div>
		</>
	);
}
