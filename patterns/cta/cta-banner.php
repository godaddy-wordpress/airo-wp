<?php
/**
 * Title: CTA Banner with Countdown
 * Slug: airo-wp/cta/cta-banner
 * Categories: airo-wp-cta
 * Description: A bold urgency CTA with countdown timer
 * Keywords: cta, banner, countdown, urgency, bold
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'CTA Banner with Countdown', 'airo-wp' ),
	'categories' => array( 'airo-wp-cta' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"gradient":"vivid-cyan-blue-to-vivid-purple","metadata":{"categories":["airo-wp-cta"],"patternName":"airo-wp/cta/cta-banner","name":"CTA Banner with Countdown"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-vivid-cyan-blue-to-vivid-purple-gradient-background has-background" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section {"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"textColor":"base","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-base-color has-text-color has-x-large-font-size" style="font-style:normal;font-weight:700">Limited Time Offer!</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"medium"} -->
<p class="has-base-color has-text-color has-medium-font-size">Get 50% off your first year. This exclusive deal ends soon.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/countdown-timer {"targetDateTime":"2026-02-10T18:56:16.324Z","unitBackgroundColor":"rgba(255,255,255,0.2)","unitPadding":"var:preset|spacing|30","className":"airo-wp-countdown airo-wp-countdown\u002d\u002dboxed","textColor":"base","fontSize":"small"} -->
<div class="wp-block-airo-wp-countdown-timer airo-wp-countdown-timer airo-wp-countdown-timer--boxed airo-wp-countdown airo-wp-countdown--boxed has-base-color has-text-color has-small-font-size" style="gap:1rem" data-target-datetime="2026-02-10T18:56:16.324Z" data-timezone="" data-show-days="true" data-show-hours="true" data-show-minutes="true" data-show-seconds="true" data-completion-action="message"><div class="airo-wp-countdown-timer__units"><div class="airo-wp-countdown-timer__unit" data-unit-type="days" style="background-color:rgba(255,255,255,0.2);border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:var:preset|spacing|30"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Days</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="hours" style="background-color:rgba(255,255,255,0.2);border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:var:preset|spacing|30"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Hours</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="minutes" style="background-color:rgba(255,255,255,0.2);border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:var:preset|spacing|30"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Min</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="seconds" style="background-color:rgba(255,255,255,0.2);border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:var:preset|spacing|30"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Sec</div></div></div><div class="airo-wp-countdown-timer__completion-message">Offer has ended!</div></div>
<!-- /wp:airo-wp/countdown-timer --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
