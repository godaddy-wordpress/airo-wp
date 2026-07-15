<?php
/**
 * Title: Team Grid with Social
 * Slug: airo-wp/team/team-grid-social
 * Categories: airo-wp-team
 * Description: Team members grid with photos and social links
 * Keywords: team, members, social, grid, staff
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Team Grid with Social', 'airo-wp' ),
	'categories' => array( 'airo-wp-team' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"base-2","metadata":{"categories":["airo-wp-team"],"patternName":"airo-wp/team/team-grid-social","name":"Team Grid with Social"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"has-airo-wp-text-reveal"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-text-reveal" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Meet Our Team</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">The talented people behind our success</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"desktopColumns":4,"style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"airo-wp-grid-cols-4"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-4 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(4, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--40);column-gap:var(--wp--preset--spacing--40)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"}},"border":{"radius":"16px"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":{"topLeft":"16px","topRight":"16px","bottomLeft":"0px","bottomRight":"0px"}}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-square}}" alt="John Smith" style="border-top-left-radius:16px;border-top-right-radius:16px;border-bottom-left-radius:0px;border-bottom-right-radius:0px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"0","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner"><!-- wp:heading {"textAlign":"center","level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-text-align-center has-medium-font-size">John Smith</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#6366f1"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#6366f1">CEO &amp; Founder</p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"contrast","iconColorValue":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dcolor\u002d\u002dcontrast)","size":"has-small-icon-size","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
<!-- /wp:social-links --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"}},"border":{"radius":"16px"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":{"topLeft":"16px","topRight":"16px","bottomLeft":"0px","bottomRight":"0px"}}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-square}}" alt="Sarah Johnson" style="border-top-left-radius:16px;border-top-right-radius:16px;border-bottom-left-radius:0px;border-bottom-right-radius:0px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"0","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner"><!-- wp:heading {"textAlign":"center","level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-text-align-center has-medium-font-size">Sarah Johnson</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#6366f1"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#6366f1">CTO</p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"contrast","iconColorValue":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dcolor\u002d\u002dcontrast)","size":"has-small-icon-size","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:social-link {"url":"#","service":"github"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /--></ul>
<!-- /wp:social-links --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"}},"border":{"radius":"16px"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":{"topLeft":"16px","topRight":"16px","bottomLeft":"0px","bottomRight":"0px"}}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-square}}" alt="Mike Chen" style="border-top-left-radius:16px;border-top-right-radius:16px;border-bottom-left-radius:0px;border-bottom-right-radius:0px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"0","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner"><!-- wp:heading {"textAlign":"center","level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-text-align-center has-medium-font-size">Mike Chen</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#6366f1"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#6366f1">Lead Designer</p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"contrast","iconColorValue":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dcolor\u002d\u002dcontrast)","size":"has-small-icon-size","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:social-link {"url":"#","service":"dribbble"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
<!-- /wp:social-links --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"}},"border":{"radius":"16px"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":{"topLeft":"16px","topRight":"16px","bottomLeft":"0px","bottomRight":"0px"}}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-square}}" alt="Emily Davis" style="border-top-left-radius:16px;border-top-right-radius:16px;border-bottom-left-radius:0px;border-bottom-right-radius:0px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"0","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner"><!-- wp:heading {"textAlign":"center","level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-text-align-center has-medium-font-size">Emily Davis</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#6366f1"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#6366f1">Marketing Lead</p>
<!-- /wp:paragraph -->

<!-- wp:social-links {"iconColor":"contrast","iconColorValue":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dcolor\u002d\u002dcontrast)","size":"has-small-icon-size","className":"is-style-logos-only","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only" style="margin-top:var(--wp--preset--spacing--20)"><!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"x"} /--></ul>
<!-- /wp:social-links --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
