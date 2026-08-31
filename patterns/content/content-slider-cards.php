<?php
/**
 * Title: Content Cards Slider
 * Slug: airo-wp/content/content-slider-cards
 * Categories: airo-wp-content
 * Description: Animated content cards in a draggable slider
 * Keywords: slider, cards, carousel, content, features
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Content Cards Slider', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-slider-cards","name":"Content Cards Slider"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Explore Our Features</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">Drag to explore the cards or use navigation arrows</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/slider {"slidesPerView":3,"slidesPerViewTablet":2,"gap":"24px","arrowStyle":"rounded","arrowPosition":"outside","dotStyle":"dots"} -->
<div class="wp-block-airo-wp-slider airo-wp-slider airo-wp-slider--classic airo-wp-slider--effect-slide airo-wp-slider--has-arrows airo-wp-slider--has-dots" style="--airo-wp-slider-height:500px;--airo-wp-slider-aspect-ratio:16/9;--airo-wp-slider-gap:24px;--airo-wp-slider-transition:0.5s;--airo-wp-slider-slides-per-view:3;--airo-wp-slider-slides-per-view-tablet:2;--airo-wp-slider-slides-per-view-mobile:1;--airo-wp-slider-arrow-size:48px" data-slides-per-view="3" data-slides-per-view-tablet="2" data-slides-per-view-mobile="1" data-use-aspect-ratio="false" data-show-arrows="true" data-show-dots="true" data-arrow-style="rounded" data-arrow-position="outside" data-arrow-vertical-position="center" data-dot-style="dots" data-dot-position="bottom" data-effect="slide" data-transition-duration="0.5s" data-transition-easing="ease-in-out" data-autoplay="false" data-autoplay-interval="3000" data-pause-on-hover="true" data-pause-on-interaction="true" data-loop="true" data-draggable="true" data-swipeable="true" data-free-mode="false" data-centered-slides="false" data-mobile-breakpoint="768" data-tablet-breakpoint="1024" data-active-slide="0" role="region" aria-label="Image slider" aria-roledescription="slider"><div class="airo-wp-slider__viewport"><div class="airo-wp-slider__track"><!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"lightning","justification":"left","className":"airo-wp-lazy-icon","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Lightning Fast</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Our optimized infrastructure ensures sub-second load times for all your pages and assets.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left" style="margin-top:var(--wp--preset--spacing--30)"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn More</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"shield-check","justification":"left","className":"airo-wp-lazy-icon","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Enterprise Security</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Bank-level encryption and security protocols protect your data around the clock.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left" style="margin-top:var(--wp--preset--spacing--30)"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn More</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"users","justification":"left","className":"airo-wp-lazy-icon","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Team Collaboration</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Work together seamlessly with real-time editing and commenting features.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left" style="margin-top:var(--wp--preset--spacing--30)"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn More</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":{"topLeft":"0px","topRight":"0px","bottomLeft":"0px","bottomRight":"0px"}}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-left-radius:0px;border-bottom-right-radius:0px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"bar-chart-2","justification":"left","className":"airo-wp-lazy-icon","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Analytics Dashboard</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Track performance metrics and gain actionable insights with our powerful dashboard.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left" style="margin-top:var(--wp--preset--spacing--30)"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn More</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":{"topLeft":"0px","topRight":"0px","bottomLeft":"0px","bottomRight":"0px"}}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-top-left-radius:0px;border-top-right-radius:0px;border-bottom-left-radius:0px;border-bottom-right-radius:0px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"globe","justification":"left","className":"airo-wp-lazy-icon","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Global CDN</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size">Content delivered from edge locations worldwide for minimal latency everywhere.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left" style="margin-top:var(--wp--preset--spacing--30)"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn More</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide --></div></div></div>
<!-- /wp:airo-wp/slider --></div></div>
<!-- /wp:airo-wp/section -->',
);
