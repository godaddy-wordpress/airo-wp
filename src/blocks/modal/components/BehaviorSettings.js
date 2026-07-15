/**
 * Behavior Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for the modal's behaviour
 * toggles, meant to be composed inside the Settings panel in
 * modal/edit.js.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { ToggleControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../components/shared';

export default function BehaviorSettings({ attributes, setAttributes }) {
	const {
		closeOnBackdrop,
		closeOnEsc,
		disableBodyScroll,
		allowHashTrigger,
		updateUrlOnOpen,
	} = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Close on Backdrop Click', 'airo-wp')}
				hasValue={() => closeOnBackdrop !== true}
				onDeselect={() => setAttributes({ closeOnBackdrop: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Close on Backdrop Click', 'airo-wp')}
					checked={closeOnBackdrop}
					onChange={(value) =>
						setAttributes({ closeOnBackdrop: value })
					}
					help={__(
						'Allow closing the modal by clicking outside of it.',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Close on ESC Key', 'airo-wp')}
				hasValue={() => closeOnEsc !== true}
				onDeselect={() => setAttributes({ closeOnEsc: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Close on ESC Key', 'airo-wp')}
					checked={closeOnEsc}
					onChange={(value) => setAttributes({ closeOnEsc: value })}
					help={__(
						'Allow closing the modal with the Escape key.',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Disable Body Scroll', 'airo-wp')}
				hasValue={() => disableBodyScroll !== true}
				onDeselect={() => setAttributes({ disableBodyScroll: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Disable Body Scroll', 'airo-wp')}
					checked={disableBodyScroll}
					onChange={(value) =>
						setAttributes({ disableBodyScroll: value })
					}
					help={__(
						'Prevent scrolling the page when modal is open.',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Allow Hash Trigger', 'airo-wp')}
				hasValue={() => allowHashTrigger !== true}
				onDeselect={() => setAttributes({ allowHashTrigger: true })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Allow Hash Trigger', 'airo-wp')}
					checked={allowHashTrigger}
					onChange={(value) =>
						setAttributes({ allowHashTrigger: value })
					}
					help={__(
						'Open modal when URL hash matches modal ID (e.g., #airo-wp-modal-123).',
						'airo-wp'
					)}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{allowHashTrigger && (
				<DsgoInspectorPanel.Item
					label={__('Update URL on Open', 'airo-wp')}
					hasValue={() => updateUrlOnOpen !== false}
					onDeselect={() => setAttributes({ updateUrlOnOpen: false })}
					isShownByDefault
				>
					<ToggleControl
						label={__('Update URL on Open', 'airo-wp')}
						checked={updateUrlOnOpen}
						onChange={(value) =>
							setAttributes({ updateUrlOnOpen: value })
						}
						help={__(
							'Update the browser URL with modal ID when opened.',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
}
