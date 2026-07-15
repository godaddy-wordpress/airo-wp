<?php
/**
 * Title: Professional Services Hero
 * Slug: airo-wp/hero/hero-professional-services
 * Categories: airo-wp-hero
 * Description: A two-column hero for professional services firms with credentials, trust messaging, and consultation CTA
 * Keywords: hero, professional, services, law, consulting, credentials
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Professional Services Hero', 'airo-wp' ),
	'categories' => array( 'airo-wp-hero' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2","metadata":{"categories":["airo-wp-homepage"],"patternName":"airo-wp/homepage/homepage-professional","name":"Professional Services Homepage"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"2px"},"color":{"text":"#2563eb"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#2563eb;letter-spacing:2px;text-transform:uppercase">Trusted Advisors Since 1995</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","lineHeight":"1.2"},"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size" style="margin-top:var(--wp--preset--spacing--20);font-style:normal;font-weight:700;line-height:1.2">Expert Guidance for Your Most Important Decisions</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--30)">We provide strategic counsel to businesses and individuals, helping you navigate complex challenges with confidence and achieve your goals.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"text":"Schedule Consultation","url":"#contact","icon":"calendar","iconGap":"8px","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"4px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-base-color has-contrast-background-color has-text-color has-background airo-wp-icon-button--has-icon" style="border-radius:4px;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#contact" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="calendar"></span><span class="airo-wp-icon-button__text">Schedule Consultation</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"text":"Call (555) 123-4567","url":"tel:5551234567","icon":"phone","iconGap":"8px","backgroundColor":"transparent","textColor":"contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"4px","width":"2px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-contrast-color has-transparent-background-color has-text-color has-background airo-wp-icon-button--has-icon" style="border-width:2px;border-radius:4px;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="tel:5551234567" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="phone"></span><span class="airo-wp-icon-button__text">Call (555) 123-4567</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"8px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Professional office" style="border-radius:8px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
