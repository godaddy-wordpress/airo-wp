<?php
/**
 * Title: SaaS Pricing Tiers
 * Slug: airo-wp/pricing/pricing-saas-tiers
 * Categories: airo-wp-pricing
 * Description: A three-tier SaaS pricing section with Starter, Professional, and Enterprise plans with feature lists
 * Keywords: pricing, saas, tiers, plans, subscription, software
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'SaaS Pricing Tiers', 'airo-wp' ),
	'categories' => array( 'airo-wp-pricing' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:700">Simple, Transparent Pricing</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">Choose the plan that fits your needs. Upgrade or downgrade anytime.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--30);column-gap:var(--wp--preset--spacing--30)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Starter</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--10)">Perfect for individuals and small teams getting started.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"xx-large"} -->
<p class="has-xx-large-font-size" style="margin-top:var(--wp--preset--spacing--30);font-style:normal;font-weight:700">$19<span style="font-size:16px;font-weight:400">/month</span></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"/>
<!-- /wp:separator -->

<!-- wp:list {"style":{"spacing":{"padding":{"left":"var:preset|spacing|20"}}}} -->
<ul style="padding-left:var(--wp--preset--spacing--20)" class="wp-block-list"><!-- wp:list-item -->
<li>Up to 5 team members</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>10GB storage</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Basic analytics</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Email support</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px"} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Purchase</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}},"border":{"radius":{"topLeft":"16px","topRight":"16px","bottomLeft":"16px","bottomRight":"16px"}}},"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-color has-contrast-background-color has-text-color has-background has-link-color" style="border-top-left-radius:16px;border-top-right-radius:16px;border-bottom-left-radius:16px;border-bottom-right-radius:16px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/pill {"content":"Most Popular","justification":"left","backgroundColor":"base","textColor":"contrast","fontSize":"small","style":{"spacing":{"padding":{"top":"var:preset|spacing|10","bottom":"var:preset|spacing|10","left":"var:preset|spacing|20","right":"var:preset|spacing|20"}},"border":{"radius":"50px"}}} /-->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="margin-top:var(--wp--preset--spacing--20)">Professional</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--10)">For growing teams that need more power and flexibility.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"xx-large"} -->
<p class="has-xx-large-font-size" style="margin-top:var(--wp--preset--spacing--30);font-style:normal;font-weight:700">$49<span style="font-size:16px;font-weight:400">/month</span></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"/>
<!-- /wp:separator -->

<!-- wp:list {"style":{"spacing":{"padding":{"left":"var:preset|spacing|20"}}}} -->
<ul style="padding-left:var(--wp--preset--spacing--20)" class="wp-block-list"><!-- wp:list-item -->
<li>Up to 20 team members</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>100GB storage</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Advanced analytics</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Priority support</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Custom integrations</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px","backgroundColor":"base","textColor":"contrast","style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-contrast-color has-base-background-color has-text-color has-background has-link-color airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Purchase</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Enterprise</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--10)">For large organizations with advanced needs and custom requirements.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"xx-large"} -->
<p class="has-xx-large-font-size" style="margin-top:var(--wp--preset--spacing--30);font-style:normal;font-weight:700">$149<span style="font-size:16px;font-weight:400">/month</span></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30)"/>
<!-- /wp:separator -->

<!-- wp:list {"style":{"spacing":{"padding":{"left":"var:preset|spacing|20"}}}} -->
<ul style="padding-left:var(--wp--preset--spacing--20)" class="wp-block-list"><!-- wp:list-item -->
<li>Unlimited team members</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Unlimited storage</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Custom analytics</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>24/7 dedicated support</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>SLA guarantee</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:airo-wp/icon-button {"icon":"arrow-right","iconPosition":"end","iconGap":"8px"} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><button class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="gap:8px" type="button"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Purchase</span></button></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
