/**
 * CarouselNotice — explains why infinite scroll will not run in a carousel.
 *
 * Infinite scroll hangs on a sentinel below the items: as the reader scrolls
 * the page down past the list, the sentinel comes into view and the next page
 * loads. A carousel host keeps its items inside a fixed-height viewport, so
 * that sentinel no longer tracks how far the reader has got — it either sits
 * on screen from first paint and pulls every page down at once, or it is never
 * reached at all.
 *
 * The presentation wins: query-pagination/render.php renders a Load more
 * button instead. Saying so here, next to the setting, is the difference
 * between a considered fallback and a control that silently does nothing.
 *
 * @since 2.7.0
 */
import { __, sprintf } from '@wordpress/i18n';
import { Button, Notice } from '@wordpress/components';

const HOST_LABELS = {
	'airo-wp/slider': __('Slider', 'airo-wp'),
	'airo-wp/scroll-slides': __('Scroll Slides', 'airo-wp'),
};

export default function CarouselNotice({ itemHost, setAttributes }) {
	const hostLabel = HOST_LABELS[itemHost] || __('carousel', 'airo-wp');

	return (
		<Notice status="warning" isDismissible={false}>
			<p>
				{sprintf(
					/* translators: %s: name of the layout block presenting the query results. */
					__(
						'This query presents its results in a %s, which has no scrolling list for infinite scroll to follow. The front end renders a Load more button instead.',
						'airo-wp'
					),
					hostLabel
				)}
			</p>
			<Button
				variant="secondary"
				size="small"
				onClick={() =>
					setAttributes({
						paginationKind: 'loadmore',
						mode: 'loadmore',
					})
				}
			>
				{__('Switch to Load more', 'airo-wp')}
			</Button>
		</Notice>
	);
}
