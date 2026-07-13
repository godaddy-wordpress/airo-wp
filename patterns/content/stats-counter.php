<?php
/**
 * Title: Stats Counter
 * Slug: airo-wp/content/stats-counter
 * Categories: airo-wp-content
 * Description: A bold animated statistics section with counters
 * Keywords: stats, counter, numbers, animated, bold
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Stats Counter', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"gradient":"vivid-cyan-blue-to-vivid-purple","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/stats-counter","name":"Stats Counter"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-vivid-cyan-blue-to-vivid-purple-gradient-background has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","textColor":"base"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color">By the Numbers</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"medium"} -->
<p class="has-text-align-center has-base-color has-text-color has-medium-font-size">See the impact we have made together</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/counter-group {"columns":4,"animationDuration":2000,"animationDelay":200,"animationEasing":"easeOutExpo","className":"airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-1","textColor":"base","style":{"elements":{"link":{"color":{"text":"var:preset|color|base"}}}}} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-1 has-base-color has-text-color has-link-color" style="align-self:stretch;--airo-wp-counter-columns-desktop:4;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2000" data-animation-delay="200" data-animation-easing="easeOutExpo" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"counter-iarizr97y","endValue":50000,"suffix":"+","label":"Active Users","icon":"users","className":"airo-wp-counter\u002d\u002dicon-top airo-wp-counter\u002d\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--icon-top airo-wp-counter--align-center" id="counter-iarizr97y" style="text-align:center" data-start-value="0" data-end-value="50000" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Active Users</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-4pl2caks8","endValue":1000000,"suffix":"+","label":"Downloads","icon":"download","className":"airo-wp-counter\u002d\u002dicon-top airo-wp-counter\u002d\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--icon-top airo-wp-counter--align-center" id="counter-4pl2caks8" style="text-align:center" data-start-value="0" data-end-value="1000000" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Downloads</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-leshz1voy","endValue":99,"suffix":"%","label":"Satisfaction Rate","className":"airo-wp-counter\u002d\u002dicon-top airo-wp-counter\u002d\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--icon-top airo-wp-counter--align-center" id="counter-leshz1voy" style="text-align:center" data-start-value="0" data-end-value="99" data-decimals="0" data-prefix="" data-suffix="%" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Satisfaction Rate</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"counter-0xsc86u94","endValue":24,"suffix":"/7","label":"Support Available","icon":"headphones","className":"airo-wp-counter\u002d\u002dicon-top airo-wp-counter\u002d\u002dalign-center"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter airo-wp-counter--icon-top airo-wp-counter--align-center" id="counter-0xsc86u94" style="text-align:center" data-start-value="0" data-end-value="24" data-decimals="0" data-prefix="" data-suffix="/7" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Support Available</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section -->',
);
