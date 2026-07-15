<?php
/**
 * Title: Growth Stats Counters
 * Slug: airo-wp/content/content-growth-stats
 * Categories: airo-wp-content
 * Description: A statistics section with animated counters showing company growth and trust metrics
 * Keywords: stats, counters, growth, metrics, saas, trust
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Growth Stats Counters', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"gradient":"vivid-cyan-blue-to-vivid-purple"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-vivid-cyan-blue-to-vivid-purple-gradient-background has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","textColor":"base","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color has-x-large-font-size">Trusted by Growing Companies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"medium"} -->
<p class="has-text-align-center has-base-color has-text-color has-medium-font-size">See the impact we have made together</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/counter-group {"columns":4,"animationDuration":2000,"animationDelay":200,"animationEasing":"easeOutExpo","className":"airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-1","textColor":"base"} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-1 has-base-color has-text-color" style="align-self:stretch;--airo-wp-counter-columns-desktop:4;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2000" data-animation-delay="200" data-animation-easing="easeOutExpo" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"counter-saas-1","endValue":50000,"suffix":"+","label":"Active Users","className":"airo-wp-counter\\u002d\\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--align-center" id="counter-saas-1" style="text-align:center" data-start-value="0" data-end-value="50000" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Active Users</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-saas-2","endValue":99,"suffix":"%","label":"Uptime SLA","className":"airo-wp-counter\\u002d\\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--align-center" id="counter-saas-2" style="text-align:center" data-start-value="0" data-end-value="99" data-decimals="0" data-prefix="" data-suffix="%" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Uptime SLA</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-saas-3","endValue":150,"suffix":"+","label":"Integrations","className":"airo-wp-counter\\u002d\\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--align-center" id="counter-saas-3" style="text-align:center" data-start-value="0" data-end-value="150" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Integrations</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-saas-4","endValue":24,"suffix":"/7","label":"Support","className":"airo-wp-counter\\u002d\\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--align-center" id="counter-saas-4" style="text-align:center" data-start-value="0" data-end-value="24" data-decimals="0" data-prefix="" data-suffix="/7" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Support</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section -->',
);
