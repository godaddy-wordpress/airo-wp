/**
 * Clickable Group Extension - Editor Controls
 *
 * Inspector panel for link settings on container blocks.
 * Lazy-loaded to reduce initial bundle size.
 *
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	TextControl,
	ExternalLink,
} from '@wordpress/components';
import { Fragment, useState } from '@wordpress/element';

/**
 * Check if a URL uses a safe protocol
 *
 * @param {string} url URL to validate
 * @return {boolean} True if safe
 */
function isSafeUrl(url) {
	if (!url) {
		return true;
	}
	const trimmed = url.trim().toLowerCase();
	return (
		!trimmed.startsWith('javascript:') &&
		!trimmed.startsWith('data:') &&
		!trimmed.startsWith('vbscript:')
	);
}

/**
 * Clickable group inspector controls panel
 *
 * @param {Object} props Block props
 */
export default function ClickableGroupPanel(props) {
	const { attributes, setAttributes } = props;
	const { dsgoLinkUrl, dsgoLinkTarget, dsgoLinkRel } = attributes;
	const [urlError, setUrlError] = useState('');

	return (
		<InspectorControls>
			<PanelBody
				title={__('Link Settings', 'airo-wp')}
				initialOpen={false}
			>
				<p className="components-base-control__help">
					{__(
						'Make the entire container clickable. Perfect for card designs.',
						'airo-wp'
					)}
				</p>
				<TextControl
					label={__('URL', 'airo-wp')}
					value={dsgoLinkUrl}
					onChange={(value) => {
						const trimmed = value?.trim() || '';
						if (!isSafeUrl(trimmed)) {
							setUrlError(
								__(
									'URLs with javascript:, data:, or vbscript: protocols are not allowed.',
									'airo-wp'
								)
							);
							return;
						}
						setUrlError('');
						setAttributes({ dsgoLinkUrl: trimmed });
					}}
					placeholder="https://example.com"
					help={
						urlError || __('Enter the destination URL', 'airo-wp')
					}
					className={
						urlError ? 'airo-wp-text-control--error' : undefined
					}
					__nextHasNoMarginBottom
				/>
				{dsgoLinkUrl && (
					<Fragment>
						<ToggleControl
							label={__('Open in new tab', 'airo-wp')}
							checked={dsgoLinkTarget}
							onChange={(value) =>
								setAttributes({ dsgoLinkTarget: value })
							}
							help={__(
								'Open link in a new browser tab',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
						<TextControl
							label={__('Link Rel', 'airo-wp')}
							value={dsgoLinkRel}
							onChange={(value) =>
								setAttributes({ dsgoLinkRel: value })
							}
							placeholder="nofollow noopener"
							help={__(
								'Add rel attribute (e.g., nofollow, sponsored)',
								'airo-wp'
							)}
							__nextHasNoMarginBottom
						/>
						<div style={{ marginTop: '16px' }}>
							<ExternalLink href={dsgoLinkUrl}>
								{__('Preview link', 'airo-wp')}
							</ExternalLink>
						</div>
					</Fragment>
				)}
			</PanelBody>
		</InspectorControls>
	);
}
