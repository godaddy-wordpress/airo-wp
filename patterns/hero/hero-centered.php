<?php
/**
 * Title: Hero Centered
 * Slug: airo-wp/hero/hero-centered
 * Categories: airo-wp-hero
 * Description: A centered hero section with animated blob background, headline, and CTA buttons
 * Keywords: hero, centered, blob, cta, landing
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Hero Centered', 'airo-wp' ),
	'categories' => array( 'airo-wp-hero' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"12rem","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2","metadata":{"categories":["airo-wp-hero"],"patternName":"airo-wp/hero/hero-centered","name":"Hero Centered"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:12rem;padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/blobs {"align":"center","blobShape":"shape-2","blobAnimation":"morph-1","size":"400px","style":{"spacing":{"margin":{"bottom":"0"}}}} -->
<div class="wp-block-airo-wp-blobs aligncenter airo-wp-blobs-wrapper" style="margin-bottom:0"><div class="airo-wp-blobs airo-wp-blobs--shape-2 airo-wp-blobs--morph-1" style="--airo-wp-blob-size:400px;--airo-wp-blob-animation-duration:8s;--airo-wp-blob-animation-easing:ease-in-out" data-blob-animation="morph-1"><div class="airo-wp-blobs__shape"><div class="airo-wp-blobs__content"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} -->
<h1 class="wp-block-heading has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:700">Build Something Amazing</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size" style="margin-top:var(--wp--preset--spacing--30)">Create stunning websites with powerful blocks designed for modern WordPress.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-button {"text":"Get Started","url":"#","icon":"arrow-right","iconPosition":"end","iconGap":"8px","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"4px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-base-color has-contrast-background-color has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:4px;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Get Started</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div></div></div>
<!-- /wp:airo-wp/blobs --></div></div>
<!-- /wp:airo-wp/section -->',
);
