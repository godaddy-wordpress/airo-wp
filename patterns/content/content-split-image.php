<?php
/**
 * Title: Split Content with Image
 * Slug: airo-wp/content/content-split-image
 * Categories: airo-wp-content
 * Description: Two-column layout with content and image
 * Keywords: split, image, content, two-column, text
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Split Content with Image', 'airo-wp' ),
	'categories'    => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-split-image","name":"Split Content with Image"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|60","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--60);column-gap:var(--wp--preset--spacing--60)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInLeft"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInLeft" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInLeft"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/pill {"content":"Why Choose Us","justification":"left","backgroundColor":"accent-3","textColor":"#ffffff","fontSize":"small","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->

<!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">We Build Solutions That Last</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--30)">Our team combines creativity with technical expertise to deliver exceptional results that drive real business growth.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":20,"iconColor":"#10b981","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="margin-bottom:var(--wp--preset--spacing--40);width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">10+ years of industry experience</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Award-winning design team</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">24/7 dedicated support</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">100% satisfaction guarantee</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","backgroundColor":"contrast","textColor":"base","style":{"border":{"radius":"8px"},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-base-color has-contrast-background-color has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:8px;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn More</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInRight"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInRight" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInRight"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Team collaboration" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
