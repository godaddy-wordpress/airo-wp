<?php
/**
 * Title: Event Countdown Hero
 * Slug: airo-wp/hero/hero-event-countdown
 * Categories: airo-wp-hero
 * Description: Full-screen hero with countdown timer for events and launches
 * Keywords: hero, countdown, event, launch, timer
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Event Countdown Hero', 'airo-wp' ),
	'categories' => array( 'airo-wp-hero' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"120px","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"color":{"gradient":"linear-gradient(135deg,rgb(30,27,75) 0%,rgb(76,29,149) 100%)"}},"className":"has-airo-wp-parallax","metadata":{"categories":["airo-wp-hero"],"patternName":"airo-wp/hero/hero-event-countdown","name":"Event Countdown Hero"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-parallax has-background" style="background:linear-gradient(135deg,rgb(30,27,75) 0%,rgb(76,29,149) 100%);padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:120px;padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"has-airo-wp-text-reveal"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-text-reveal" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/pill {"content":"Coming Soon","backgroundColor":"accent-3","textColor":"#ffffff","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} /-->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"800","fontSize":"clamp(2.5rem, 6vw, 4.5rem)","lineHeight":"1.1"}},"textColor":"base"} -->
<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color" style="font-size:clamp(2.5rem, 6vw, 4.5rem);font-style:normal;font-weight:800;line-height:1.1">The Future of Web Design</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|50"}},"typography":{"fontSize":"1.25rem"}},"textColor":"base"} -->
<p class="has-text-align-center has-base-color has-text-color" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--50);font-size:1.25rem">Join us for the biggest product launch of 2025. Reserve your spot today.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|50"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/countdown-timer {"targetDateTime":"2026-02-12T22:42:00.640Z","numberColor":"#FFFFFF","labelColor":"rgba(255,255,255,0.7)","unitBorder":{"color":"#ffffff","style":"solid","width":"2px"},"align":"center","className":"airo-wp-countdown-timer\u002d\u002dlarge aligncenter"} -->
<div class="wp-block-airo-wp-countdown-timer airo-wp-countdown-timer airo-wp-countdown-timer--boxed airo-wp-countdown-timer--large aligncenter" style="gap:1rem" data-target-datetime="2026-02-12T22:42:00.640Z" data-timezone="" data-show-days="true" data-show-hours="true" data-show-minutes="true" data-show-seconds="true" data-completion-action="message"><div class="airo-wp-countdown-timer__units"><div class="airo-wp-countdown-timer__unit" data-unit-type="days" style="background-color:transparent;border-color:#ffffff;border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:#FFFFFF">00</div><div class="airo-wp-countdown-timer__label" style="color:rgba(255,255,255,0.7)">Days</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="hours" style="background-color:transparent;border-color:#ffffff;border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:#FFFFFF">00</div><div class="airo-wp-countdown-timer__label" style="color:rgba(255,255,255,0.7)">Hours</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="minutes" style="background-color:transparent;border-color:#ffffff;border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:#FFFFFF">00</div><div class="airo-wp-countdown-timer__label" style="color:rgba(255,255,255,0.7)">Min</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="seconds" style="background-color:transparent;border-color:#ffffff;border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:#FFFFFF">00</div><div class="airo-wp-countdown-timer__label" style="color:rgba(255,255,255,0.7)">Sec</div></div></div><div class="airo-wp-countdown-timer__completion-message">The countdown has ended!</div></div>
<!-- /wp:airo-wp/countdown-timer --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","orientation":"horizontal","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap"><!-- wp:airo-wp/icon-button {"text":"Reserve Your Spot","icon":"calendar","iconGap":"8px","backgroundColor":"base","textColor":"contrast","style":{"border":{"radius":"8px"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-contrast-color has-base-background-color has-text-color has-background airo-wp-icon-button--has-icon" style="border-radius:8px;gap:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--50)" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="calendar"></span><span class="airo-wp-icon-button__text">Reserve Your Spot</span></button></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"text":"Learn More","icon":"info","iconGap":"8px","className":"has-text-color","backgroundColor":"accent-6","style":{"border":{"radius":"8px","width":"2px","color":"#ffffff"},"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"color":{"text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-accent-6-background-color has-text-color has-background airo-wp-icon-button--has-icon" style="border-color:#ffffff;border-width:2px;border-radius:8px;color:#ffffff;gap:8px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--50)" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="info"></span><span class="airo-wp-icon-button__text">Learn More</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--50);padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/counter-group {"columns":4,"textColor":"base","style":{"elements":{"link":{"color":{"text":"var:preset|color|base"}}}}} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group has-base-color has-text-color has-link-color" style="align-self:stretch;--airo-wp-counter-columns-desktop:4;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2" data-animation-delay="0" data-animation-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"counter-v1yewizj8","endValue":25,"suffix":"+","label":"Workshops"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="counter-v1yewizj8" style="text-align:center" data-start-value="0" data-end-value="25" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Workshops</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-qz34p03f1","endValue":3,"label":"Day Event"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="counter-qz34p03f1" style="text-align:center" data-start-value="0" data-end-value="3" data-decimals="0" data-prefix="" data-suffix="" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Day Event</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-1o901ccay","endValue":5000,"label":"Attendees"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="counter-1o901ccay" style="text-align:center" data-start-value="0" data-end-value="5000" data-decimals="0" data-prefix="" data-suffix="" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Attendees</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-1o901ccay","endValue":20,"decimals":1,"suffix":"%","label":"Expert Speakers"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="counter-1o901ccay" style="text-align:center" data-start-value="0" data-end-value="20" data-decimals="1" data-prefix="" data-suffix="%" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Expert Speakers</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->',
);
