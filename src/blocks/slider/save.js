import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import classnames from 'classnames';
import { cssVars } from '../../utils/css-vars';

const SINGLE_SLIDE_EFFECTS = ['fade', 'zoom'];

export default function SliderSave({ attributes }) {
	const {
		slidesPerView,
		slidesPerViewTablet,
		slidesPerViewMobile,
		height,
		aspectRatio,
		useAspectRatio,
		gap,
		showArrows,
		showDots,
		arrowStyle,
		arrowPosition,
		arrowVerticalPosition,
		arrowSize,
		arrowPadding,
		dotStyle,
		dotPosition,
		effect,
		transitionDuration,
		transitionEasing,
		autoplay,
		autoplayInterval,
		pauseOnHover,
		pauseOnInteraction,
		loop,
		draggable,
		swipeable,
		freeMode,
		centeredSlides,
		mobileBreakpoint,
		tabletBreakpoint,
		activeSlide,
		styleVariation,
		ariaLabel,
		scrollDriven,
		scrollDrivenSpeed,
	} = attributes;

	const requiresSingleSlideEffect = SINGLE_SLIDE_EFFECTS.includes(effect);
	const effectiveSlidesPerView = requiresSingleSlideEffect
		? 1
		: slidesPerView;
	const effectiveSlidesPerViewTablet = requiresSingleSlideEffect
		? 1
		: slidesPerViewTablet;
	const effectiveSlidesPerViewMobile = requiresSingleSlideEffect
		? 1
		: slidesPerViewMobile;

	// Same classes as edit.js - MUST MATCH EXACTLY
	const sliderClasses = classnames('airo-wp-slider', {
		[`airo-wp-slider--${styleVariation}`]: styleVariation,
		[`airo-wp-slider--effect-${effect}`]: effect,
		'airo-wp-slider--has-arrows': showArrows,
		'airo-wp-slider--has-dots': showDots,
		'airo-wp-slider--centered': centeredSlides,
		'airo-wp-slider--free-mode': freeMode,
		'airo-wp-slider--scroll-driven': scrollDriven,
	});

	// Apply settings as CSS custom properties - MUST MATCH edit.js
	const customStyles = {
		...(height && { '--airo-wp-slider-height': height }),
		'--airo-wp-slider-aspect-ratio': aspectRatio,
		'--airo-wp-slider-gap': gap,
		'--airo-wp-slider-transition': transitionDuration,
		'--airo-wp-slider-slides-per-view': String(effectiveSlidesPerView),
		'--airo-wp-slider-slides-per-view-tablet': String(
			effectiveSlidesPerViewTablet
		),
		'--airo-wp-slider-slides-per-view-mobile': String(
			effectiveSlidesPerViewMobile
		),
		...cssVars(attributes, {
			'--airo-wp-slider-arrow-color': 'arrowColor',
			'--airo-wp-slider-arrow-bg-color': 'arrowBackgroundColor',
			'--airo-wp-slider-dot-color': 'dotColor',
		}),
		...(arrowSize && { '--airo-wp-slider-arrow-size': arrowSize }),
		...(arrowPadding && { '--airo-wp-slider-arrow-padding': arrowPadding }),
	};

	// Use .save() variant for save function
	// Data attributes for JavaScript configuration
	const blockProps = useBlockProps.save({
		className: sliderClasses,
		style: customStyles,
		'data-slides-per-view': effectiveSlidesPerView,
		'data-slides-per-view-tablet': effectiveSlidesPerViewTablet,
		'data-slides-per-view-mobile': effectiveSlidesPerViewMobile,
		'data-use-aspect-ratio': useAspectRatio,
		'data-show-arrows': showArrows,
		'data-show-dots': showDots,
		'data-arrow-style': arrowStyle,
		'data-arrow-position': arrowPosition,
		'data-arrow-vertical-position': arrowVerticalPosition,
		'data-dot-style': dotStyle,
		'data-dot-position': dotPosition,
		'data-effect': effect,
		'data-transition-duration': transitionDuration,
		'data-transition-easing': transitionEasing,
		'data-autoplay': autoplay,
		'data-autoplay-interval': autoplayInterval,
		'data-pause-on-hover': pauseOnHover,
		'data-pause-on-interaction': pauseOnInteraction,
		'data-loop': loop,
		'data-draggable': draggable,
		'data-swipeable': swipeable,
		'data-free-mode': freeMode,
		'data-centered-slides': centeredSlides,
		'data-mobile-breakpoint': mobileBreakpoint,
		'data-tablet-breakpoint': tabletBreakpoint,
		'data-active-slide': activeSlide,
		...(scrollDriven && {
			'data-scroll-driven': true,
			'data-scroll-driven-speed': scrollDrivenSpeed,
		}),
		role: 'region',
		'aria-label': ariaLabel || 'Image slider',
		'aria-roledescription': 'slider',
	});

	const innerBlocksProps = useInnerBlocksProps.save({
		className: 'airo-wp-slider__track',
	});

	return (
		<div {...blockProps}>
			<div className="airo-wp-slider__viewport">
				<div {...innerBlocksProps} />
			</div>
		</div>
	);
}
