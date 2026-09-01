/**
 * Star Rating — Settings panel.
 *
 * What the rating *is*: the value, the scale it is measured against, how
 * precisely a partial star may be drawn, and the numbers printed beside it.
 * How it looks lives in StylePanel.
 *
 * @since 2.8.0
 */

import {
	RangeControl,
	TextControl,
	ToggleControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControl as ToggleGroupControl,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalVStack as VStack,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { DsgoInspectorPanel } from '../../../components/shared';
import {
	clampMaxRating,
	MAX_MAX_RATING,
	MIN_MAX_RATING,
} from '../utils/rating';
import { DEFAULTS } from '../utils/defaults';

/**
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Attribute setter.
 * @param {string}   props.clientId      Block client id.
 * @param {boolean}  props.isRatingBound Whether `rating` is driven by a binding.
 * @return {JSX.Element} Settings panel.
 */
export default function SettingsPanel({
	attributes,
	setAttributes,
	clientId,
	isRatingBound,
}) {
	const {
		rating,
		maxRating,
		precision,
		showValue,
		showMax,
		ratingCount,
		showCount,
		countTemplate,
	} = attributes;

	const max = clampMaxRating(maxRating);

	return (
		<DsgoInspectorPanel
			title={__('Settings', 'airo-wp')}
			panelName="settings"
			panelId={clientId}
			resetAll={() =>
				setAttributes({
					rating: DEFAULTS.rating,
					maxRating: DEFAULTS.maxRating,
					precision: DEFAULTS.precision,
					showValue: DEFAULTS.showValue,
					showMax: DEFAULTS.showMax,
					showCount: DEFAULTS.showCount,
					ratingCount: DEFAULTS.ratingCount,
					countTemplate: DEFAULTS.countTemplate,
				})
			}
		>
			<DsgoInspectorPanel.Item
				label={__('Rating', 'airo-wp')}
				hasValue={() => rating !== DEFAULTS.rating}
				onDeselect={() => setAttributes({ rating: DEFAULTS.rating })}
				isShownByDefault
			>
				<RangeControl
					label={__('Rating', 'airo-wp')}
					value={rating}
					onChange={(value) =>
						setAttributes({
							rating:
								typeof value === 'number'
									? value
									: DEFAULTS.rating,
						})
					}
					min={0}
					max={max}
					step={0.1}
					disabled={isRatingBound}
					help={
						isRatingBound
							? __(
									'Connected to a dynamic source. The value below is only a preview — the frontend uses the source.',
									'airo-wp'
								)
							: undefined
					}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Scale', 'airo-wp')}
				hasValue={() => maxRating !== DEFAULTS.maxRating}
				onDeselect={() =>
					setAttributes({ maxRating: DEFAULTS.maxRating })
				}
				isShownByDefault
			>
				<RangeControl
					label={__('Out of', 'airo-wp')}
					value={maxRating}
					onChange={(value) =>
						setAttributes({
							maxRating: clampMaxRating(value),
						})
					}
					min={MIN_MAX_RATING}
					max={MAX_MAX_RATING}
					step={1}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Precision', 'airo-wp')}
				hasValue={() => precision !== DEFAULTS.precision}
				onDeselect={() =>
					setAttributes({ precision: DEFAULTS.precision })
				}
				isShownByDefault
			>
				<ToggleGroupControl
					label={__('Precision', 'airo-wp')}
					value={precision}
					onChange={(value) => setAttributes({ precision: value })}
					help={__(
						'How far a partial star may be rounded when drawn. The number and any structured data stay exact.',
						'airo-wp'
					)}
					isBlock
					__nextHasNoMarginBottom
				>
					<ToggleGroupControlOption
						value="full"
						label={__('Whole', 'airo-wp')}
					/>
					<ToggleGroupControlOption
						value="half"
						label={__('Half', 'airo-wp')}
					/>
					<ToggleGroupControlOption
						value="exact"
						label={__('Exact', 'airo-wp')}
					/>
				</ToggleGroupControl>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Show value', 'airo-wp')}
				hasValue={() => showValue !== DEFAULTS.showValue}
				onDeselect={() =>
					setAttributes({
						showValue: DEFAULTS.showValue,
						showMax: DEFAULTS.showMax,
					})
				}
				isShownByDefault
			>
				<VStack spacing={3}>
					<ToggleControl
						label={__('Show value', 'airo-wp')}
						checked={showValue}
						onChange={(value) =>
							setAttributes({ showValue: value })
						}
						__nextHasNoMarginBottom
					/>
					{showValue && (
						<ToggleControl
							label={__('Show scale', 'airo-wp')}
							help={__(
								'Renders "4.5/5" instead of "4.5".',
								'airo-wp'
							)}
							checked={showMax}
							onChange={(value) =>
								setAttributes({ showMax: value })
							}
							__nextHasNoMarginBottom
						/>
					)}
				</VStack>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Rating count', 'airo-wp')}
				hasValue={() =>
					showCount !== DEFAULTS.showCount ||
					ratingCount !== DEFAULTS.ratingCount ||
					countTemplate !== DEFAULTS.countTemplate
				}
				onDeselect={() =>
					setAttributes({
						showCount: DEFAULTS.showCount,
						ratingCount: DEFAULTS.ratingCount,
						countTemplate: DEFAULTS.countTemplate,
					})
				}
				isShownByDefault
			>
				<VStack spacing={3}>
					<ToggleControl
						label={__('Show rating count', 'airo-wp')}
						checked={showCount}
						onChange={(value) =>
							setAttributes({ showCount: value })
						}
						__nextHasNoMarginBottom
					/>
					{showCount && (
						<>
							<RangeControl
								label={__('Number of ratings', 'airo-wp')}
								value={ratingCount}
								onChange={(value) =>
									setAttributes({
										ratingCount:
											typeof value === 'number'
												? value
												: DEFAULTS.ratingCount,
									})
								}
								min={0}
								max={1000}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
							<TextControl
								label={__('Count format', 'airo-wp')}
								help={
									/* translators: %s is a literal placeholder token the author types, not a value substituted here. */
									__(
										'%s is replaced by the number of ratings.',
										'airo-wp'
									)
								}
								value={countTemplate}
								onChange={(value) =>
									setAttributes({ countTemplate: value })
								}
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</>
					)}
				</VStack>
			</DsgoInspectorPanel.Item>
		</DsgoInspectorPanel>
	);
}
