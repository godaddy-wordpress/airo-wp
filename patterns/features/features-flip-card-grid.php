<?php
/**
 * Title: Features Flip Card Grid
 * Slug: airo-wp/features/features-flip-card-grid
 * Categories: airo-wp-features
 * Description: A three-column feature grid using interactive flip cards with icons and detailed descriptions
 * Keywords: features, flip cards, grid, interactive, saas, hover
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Features Flip Card Grid', 'airo-wp' ),
	'categories' => array( 'airo-wp-features' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
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
<!-- /wp:airo-wp/section -->',
);
