/**
 * Product Preview Component
 *
 * Editor preview of the product showcase hero layout.
 *
 * @since 2.1.0
 */

import { __, sprintf } from '@wordpress/i18n';
import { Disabled } from '@wordpress/components';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Format a price from the Store API response.
 *
 * @param {string} rawPrice  Price in minor units (e.g. "2999")
 * @param {Object} priceData Full prices object from Store API
 * @return {string} Formatted price string
 */
function formatPrice(rawPrice, priceData) {
	if (!rawPrice || !priceData) {
		return '';
	}

	const minorUnit = priceData.currency_minor_unit || 2;
	const value = (parseInt(rawPrice, 10) / Math.pow(10, minorUnit)).toFixed(
		minorUnit
	);
	const prefix = priceData.currency_prefix || '';
	const suffix = priceData.currency_suffix || '';

	return `${prefix}${value}${suffix}`;
}

/**
 * Render star rating as text.
 *
 * @param {string} rating      Average rating (e.g. "4.50")
 * @param {number} reviewCount Number of reviews
 * @return {string} Star rating text
 */
function renderRating(rating, reviewCount) {
	const numRating = parseFloat(rating) || 0;
	const fullStars = Math.floor(numRating);
	const hasHalf = numRating - fullStars >= 0.25;
	let stars = '\u2605'.repeat(fullStars);
	if (hasHalf) {
		stars += '\u00BD';
	}
	stars += '\u2606'.repeat(5 - fullStars - (hasHalf ? 1 : 0));
	const ratingText = numRating.toFixed(1);
	return (
		<span
			role="img"
			aria-label={sprintf(
				/* translators: 1: rating out of 5, 2: number of reviews */
				__('%1$s out of 5 stars, %2$s reviews', 'airo-wp'),
				ratingText,
				reviewCount
			)}
		>
			{stars} ({reviewCount})
		</span>
	);
}

/**
 * Product Preview Component
 *
 * @param {Object} props             Component props
 * @param {Object} props.productData Product data from Store API
 * @param {Object} props.attributes  Block attributes
 * @return {JSX.Element} Product preview
 */
export default function ProductPreview({ productData, attributes }) {
	const {
		layout,
		showPrice,
		showRating,
		showStockStatus,
		showSaleBadge,
		showShortDescription,
		showAddToCart,
		mediaFocalPoint,
		contentVerticalAlignment,
		minHeight,
	} = attributes;

	const image = productData.images?.[0];
	const prices = productData.prices;
	const isOnSale = productData.is_on_sale;

	// Map alignment to CSS.
	const alignItemsMap = {
		top: 'flex-start',
		center: 'center',
		bottom: 'flex-end',
	};

	// Focal point as object-position.
	const objectPosition = mediaFocalPoint
		? `${Number(mediaFocalPoint.x) * 100}% ${Number(mediaFocalPoint.y) * 100}%`
		: '50% 50%';

	const blockStyle = {
		'--airo-wp-psh-min-height': minHeight || undefined,
		'--airo-wp-psh-content-justify':
			alignItemsMap[contentVerticalAlignment] || 'center',
	};

	const safeLayout = layout === 'media-right' ? 'media-right' : 'media-left';

	return (
		<div
			className={`airo-wp-product-showcase-hero airo-wp-product-showcase-hero--${safeLayout}`}
			style={blockStyle}
		>
			<div className="airo-wp-product-showcase-hero__media">
				{image && (
					<img
						src={image.src}
						alt={decodeEntities(image.alt || productData.name)}
						style={{ objectPosition }}
					/>
				)}
				{showSaleBadge && isOnSale && (
					<span className="airo-wp-product-showcase-hero__sale-badge">
						{__('Sale!', 'airo-wp')}
					</span>
				)}
			</div>

			<div className="airo-wp-product-showcase-hero__content">
				<div className="airo-wp-product-showcase-hero__content-inner">
					<h2 className="airo-wp-product-showcase-hero__title">
						{decodeEntities(productData.name)}
					</h2>

					{showPrice && prices && (
						<div className="airo-wp-product-showcase-hero__price">
							{isOnSale && (
								<del>
									{formatPrice(prices.regular_price, prices)}
								</del>
							)}
							<ins>{formatPrice(prices.price, prices)}</ins>
						</div>
					)}

					{showRating &&
						parseFloat(productData.average_rating) > 0 && (
							<div className="airo-wp-product-showcase-hero__rating">
								{renderRating(
									productData.average_rating,
									productData.review_count
								)}
							</div>
						)}

					{showStockStatus && (
						<div
							className={`airo-wp-product-showcase-hero__stock airo-wp-product-showcase-hero__stock--${productData.is_in_stock ? 'instock' : 'outofstock'}`}
						>
							{productData.is_in_stock
								? __('In stock', 'airo-wp')
								: __('Out of stock', 'airo-wp')}
						</div>
					)}

					{showShortDescription && productData.short_description && (
						<p className="airo-wp-product-showcase-hero__description">
							{decodeEntities(
								new window.DOMParser().parseFromString(
									productData.short_description,
									'text/html'
								).body.textContent || ''
							)}
						</p>
					)}

					{showAddToCart && (
						<Disabled>
							<div className="airo-wp-product-showcase-hero__actions wp-block-button">
								<button
									type="button"
									className="wp-block-button__link wp-element-button"
								>
									{productData.add_to_cart?.text ||
										__('Add to cart', 'airo-wp')}
								</button>
							</div>
						</Disabled>
					)}
				</div>
			</div>
		</div>
	);
}
