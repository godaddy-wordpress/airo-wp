/**
 * Form Builder - Cloudflare Turnstile toggle.
 *
 * The server rejects every submission to a Turnstile form unless both the
 * site key and the secret key are configured (it fails closed), so the
 * toggle can't be switched on until they are. A form already switched on
 * with incomplete keys gets an error notice, since it is turning visitors
 * away.
 *
 * @package
 */

import { __ } from '@wordpress/i18n';
import { Notice, ToggleControl } from '@wordpress/components';
import { createInterpolateElement } from '@wordpress/element';

/**
 * Whether both Turnstile keys are configured.
 *
 * Localized by PHP as "1" / "" (wp_localize_script stringifies booleans).
 *
 * @return {boolean} True when the server can verify Turnstile tokens.
 */
export function isTurnstileConfigured() {
	return Boolean(window.dsgoIntegrations?.turnstileConfigured);
}

/**
 * Link to the settings page that holds the keys. Its text comes from the
 * translated sentence it sits in.
 *
 * @param {Object}      props          Component props.
 * @param {JSX.Element} props.children Link text.
 * @return {JSX.Element} Link.
 */
function SettingsLink({ children }) {
	return (
		<a
			href={`${window.airowpAdmin?.adminUrl || ''}admin.php?page=airo-wp-settings`}
			target="_blank"
			rel="noopener noreferrer"
		>
			{children}
			<span className="screen-reader-text">
				{__('(opens in a new tab)', 'airo-wp')}
			</span>
		</a>
	);
}

/**
 * A translated sentence with a <link> placeholder for the settings page.
 *
 * @param {string} text Translated text.
 * @return {JSX.Element} Sentence.
 */
function withSettingsLink(text) {
	return createInterpolateElement(text, { link: <SettingsLink /> });
}

/**
 * Turnstile toggle with configuration-aware notices.
 *
 * @param {Object}   props          Component props.
 * @param {boolean}  props.enabled  Whether Turnstile is on for this form.
 * @param {Function} props.onChange Called with the new value.
 * @return {JSX.Element} Controls.
 */
export default function TurnstileSettings({ enabled, onChange }) {
	const configured = isTurnstileConfigured();

	return (
		<>
			<ToggleControl
				label={__('Enable Cloudflare Turnstile', 'airo-wp')}
				checked={enabled}
				// Switching it off is always allowed.
				disabled={!configured && !enabled}
				onChange={(value) => {
					if (value && !configured) {
						return;
					}
					onChange(value);
				}}
				// Switched on with incomplete keys, the error notice below
				// explains; this help would contradict it.
				help={
					configured || enabled
						? __('Privacy-friendly CAPTCHA alternative', 'airo-wp')
						: __(
								'Add both Turnstile keys before turning this on.',
								'airo-wp'
							)
				}
				__nextHasNoMarginBottom
			/>

			{enabled && !configured && (
				<Notice status="error" isDismissible={false}>
					{withSettingsLink(
						__(
							'Turnstile is on, but its keys are incomplete, so this form rejects every submission. Add both keys in <link>Settings → Integrations</link>, or turn Turnstile off.',
							'airo-wp'
						)
					)}
				</Notice>
			)}

			{!enabled && !configured && (
				<p className="airo-wp-form-builder__turnstile-note">
					{withSettingsLink(
						__(
							'Turnstile needs a site key and a secret key, set in <link>Settings → Integrations</link>.',
							'airo-wp'
						)
					)}
				</p>
			)}

			{enabled && configured && (
				<p className="airo-wp-form-builder__turnstile-note">
					{__(
						'Widget mode (Managed, Non-interactive, Invisible) is configured in your Cloudflare dashboard.',
						'airo-wp'
					)}
				</p>
			)}
		</>
	);
}
