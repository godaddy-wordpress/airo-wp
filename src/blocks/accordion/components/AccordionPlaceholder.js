/**
 * Accordion Placeholder
 *
 * Thin wrapper around the shared DsgoBlockPlaceholder primitive that supplies
 * the accordion-specific copy and starter templates.
 */

import { __ } from '@wordpress/i18n';
import DsgoBlockPlaceholder from '../../../components/shared/DsgoBlockPlaceholder';
import accordionTemplates from '../templates';

export default function AccordionPlaceholder({ clientId, setAttributes }) {
	return (
		<DsgoBlockPlaceholder
			clientId={clientId}
			setAttributes={setAttributes}
			icon="menu-alt"
			label={__('Accordion', 'airo-wp')}
			instructions={__(
				'Pick a starting layout for your accordion.',
				'airo-wp'
			)}
			templates={accordionTemplates}
			variant="accordion"
		/>
	);
}
