/**
 * Slider — layout & responsive fields.
 *
 * Slides-per-view at each breakpoint, the pixel breakpoints themselves, and
 * the slider's accessible label. Composed inside the Settings
 * DsgoInspectorPanel in SettingsPanel.js.
 */

import { __ } from '@wordpress/i18n';
import { RangeControl, TextControl } from '@wordpress/components';
import { DsgoInspectorPanel } from '../../../../components/shared';

/**
 * @param {Object}   props                           Component props.
 * @param {Object}   props.attributes                Block attributes.
 * @param {Function} props.setAttributes             Attribute setter.
 * @param {boolean}  props.requiresSingleSlideEffect Whether the current
 *                                                   transition effect
 *                                                   forces one slide
 *                                                   per view.
 * @param {string}   props.singleSlideNotice         Help text shown
 *                                                   when locked to a
 *                                                   single slide.
 * @return {JSX.Element} Layout & responsive fields.
 */
export default function LayoutFields({
	attributes,
	setAttributes,
	requiresSingleSlideEffect,
	singleSlideNotice,
}) {
	const {
		slidesPerView,
		slidesPerViewTablet,
		slidesPerViewMobile,
		mobileBreakpoint,
		tabletBreakpoint,
		ariaLabel,
	} = attributes;

	return (
		<>
			<DsgoInspectorPanel.Item
				label={__('Slides Per View (Desktop)', 'airo-wp')}
				hasValue={() => slidesPerView !== 1}
				onDeselect={() => setAttributes({ slidesPerView: 1 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Slides Per View (Desktop)', 'airo-wp')}
					value={slidesPerView}
					onChange={(value) =>
						setAttributes({ slidesPerView: value })
					}
					min={1}
					max={6}
					help={
						requiresSingleSlideEffect
							? singleSlideNotice
							: __('Number of slides visible at once', 'airo-wp')
					}
					disabled={requiresSingleSlideEffect}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Slides Per View (Tablet)', 'airo-wp')}
				hasValue={() => slidesPerViewTablet !== 1}
				onDeselect={() => setAttributes({ slidesPerViewTablet: 1 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Slides Per View (Tablet)', 'airo-wp')}
					value={slidesPerViewTablet}
					onChange={(value) =>
						setAttributes({ slidesPerViewTablet: value })
					}
					min={1}
					max={4}
					help={requiresSingleSlideEffect ? singleSlideNotice : ''}
					disabled={requiresSingleSlideEffect}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Slides Per View (Mobile)', 'airo-wp')}
				hasValue={() => slidesPerViewMobile !== 1}
				onDeselect={() => setAttributes({ slidesPerViewMobile: 1 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Slides Per View (Mobile)', 'airo-wp')}
					value={slidesPerViewMobile}
					onChange={(value) =>
						setAttributes({ slidesPerViewMobile: value })
					}
					min={1}
					max={2}
					help={requiresSingleSlideEffect ? singleSlideNotice : ''}
					disabled={requiresSingleSlideEffect}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Mobile Breakpoint (px)', 'airo-wp')}
				hasValue={() => mobileBreakpoint !== 768}
				onDeselect={() => setAttributes({ mobileBreakpoint: 768 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Mobile Breakpoint (px)', 'airo-wp')}
					value={mobileBreakpoint}
					onChange={(value) =>
						setAttributes({ mobileBreakpoint: value })
					}
					min={320}
					max={900}
					help={__(
						'Below this width, uses mobile slides per view',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('Tablet Breakpoint (px)', 'airo-wp')}
				hasValue={() => tabletBreakpoint !== 1024}
				onDeselect={() => setAttributes({ tabletBreakpoint: 1024 })}
				isShownByDefault
			>
				<RangeControl
					label={__('Tablet Breakpoint (px)', 'airo-wp')}
					value={tabletBreakpoint}
					onChange={(value) =>
						setAttributes({ tabletBreakpoint: value })
					}
					min={768}
					max={1280}
					help={__(
						'Below this width, uses tablet slides per view',
						'airo-wp'
					)}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>

			<DsgoInspectorPanel.Item
				label={__('ARIA Label', 'airo-wp')}
				hasValue={() => ariaLabel !== ''}
				onDeselect={() => setAttributes({ ariaLabel: '' })}
				isShownByDefault
			>
				<TextControl
					label={__('ARIA Label', 'airo-wp')}
					value={ariaLabel}
					onChange={(value) => setAttributes({ ariaLabel: value })}
					help={__('Accessible label for screen readers', 'airo-wp')}
					placeholder={__('Image slider', 'airo-wp')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>
			</DsgoInspectorPanel.Item>
		</>
	);
}
