<?php
/**
 * Title: Alternating Content Rows
 * Slug: airo-wp/content/content-alternating-rows
 * Categories: airo-wp-content
 * Description: Zigzag layout with alternating image and text positions
 * Keywords: alternating, zigzag, features, content, rows
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Alternating Content Rows', 'airo-wp' ),
	'categories'    => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-alternating-rows","name":"Alternating Content Rows"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">How We Help You Succeed</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">A comprehensive approach to achieving your goals</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|60","padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|60"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--60);column-gap:var(--wp--preset--spacing--60)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Strategy planning" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInRight"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInRight" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInRight"><div class="airo-wp-stack__inner"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"},"color":{"text":"#6366f1"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#6366f1;font-weight:600">STEP 01</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Strategic Planning</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--30)">We start by understanding your business goals and creating a comprehensive roadmap for success. Our team analyzes market trends and competitive landscapes.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":18,"iconColor":"#10b981"} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Market research &amp; analysis</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Goal setting &amp; KPIs</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Resource allocation</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid -->

<!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|60","padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|60"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--60);column-gap:var(--wp--preset--spacing--60)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}},"className":"airo-wp-order-2-mobile"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint airo-wp-order-2-mobile" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInLeft"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInLeft" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInLeft"><div class="airo-wp-stack__inner"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600","fontStyle":"normal"},"color":{"text":"#10b981"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#10b981;font-style:normal;font-weight:600">STEP 02</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Design &amp; Development</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--30)">Our expert team brings your vision to life with cutting-edge design and robust development practices that ensure scalability and performance.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":18,"iconColor":"#10b981"} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">User-centered design</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Agile development</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Quality assurance</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}},"className":"airo-wp-order-1-mobile","dsgoMobileOrder":0} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint airo-wp-order-1-mobile" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;--airo-wp-mobile-order:0"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Design process" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid -->

<!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|60","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--60);column-gap:var(--wp--preset--spacing--60)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Launch and growth" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 1"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInRight"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInRight" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInRight"><div class="airo-wp-stack__inner"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"},"color":{"text":"#f59e0b"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#f59e0b;font-weight:600">STEP 03</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Launch &amp; Growth</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--30)">We ensure a smooth launch and provide ongoing support to help you grow. Our data-driven approach helps optimize performance continuously.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":18,"iconColor":"#10b981"} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Seamless deployment</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Performance monitoring</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Continuous optimization</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
