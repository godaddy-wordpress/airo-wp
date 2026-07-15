<?php
/**
 * Title: Impact Stories Slider
 * Slug: airo-wp/content/content-impact-stories
 * Categories: airo-wp-content
 * Description: A story slider showcasing real impact stories with images, titles, and descriptions
 * Keywords: stories, impact, slider, nonprofit, charity, testimonials
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Impact Stories Slider', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#10b981"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#10b981;letter-spacing:3px;text-transform:uppercase">Success Stories</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Real Impact, Real Stories</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/slider {"slidesPerView":3,"autoplay":true,"autoplayInterval":6000} -->
<div class="wp-block-airo-wp-slider airo-wp-slider airo-wp-slider--classic airo-wp-slider--effect-slide airo-wp-slider--has-arrows airo-wp-slider--has-dots" style="--airo-wp-slider-height:500px;--airo-wp-slider-aspect-ratio:16/9;--airo-wp-slider-gap:20px;--airo-wp-slider-transition:0.5s;--airo-wp-slider-slides-per-view:3;--airo-wp-slider-slides-per-view-tablet:1;--airo-wp-slider-slides-per-view-mobile:1;--airo-wp-slider-arrow-size:48px" data-slides-per-view="3" data-slides-per-view-tablet="1" data-slides-per-view-mobile="1" data-use-aspect-ratio="false" data-show-arrows="true" data-show-dots="true" data-arrow-style="default" data-arrow-position="sides" data-arrow-vertical-position="center" data-dot-style="default" data-dot-position="bottom" data-effect="slide" data-transition-duration="0.5s" data-transition-easing="ease-in-out" data-autoplay="true" data-autoplay-interval="6000" data-pause-on-hover="true" data-pause-on-interaction="true" data-loop="true" data-draggable="true" data-swipeable="true" data-free-mode="false" data-centered-slides="false" data-mobile-breakpoint="768" data-tablet-breakpoint="1024" data-active-slide="0" role="region" aria-label="Image slider" aria-roledescription="slider"><div class="airo-wp-slider__viewport"><div class="airo-wp-slider__track"><!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"8px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Maria with her family" style="border-radius:8px"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Maria\\\'s Journey to Self-Sufficiency</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--10)">"Thanks to the microloan program, I was able to start my own tailoring business. Now I can provide for my children and send them to school. This organization gave me hope when I had none."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic","fontWeight":"500"},"color":{"text":"#10b981"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#10b981;margin-top:var(--wp--preset--spacing--10);font-style:italic;font-weight:500">— Maria, Guatemala</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"8px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Village with clean water" style="border-radius:8px"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Clean Water for Kibera Village</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--10)">"Before the well was built, we walked 3 miles every day for water. Now our children are healthier and can focus on their education instead of carrying water."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic","fontWeight":"500"},"color":{"text":"#10b981"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#10b981;margin-top:var(--wp--preset--spacing--10);font-style:italic;font-weight:500">— Village Elder, Kenya</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"8px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Students in classroom" style="border-radius:8px"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">First in Her Family to Graduate</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--10)">"The scholarship changed everything. I became the first person in my family to finish high school. Now I am studying to become a doctor so I can help my community."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic","fontWeight":"500"},"color":{"text":"#10b981"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#10b981;margin-top:var(--wp--preset--spacing--10);font-style:italic;font-weight:500">— Amara, Nigeria</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide --></div></div></div>
<!-- /wp:airo-wp/slider --></div></div>
<!-- /wp:airo-wp/section -->',
);
