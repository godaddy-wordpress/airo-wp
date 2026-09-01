/**
 * Draft Mode Status Views
 *
 * Different view states for the Draft Mode panel based on draft status.
 *
 * @package
 * @since 1.4.0
 */

import { __ } from '@wordpress/i18n';
import { Notice, Button, ExternalLink } from '@wordpress/components';

/**
 * View when editing a draft of a published page
 *
 * @param {Object}   props           Component props.
 * @param {Object}   props.status    Draft status data.
 * @param {Function} props.onPublish Callback to publish draft.
 * @param {Function} props.onDiscard Callback to discard draft.
 * @param {boolean}  props.isLoading Whether an action is loading.
 */
export function IsDraftView({ status, onPublish, onDiscard, isLoading }) {
	return (
		<div className="airo-wp-draft-mode-panel__content airo-wp-draft-mode-panel__content--is-draft">
			<Notice
				status="warning"
				isDismissible={false}
				className="airo-wp-draft-mode-panel__notice"
			>
				{__('You are editing a draft version.', 'airo-wp')}
			</Notice>

			<p className="airo-wp-draft-mode-panel__description">
				{__(
					"Changes here won't affect the live page until you publish them.",
					'airo-wp'
				)}
			</p>

			{status.original_view_url && (
				<p className="airo-wp-draft-mode-panel__link">
					<ExternalLink href={status.original_view_url}>
						{__('View live page', 'airo-wp')}
					</ExternalLink>
				</p>
			)}

			{/* Action buttons */}
			<div className="airo-wp-draft-mode-panel__actions">
				<Button
					variant="primary"
					onClick={onPublish}
					disabled={isLoading}
					isBusy={isLoading}
				>
					{__('Publish Changes', 'airo-wp')}
				</Button>

				<Button
					variant="secondary"
					isDestructive
					onClick={onDiscard}
					disabled={isLoading}
				>
					{__('Discard Draft', 'airo-wp')}
				</Button>
			</div>
		</div>
	);
}

/**
 * View when published page has a pending draft
 *
 * @param {Object} props        Component props.
 * @param {Object} props.status Draft status data.
 */
export function HasDraftView({ status }) {
	return (
		<div className="airo-wp-draft-mode-panel__content airo-wp-draft-mode-panel__content--has-draft">
			<Notice
				status="info"
				isDismissible={false}
				className="airo-wp-draft-mode-panel__notice"
			>
				{__('A draft version exists for this page.', 'airo-wp')}
			</Notice>

			<p className="airo-wp-draft-mode-panel__description">
				{__(
					'Edit the draft to make changes without affecting the live page.',
					'airo-wp'
				)}
			</p>

			{status.draft_created && (
				<p className="airo-wp-draft-mode-panel__meta">
					{__('Created:', 'airo-wp')} {status.draft_created}
				</p>
			)}

			{status.draft_edit_url && (
				<div className="airo-wp-draft-mode-panel__actions">
					<Button variant="primary" href={status.draft_edit_url}>
						{__('Edit Draft', 'airo-wp')}
					</Button>
				</div>
			)}
		</div>
	);
}

/**
 * View when published page can create a draft
 *
 * @param {Object}   props          Component props.
 * @param {Function} props.onCreate Callback to create draft.
 */
export function CanCreateDraftView({ onCreate }) {
	return (
		<div className="airo-wp-draft-mode-panel__content airo-wp-draft-mode-panel__content--can-create">
			<p className="airo-wp-draft-mode-panel__description">
				{__(
					'Create a draft to make changes without affecting the live page.',
					'airo-wp'
				)}
			</p>

			<div className="airo-wp-draft-mode-panel__actions">
				<Button variant="primary" onClick={onCreate}>
					{__('Create Draft', 'airo-wp')}
				</Button>
			</div>
		</div>
	);
}

/**
 * View when draft mode is not available (not a published page)
 */
export function UnavailableView() {
	return (
		<div className="airo-wp-draft-mode-panel__content airo-wp-draft-mode-panel__content--unavailable">
			<p className="airo-wp-draft-mode-panel__description airo-wp-draft-mode-panel__description--muted">
				{__(
					'Draft mode is only available for published pages.',
					'airo-wp'
				)}
			</p>
		</div>
	);
}
