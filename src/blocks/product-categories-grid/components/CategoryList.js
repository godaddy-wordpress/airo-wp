/**
 * Category List Component
 *
 * Sortable list of selected categories for the sidebar in manual mode.
 * Allows reordering, toggling featured status, and removing categories.
 *
 * @since 2.1.0
 */

import { __, sprintf } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import {
	starFilled,
	starEmpty,
	chevronUp,
	chevronDown,
	closeSmall,
} from '@wordpress/icons';

/**
 * Category List Component
 *
 * @param {Object}   props                    Component props
 * @param {Array}    props.selectedCategories Array of { id: number, featured: boolean }
 * @param {Object}   props.categoryNames      Map of id -> name (e.g. { 16: "Clothing" })
 * @param {Function} props.onChange           Callback with updated selectedCategories array
 * @return {JSX.Element|null} Category list or null if empty
 */
export default function CategoryList({
	selectedCategories,
	categoryNames,
	onChange,
}) {
	if (!selectedCategories.length) {
		return null;
	}

	/**
	 * Move an item up or down in the list.
	 *
	 * @param {number} index     Current index of the item.
	 * @param {number} direction +1 to move down, -1 to move up.
	 */
	const moveItem = (index, direction) => {
		const newList = [...selectedCategories];
		const targetIndex = index + direction;
		[newList[index], newList[targetIndex]] = [
			newList[targetIndex],
			newList[index],
		];
		onChange(newList);
	};

	/**
	 * Toggle the featured flag for an item.
	 *
	 * @param {number} index Index of the item to toggle.
	 */
	const toggleFeatured = (index) => {
		const newList = selectedCategories.map((cat, i) =>
			i === index ? { ...cat, featured: !cat.featured } : cat
		);
		onChange(newList);
	};

	/**
	 * Remove an item from the list.
	 *
	 * @param {number} index Index of the item to remove.
	 */
	const removeItem = (index) => {
		onChange(selectedCategories.filter((_, i) => i !== index));
	};

	const lastIndex = selectedCategories.length - 1;

	return (
		<ul className="airo-wp-product-categories-grid__category-list">
			{selectedCategories.map((cat, index) => {
				const name =
					categoryNames[cat.id] ||
					/* translators: %d: numeric category ID */
					sprintf(
						/* translators: %d: category ID number */
						__('Category #%d', 'airo-wp'),
						cat.id
					);

				return (
					<li
						key={cat.id}
						className="airo-wp-product-categories-grid__category-list-item"
					>
						<span className="airo-wp-product-categories-grid__category-list-name">
							{name}
						</span>

						<div className="airo-wp-product-categories-grid__category-list-actions">
							<Button
								size="small"
								icon={cat.featured ? starFilled : starEmpty}
								label={
									cat.featured
										? __('Unmark as featured', 'airo-wp')
										: __('Mark as featured', 'airo-wp')
								}
								onClick={() => toggleFeatured(index)}
							/>

							{index > 0 && (
								<Button
									size="small"
									icon={chevronUp}
									label={__('Move up', 'airo-wp')}
									onClick={() => moveItem(index, -1)}
								/>
							)}

							{index < lastIndex && (
								<Button
									size="small"
									icon={chevronDown}
									label={__('Move down', 'airo-wp')}
									onClick={() => moveItem(index, 1)}
								/>
							)}

							<Button
								size="small"
								icon={closeSmall}
								label={__('Remove category', 'airo-wp')}
								onClick={() => removeItem(index)}
							/>
						</div>
					</li>
				);
			})}
		</ul>
	);
}
