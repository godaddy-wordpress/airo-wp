/**
 * Counter Block - Label Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item for the counter label. Meant to be
 * composed inside the Settings DsgoInspectorPanel in counter/edit.js.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { TextControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

export const LabelSettingsPanel = ({ label, setAttributes }) => {
	return (
		<DsgoInspectorPanel.Item
			label={__('Label', 'airo-wp')}
			hasValue={() => label !== ''}
			onDeselect={() => setAttributes({ label: '' })}
			isShownByDefault
		>
			<TextControl
				label={__('Label', 'airo-wp')}
				value={label}
				onChange={(value) => setAttributes({ label: value })}
				placeholder={__('Enter label…', 'airo-wp')}
				help={__('Description text below counter', 'airo-wp')}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
		</DsgoInspectorPanel.Item>
	);
};
