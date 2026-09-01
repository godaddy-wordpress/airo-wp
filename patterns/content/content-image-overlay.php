<?php
/**
 * Title: Image with Text Overlay
 * Slug: airo-wp/content/content-image-overlay
 * Categories: airo-wp-content
 * Description: Full-width image with gradient overlay and text
 * Keywords: image, overlay, gradient, text, banner
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Image with Text Overlay', 'airo-wp' ),
	'categories'    => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"dimensions":{"minHeight":"500px"},"background":{"backgroundImage":{"url":"{{dsgo:placeholder-landscape}}","id":85,"source":"file","title":"bakery"},"backgroundSize":"cover","backgroundAttachment":"fixed"}},"overlayColor":"#111111","backgroundColor":"accent-3","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-image-overlay","name":"Image with Text Overlay"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-stack--has-overlay has-accent-3-background-color has-background has-airo-wp-animation airo-wp-animation-fadeIn" style="min-height:500px;padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30);--airo-wp-overlay-color:#111111;--airo-wp-overlay-opacity:0.8" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":1,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-1 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(1, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 6"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"has-airo-wp-text-reveal"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-text-reveal" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/pill {"content":"Featured","justification":"left","backgroundColor":"#6366f1","textColor":"#ffffff","fontSize":"small","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} /-->

<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"clamp(2rem, 5vw, 3.5rem)","fontWeight":"700","lineHeight":"1.2","fontStyle":"normal"}},"textColor":"base"} -->
<h1 class="wp-block-heading has-base-color has-text-color" style="font-size:clamp(2rem, 5vw, 3.5rem);font-style:normal;font-weight:700;line-height:1.2">Transform Your Business with Modern Solutions</h1>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|40"}},"color":{"text":"rgba(255,255,255,0.8)"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:rgba(255,255,255,0.8);margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--40)">Discover how our cutting-edge technology can help you achieve your goals and stay ahead of the competition.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","orientation":"horizontal","justifyContent":"left","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","backgroundColor":"base","textColor":"contrast","style":{"border":{"radius":"8px"},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-contrast-color has-base-background-color has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:8px;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Get Started</span></button></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"icon":"play-circle","iconGap":"8px","className":"has-text-color","backgroundColor":"accent-6","style":{"border":{"radius":"8px","width":"2px","color":"#ffffff"},"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"color":{"text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-accent-6-background-color has-text-color has-background airo-wp-icon-button--has-icon" style="border-color:#ffffff;border-width:2px;border-radius:8px;color:#ffffff;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="play-circle"></span><span class="airo-wp-icon-button__text">Watch Video</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
