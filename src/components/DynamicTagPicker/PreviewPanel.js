/**
 * PreviewPanel — live preview of a Dynamic Tag source.
 *
 * Renders an image preview for image-typed sources or the scalar
 * value for text/url/number/date sources. Displays appropriate
 * states for loading, empty, unauthorized, and error responses
 * from the REST preview endpoint.
 */
import { Spinner, Notice } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

export default function PreviewPanel({ preview, returns = [] }) {
	const isImage = returns.includes('image');

	if (preview.status === 'idle') {
		return null;
	}

	if (preview.status === 'loading') {
		return (
			<div className="airo-wp-dynamic-tag-picker__preview">
				<Spinner />
			</div>
		);
	}

	if (preview.status === 'empty') {
		return (
			<Notice status="info" isDismissible={false}>
				{__('Preview is empty for the current post.', 'airo-wp')}
			</Notice>
		);
	}

	if (preview.status === 'unauthorized') {
		return (
			<Notice status="warning" isDismissible={false}>
				{__(
					'Preview is hidden because the post is password-protected or private.',
					'airo-wp'
				)}
			</Notice>
		);
	}

	if (preview.status !== 'resolved') {
		return (
			<Notice status="error" isDismissible={false}>
				{__('Unable to preview this source.', 'airo-wp')}
			</Notice>
		);
	}

	if (isImage && preview.value && typeof preview.value === 'object') {
		return (
			<div className="airo-wp-dynamic-tag-picker__preview">
				<h4>{__('Preview', 'airo-wp')}</h4>
				<img src={preview.value.url} alt={preview.value.alt || ''} />
				<p className="airo-wp-dynamic-tag-picker__preview-meta">
					{sprintf(
						/* translators: %1$s image width, %2$s image height */
						__('%1$s × %2$s', 'airo-wp'),
						preview.value.width || '?',
						preview.value.height || '?'
					)}
				</p>
			</div>
		);
	}

	return (
		<div className="airo-wp-dynamic-tag-picker__preview">
			<h4>{__('Preview', 'airo-wp')}</h4>
			<p className="airo-wp-dynamic-tag-picker__preview-value">
				{String(preview.value)}
			</p>
		</div>
	);
}
