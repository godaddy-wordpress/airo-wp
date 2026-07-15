/**
 * Slider Placeholder
 *
 * Thin wrapper around the shared DsgoBlockPlaceholder primitive that supplies
 * the slider-specific copy and starter templates.
 */

import { __ } from '@wordpress/i18n';
import DsgoBlockPlaceholder from '../../../components/shared/DsgoBlockPlaceholder';
import sliderTemplates from '../templates';

export default function SliderPlaceholder({ clientId, setAttributes }) {
	return (
		<DsgoBlockPlaceholder
			clientId={clientId}
			setAttributes={setAttributes}
			icon="images-alt2"
			label={__('Slider', 'airo-wp')}
			instructions={__(
				'Pick a starting layout for your slider.',
				'airo-wp'
			)}
			templates={sliderTemplates}
			variant="slider"
		/>
	);
}
