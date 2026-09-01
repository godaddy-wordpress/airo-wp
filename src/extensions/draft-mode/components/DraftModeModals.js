/**
 * Draft Mode Confirmation Modals
 *
 * Reusable confirmation modals for draft operations.
 *
 * @package
 * @since 1.4.0
 */

import { __ } from '@wordpress/i18n';
import { Modal, Flex, FlexItem, Button } from '@wordpress/components';

/**
 * Publish Draft Confirmation Modal
 *
 * @param {Object}   props           Component props.
 * @param {boolean}  props.isOpen    Whether the modal is open.
 * @param {Function} props.onConfirm Callback when user confirms.
 * @param {Function} props.onCancel  Callback when user cancels.
 */
export function PublishConfirmModal({ isOpen, onConfirm, onCancel }) {
	if (!isOpen) {
		return null;
	}

	return (
		<Modal
			title={__('Publish Changes?', 'airo-wp')}
			onRequestClose={onCancel}
			size="small"
		>
			<Flex direction="column" gap={4}>
				<FlexItem>
					<p style={{ margin: 0 }}>
						{__(
							'This will replace the current live content with your draft changes.',
							'airo-wp'
						)}
					</p>
				</FlexItem>
				<Flex justify="flex-end" gap={3}>
					<FlexItem>
						<Button variant="tertiary" onClick={onCancel}>
							{__('Cancel', 'airo-wp')}
						</Button>
					</FlexItem>
					<FlexItem>
						<Button variant="primary" onClick={onConfirm}>
							{__('Publish', 'airo-wp')}
						</Button>
					</FlexItem>
				</Flex>
			</Flex>
		</Modal>
	);
}

/**
 * Discard Draft Confirmation Modal
 *
 * @param {Object}   props           Component props.
 * @param {boolean}  props.isOpen    Whether the modal is open.
 * @param {Function} props.onConfirm Callback when user confirms.
 * @param {Function} props.onCancel  Callback when user cancels.
 */
export function DiscardConfirmModal({ isOpen, onConfirm, onCancel }) {
	if (!isOpen) {
		return null;
	}

	return (
		<Modal
			title={__('Discard Draft?', 'airo-wp')}
			onRequestClose={onCancel}
			size="small"
		>
			<Flex direction="column" gap={4}>
				<FlexItem>
					<p style={{ margin: 0 }}>
						{__(
							'All changes will be lost and cannot be recovered. The live page will remain unchanged.',
							'airo-wp'
						)}
					</p>
				</FlexItem>
				<Flex justify="flex-end" gap={3}>
					<FlexItem>
						<Button variant="tertiary" onClick={onCancel}>
							{__('Cancel', 'airo-wp')}
						</Button>
					</FlexItem>
					<FlexItem>
						<Button
							variant="primary"
							isDestructive
							onClick={onConfirm}
						>
							{__('Discard', 'airo-wp')}
						</Button>
					</FlexItem>
				</Flex>
			</Flex>
		</Modal>
	);
}

/**
 * Create Draft Confirmation Modal
 *
 * @param {Object}   props           Component props.
 * @param {boolean}  props.isOpen    Whether the modal is open.
 * @param {Function} props.onConfirm Callback when user confirms.
 * @param {Function} props.onCancel  Callback when user cancels.
 */
export function CreateConfirmModal({ isOpen, onConfirm, onCancel }) {
	if (!isOpen) {
		return null;
	}

	return (
		<Modal
			title={__('Create Draft?', 'airo-wp')}
			onRequestClose={onCancel}
			size="small"
		>
			<Flex direction="column" gap={4}>
				<FlexItem>
					<p style={{ margin: 0 }}>
						{__(
							'This will create a draft version of this page with your current edits. The live page will remain unchanged.',
							'airo-wp'
						)}
					</p>
				</FlexItem>
				<Flex justify="flex-end" gap={3}>
					<FlexItem>
						<Button variant="tertiary" onClick={onCancel}>
							{__('Cancel', 'airo-wp')}
						</Button>
					</FlexItem>
					<FlexItem>
						<Button variant="primary" onClick={onConfirm}>
							{__('Create Draft', 'airo-wp')}
						</Button>
					</FlexItem>
				</Flex>
			</Flex>
		</Modal>
	);
}
