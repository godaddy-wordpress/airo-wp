<?php
/**
 * Title: Property Image Accordion
 * Slug: airo-wp/gallery/gallery-property-showcase
 * Categories: airo-wp-gallery
 * Description: An interactive image accordion showcasing luxury property listings with expandable panels
 * Keywords: gallery, properties, image accordion, real estate, luxury, showcase
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Property Image Accordion', 'airo-wp' ),
	'categories' => array( 'airo-wp-gallery' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"shapeDividerTop":"tilt","shapeDividerTopHeight":60,"shapeDividerTopFlipY":true,"shapeDividerBottom":"tilt-reverse","shapeDividerBottomHeight":60,"shapeDividerBottomFlipY":true,"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-stack--has-shape-divider has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-shape-divider airo-wp-shape-divider--top is-shape-tilt is-flip-y" style="--airo-wp-shape-height:60px" aria-hidden="true"></div><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto;padding-top:60px;padding-bottom:60px"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"4px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:4px;text-transform:uppercase">Featured Listings</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"300","letterSpacing":"-0.5px"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:300;letter-spacing:-0.5px">Exceptional Properties Await</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/image-accordion {"height":"550px","overlayOpacityExpanded":15} -->
<div class="wp-block-airo-wp-image-accordion airo-wp-image-accordion airo-wp-image-accordion--hover" style="--airo-wp-image-accordion-height:550px;--airo-wp-image-accordion-expanded-ratio:3;--airo-wp-image-accordion-transition:0.5s;--airo-wp-image-accordion-overlay-opacity-expanded:0.15" data-trigger-type="hover" data-default-expanded="0" data-enable-overlay="true"><div class="airo-wp-image-accordion__items"><!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-1","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-landscape}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="prop-1" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:3px;text-transform:uppercase">Beverly Hills</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Modern Estate</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">$12,500,000 | 6 Bed | 8 Bath</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-2","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-landscape}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="prop-2" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:3px;text-transform:uppercase">Malibu</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Oceanfront Villa</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">$28,000,000 | 5 Bed | 7 Bath</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-3","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-landscape}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="prop-3" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:3px;text-transform:uppercase">Bel Air</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Contemporary Mansion</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">$45,000,000 | 8 Bed | 12 Bath</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item -->

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-4","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-landscape}}"}}}} -->
<div class="wp-block-airo-wp-image-accordion-item airo-wp-image-accordion-item airo-wp-image-accordion-item--has-overlay airo-wp-image-accordion__item" style="--airo-wp-vertical-alignment:center;--airo-wp-horizontal-alignment:center" data-unique-id="prop-4" role="button" tabindex="0"><div class="airo-wp-image-accordion-item__content"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:3px;text-transform:uppercase">Hollywood Hills</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size">Architectural Masterpiece</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"base","fontSize":"small"} -->
<p class="has-base-color has-text-color has-small-font-size">$18,900,000 | 5 Bed | 6 Bath</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/image-accordion-item --></div></div>
<!-- /wp:airo-wp/image-accordion -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"text":"View All Properties","url":"#all-properties","icon":"arrow-right","iconPosition":"end","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0"},"color":{"background":"#d4af37","text":"#0f172a"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:0;color:#0f172a;background-color:#d4af37;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#all-properties" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">View All Properties</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div><div class="airo-wp-shape-divider airo-wp-shape-divider--bottom is-shape-tilt-reverse" style="--airo-wp-shape-height:60px" aria-hidden="true"></div></div>
<!-- /wp:airo-wp/section -->',
);
