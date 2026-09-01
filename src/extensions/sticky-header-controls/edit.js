/**
 * Sticky Header Controls Extension - Editor Panel
 *
 * Inspector controls for sticky header configuration.
 * Lazy-loaded to reduce initial bundle size.
 *
 * @package
 * @since 1.0.0
 */

import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	RangeControl,
	SelectControl,
	Notice,
} from '@wordpress/components';

/**
 * Sticky header inspector controls panel
 *
 * @param {Object} props Block props
 */
export default function StickyHeaderPanel(props) {
	const { attributes, setAttributes } = props;

	// Check if global sticky header is enabled.
	const globalEnabled =
		window.dsgoStickyHeaderGlobalSettings?.enabled ?? true;

	return (
		<InspectorControls>
			<PanelBody
				title={__('Sticky Header', 'airo-wp')}
				initialOpen={false}
			>
				{!globalEnabled && (
					<Notice status="warning" isDismissible={false}>
						{__(
							'Sticky header is disabled in airo-wp Settings. Enable it in Settings > airo-wp to use these controls.',
							'airo-wp'
						)}
					</Notice>
				)}

				<p className="components-base-control__help">
					{__(
						'Configure sticky header behavior for this template part.',
						'airo-wp'
					)}
				</p>

				<ToggleControl
					label={__('Enable Sticky Header', 'airo-wp')}
					help={__(
						'Make this header stick to the top when scrolling.',
						'airo-wp'
					)}
					checked={attributes.dsgoStickyEnabled || false}
					disabled={!globalEnabled}
					onChange={(value) =>
						setAttributes({ dsgoStickyEnabled: value })
					}
				/>

				{attributes.dsgoStickyEnabled && globalEnabled && (
					<>
						<SelectControl
							label={__('Shadow Size', 'airo-wp')}
							value={attributes.dsgoStickyShadow || 'medium'}
							options={[
								{
									label: __('None', 'airo-wp'),
									value: 'none',
								},
								{
									label: __('Small', 'airo-wp'),
									value: 'small',
								},
								{
									label: __('Medium', 'airo-wp'),
									value: 'medium',
								},
								{
									label: __('Large', 'airo-wp'),
									value: 'large',
								},
							]}
							onChange={(value) =>
								setAttributes({
									dsgoStickyShadow: value,
								})
							}
							help={__('Shadow depth when scrolled.', 'airo-wp')}
						/>

						<ToggleControl
							label={__('Shrink Logo on Scroll', 'airo-wp')}
							help={__(
								'Scale down images (site logo, image blocks) inside the header when scrolled. Keeps aspect ratio — works for wide or tall logos.',
								'airo-wp'
							)}
							checked={attributes.dsgoStickyShrink || false}
							onChange={(value) =>
								setAttributes({
									dsgoStickyShrink: value,
								})
							}
						/>

						{attributes.dsgoStickyShrink && (
							<RangeControl
								label={__('Shrink Amount (%)', 'airo-wp')}
								help={__(
									'How much to shrink the logo. 40% means scaled to 60% of original size.',
									'airo-wp'
								)}
								value={attributes.dsgoStickyShrinkAmount ?? 50}
								onChange={(value) =>
									setAttributes({
										dsgoStickyShrinkAmount: value,
									})
								}
								min={5}
								max={70}
								step={5}
							/>
						)}

						<ToggleControl
							label={__('Hide on Scroll Down', 'airo-wp')}
							checked={attributes.dsgoStickyHideOnScroll || false}
							onChange={(value) =>
								setAttributes({
									dsgoStickyHideOnScroll: value,
								})
							}
							help={__(
								'Auto-hide when scrolling down, show when scrolling up.',
								'airo-wp'
							)}
						/>

						<ToggleControl
							label={__('Background on Scroll', 'airo-wp')}
							checked={attributes.dsgoStickyBackground || false}
							onChange={(value) =>
								setAttributes({
									dsgoStickyBackground: value,
								})
							}
							help={__(
								'Use global background color setting when scrolled.',
								'airo-wp'
							)}
						/>

						<ToggleControl
							label={__('Skip Top Bar on Scroll', 'airo-wp')}
							checked={attributes.dsgoStickySkipTopBar !== false}
							onChange={(value) =>
								setAttributes({
									dsgoStickySkipTopBar: value,
								})
							}
							help={__(
								'If your header has a top bar (e.g. social links), it scrolls away before the nav sticks.',
								'airo-wp'
							)}
						/>

						<p
							className="components-base-control__help"
							style={{ marginTop: '16px' }}
						>
							{__(
								'Additional settings like z-index, transition speed, and background color can be configured in airo-wp Settings.',
								'airo-wp'
							)}
						</p>
					</>
				)}
			</PanelBody>
		</InspectorControls>
	);
}
