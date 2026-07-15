<?php
/**
 * Title: Product Demo Showcase
 * Slug: airo-wp/content/content-product-demo
 * Categories: airo-wp-content
 * Description: An image accordion section showcasing product screenshots and demo views
 * Keywords: product, demo, showcase, screenshots, image accordion, saas
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Product Demo Showcase', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#8b5cf6"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#8b5cf6;letter-spacing:3px;text-transform:uppercase">Product Showcase</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">See It in Action</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/image-accordion {"gap":"8px","overlayOpacity":30,"overlayOpacityExpanded":10} -->
<div class="wp-block-airo-wp-image-accordion airo-wp-image-accordion airo-wp-image-accordion--hover" style="--airo-wp-image-accordion-gap:8px;--airo-wp-image-accordion-expanded-ratio:3;--airo-wp-image-accordion-transition:0.5s;--airo-wp-image-accordion-overlay-opacity:0.3;--airo-wp-image-accordion-overlay-opacity-expanded:0.1" data-trigger-type="hover" data-default-expanded="0" data-enable-overlay="true"><div class="airo-wp-image-accordion__items"><!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-1","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-1" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Dashboard</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Powerful analytics at your fingertips</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-2","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-2" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Reports</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Generate insights in seconds</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-3","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-3" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Team Hub</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Collaborate in real-time</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"acc-4","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="acc-4" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Integrations</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">Connect with 100+ tools</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item --></div></div>
<!-- /wp:airo-wp/image-accordion --></div></div>
<!-- /wp:airo-wp/section -->',
);
