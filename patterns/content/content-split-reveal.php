<?php
/**
 * Title: Content Split Reveal
 * Slug: airo-wp/content/content-split-reveal
 * Categories: airo-wp-content
 * Description: A split content section with text reveal animation, parallax image, and animated counters
 * Keywords: content, split, text reveal, parallax, counters, animation
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Content Split Reveal', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-split-reveal","name":"Content Split Reveal"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInLeft","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInLeft" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInLeft" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#6366f1"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#6366f1;letter-spacing:3px;text-transform:uppercase">About Us</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large","dsgoTextRevealEnabled":true,"dsgoTextRevealColor":"#6366f1","dsgoTextRevealSplitMode":"words"} -->
<h2 class="wp-block-heading has-x-large-font-size has-airo-wp-text-reveal" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700" data-airo-wp-text-reveal-enabled="true" data-airo-wp-text-reveal-color="#6366f1" data-airo-wp-text-reveal-split-mode="words" data-airo-wp-text-reveal-transition="150">We Build Digital Experiences That Matter</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#64748b;margin-top:var(--wp--preset--spacing--20)">For over a decade, we have been helping businesses transform their digital presence. Our team of experts combines creativity with technical excellence to deliver solutions that drive real results.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}},"color":{"text":"#64748b"}}} -->
<p class="has-text-color" style="color:#64748b;margin-top:var(--wp--preset--spacing--15)">We believe in building lasting partnerships with our clients, understanding their unique challenges, and crafting tailored solutions that exceed expectations.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/counter-group {"animationDuration":2000,"animationEasing":"easeOutExpo","className":"airo-wp-counter-group-cols-3 airo-wp-counter-group-cols-tablet-3 airo-wp-counter-group-cols-mobile-3","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group airo-wp-counter-group-cols-3 airo-wp-counter-group-cols-tablet-3 airo-wp-counter-group-cols-mobile-3" style="margin-top:var(--wp--preset--spacing--40);align-self:stretch;--airo-wp-counter-columns-desktop:3;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2000" data-animation-delay="0" data-animation-easing="easeOutExpo" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"sr-1","endValue":150,"suffix":"+","label":"Projects Delivered"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="sr-1" style="text-align:center" data-start-value="0" data-end-value="150" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Projects Delivered</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"sr-2","endValue":50,"suffix":"+","label":"Team Members"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="sr-2" style="text-align:center" data-start-value="0" data-end-value="50" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Team Members</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"sr-3","endValue":10,"label":"Years of Experience"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="sr-3" style="text-align:center" data-start-value="0" data-end-value="10" data-decimals="0" data-prefix="" data-suffix="" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Years of Experience</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInRight","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInRight" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInRight" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}},"dsgoParallaxEnabled":true,"dsgoParallaxSpeed":4} -->
<figure class="wp-block-image size-large has-custom-border airo-wp-has-parallax" data-airo-wp-parallax-enabled="true" data-airo-wp-parallax-direction="up" data-airo-wp-parallax-speed="4" data-airo-wp-parallax-viewport-start="0" data-airo-wp-parallax-viewport-end="100" data-airo-wp-parallax-relative-to="viewport" data-airo-wp-parallax-desktop="true" data-airo-wp-parallax-tablet="true" data-airo-wp-parallax-mobile="false" data-airo-wp-parallax-rotate-enabled="false" data-airo-wp-parallax-rotate-direction="cw" data-airo-wp-parallax-rotate-speed="3"><img src="{{dsgo:placeholder-portrait}}" alt="Team collaboration" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
