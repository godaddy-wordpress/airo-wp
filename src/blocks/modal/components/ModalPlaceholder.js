/**
 * Modal Placeholder Component
 *
 * Thin wrapper around the shared DsgoBlockPlaceholder primitive that supplies
 * the modal-specific copy and starter templates.
 */

import { __ } from '@wordpress/i18n';
import DsgoBlockPlaceholder from '../../../components/shared/DsgoBlockPlaceholder';
import { modalTemplates } from '../templates';

export default function ModalPlaceholder({ clientId, setAttributes }) {
	return (
		<DsgoBlockPlaceholder
			clientId={clientId}
			setAttributes={setAttributes}
			icon="feedback"
			label={__('Modal', 'airo-wp')}
			instructions={__(
				'Choose a template to get started, or start with a blank modal.',
				'airo-wp'
			)}
			templates={modalTemplates}
			variant="modal"
		/>
	);
}
