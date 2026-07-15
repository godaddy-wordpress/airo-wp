/**
 * Visual Icon Picker Component
 *
 * Displays icons in a visual grid for easy selection
 */

import { Button, Popover, SearchControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState, useMemo } from '@wordpress/element';
import { getIcon, getIconNames, getIconAliases } from '../utils/svg-icons';

/**
 * Icon categories for organization
 */
const ICON_CATEGORIES = {
	all: {
		label: __('All', 'airo-wp'),
		icons: null, // null means show all icons
	},
	popular: {
		label: __('Popular', 'airo-wp'),
		icons: [
			'star',
			'heart',
			'check',
			'circle-check',
			'verified-check',
			'lightning',
			'fire',
			'blocks',
			'user',
			'envelope',
			'phone',
			'location',
			'thumbs-up',
			'rocket',
			'lightbulb',
			'shield',
			'shield-check',
		],
	},
	social: {
		label: __('Social', 'airo-wp'),
		icons: [
			'facebook',
			'x',
			'instagram',
			'linkedin',
			'youtube',
			'github',
			'tiktok',
		],
	},
	business: {
		label: __('Business', 'airo-wp'),
		icons: [
			'briefcase',
			'building',
			'chart',
			'calendar',
			'clipboard',
			'trophy',
			'certificate',
			'dollar',
			'wallet',
		],
	},
	ecommerce: {
		label: __('Shop', 'airo-wp'),
		icons: ['shopping-cart', 'credit-card', 'tag', 'gift', 'percentage'],
	},
	tech: {
		label: __('Tech', 'airo-wp'),
		icons: [
			'monitor',
			'smartphone',
			'tablet',
			'wifi',
			'database',
			'cloud',
			'code',
			'layers',
		],
	},
	communication: {
		label: __('Contact', 'airo-wp'),
		icons: ['comment', 'chat', 'bell', 'info', 'warning', 'quote'],
	},
	actions: {
		label: __('Actions', 'airo-wp'),
		icons: [
			'plus',
			'minus',
			'times',
			'edit',
			'trash',
			'download',
			'upload',
			'search',
			'settings',
			'filter',
			'refresh',
			'refresh-cw',
			'lock',
			'unlock',
		],
	},
	media: {
		label: __('Media', 'airo-wp'),
		icons: ['play', 'pause', 'video', 'image', 'camera'],
	},
	arrows: {
		label: __('Arrows', 'airo-wp'),
		icons: [
			'arrow-right',
			'arrow-left',
			'arrow-up',
			'arrow-down',
			'chevron-right',
			'chevron-left',
		],
	},
	ui: {
		label: __('UI', 'airo-wp'),
		icons: ['home', 'bookmark', 'link', 'folder', 'file', 'menu', 'blocks'],
	},
	other: {
		label: __('Other', 'airo-wp'),
		icons: [
			'users',
			'clock',
			'book',
			'graduation-cap',
			'fitness',
			'dumbbell',
			'medkit',
			'leaf',
			'sun',
			'moon',
		],
	},
};

/**
 * IconPicker Component
 *
 * @param {Object}   props
 * @param {string}   props.value    - Currently selected icon name
 * @param {Function} props.onChange - Callback when icon is selected
 */
export const IconPicker = ({ value, onChange }) => {
	const [isOpen, setIsOpen] = useState(false);
	const [searchTerm, setSearchTerm] = useState('');
	const [activeCategory, setActiveCategory] = useState('all');

	// Get all icon names for search
	const allIcons = getIconNames();

	// Build reverse alias map: canonical name → list of aliases
	const aliasMap = useMemo(() => getIconAliases(), []);

	// Filter icons based on search
	const getFilteredIcons = () => {
		if (!searchTerm) {
			const categoryIcons = ICON_CATEGORIES[activeCategory]?.icons;
			// If icons is null (like "all" category), show all icons
			return categoryIcons === null ? allIcons : categoryIcons || [];
		}

		const term = searchTerm.toLowerCase();

		// Search across icon names and their aliases
		return allIcons.filter((iconName) => {
			if (iconName.toLowerCase().includes(term)) {
				return true;
			}
			const aliases = aliasMap[iconName];
			return aliases?.some((alias) => alias.includes(term));
		});
	};

	const filteredIcons = getFilteredIcons();

	return (
		<div className="airo-wp-icon-picker">
			<div className="airo-wp-icon-picker__label">
				{__('Choose Icon', 'airo-wp')}
			</div>

			{/* Current Icon Preview */}
			<Button
				className="airo-wp-icon-picker__trigger"
				onClick={() => setIsOpen(!isOpen)}
				aria-expanded={isOpen}
			>
				<span
					className="airo-wp-icon-picker__preview"
					style={{ width: '32px', height: '32px' }}
				>
					{getIcon(value)}
				</span>
				<span className="airo-wp-icon-picker__name">
					{value.charAt(0).toUpperCase() +
						value.slice(1).replace(/-/g, ' ')}
				</span>
			</Button>

			{/* Popover with Icon Grid */}
			{isOpen && (
				<Popover
					position="bottom left"
					onClose={() => {
						setIsOpen(false);
						setSearchTerm('');
					}}
					className="airo-wp-icon-picker__popover"
				>
					<div className="airo-wp-icon-picker__content">
						{/* Search */}
						<SearchControl
							value={searchTerm}
							onChange={setSearchTerm}
							placeholder={__('Search icons…', 'airo-wp')}
						/>

						{/* Category Tabs */}
						{!searchTerm && (
							<div className="airo-wp-icon-picker__categories">
								{Object.keys(ICON_CATEGORIES).map(
									(categoryKey) => (
										<Button
											key={categoryKey}
											className={`airo-wp-icon-picker__category ${
												activeCategory === categoryKey
													? 'is-active'
													: ''
											}`}
											onClick={() =>
												setActiveCategory(categoryKey)
											}
										>
											{ICON_CATEGORIES[categoryKey].label}
										</Button>
									)
								)}
							</div>
						)}

						{/* Icon Grid */}
						<div className="airo-wp-icon-picker__grid">
							{filteredIcons.length > 0 ? (
								filteredIcons.map((iconName) => (
									<Button
										key={iconName}
										className={`airo-wp-icon-picker__icon ${
											value === iconName
												? 'is-selected'
												: ''
										}`}
										onClick={() => {
											onChange(iconName);
											setIsOpen(false);
											setSearchTerm('');
										}}
										aria-label={iconName}
										title={
											iconName.charAt(0).toUpperCase() +
											iconName.slice(1).replace(/-/g, ' ')
										}
									>
										<span
											style={{
												width: '24px',
												height: '24px',
												display: 'flex',
												alignItems: 'center',
												justifyContent: 'center',
											}}
										>
											{getIcon(iconName)}
										</span>
									</Button>
								))
							) : (
								<div className="airo-wp-icon-picker__empty">
									{__('No icons found', 'airo-wp')}
								</div>
							)}
						</div>
					</div>
				</Popover>
			)}
		</div>
	);
};
