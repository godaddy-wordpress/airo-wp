<?php
/**
 * Title: Modern SaaS Homepage
 * Slug: airo-wp/homepage/homepage-modern-saas
 * Categories: airo-wp-homepage
 * Description: A stunning modern SaaS homepage with shape dividers, parallax effects, image accordion, flip cards, and premium animations
 * Keywords: homepage, saas, product, software, startup, modern, premium
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Modern SaaS Homepage', 'airo-wp' ),
	'categories' => array( 'airo-wp-homepage' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"shapeDividerBottom":"wave","shapeDividerBottomHeight":120,"shapeDividerBottomFlipY":true,"backgroundColor":"contrast","textColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn","dsgoAnimationDuration":800} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-stack--has-shape-divider has-base-color has-contrast-background-color has-text-color has-background has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn" data-airo-wp-animation-duration="800"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto;padding-bottom:120px"><!-- wp:airo-wp/pill {"content":"New: AI-Powered Analytics Now Available","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInDown","dsgoAnimationDuration":500,"style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|20","right":"var:preset|spacing|20"},"margin":{"bottom":"var:preset|spacing|30"}},"border":{"radius":"50px"},"color":{"background":"rgba(139,92,246,0.2)","text":"#a78bfa"}}} /-->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","lineHeight":"1.1"},"spacing":{"margin":{"top":"0"}}},"fontSize":"xx-large","dsgoTextRevealEnabled":true,"dsgoTextRevealColor":"#8b5cf6","dsgoTextRevealSplitMode":"words"} -->
<h1 class="wp-block-heading has-text-align-center has-xx-large-font-size has-airo-wp-text-reveal" style="margin-top:0;font-style:normal;font-weight:700;line-height:1.1" data-airo-wp-text-reveal-enabled="true" data-airo-wp-text-reveal-color="#8b5cf6" data-airo-wp-text-reveal-split-mode="words" data-airo-wp-text-reveal-transition="150">Build Better Products<br>Ship Faster</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}},"color":{"text":"#94a3b8"}},"fontSize":"large"} -->
<p class="has-text-align-center has-text-color has-large-font-size" style="color:#94a3b8;margin-top:var(--wp--preset--spacing--30)">The all-in-one platform that helps teams collaborate, analyze, and deliver exceptional software experiences.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDelay":200} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-delay="200"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"url":"#trial","icon":"arrow-right","iconPosition":"end","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px"},"color":{"background":"#8b5cf6","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:8px;color:#ffffff;background-color:#8b5cf6;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#trial" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Start Free Trial</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"url":"#demo","icon":"play","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px","width":"1px","color":"#475569"},"color":{"background":"transparent","text":"#e2e8f0"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background airo-wp-icon-button--has-icon" style="border-color:#475569;border-width:1px;border-radius:8px;color:#e2e8f0;background-color:transparent;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#demo" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="play"></span><span class="airo-wp-icon-button__text">Watch Demo</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:image {"sizeSlug":"large","style":{"spacing":{"margin":{"top":"var:preset|spacing|60"}},"border":{"radius":"16px"}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDuration":800,"dsgoAnimationDelay":400,"dsgoParallaxEnabled":true,"dsgoParallaxSpeed":3} -->
<figure class="wp-block-image size-large has-custom-border has-airo-wp-animation airo-wp-animation-fadeInUp airo-wp-has-parallax" style="margin-top:var(--wp--preset--spacing--60)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-duration="800" data-airo-wp-animation-delay="400" data-airo-wp-parallax-enabled="true" data-airo-wp-parallax-direction="up" data-airo-wp-parallax-speed="3" data-airo-wp-parallax-viewport-start="0" data-airo-wp-parallax-viewport-end="100" data-airo-wp-parallax-relative-to="viewport" data-airo-wp-parallax-desktop="true" data-airo-wp-parallax-tablet="true" data-airo-wp-parallax-mobile="false" data-airo-wp-parallax-rotate-enabled="false" data-airo-wp-parallax-rotate-direction="cw" data-airo-wp-parallax-rotate-speed="3"><img src="{{dsgo:placeholder-landscape}}" alt="Dashboard preview" style="border-radius:16px"/></figure>
<!-- /wp:image --></div><div class="airo-wp-shape-divider airo-wp-shape-divider--bottom is-shape-wave" style="--airo-wp-shape-height:120px" aria-hidden="true"></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}},"color":{"text":"#64748b"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#64748b;margin-bottom:var(--wp--preset--spacing--30)">TRUSTED BY INNOVATIVE TEAMS WORLDWIDE</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"blockGap":"var:preset|spacing|60","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--60)"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#94a3b8;font-style:normal;font-weight:700;letter-spacing:2px">STRIPE</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#94a3b8;font-style:normal;font-weight:700;letter-spacing:2px">NOTION</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#94a3b8;font-style:normal;font-weight:700;letter-spacing:2px">FIGMA</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#94a3b8;font-style:normal;font-weight:700;letter-spacing:2px">VERCEL</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#94a3b8;font-style:normal;font-weight:700;letter-spacing:2px">LINEAR</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#8b5cf6"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#8b5cf6;letter-spacing:3px;text-transform:uppercase">Features</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Everything You Need to Scale</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-align-center has-text-color has-medium-font-size" style="color:#64748b;margin-top:var(--wp--preset--spacing--20)">Powerful tools designed to help your team move faster and build better products.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--30);column-gap:var(--wp--preset--spacing--30)"><!-- wp:airo-wp/flip-card -->
<div class="wp-block-airo-wp-flip-card airo-wp-flip-card airo-wp-flip-card--hover airo-wp-flip-card--effect-flip airo-wp-flip-card--horizontal" style="--airo-wp-flip-duration:0.6s" data-flip-trigger="hover" data-flip-effect="flip" data-flip-direction="horizontal"><div class="airo-wp-flip-card__container"><!-- wp:airo-wp/flip-card-face {"side":"front","className":"airo-wp-flip-card__face\u002d\u002dfront","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"16px"},"color":{"gradient":"linear-gradient(135deg,rgb(139,92,246) 0%,rgb(79,70,229) 100%)"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__front airo-wp-flip-card__face--front has-background" style="border-radius:16px;background:linear-gradient(135deg,rgb(139,92,246) 0%,rgb(79,70,229) 100%);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:airo-wp/icon {"icon":"chart","iconSize":56,"style":{"color":{"text":"#ffffff"}}} /-->
<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size" style="margin-top:var(--wp--preset--spacing--30)">Real-time Analytics</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}},"color":{"text":"rgba(255,255,255,0.8)"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:rgba(255,255,255,0.8);margin-top:var(--wp--preset--spacing--10)">Hover to learn more</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face -->

<!-- wp:airo-wp/flip-card-face {"side":"back","className":"airo-wp-flip-card__face\u002d\u002dback","backgroundColor":"base-2","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"16px"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__back airo-wp-flip-card__face--back has-base-2-background-color has-background" style="border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Real-time Analytics</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--20)">Track every metric that matters with live dashboards. Get instant insights into user behavior, conversion rates, and performance metrics.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#8b5cf6"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#8b5cf6;margin-top:var(--wp--preset--spacing--20);font-style:normal;font-weight:600">Learn more →</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face --></div></div>
<!-- /wp:airo-wp/flip-card -->

<!-- wp:airo-wp/flip-card -->
<div class="wp-block-airo-wp-flip-card airo-wp-flip-card airo-wp-flip-card--hover airo-wp-flip-card--effect-flip airo-wp-flip-card--horizontal" style="--airo-wp-flip-duration:0.6s" data-flip-trigger="hover" data-flip-effect="flip" data-flip-direction="horizontal"><div class="airo-wp-flip-card__container"><!-- wp:airo-wp/flip-card-face {"side":"front","className":"airo-wp-flip-card__face\u002d\u002dfront","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"16px"},"color":{"gradient":"linear-gradient(135deg,rgb(6,182,212) 0%,rgb(59,130,246) 100%)"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__front airo-wp-flip-card__face--front has-background" style="border-radius:16px;background:linear-gradient(135deg,rgb(6,182,212) 0%,rgb(59,130,246) 100%);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:airo-wp/icon {"icon":"users","iconSize":56,"style":{"color":{"text":"#ffffff"}}} /-->
<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size" style="margin-top:var(--wp--preset--spacing--30)">Team Collaboration</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}},"color":{"text":"rgba(255,255,255,0.8)"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:rgba(255,255,255,0.8);margin-top:var(--wp--preset--spacing--10)">Hover to learn more</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face -->

<!-- wp:airo-wp/flip-card-face {"side":"back","className":"airo-wp-flip-card__face\u002d\u002dback","backgroundColor":"base-2","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"16px"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__back airo-wp-flip-card__face--back has-base-2-background-color has-background" style="border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Team Collaboration</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--20)">Work together seamlessly with real-time editing, comments, and shared workspaces. Keep everyone aligned and moving fast.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#ec4899"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#ec4899;margin-top:var(--wp--preset--spacing--20);font-style:normal;font-weight:600">Learn more →</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face --></div></div>
<!-- /wp:airo-wp/flip-card -->

<!-- wp:airo-wp/flip-card -->
<div class="wp-block-airo-wp-flip-card airo-wp-flip-card airo-wp-flip-card--hover airo-wp-flip-card--effect-flip airo-wp-flip-card--horizontal" style="--airo-wp-flip-duration:0.6s" data-flip-trigger="hover" data-flip-effect="flip" data-flip-direction="horizontal"><div class="airo-wp-flip-card__container"><!-- wp:airo-wp/flip-card-face {"side":"front","className":"airo-wp-flip-card__face\u002d\u002dfront","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"16px"},"color":{"gradient":"linear-gradient(135deg,rgb(236,72,153) 0%,rgb(239,68,68) 100%)"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__front airo-wp-flip-card__face--front has-background" style="border-radius:16px;background:linear-gradient(135deg,rgb(236,72,153) 0%,rgb(239,68,68) 100%);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:airo-wp/icon {"icon":"lightning","iconSize":56,"style":{"color":{"text":"#ffffff"}}} /-->
<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size" style="margin-top:var(--wp--preset--spacing--30)">Automation</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}},"color":{"text":"rgba(255,255,255,0.8)"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:rgba(255,255,255,0.8);margin-top:var(--wp--preset--spacing--10)">Hover to learn more</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face -->

<!-- wp:airo-wp/flip-card-face {"side":"back","className":"airo-wp-flip-card__face\u002d\u002dback","backgroundColor":"base-2","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"16px"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__back airo-wp-flip-card__face--back has-base-2-background-color has-background" style="border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Automation</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--20)">Automate repetitive tasks and workflows. Set up triggers, actions, and rules that work while you sleep.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#ec4899"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#ec4899;margin-top:var(--wp--preset--spacing--20);font-style:normal;font-weight:600">Learn more →</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face --></div></div>
<!-- /wp:airo-wp/flip-card --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"shapeDividerTop":"tilt","shapeDividerTopColor":"#ffffff","shapeDividerTopHeight":80,"shapeDividerTopFlipY":true,"shapeDividerBottom":"tilt-reverse","shapeDividerBottomColor":"#ffffff","shapeDividerBottomHeight":80,"shapeDividerBottomFlipY":true,"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-stack--has-shape-divider has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-shape-divider airo-wp-shape-divider--top is-shape-tilt is-flip-y" style="--airo-wp-shape-height:80px" aria-hidden="true"></div><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto;padding-top:80px;padding-bottom:80px"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInLeft","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInLeft" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInLeft" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#8b5cf6"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#8b5cf6;letter-spacing:3px;text-transform:uppercase">Why Choose Us</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Built for Teams Who Ship</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#94a3b8;margin-top:var(--wp--preset--spacing--20)">We understand the challenges of modern product development. That is why we built a platform that gets out of your way and lets you focus on what matters.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/counter-group {"columns":2,"animationDuration":2000,"animationDelay":200,"animationEasing":"easeOutExpo","className":"airo-wp-counter-group-cols-2 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-1","textColor":"base","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group airo-wp-counter-group-cols-2 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-1 has-base-color has-text-color" style="margin-top:var(--wp--preset--spacing--40);align-self:stretch;--airo-wp-counter-columns-desktop:2;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2000" data-animation-delay="200" data-animation-easing="easeOutExpo" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"saas-1","endValue":99,"suffix":"%","label":"Uptime SLA"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="saas-1" style="text-align:center" data-start-value="0" data-end-value="99" data-decimals="0" data-prefix="" data-suffix="%" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Uptime SLA</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"saas-2","endValue":50,"suffix":"ms","label":"Avg Response"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="saas-2" style="text-align:center" data-start-value="0" data-end-value="50" data-decimals="0" data-prefix="" data-suffix="ms" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Avg Response</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"saas-3","endValue":10,"suffix":"K+","label":"Active Users"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="saas-3" style="text-align:center" data-start-value="0" data-end-value="10" data-decimals="0" data-prefix="" data-suffix="K+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Active Users</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"saas-4","endValue":24,"suffix":"/7","label":"Support"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="saas-4" style="text-align:center" data-start-value="0" data-end-value="24" data-decimals="0" data-prefix="" data-suffix="/7" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Support</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInRight","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInRight" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInRight" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}},"dsgoParallaxEnabled":true} -->
<figure class="wp-block-image size-large has-custom-border airo-wp-has-parallax" data-airo-wp-parallax-enabled="true" data-airo-wp-parallax-direction="up" data-airo-wp-parallax-speed="5" data-airo-wp-parallax-viewport-start="0" data-airo-wp-parallax-viewport-end="100" data-airo-wp-parallax-relative-to="viewport" data-airo-wp-parallax-desktop="true" data-airo-wp-parallax-tablet="true" data-airo-wp-parallax-mobile="false" data-airo-wp-parallax-rotate-enabled="false" data-airo-wp-parallax-rotate-direction="cw" data-airo-wp-parallax-rotate-speed="3"><img src="{{dsgo:placeholder-landscape}}" alt="Analytics dashboard" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div><div class="airo-wp-shape-divider airo-wp-shape-divider--bottom is-shape-tilt-reverse" style="--airo-wp-shape-height:80px" aria-hidden="true"></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#8b5cf6"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#8b5cf6;letter-spacing:3px;text-transform:uppercase">Product Showcase</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">See It in Action</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/image-accordion {"gap":"8px","overlayOpacity":30,"overlayOpacityExpanded":10} -->
<div class="wp-block-airo-wp-image-accordion airo-wp-image-accordion airo-wp-image-accordion--hover" style="--airo-wp-image-accordion-gap:8px;--airo-wp-image-accordion-expanded-ratio:3;--airo-wp-image-accordion-transition:0.5s;--airo-wp-image-accordion-overlay-opacity:0.3;--airo-wp-image-accordion-overlay-opacity-expanded:0.1" data-trigger-type="hover" data-default-expanded="0" data-enable-overlay="true"><div class="airo-wp-image-accordion__items"><!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-1","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-1" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Dashboard</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Powerful analytics at your fingertips</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-2","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-2" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Reports</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Generate insights in seconds</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-3","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-3" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Team Hub</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Collaborate in real-time</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-4","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-4" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Integrations</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Connect with 100+ tools</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item --></div></div>
<!-- /wp:airo-wp/image-accordion --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#8b5cf6"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#8b5cf6;letter-spacing:3px;text-transform:uppercase">Pricing</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Simple, Transparent Pricing</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--30);column-gap:var(--wp--preset--spacing--30)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDuration":500} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-duration="500"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-text-align-center has-large-font-size">Starter</h3>
<!-- /wp:heading -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"},"blockGap":"0","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--20);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:nowrap;gap:0"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#64748b;font-style:normal;font-weight:400">$</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"xx-large"} -->
<p class="has-xx-large-font-size" style="font-style:normal;font-weight:700">0</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"color":{"text":"#64748b"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#64748b;font-style:normal;font-weight:400">/month</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"/>
<!-- /wp:separator -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"var:preset|spacing|15"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Up to 3 team members</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Basic analytics</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">1GB storage</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Community support</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/icon-button {"justification":"center","url":"#starter","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|30"}},"border":{"radius":"8px","width":"1px","color":"#e2e8f0"},"color":{"background":"transparent","text":"#0f172a"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--center has-text-color has-background" style="margin-top:var(--wp--preset--spacing--30)"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:#e2e8f0;border-width:1px;border-radius:8px;color:#0f172a;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#starter" target="_self"><span class="airo-wp-icon-button__text">Get Started Free</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px","width":"2px","color":"#8b5cf6"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDuration":500,"dsgoAnimationDelay":100} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-border-color has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-color:#8b5cf6;border-width:2px;border-radius:16px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-duration="500" data-airo-wp-animation-delay="100"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/pill {"content":"Most Popular","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|20","right":"var:preset|spacing|20"},"margin":{"bottom":"var:preset|spacing|20"}},"border":{"radius":"50px"},"color":{"background":"#8b5cf6","text":"#ffffff"}}} /-->

<!-- wp:heading {"textAlign":"center","level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-text-align-center has-large-font-size">Pro</h3>
<!-- /wp:heading -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"},"blockGap":"0","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--20);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:nowrap;gap:0"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#64748b;font-style:normal;font-weight:400">$</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"xx-large"} -->
<p class="has-xx-large-font-size" style="font-style:normal;font-weight:700">29</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"400"},"color":{"text":"#64748b"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#64748b;font-style:normal;font-weight:400">/month</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"/>
<!-- /wp:separator -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"var:preset|spacing|15"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Unlimited team members</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Advanced analytics</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">100GB storage</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Priority support</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">API access</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/icon-button {"justification":"center","url":"#pro","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|30"}},"border":{"radius":"8px"},"color":{"background":"#8b5cf6","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--center has-text-color has-background" style="margin-top:var(--wp--preset--spacing--30)"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background" style="border-radius:8px;color:#ffffff;background-color:#8b5cf6;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#pro" target="_self"><span class="airo-wp-icon-button__text">Start Free Trial</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDuration":500,"dsgoAnimationDelay":200} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-duration="500" data-airo-wp-animation-delay="200"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-text-align-center has-large-font-size">Enterprise</h3>
<!-- /wp:heading -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"},"blockGap":"0","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--20);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:nowrap;gap:0"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"xx-large"} -->
<p class="has-xx-large-font-size" style="font-style:normal;font-weight:700">Custom</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"/>
<!-- /wp:separator -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"var:preset|spacing|15"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Everything in Pro</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Custom integrations</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Unlimited storage</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">Dedicated support</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size">SLA guarantee</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/icon-button {"justification":"center","url":"#enterprise","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|30"}},"border":{"radius":"8px","width":"1px","color":"#e2e8f0"},"color":{"background":"transparent","text":"#0f172a"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--center has-text-color has-background" style="margin-top:var(--wp--preset--spacing--30)"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:#e2e8f0;border-width:1px;border-radius:8px;color:#0f172a;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#enterprise" target="_self"><span class="airo-wp-icon-button__text">Contact Sales</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"shapeDividerTop":"curve","shapeDividerTopFlipY":true,"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-stack--has-shape-divider has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-shape-divider airo-wp-shape-divider--top is-shape-curve is-flip-y" aria-hidden="true"></div><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto;padding-top:100px"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:700">Ready to Transform Your Workflow?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-align-center has-text-color has-medium-font-size" style="color:#94a3b8;margin-top:var(--wp--preset--spacing--20)">Join thousands of teams already using our platform to ship better products faster. Start your free trial today.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"url":"#trial","icon":"arrow-right","iconPosition":"end","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"8px"},"color":{"background":"#8b5cf6","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:8px;color:#ffffff;background-color:#8b5cf6;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#trial" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Start Free Trial</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"url":"#sales","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"8px","width":"1px","color":"#475569"},"color":{"background":"transparent","text":"#e2e8f0"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:#475569;border-width:1px;border-radius:8px;color:#e2e8f0;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#sales" target="_self"><span class="airo-wp-icon-button__text">Talk to Sales</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section -->',
);
