<?php
/**
 * Title: CTA Countdown
 * Slug: airo-wp/cta/cta-countdown
 * Categories: airo-wp-cta
 * Description: Urgency-driven CTA section with countdown timer, gradient background, and animated elements
 * Keywords: cta, countdown, timer, urgency, sale, promo
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'CTA Countdown', 'airo-wp' ),
	'categories' => array( 'airo-wp-cta' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"color":{"gradient":"linear-gradient(135deg,rgb(99,102,241) 0%,rgb(168,85,247) 50%,rgb(236,72,153) 100%)"}},"metadata":{"categories":["airo-wp-cta"],"patternName":"airo-wp/cta/cta-countdown","name":"CTA Countdown"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-background has-airo-wp-animation airo-wp-animation-fadeIn" style="background:linear-gradient(135deg,rgb(99,102,241) 0%,rgb(168,85,247) 50%,rgb(236,72,153) 100%);padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/pill {"content":"Limited Time Offer","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInDown","dsgoAnimationDuration":400,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}},"border":{"radius":"50px"},"color":{"background":"rgba(255,255,255,0.2)","text":"#ffffff"}}} /-->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"color":{"text":"#ffffff"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-text-color has-x-large-font-size" style="color:#ffffff;font-style:normal;font-weight:700">Black Friday Sale - 50% Off</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}},"color":{"text":"rgba(255,255,255,0.9)"}},"fontSize":"medium"} -->
<p class="has-text-align-center has-text-color has-medium-font-size" style="color:rgba(255,255,255,0.9);margin-top:var(--wp--preset--spacing--15)">Don\'t miss out on our biggest sale of the year. Offer ends soon!</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/countdown-timer {"targetDateTime":"2026-02-12T14:02:19.824Z","align":"center","className":"aligncenter airo-wp-countdown airo-wp-countdown\u002d\u002dseparator airo-wp-countdown\u002d\u002dlabels-below","style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"color":{"text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-countdown-timer airo-wp-countdown-timer airo-wp-countdown-timer--boxed aligncenter airo-wp-countdown airo-wp-countdown--separator airo-wp-countdown--labels-below has-text-color" style="color:#ffffff;margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40);gap:1rem" data-target-datetime="2026-02-12T14:02:19.824Z" data-timezone="" data-show-days="true" data-show-hours="true" data-show-minutes="true" data-show-seconds="true" data-completion-action="message"><div class="airo-wp-countdown-timer__units"><div class="airo-wp-countdown-timer__unit" data-unit-type="days" style="background-color:transparent;border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Days</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="hours" style="background-color:transparent;border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Hours</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="minutes" style="background-color:transparent;border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Min</div></div><div class="airo-wp-countdown-timer__unit" data-unit-type="seconds" style="background-color:transparent;border-color:var(--wp--preset--color--accent-2, currentColor);border-width:2px;border-style:solid;border-radius:12px;padding:1.5rem"><div class="airo-wp-countdown-timer__number" style="color:var(--wp--preset--color--accent-2, currentColor)">00</div><div class="airo-wp-countdown-timer__label" style="color:currentColor">Sec</div></div></div><div class="airo-wp-countdown-timer__completion-message">The countdown has ended!</div></div>
<!-- /wp:airo-wp/countdown-timer -->

<!-- wp:airo-wp/row {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDelay":200} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-delay="200"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"text":"Claim Your Discount","url":"#","icon":"arrow-right","iconPosition":"end","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"8px"},"color":{"background":"#ffffff","text":"#6366f1"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:8px;color:#6366f1;background-color:#ffffff;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Claim Your Discount</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"text":"Learn More","url":"#","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"8px","width":"2px","color":"rgba(255,255,255,0.5)"},"color":{"background":"transparent","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:rgba(255,255,255,0.5);border-width:2px;border-radius:8px;color:#ffffff;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#" target="_self"><span class="airo-wp-icon-button__text">Learn More</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}},"color":{"text":"rgba(255,255,255,0.7)"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:rgba(255,255,255,0.7);margin-top:var(--wp--preset--spacing--30)">No credit card required. Cancel anytime.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->',
);
