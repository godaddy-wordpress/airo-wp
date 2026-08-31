<?php
/**
 * Title: Pricing Cards
 * Slug: airo-wp/pricing/pricing-cards
 * Categories: airo-wp-pricing
 * Description: A bold 3-tier pricing section with highlighted popular plan
 * Keywords: pricing, cards, tiers, popular, bold
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Pricing Cards', 'airo-wp' ),
	'categories' => array( 'airo-wp-pricing' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"metadata":{"categories":["airo-wp-pricing"],"patternName":"airo-wp/pricing/pricing-cards","name":"Pricing Cards"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:700">Simple, Transparent Pricing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">Choose the plan that fits your needs. Upgrade or downgrade anytime.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--40);column-gap:var(--wp--preset--spacing--40)"><!-- wp:airo-wp/card {"layoutPreset":"minimal","visualStyle":"outlined","showImage":false,"showBadge":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px","width":"2px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-outlined" style="border-width:2px;border-radius:16px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Starter</h3><p class="airo-wp-card__subtitle">$9/month</p><p class="airo-wp-card__body">Perfect for individuals and small projects getting started.</p><div class="airo-wp-card__cta"><!-- wp:airo-wp/icon-button {"url":"#","icon":"","iconPosition":"none","iconGap":"8px","backgroundColor":"transparent","textColor":"contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px","width":"2px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-contrast-color has-transparent-background-color has-text-color has-background" style="border-width:2px;border-radius:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#" target="_self"><span class="airo-wp-icon-button__text">Get Started</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div></div></div>
<!-- /wp:airo-wp/card -->

<!-- wp:airo-wp/card {"layoutPreset":"minimal","visualStyle":"filled","showImage":false,"backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-filled has-base-color has-contrast-background-color has-text-color has-background" style="border-radius:16px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><span class="airo-wp-card__badge airo-wp-card__badge--floating airo-wp-card__badge--top-right" role="status" aria-label="Badge">Most Popular</span><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Professional</h3><p class="airo-wp-card__subtitle">$29/month</p><p class="airo-wp-card__body">For growing teams that need more power and flexibility.</p><div class="airo-wp-card__cta"><!-- wp:airo-wp/icon-button {"url":"#","icon":"","iconPosition":"none","iconGap":"8px","backgroundColor":"base","textColor":"contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-contrast-color has-base-background-color has-text-color has-background" style="border-radius:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#" target="_self"><span class="airo-wp-icon-button__text">Get Started</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div></div></div>
<!-- /wp:airo-wp/card -->

<!-- wp:airo-wp/card {"layoutPreset":"minimal","visualStyle":"outlined","showImage":false,"showBadge":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px","width":"2px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-outlined" style="border-width:2px;border-radius:16px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Enterprise</h3><p class="airo-wp-card__subtitle">$99/month</p><p class="airo-wp-card__body">For large organizations with advanced needs and support.</p><div class="airo-wp-card__cta"><!-- wp:airo-wp/icon-button {"url":"#","icon":"","iconPosition":"none","iconGap":"8px","backgroundColor":"transparent","textColor":"contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px","width":"2px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-contrast-color has-transparent-background-color has-text-color has-background" style="border-width:2px;border-radius:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#" target="_self"><span class="airo-wp-icon-button__text">Contact Sales</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div></div></div>
<!-- /wp:airo-wp/card --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
