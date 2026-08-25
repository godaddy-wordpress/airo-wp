/**
 * Draft Mode Header Control
 *
 * Displays header controls based on draft status.
 *
 * @package
 * @since 1.4.0
 */

import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

/**
 * Header control component
 *
 * @param {Object}   props                 Component props.
 * @param {Object}   props.status          Draft status data.
 * @param {string}   props.postStatus      Current post status.
 * @param {boolean}  props.hasEdits        Whether there are unsaved edits.
 * @param {boolean}  props.isSavingPost    Whether the post is currently saving.
 * @param {boolean}  props.isCreatingDraft Whether a draft is being created.
 * @param {Function} props.onSaveDraft     Callback to save draft.
 * @param {Function} props.onCreateDraft   Callback to create draft.
 */
export default function DraftModeHeaderControl({
	status,
	postStatus,
	hasEdits,
	isSavingPost,
	isCreatingDraft,
	onSaveDraft,
	onCreateDraft,
}) {
	if (status?.is_draft) {
		// State 1: This IS a draft - show "Save Draft" link when there are edits.
		if (hasEdits) {
			return (
				<Button
					variant="tertiary"
					onClick={onSaveDraft}
					disabled={isSavingPost}
					className="airo-wp-draft-mode-save-draft"
				>
					{isSavingPost
						? __('Saving…', 'airo-wp')
						: __('Save Draft', 'airo-wp')}
				</Button>
			);
		}
	} else if (postStatus === 'publish') {
		// State 2 or 3: Published page - show "Edit Draft" or "Create Draft".
		if (status?.has_draft && status?.draft_edit_url) {
			// Has existing draft - show "Edit Draft" link.
			return (
				<Button
					variant="tertiary"
					href={status.draft_edit_url}
					className="airo-wp-draft-mode-save-draft"
				>
					{__('Edit Draft', 'airo-wp')}
				</Button>
			);
		}
		// No draft - show "Create Draft" link.
		return (
			<Button
				variant="tertiary"
				onClick={onCreateDraft}
				disabled={isCreatingDraft}
				isBusy={isCreatingDraft}
				className="airo-wp-draft-mode-save-draft"
			>
				{isCreatingDraft
					? __('Creating…', 'airo-wp')
					: __('Create Draft', 'airo-wp')}
			</Button>
		);
	}

	return null;
}
