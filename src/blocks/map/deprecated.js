/**
 * Map Block - Deprecated Versions
 *
 * Handles backward compatibility when block attributes or save format changes.
 */

import { useBlockProps } from '@wordpress/block-editor';
import { __, sprintf } from '@wordpress/i18n';
import classnames from 'classnames';
import { getDeprecatedBlockHTML } from '../../utils/deprecated-block-html';

/**
 * Shared supports for all deprecated versions.
 * Uses __experimentalBorder (the historical name) instead of border.
 */
const sharedSupports = {
	anchor: true,
	align: ['wide', 'full'],
	html: false,
	spacing: {
		margin: true,
		padding: true,
		blockGap: false,
		__experimentalDefaultControls: {
			padding: false,
			margin: false,
		},
	},
	color: {
		background: true,
		text: false,
		link: false,
		__experimentalDefaultControls: {
			background: false,
		},
	},
	__experimentalBorder: {
		color: true,
		radius: true,
		style: true,
		width: true,
		__experimentalDefaultControls: {
			radius: true,
		},
	},
};

/**
 * vStatic: the last STATIC save, immediately before the Map block became
 * server-rendered (save() now returns null; render.php owns output).
 *
 * isEligible matches any stored static map that is NOT a v1 block (v1 carries
 * the removed dsgoMarkerPopup / dsgoGrayscale attributes and is handled below).
 * Migration is a passthrough — only the render path changed.
 */
const vStatic = {
	supports: sharedSupports,
	attributes: {
		dsgoProvider: { type: 'string', default: 'openstreetmap' },
		dsgoLatitude: { type: 'number', default: 40.7128 },
		dsgoLongitude: { type: 'number', default: -74.006 },
		dsgoZoom: { type: 'number', default: 13 },
		dsgoAddress: { type: 'string', default: '' },
		dsgoMarkerIcon: { type: 'string', default: '📍' },
		dsgoMarkerColor: { type: 'string', default: '#e74c3c' },
		dsgoHeight: { type: 'string', default: '400px' },
		dsgoAspectRatio: { type: 'string', default: 'custom' },
		dsgoPrivacyMode: { type: 'boolean', default: false },
		dsgoPrivacyNotice: {
			type: 'string',
			default:
				'This map will load content from external services. Click to load and view the map.',
		},
		dsgoMapStyle: { type: 'string', default: 'standard' },
	},

	isEligible(attributes, innerBlocks, extra) {
		const innerHTML = getDeprecatedBlockHTML(extra);
		return (
			Boolean(innerHTML) &&
			innerHTML.includes('airo-wp-map') &&
			!Object.prototype.hasOwnProperty.call(
				attributes,
				'dsgoMarkerPopup'
			) &&
			!Object.prototype.hasOwnProperty.call(attributes, 'dsgoGrayscale')
		);
	},

	save({ attributes }) {
		const {
			dsgoProvider,
			dsgoLatitude,
			dsgoLongitude,
			dsgoZoom,
			dsgoAddress,
			dsgoMarkerIcon,
			dsgoMarkerColor,
			dsgoHeight,
			dsgoAspectRatio,
			dsgoPrivacyMode,
			dsgoPrivacyNotice,
			dsgoMapStyle,
		} = attributes;

		const blockClasses = classnames('airo-wp-map', {
			'airo-wp-map--privacy-mode': dsgoPrivacyMode,
			[`airo-wp-map--aspect-${dsgoAspectRatio.replace(':', '-')}`]:
				dsgoAspectRatio !== 'custom',
		});

		const mapStyles = {};
		if (dsgoAspectRatio === 'custom') {
			mapStyles.height = dsgoHeight;
		}

		const safeLat = Math.max(-90, Math.min(90, dsgoLatitude || 0));
		const safeLng = Math.max(-180, Math.min(180, dsgoLongitude || 0));
		const safeZoom = Math.max(1, Math.min(20, dsgoZoom || 13));

		const dataAttributes = {
			'data-airo-wp-provider': dsgoProvider,
			'data-airo-wp-lat': safeLat,
			'data-airo-wp-lng': safeLng,
			'data-airo-wp-zoom': safeZoom,
			'data-airo-wp-address': dsgoAddress || '',
			'data-airo-wp-marker-icon': dsgoMarkerIcon || '📍',
			'data-airo-wp-marker-color': dsgoMarkerColor || '#e74c3c',
			'data-airo-wp-privacy-mode': dsgoPrivacyMode ? 'true' : 'false',
			'data-airo-wp-map-style': dsgoMapStyle,
		};

		const blockProps = useBlockProps.save({
			className: blockClasses,
			style: mapStyles,
			...dataAttributes,
		});

		const mapAriaLabel = dsgoAddress
			? /* translators: %s: The address being shown on the map */
				sprintf(__('Map showing %s', 'airo-wp'), dsgoAddress)
			: __('Interactive map', 'airo-wp');

		return (
			<div {...blockProps}>
				{dsgoPrivacyMode ? (
					<div className="airo-wp-map__privacy-overlay">
						<div className="airo-wp-map__privacy-content">
							<svg
								className="airo-wp-map__privacy-icon"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 24 24"
								fill="none"
								stroke="currentColor"
								strokeWidth="2"
								strokeLinecap="round"
								strokeLinejoin="round"
								aria-hidden="true"
							>
								<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
								<circle cx="12" cy="10" r="3" />
							</svg>
							<p className="airo-wp-map__privacy-text">
								{dsgoPrivacyNotice ||
									__('Click to load map', 'airo-wp')}
							</p>
							<button
								className="airo-wp-map__load-button"
								type="button"
								aria-label={__(
									'Load map. This will connect to external map services.',
									'airo-wp'
								)}
							>
								{__('Load Map', 'airo-wp')}
							</button>
						</div>
					</div>
				) : (
					<div
						className="airo-wp-map__container"
						role="region"
						aria-label={mapAriaLabel}
					/>
				)}
			</div>
		);
	},

	migrate(attributes) {
		return attributes;
	},
};

/**
 * Version 1: Original version with marker attributes
 * Deprecated when markers were removed from the block
 */
const v1 = {
	supports: sharedSupports,
	attributes: {
		dsgoProvider: {
			type: 'string',
			default: 'openstreetmap',
		},
		dsgoLatitude: {
			type: 'number',
			default: 40.7128,
		},
		dsgoLongitude: {
			type: 'number',
			default: -74.006,
		},
		dsgoZoom: {
			type: 'number',
			default: 13,
		},
		dsgoAddress: {
			type: 'string',
			default: '',
		},
		dsgoMarkerIcon: {
			type: 'string',
			default: '📍',
		},
		dsgoMarkerColor: {
			type: 'string',
			default: '#e74c3c',
		},
		dsgoMarkerPopup: {
			type: 'string',
			default: '',
		},
		dsgoHeight: {
			type: 'string',
			default: '400px',
		},
		dsgoAspectRatio: {
			type: 'string',
			default: 'custom',
		},
		dsgoGrayscale: {
			type: 'boolean',
			default: false,
		},
		dsgoPrivacyMode: {
			type: 'boolean',
			default: false,
		},
		dsgoPrivacyNotice: {
			type: 'string',
			default:
				'This map will load content from external services. Click to load and view the map.',
		},
		dsgoMapStyle: {
			type: 'string',
			default: 'standard',
		},
	},

	isEligible(attributes) {
		// v1 blocks have dsgoMarkerPopup and dsgoGrayscale attributes
		return (
			Object.prototype.hasOwnProperty.call(
				attributes,
				'dsgoMarkerPopup'
			) ||
			Object.prototype.hasOwnProperty.call(attributes, 'dsgoGrayscale')
		);
	},

	save({ attributes }) {
		const {
			dsgoProvider,
			dsgoLatitude,
			dsgoLongitude,
			dsgoZoom,
			dsgoAddress,
			dsgoMarkerIcon,
			dsgoMarkerColor,
			dsgoMarkerPopup,
			dsgoHeight,
			dsgoAspectRatio,
			dsgoGrayscale,
			dsgoPrivacyMode,
			dsgoPrivacyNotice,
			dsgoMapStyle,
		} = attributes;

		// Ensure coordinates are within valid ranges (security)
		const safeLat = Math.max(-90, Math.min(90, dsgoLatitude || 0));
		const safeLng = Math.max(-180, Math.min(180, dsgoLongitude || 0));
		const safeZoom = Math.max(1, Math.min(20, dsgoZoom || 13));

		// Block classes
		const blockClasses = classnames('airo-wp-map', {
			'airo-wp-map--grayscale': dsgoGrayscale,
			'airo-wp-map--privacy-mode': dsgoPrivacyMode,
			[`airo-wp-map--aspect-${dsgoAspectRatio.replace(':', '-')}`]:
				dsgoAspectRatio !== 'custom',
		});

		// Custom styles
		const blockStyles = {};
		if (dsgoAspectRatio === 'custom') {
			blockStyles.height = dsgoHeight;
		}

		// Data attributes for view.js
		const dataAttributes = {
			'data-airo-wp-provider': dsgoProvider,
			'data-airo-wp-lat': safeLat,
			'data-airo-wp-lng': safeLng,
			'data-airo-wp-zoom': safeZoom,
			'data-airo-wp-address': dsgoAddress || '',
			'data-airo-wp-marker-icon': dsgoMarkerIcon || '📍',
			'data-airo-wp-marker-color': dsgoMarkerColor || '#e74c3c',
			'data-airo-wp-marker-popup': dsgoMarkerPopup || '',
			'data-airo-wp-grayscale': dsgoGrayscale,
			'data-airo-wp-privacy-mode': dsgoPrivacyMode,
			'data-airo-wp-map-style': dsgoMapStyle || 'standard',
		};

		const blockProps = useBlockProps.save({
			className: blockClasses,
			style: blockStyles,
			...dataAttributes,
		});

		// Compute aria-label for map container
		const mapAriaLabel = dsgoAddress
			? /* translators: %s: The address being shown on the map */
				sprintf(__('Map showing %s', 'airo-wp'), dsgoAddress)
			: __('Interactive map', 'airo-wp');

		// Render privacy overlay or map container
		if (dsgoPrivacyMode) {
			return (
				<div {...blockProps}>
					<div className="airo-wp-map__privacy-overlay">
						<svg
							className="airo-wp-map__privacy-icon"
							xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeWidth="2"
							strokeLinecap="round"
							strokeLinejoin="round"
							aria-hidden="true"
						>
							<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
							<circle cx="12" cy="10" r="3" />
						</svg>
						<p className="airo-wp-map__privacy-text">
							{dsgoPrivacyNotice ||
								__('Click to load map', 'airo-wp')}
						</p>
						<button
							className="airo-wp-map__load-button"
							type="button"
							aria-label={__(
								'Load map. This will connect to external map services.',
								'airo-wp'
							)}
						>
							{__('Load Map', 'airo-wp')}
						</button>
					</div>
				</div>
			);
		}

		return (
			<div {...blockProps}>
				<div
					className="airo-wp-map__container"
					role="region"
					aria-label={mapAriaLabel}
				/>
			</div>
		);
	},

	migrate(attributes) {
		// Remove deprecated attributes (popup message and grayscale)
		const { dsgoMarkerPopup, dsgoGrayscale, ...newAttributes } = attributes;

		return newAttributes;
	},
};

export default [vStatic, v1];
