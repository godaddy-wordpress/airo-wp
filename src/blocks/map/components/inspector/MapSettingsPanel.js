/**
 * Map Settings Panel Component
 *
 * Renders DsgoInspectorPanel.Item entries for all map settings.
 * Meant to be composed inside the Settings DsgoInspectorPanel in
 * map/edit.js.
 */

import { __ } from '@wordpress/i18n';
import {
	SelectControl,
	TextControl,
	RangeControl,
	Button,
	Notice,
	ToggleControl,
	TextareaControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUnitControl as UnitControl,
} from '@wordpress/components';
import { useState, useCallback } from '@wordpress/element';
import { DsgoInspectorPanel } from '../../../../components/shared';
import { geocodeAddress } from '../../utils/geocoding';

const DEFAULT_PRIVACY_NOTICE =
	'This map will load content from external services. Click to load and view the map.';

/**
 * Help text describing what each provider costs the author.
 *
 * @param {string} provider - Current dsgoProvider value.
 * @return {string} Help text.
 */
function providerHelp(provider) {
	if (provider === 'googlemaps') {
		return __('Requires a Google Maps API key.', 'airo-wp');
	}

	if (provider === 'googlemaps-embed') {
		return __(
			'No API key needed. Google renders the map in an embedded frame, so the marker icon, marker color, and map style settings do not apply.',
			'airo-wp'
		);
	}

	return __('Privacy-friendly and free to use.', 'airo-wp');
}

export default function MapSettingsPanel({ attributes, setAttributes }) {
	const {
		dsgoProvider,
		dsgoLatitude,
		dsgoLongitude,
		dsgoZoom,
		dsgoAddress,
		dsgoMarkerIcon,
		dsgoHeight,
		dsgoAspectRatio,
		dsgoMapStyle,
		dsgoPrivacyMode,
		dsgoPrivacyNotice,
	} = attributes;

	// Google owns the rendering in embed mode, so the marker and style controls
	// would be dead UI — hide them rather than let them silently do nothing.
	const isEmbedProvider = dsgoProvider === 'googlemaps-embed';

	const [isSearching, setIsSearching] = useState(false);
	const [searchError, setSearchError] = useState('');

	const handleAddressSearch = useCallback(async () => {
		if (!dsgoAddress || dsgoAddress.trim() === '') {
			setSearchError(__('Please enter an address to search.', 'airo-wp'));
			return;
		}

		setIsSearching(true);
		setSearchError('');

		try {
			const result = await geocodeAddress(dsgoAddress);

			if (result) {
				setAttributes({
					dsgoLatitude: result.lat,
					dsgoLongitude: result.lng,
					dsgoAddress: result.display_name,
				});
			} else {
				setSearchError(
					__(
						'Address not found. Please try a different search.',
						'airo-wp'
					)
				);
			}
		} catch (error) {
			setSearchError(
				__('Failed to search address. Please try again.', 'airo-wp')
			);
		} finally {
			setIsSearching(false);
		}
	}, [dsgoAddress, setAttributes]);

	const handleAddressKeyPress = (event) => {
		if (event.key === 'Enter') {
			event.preventDefault();
			handleAddressSearch();
		}
	};

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Map Provider', 'airo-wp')}
				hasValue={() => dsgoProvider !== 'openstreetmap'}
				onDeselect={() =>
					setAttributes({ dsgoProvider: 'openstreetmap' })
				}
				isShownByDefault
			>
				<SelectControl
					label={__('Map Provider', 'airo-wp')}
					value={dsgoProvider}
					options={[
						{
							label: __(
								'OpenStreetMap (No API key required)',
								'airo-wp'
							),
							value: 'openstreetmap',
						},
						{
							label: __(
								'Google Maps (Requires API key)',
								'airo-wp'
							),
							value: 'googlemaps',
						},
						{
							label: __('Google Maps (No API key)', 'airo-wp'),
							value: 'googlemaps-embed',
						},
					]}
					onChange={(value) => setAttributes({ dsgoProvider: value })}
					help={providerHelp(dsgoProvider)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
				{dsgoProvider === 'googlemaps' &&
					(window.dsgoIntegrations?.googleMapsApiKey ? (
						<Notice
							status="success"
							isDismissible={false}
							style={{ marginTop: '12px' }}
						>
							{__(
								'✓ Google Maps API key configured in',
								'airo-wp'
							)}
							<a
								href="/wp-admin/admin.php?page=airo-wp-settings"
								target="_blank"
								rel="noopener noreferrer"
							>
								{__('Settings', 'airo-wp')}
							</a>
							.
						</Notice>
					) : (
						<Notice
							status="warning"
							isDismissible={false}
							style={{ marginTop: '12px' }}
						>
							<strong>
								{__('⚠ No API key configured.', 'airo-wp')}
							</strong>{' '}
							{__('Add a Google Maps API key in', 'airo-wp')}
							<a
								href="/wp-admin/admin.php?page=airo-wp-settings"
								target="_blank"
								rel="noopener noreferrer"
							>
								{__('Settings', 'airo-wp')}
							</a>{' '}
							{__('to use Google Maps.', 'airo-wp')}
						</Notice>
					))}
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Address', 'airo-wp')}
				hasValue={() => dsgoAddress !== ''}
				onDeselect={() => setAttributes({ dsgoAddress: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('Search Address', 'airo-wp')}
					value={dsgoAddress}
					onChange={(value) => {
						setAttributes({ dsgoAddress: value });
						setSearchError('');
					}}
					onKeyPress={handleAddressKeyPress}
					placeholder={__('Enter an address or location', 'airo-wp')}
					help={__(
						'Search for a location to automatically set coordinates.',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>

				<Button
					variant="secondary"
					onClick={handleAddressSearch}
					isBusy={isSearching}
					disabled={!dsgoAddress || isSearching}
					style={{ marginTop: '8px' }}
				>
					{isSearching
						? __('Searching…', 'airo-wp')
						: __('Search Address', 'airo-wp')}
				</Button>

				{searchError && (
					<Notice
						status="error"
						isDismissible={false}
						style={{ marginTop: '12px' }}
					>
						{searchError}
					</Notice>
				)}
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Latitude', 'airo-wp')}
				hasValue={() => dsgoLatitude !== 40.7128}
				onDeselect={() => setAttributes({ dsgoLatitude: 40.7128 })}
				isShownByDefault
			>
				<TextControl
					label={__('Latitude', 'airo-wp')}
					type="number"
					value={dsgoLatitude}
					onChange={(value) => {
						const num = parseFloat(value);
						const clamped = Number.isFinite(num)
							? Math.max(-90, Math.min(90, num))
							: 0;
						setAttributes({ dsgoLatitude: clamped });
					}}
					step="0.000001"
					min="-90"
					max="90"
					help={__(
						'Manual coordinate entry (between -90 and 90).',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Longitude', 'airo-wp')}
				hasValue={() => dsgoLongitude !== -74.006}
				onDeselect={() => setAttributes({ dsgoLongitude: -74.006 })}
				isShownByDefault
			>
				<TextControl
					label={__('Longitude', 'airo-wp')}
					type="number"
					value={dsgoLongitude}
					onChange={(value) => {
						const num = parseFloat(value);
						const clamped = Number.isFinite(num)
							? Math.max(-180, Math.min(180, num))
							: 0;
						setAttributes({ dsgoLongitude: clamped });
					}}
					step="0.000001"
					min="-180"
					max="180"
					help={__(
						'Manual coordinate entry (between -180 and 180).',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Zoom Level', 'airo-wp')}
				hasValue={() => dsgoZoom !== 13}
				onDeselect={() => setAttributes({ dsgoZoom: 13 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Zoom Level', 'airo-wp')}
					value={dsgoZoom}
					onChange={(value) => setAttributes({ dsgoZoom: value })}
					min={1}
					max={20}
					step={1}
					help={__('1 = world view, 20 = street level.', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{!isEmbedProvider && (
				<DsgoInspectorPanel.Item
					label={__('Marker Icon', 'airo-wp')}
					hasValue={() => dsgoMarkerIcon !== '📍'}
					onDeselect={() => setAttributes({ dsgoMarkerIcon: '📍' })}
					isShownByDefault
				>
					<TextControl
						label={__('Marker Icon', 'airo-wp')}
						value={dsgoMarkerIcon}
						onChange={(value) =>
							setAttributes({ dsgoMarkerIcon: value || '📍' })
						}
						help={__(
							'Enter an emoji or icon character.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Aspect Ratio', 'airo-wp')}
				hasValue={() => dsgoAspectRatio !== 'custom'}
				onDeselect={() => setAttributes({ dsgoAspectRatio: 'custom' })}
				isShownByDefault
			>
				<SelectControl
					label={__('Aspect Ratio', 'airo-wp')}
					value={dsgoAspectRatio}
					options={[
						{
							label: __('16:9 (Widescreen)', 'airo-wp'),
							value: '16:9',
						},
						{
							label: __('4:3 (Standard)', 'airo-wp'),
							value: '4:3',
						},
						{
							label: __('1:1 (Square)', 'airo-wp'),
							value: '1:1',
						},
						{
							label: __('Custom Height', 'airo-wp'),
							value: 'custom',
						},
					]}
					onChange={(value) =>
						setAttributes({ dsgoAspectRatio: value })
					}
					help={
						dsgoAspectRatio === 'custom'
							? __('Set a custom height below.', 'airo-wp')
							: __(
									'Maintains aspect ratio across screen sizes.',
									'airo-wp'
								)
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{dsgoAspectRatio === 'custom' && (
				<DsgoInspectorPanel.Item
					label={__('Map Height', 'airo-wp')}
					hasValue={() => dsgoHeight !== '400px'}
					onDeselect={() => setAttributes({ dsgoHeight: '400px' })}
					isShownByDefault
				>
					<UnitControl
						label={__('Map Height', 'airo-wp')}
						value={dsgoHeight}
						onChange={(value) =>
							setAttributes({ dsgoHeight: value || '400px' })
						}
						units={[
							{ value: 'px', label: 'px' },
							{ value: '%', label: '%' },
							{ value: 'vh', label: 'vh' },
						]}
						help={__('Set a custom height for the map.', 'airo-wp')}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			{dsgoProvider === 'googlemaps' && (
				<DsgoInspectorPanel.Item
					label={__('Map Style', 'airo-wp')}
					hasValue={() => dsgoMapStyle !== 'standard'}
					onDeselect={() =>
						setAttributes({ dsgoMapStyle: 'standard' })
					}
					isShownByDefault
				>
					<SelectControl
						label={__('Map Style', 'airo-wp')}
						value={dsgoMapStyle}
						options={[
							{
								label: __('Standard', 'airo-wp'),
								value: 'standard',
							},
							{
								label: __('Silver (Minimalist)', 'airo-wp'),
								value: 'silver',
							},
							{
								label: __('Dark Mode', 'airo-wp'),
								value: 'dark',
							},
						]}
						onChange={(value) =>
							setAttributes({ dsgoMapStyle: value })
						}
						help={__(
							'Choose a visual style for Google Maps.',
							'airo-wp'
						)}
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}

			<DsgoInspectorPanel.Item
				label={__('Enable Privacy Mode', 'airo-wp')}
				hasValue={() => dsgoPrivacyMode !== false}
				onDeselect={() => setAttributes({ dsgoPrivacyMode: false })}
				isShownByDefault
			>
				<ToggleControl
					label={__('Enable Privacy Mode', 'airo-wp')}
					checked={dsgoPrivacyMode}
					onChange={(value) =>
						setAttributes({ dsgoPrivacyMode: value })
					}
					help={
						dsgoPrivacyMode
							? __(
									'Map will not load until user clicks to consent.',
									'airo-wp'
								)
							: __(
									'Map will load automatically when page loads.',
									'airo-wp'
								)
					}
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			{dsgoPrivacyMode && (
				<DsgoInspectorPanel.Item
					label={__('Privacy Notice', 'airo-wp')}
					hasValue={() =>
						dsgoPrivacyNotice !== DEFAULT_PRIVACY_NOTICE
					}
					onDeselect={() =>
						setAttributes({
							dsgoPrivacyNotice: DEFAULT_PRIVACY_NOTICE,
						})
					}
					isShownByDefault
				>
					<TextareaControl
						label={__('Privacy Notice', 'airo-wp')}
						value={dsgoPrivacyNotice}
						onChange={(value) =>
							setAttributes({ dsgoPrivacyNotice: value })
						}
						rows={4}
						help={__(
							'Message shown to users before loading the map.',
							'airo-wp'
						)}
						__nextHasNoMarginBottom
					/>
				</DsgoInspectorPanel.Item>
			)}
		</>
	);
}
