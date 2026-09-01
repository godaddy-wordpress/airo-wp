<?php
/**
 * Title: Luxury Real Estate Homepage
 * Slug: airo-wp/homepage/homepage-luxury-realestate
 * Categories: airo-wp-homepage
 * Description: An elegant luxury real estate homepage with parallax effects, image accordion property showcase, sophisticated animations, and premium design
 * Keywords: homepage, real estate, luxury, property, homes, agent, broker, premium
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Luxury Real Estate Homepage', 'airo-wp' ),
	'categories' => array( 'airo-wp-homepage' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:cover {"url":"{{dsgo:placeholder-landscape-wide}}","alt":"Luxury modern home exterior","dimRatio":70,"overlayColor":"contrast","minHeight":750,"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-cover alignfull" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30);min-height:750px"><img class="wp-block-cover__image-background" alt="Luxury modern home exterior" src="{{dsgo:placeholder-landscape-wide}}" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-70 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn","dsgoAnimationDuration":1000} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn" data-airo-wp-animation-duration="1000"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"6px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:6px;text-transform:uppercase">Exceptional Properties</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"300","lineHeight":"1.1","letterSpacing":"-1px"},"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"textColor":"base","fontSize":"xx-large","dsgoTextRevealEnabled":true,"dsgoTextRevealColor":"#d4af37","dsgoTextRevealSplitMode":"words"} -->
<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color has-xx-large-font-size has-airo-wp-text-reveal" style="margin-top:var(--wp--preset--spacing--20);font-style:normal;font-weight:300;letter-spacing:-1px;line-height:1.1" data-airo-wp-text-reveal-enabled="true" data-airo-wp-text-reveal-color="#d4af37" data-airo-wp-text-reveal-split-mode="words" data-airo-wp-text-reveal-transition="150">Discover Your<br>Dream Residence</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}},"color":{"text":"rgba(255,255,255,0.85)"}},"fontSize":"medium"} -->
<p class="has-text-align-center has-text-color has-medium-font-size" style="color:rgba(255,255,255,0.85);margin-top:var(--wp--preset--spacing--30)">Curating the world\'s most prestigious properties for discerning buyers who demand excellence.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDelay":300} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="margin-top:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-delay="300"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"url":"#properties","icon":"arrow-right","iconPosition":"end","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0"},"color":{"background":"#d4af37","text":"#0f172a"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:0;color:#0f172a;background-color:#d4af37;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#properties" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">View Properties</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"url":"#contact","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0","width":"1px","color":"rgba(255,255,255,0.5)"},"color":{"background":"transparent","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:rgba(255,255,255,0.5);border-width:1px;border-radius:0;color:#ffffff;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#contact" target="_self"><span class="airo-wp-icon-button__text">Schedule Consultation</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:cover -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInLeft","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInLeft" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInLeft" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"4px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:4px;text-transform:uppercase">About Us</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"300","letterSpacing":"-0.5px"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:300;letter-spacing:-0.5px">A Legacy of Excellence in Luxury Real Estate</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#64748b;margin-top:var(--wp--preset--spacing--20)">For over two decades, we have been the trusted advisors for clients seeking extraordinary properties. Our deep expertise in luxury markets, combined with unparalleled discretion and personalized service, sets us apart.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#64748b"}}} -->
<p class="has-text-color" style="color:#64748b;margin-top:var(--wp--preset--spacing--20)">We understand that finding the perfect home is about more than square footage and amenities. It is about discovering a residence that reflects your lifestyle, aspirations, and vision for the future.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/counter-group {"columns":2,"animationDuration":2500,"animationDelay":200,"animationEasing":"easeOutExpo","className":"airo-wp-counter-group-cols-2 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-2","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group airo-wp-counter-group-cols-2 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-2" style="margin-top:var(--wp--preset--spacing--40);align-self:stretch;--airo-wp-counter-columns-desktop:2;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2500" data-animation-delay="200" data-animation-easing="easeOutExpo" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"re-1","endValue":2,"prefix":"$","suffix":"B+","label":"In Sales Volume"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="re-1" style="text-align:center" data-start-value="0" data-end-value="2" data-decimals="0" data-prefix="$" data-suffix="B+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">In Sales Volume</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"re-2","endValue":500,"suffix":"+","label":"Properties Sold"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="re-2" style="text-align:center" data-start-value="0" data-end-value="500" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Properties Sold</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInRight","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInRight" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInRight" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"0"}},"dsgoParallaxEnabled":true,"dsgoParallaxSpeed":4} -->
<figure class="wp-block-image size-large has-custom-border airo-wp-has-parallax" data-airo-wp-parallax-enabled="true" data-airo-wp-parallax-direction="up" data-airo-wp-parallax-speed="4" data-airo-wp-parallax-viewport-start="0" data-airo-wp-parallax-viewport-end="100" data-airo-wp-parallax-relative-to="viewport" data-airo-wp-parallax-desktop="true" data-airo-wp-parallax-tablet="true" data-airo-wp-parallax-mobile="false" data-airo-wp-parallax-rotate-enabled="false" data-airo-wp-parallax-rotate-direction="cw" data-airo-wp-parallax-rotate-speed="3"><img src="{{dsgo:placeholder-portrait}}" alt="Luxury home interior" style="border-radius:0"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"shapeDividerTop":"tilt","shapeDividerTopHeight":60,"shapeDividerTopFlipY":true,"shapeDividerBottom":"tilt-reverse","shapeDividerBottomHeight":60,"shapeDividerBottomFlipY":true,"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-stack--has-shape-divider has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-shape-divider airo-wp-shape-divider--top is-shape-tilt is-flip-y" style="--airo-wp-shape-height:60px" aria-hidden="true"></div><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto;padding-top:60px;padding-bottom:60px"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"4px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:4px;text-transform:uppercase">Featured Listings</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"300","letterSpacing":"-0.5px"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:300;letter-spacing:-0.5px">Exceptional Properties Await</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/image-accordion {"height":"550px","overlayOpacityExpanded":15} -->
<div class="wp-block-airo-wp-image-accordion airo-wp-image-accordion airo-wp-image-accordion--hover" style="--airo-wp-image-accordion-height:550px;--airo-wp-image-accordion-expanded-ratio:3;--airo-wp-image-accordion-transition:0.5s;--airo-wp-image-accordion-overlay-opacity-expanded:0.15" data-trigger-type="hover" data-default-expanded="0" data-enable-overlay="true"><div class="airo-wp-image-accordion__items"><!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-1","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
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

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-2","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
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

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-3","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
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

<!-- wp:airo-wp/image-accordion-item {"uniqueId":"prop-4","className":"airo-wp-image-accordion__item","style":{"background":{"backgroundImage":{"url":"{{dsgo:placeholder-portrait}}"}}}} -->
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
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"url":"#all-properties","icon":"arrow-right","iconPosition":"end","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0"},"color":{"background":"#d4af37","text":"#0f172a"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:0;color:#0f172a;background-color:#d4af37;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#all-properties" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">View All Properties</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div><div class="airo-wp-shape-divider airo-wp-shape-divider--bottom is-shape-tilt-reverse" style="--airo-wp-shape-height:60px" aria-hidden="true"></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"4px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:4px;text-transform:uppercase">Our Services</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"300","letterSpacing":"-0.5px"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:300;letter-spacing:-0.5px">White-Glove Service at Every Step</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--30);column-gap:var(--wp--preset--spacing--30)"><!-- wp:airo-wp/flip-card -->
<div class="wp-block-airo-wp-flip-card airo-wp-flip-card airo-wp-flip-card--hover airo-wp-flip-card--effect-flip airo-wp-flip-card--horizontal" style="--airo-wp-flip-duration:0.6s" data-flip-trigger="hover" data-flip-effect="flip" data-flip-direction="horizontal"><div class="airo-wp-flip-card__container"><!-- wp:airo-wp/flip-card-face {"side":"front","className":"airo-wp-flip-card__face\u002d\u002dfront","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"0"},"color":{"gradient":"linear-gradient(135deg,rgb(15,23,42) 0%,rgb(30,41,59) 100%)"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__front airo-wp-flip-card__face--front has-background" style="border-radius:0;background:linear-gradient(135deg,rgb(15,23,42) 0%,rgb(30,41,59) 100%);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:airo-wp/icon {"icon":"home","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"zoomIn","dsgoAnimationDuration":500,"style":{"color":{"text":"#d4af37"}}} /-->
<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size" style="margin-top:var(--wp--preset--spacing--30)">Buyer Representation</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}},"color":{"text":"rgba(255,255,255,0.6)"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:rgba(255,255,255,0.6);margin-top:var(--wp--preset--spacing--10)">Hover to learn more</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face -->

<!-- wp:airo-wp/flip-card-face {"side":"back","className":"airo-wp-flip-card__face\u002d\u002dback","backgroundColor":"base-2","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"0"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__back airo-wp-flip-card__face--back has-base-2-background-color has-background" style="border-radius:0;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Buyer Representation</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--15)">Our expert agents guide you through every step of finding and acquiring your perfect property. From initial search to closing, we negotiate on your behalf to secure the best terms.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}},"color":{"text":"#d4af37"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;margin-top:var(--wp--preset--spacing--15);font-style:normal;font-weight:600">Learn more →</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face --></div></div>
<!-- /wp:airo-wp/flip-card -->

<!-- wp:airo-wp/flip-card -->
<div class="wp-block-airo-wp-flip-card airo-wp-flip-card airo-wp-flip-card--hover airo-wp-flip-card--effect-flip airo-wp-flip-card--horizontal" style="--airo-wp-flip-duration:0.6s" data-flip-trigger="hover" data-flip-effect="flip" data-flip-direction="horizontal"><div class="airo-wp-flip-card__container"><!-- wp:airo-wp/flip-card-face {"side":"front","className":"airo-wp-flip-card__face\u002d\u002dfront","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"0"},"color":{"gradient":"linear-gradient(135deg,rgb(15,23,42) 0%,rgb(30,41,59) 100%)"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__front airo-wp-flip-card__face--front has-background" style="border-radius:0;background:linear-gradient(135deg,rgb(15,23,42) 0%,rgb(30,41,59) 100%);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:airo-wp/icon {"icon":"chart","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"zoomIn","dsgoAnimationDuration":500,"dsgoAnimationDelay":100,"style":{"color":{"text":"#d4af37"}}} /-->
<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size" style="margin-top:var(--wp--preset--spacing--30)">Property Valuation</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}},"color":{"text":"rgba(255,255,255,0.6)"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:rgba(255,255,255,0.6);margin-top:var(--wp--preset--spacing--10)">Hover to learn more</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face -->

<!-- wp:airo-wp/flip-card-face {"side":"back","className":"airo-wp-flip-card__face\u002d\u002dback","backgroundColor":"base-2","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"0"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__back airo-wp-flip-card__face--back has-base-2-background-color has-background" style="border-radius:0;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Property Valuation</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--15)">Receive accurate, data-driven valuations backed by our deep knowledge of luxury markets. We analyze comparable sales, market trends, and unique property features.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}},"color":{"text":"#d4af37"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;margin-top:var(--wp--preset--spacing--15);font-style:normal;font-weight:600">Learn more →</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face --></div></div>
<!-- /wp:airo-wp/flip-card -->

<!-- wp:airo-wp/flip-card -->
<div class="wp-block-airo-wp-flip-card airo-wp-flip-card airo-wp-flip-card--hover airo-wp-flip-card--effect-flip airo-wp-flip-card--horizontal" style="--airo-wp-flip-duration:0.6s" data-flip-trigger="hover" data-flip-effect="flip" data-flip-direction="horizontal"><div class="airo-wp-flip-card__container"><!-- wp:airo-wp/flip-card-face {"side":"front","className":"airo-wp-flip-card__face\u002d\u002dfront","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"0"},"color":{"gradient":"linear-gradient(135deg,rgb(15,23,42) 0%,rgb(30,41,59) 100%)"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__front airo-wp-flip-card__face--front has-background" style="border-radius:0;background:linear-gradient(135deg,rgb(15,23,42) 0%,rgb(30,41,59) 100%);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)">
<!-- wp:airo-wp/icon {"icon":"globe","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"zoomIn","dsgoAnimationDuration":500,"dsgoAnimationDelay":200,"style":{"color":{"text":"#d4af37"}}} /-->
<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"textColor":"base","fontSize":"large"} -->
<h3 class="wp-block-heading has-base-color has-text-color has-large-font-size" style="margin-top:var(--wp--preset--spacing--30)">Global Reach</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}},"color":{"text":"rgba(255,255,255,0.6)"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:rgba(255,255,255,0.6);margin-top:var(--wp--preset--spacing--10)">Hover to learn more</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face -->

<!-- wp:airo-wp/flip-card-face {"side":"back","className":"airo-wp-flip-card__face\u002d\u002dback","backgroundColor":"base-2","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"0"}}} -->
<div class="wp-block-airo-wp-flip-card-face airo-wp-flip-card__face airo-wp-flip-card__back airo-wp-flip-card__face--back has-base-2-background-color has-background" style="border-radius:0;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)">
<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size">Global Reach</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--15)">Access exclusive listings worldwide through our international network. From New York penthouses to Mediterranean villas, we connect you with extraordinary properties globally.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|15"}},"color":{"text":"#d4af37"},"typography":{"fontStyle":"normal","fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;margin-top:var(--wp--preset--spacing--15);font-style:normal;font-weight:600">Learn more →</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:airo-wp/flip-card-face --></div></div>
<!-- /wp:airo-wp/flip-card --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"4px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:4px;text-transform:uppercase">Testimonials</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"300","letterSpacing":"-0.5px"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:300;letter-spacing:-0.5px">What Our Clients Say</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/slider {"slidesPerView":2,"autoplay":true,"autoplayInterval":7000} -->
<div class="wp-block-airo-wp-slider airo-wp-slider airo-wp-slider--classic airo-wp-slider--effect-slide airo-wp-slider--has-arrows airo-wp-slider--has-dots" style="--airo-wp-slider-height:500px;--airo-wp-slider-aspect-ratio:16/9;--airo-wp-slider-gap:20px;--airo-wp-slider-transition:0.5s;--airo-wp-slider-slides-per-view:2;--airo-wp-slider-slides-per-view-tablet:1;--airo-wp-slider-slides-per-view-mobile:1;--airo-wp-slider-arrow-size:48px" data-slides-per-view="2" data-slides-per-view-tablet="1" data-slides-per-view-mobile="1" data-use-aspect-ratio="false" data-show-arrows="true" data-show-dots="true" data-arrow-style="default" data-arrow-position="sides" data-arrow-vertical-position="center" data-dot-style="default" data-dot-position="bottom" data-effect="slide" data-transition-duration="0.5s" data-transition-easing="ease-in-out" data-autoplay="true" data-autoplay-interval="7000" data-pause-on-hover="true" data-pause-on-interaction="true" data-loop="true" data-draggable="true" data-swipeable="true" data-free-mode="false" data-centered-slides="false" data-mobile-breakpoint="768" data-tablet-breakpoint="1024" data-active-slide="0" role="region" aria-label="Image slider" aria-roledescription="slider"><div class="airo-wp-slider__viewport"><div class="airo-wp-slider__track"><!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic"},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#64748b;font-style:italic">"The team made finding our dream home an absolute pleasure. Their knowledge of the luxury market is unmatched, and they found us a property that exceeded all our expectations."</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--30);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:nowrap;gap:var(--wp--preset--spacing--20)"><!-- wp:image {"sizeSlug":"thumbnail","style":{"border":{"radius":"100px"}}} -->
<figure class="wp-block-image size-thumbnail has-custom-border"><img src="{{dsgo:placeholder-square}}" alt="Client photo" style="border-radius:100px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}}} -->
<p style="font-style:normal;font-weight:600">Michael Richardson</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37">Beverly Hills Buyer</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic"},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#64748b;font-style:italic">"Selling our estate was a seamless experience. They positioned our property perfectly in the market and attracted qualified buyers from around the world. Truly exceptional service."</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--30);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:nowrap;gap:var(--wp--preset--spacing--20)"><!-- wp:image {"sizeSlug":"thumbnail","style":{"border":{"radius":"100px"}}} -->
<figure class="wp-block-image size-thumbnail has-custom-border"><img src="{{dsgo:placeholder-square}}" alt="Client photo" style="border-radius:100px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}}} -->
<p style="font-style:normal;font-weight:600">Sarah Chen</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37">Bel Air Seller</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide -->

<!-- wp:airo-wp/slide -->
<div class="wp-block-airo-wp-slide airo-wp-slide" style="--airo-wp-slide-content-vertical-align:center;--airo-wp-slide-content-horizontal-align:center" role="group" aria-roledescription="slide"><div class="airo-wp-slide__content"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"italic"},"color":{"text":"#64748b"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#64748b;font-style:italic">"As international buyers, we relied heavily on their expertise. They navigated complex legal requirements and found us a stunning oceanfront property that serves as our perfect retreat."</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--30);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:nowrap;gap:var(--wp--preset--spacing--20)"><!-- wp:image {"sizeSlug":"thumbnail","style":{"border":{"radius":"100px"}}} -->
<figure class="wp-block-image size-thumbnail has-custom-border"><img src="{{dsgo:placeholder-square}}" alt="Client photo" style="border-radius:100px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"600"}}} -->
<p style="font-style:normal;font-weight:600">David Laurent</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37">International Buyer</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/slide --></div></div></div>
<!-- /wp:airo-wp/slider --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"shapeDividerTop":"wave","shapeDividerTopHeight":80,"shapeDividerTopFlipY":true,"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-stack--has-shape-divider has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-shape-divider airo-wp-shape-divider--top is-shape-wave is-flip-y" style="--airo-wp-shape-height:80px" aria-hidden="true"></div><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto;padding-top:80px"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInLeft","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInLeft" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInLeft" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"4px"},"color":{"text":"#d4af37"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#d4af37;letter-spacing:4px;text-transform:uppercase">Contact Us</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"300","letterSpacing":"-0.5px"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:300;letter-spacing:-0.5px">Begin Your Journey to Extraordinary Living</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}},"color":{"text":"#94a3b8"}},"fontSize":"medium"} -->
<p class="has-text-color has-medium-font-size" style="color:#94a3b8;margin-top:var(--wp--preset--spacing--20)">Whether you are searching for your dream home or considering selling your property, our team is ready to provide the exceptional service you deserve.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/row {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--20);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:nowrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon {"icon":"phone","iconSize":24,"style":{"color":{"text":"#d4af37"}}} /-->

<!-- wp:paragraph {"fontSize":"medium"} -->
<p class="has-medium-font-size">+1 (310) 555-0100</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:airo-wp/row {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|20"}}},"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--20);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:nowrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon {"icon":"mail","iconSize":24,"style":{"color":{"text":"#d4af37"}}} /-->

<!-- wp:paragraph {"fontSize":"medium"} -->
<p class="has-medium-font-size">inquiries@luxuryestates.com</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:airo-wp/row {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"left","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:nowrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon {"icon":"location","iconSize":24,"style":{"color":{"text":"#d4af37"}}} /-->

<!-- wp:paragraph {"fontSize":"medium"} -->
<p class="has-medium-font-size">9876 Wilshire Blvd, Beverly Hills, CA 90210</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"0"}},"backgroundColor":"base","textColor":"contrast","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInRight","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-contrast-color has-base-background-color has-text-color has-background has-airo-wp-animation airo-wp-animation-fadeInRight" style="border-radius:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInRight" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="margin-bottom:var(--wp--preset--spacing--30)">Schedule a Consultation</h3>
<!-- /wp:heading -->

<!-- wp:airo-wp/form-builder {"formId":"consultation-form","fieldSpacing":"1.5rem","inputHeight":"44px","inputPadding":"0.75rem","submitButtonPaddingVertical":"0.75rem","submitButtonPaddingHorizontal":"2rem","submitButtonHeight":"44px","className":"airo-wp-form"} -->
<div class="wp-block-airo-wp-form-builder airo-wp-form-builder airo-wp-form-builder--align-left airo-wp-form" style="--airo-wp-form-field-spacing:1.5rem;--airo-wp-form-input-height:44px;--airo-wp-form-input-padding:0.75rem" data-form-id="consultation-form" data-ajax-submit="true" data-success-message="Thank you! Your form has been submitted successfully." data-error-message="There was an error submitting the form. Please try again."><form class="airo-wp-form" method="post" novalidate><div class="airo-wp-form__fields"><!-- wp:airo-wp/form-text-field {"fieldName":"field_name_re","label":"Full Name","placeholder":"Enter your full name","required":true} -->
<div class="wp-block-airo-wp-form-text-field airo-wp-form-field airo-wp-form-field--text" style="flex-basis:100%;max-width:100%"><label for="field-field_name_re" class="airo-wp-form-field__label">Full Name<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="text" id="field-field_name_re" name="field_name_re" class="airo-wp-form-field__input" placeholder="Enter your full name" required aria-required="true" data-field-type="text"/></div>
<!-- /wp:airo-wp/form-text-field -->

<!-- wp:airo-wp/form-email-field {"fieldName":"field_email_re","label":"Email Address","placeholder":"Enter your email","required":true} -->
<div class="wp-block-airo-wp-form-email-field airo-wp-form-field airo-wp-form-field--email" style="flex-basis:100%;max-width:100%"><label for="field-field_email_re" class="airo-wp-form-field__label">Email Address<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="email" id="field-field_email_re" name="field_email_re" class="airo-wp-form-field__input" placeholder="Enter your email" required aria-required="true" data-field-type="email"/></div>
<!-- /wp:airo-wp/form-email-field -->

<!-- wp:airo-wp/form-phone-field {"fieldName":"field_phone_re","label":"Phone Number","placeholder":"(310) 555-0100"} -->
<div class="wp-block-airo-wp-form-phone-field airo-wp-form-field airo-wp-form-field--phone" style="flex-basis:100%;max-width:100%"><label for="field-field_phone_re" class="airo-wp-form-field__label">Phone Number</label><div class="airo-wp-form-field__phone-wrapper" style="display:flex;gap:0.5rem" data-auto-format="true"><select name="field_phone_re_country_code" class="airo-wp-form-field__country-code" data-airo-wp-country-code="+1" style="min-width:85px;flex-shrink:0" aria-label="Country Code"></select><input type="tel" id="field-field_phone_re" name="field_phone_re" class="airo-wp-form-field__input" placeholder="(310) 555-0100" data-field-type="tel" data-phone-format="any" style="flex:1"/></div></div>
<!-- /wp:airo-wp/form-phone-field -->

<!-- wp:airo-wp/form-select-field {"fieldName":"select_interest_re","label":"I am interested in","options":[{"value":"buying","label":"Buying a Property"},{"value":"selling","label":"Selling a Property"},{"value":"both","label":"Both Buying and Selling"},{"value":"valuation","label":"Property Valuation"}]} -->
<div class="wp-block-airo-wp-form-select-field airo-wp-form-field airo-wp-form-field--select" style="flex-basis:100%;max-width:100%"><label for="field-select_interest_re" class="airo-wp-form-field__label">I am interested in</label><select id="field-select_interest_re" name="select_interest_re" class="airo-wp-form-field__select" data-field-type="select"><option value="">-- Select an option --</option><option value="buying">Buying a Property</option><option value="selling">Selling a Property</option><option value="both">Both Buying and Selling</option><option value="valuation">Property Valuation</option></select></div>
<!-- /wp:airo-wp/form-select-field --></div><input type="text" name="dsg_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"/><input type="hidden" name="dsg_form_id" value="consultation-form"/><div class="airo-wp-form__footer"><button type="submit" class="airo-wp-form__submit wp-element-button" style="min-height:44px;padding-top:0.75rem;padding-bottom:0.75rem;padding-left:2rem;padding-right:2rem">Request Consultation</button></div><div class="airo-wp-form__message" role="status" aria-live="polite" aria-atomic="true" style="display:none"></div></form></div>
<!-- /wp:airo-wp/form-builder --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
