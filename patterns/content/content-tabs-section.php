<?php
/**
 * Title: Content Tabs Section
 * Slug: airo-wp/content/content-tabs-section
 * Categories: airo-wp-content
 * Description: Tabbed content section with multiple panels
 * Keywords: tabs, content, panels, navigation, sections
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Content Tabs Section', 'airo-wp' ),
	'categories'    => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-tabs-section","name":"Content Tabs Section"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"has-airo-wp-text-reveal"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-text-reveal" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Explore Our Services</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Discover how we can help you achieve your goals</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/tabs {"uniqueId":"f8e2a1b4","activeTab":3,"alignment":"center","tabStyle":"pills","activeTabBackgroundColor":"#6366f1","style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"margin":{"top":"var:preset|spacing|50"}},"typography":{"fontSize":"var:preset|font-size|large"}}} -->
<div class="wp-block-airo-wp-tabs airo-wp-tabs airo-wp-tabs-f8e2a1b4 airo-wp-tabs--horizontal airo-wp-tabs--pills airo-wp-tabs--align-center" data-active-tab="3" data-mobile-breakpoint="768" data-mobile-mode="accordion" data-deep-linking="false" style="margin-top:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0;font-size:var(--wp--preset--font-size--large);--airo-wp-tabs-gap:8px;--airo-wp-tab-bg-active:#6366f1"><div class="airo-wp-tabs__nav" role="tablist"></div><div class="airo-wp-tabs__panels"><!-- wp:airo-wp/tab {"uniqueId":"3c7d9e2f","title":"Web Design","anchor":"web-design"} -->
<div class="wp-block-airo-wp-tab airo-wp-tab" role="tabpanel" aria-labelledby="tab-3c7d9e2f" aria-label="Web Design" id="panel-web-design" hidden style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-tab__content"><!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Web Design Services" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Beautiful, Responsive Web Design</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--30)">We create stunning websites that work perfectly on all devices. Our design process focuses on user experience and conversion optimization.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":20,"iconColor":"#10b981"} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Custom UI/UX Design</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Mobile-First Approach</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Brand Integration</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/tab -->

<!-- wp:airo-wp/tab {"uniqueId":"b5a4c8d1","title":"Development","anchor":"development"} -->
<div class="wp-block-airo-wp-tab airo-wp-tab" role="tabpanel" aria-labelledby="tab-b5a4c8d1" aria-label="Development" id="panel-development" hidden style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-tab__content"><!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Development Services" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Robust Web Development</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--30)">Our development team builds scalable, secure, and high-performance web applications using the latest technologies and best practices.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":20,"iconColor":"#10b981"} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Custom WordPress Development</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">E-commerce Solutions</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">API Integration</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/tab -->

<!-- wp:airo-wp/tab {"uniqueId":"e9f3b6a2","title":"Marketing","anchor":"marketing"} -->
<div class="wp-block-airo-wp-tab airo-wp-tab" role="tabpanel" aria-labelledby="tab-e9f3b6a2" aria-label="Marketing" id="panel-marketing" hidden style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-tab__content"><!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Marketing Services" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Digital Marketing Excellence</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--30)">Drive growth with our comprehensive digital marketing strategies. We help you reach your target audience and convert them into loyal customers.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":20,"iconColor":"#10b981"} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">SEO Optimization</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Social Media Marketing</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Content Strategy</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/tab -->

<!-- wp:airo-wp/tab {"uniqueId":"7d2c5e8f","title":"Support","anchor":"support"} -->
<div class="wp-block-airo-wp-tab airo-wp-tab" role="tabpanel" aria-labelledby="tab-7d2c5e8f" aria-label="Support" id="panel-support" hidden style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-tab__content"><!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Support Services" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">24/7 Customer Support</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--30)">We are always here to help. Our dedicated support team ensures your website runs smoothly and any issues are resolved quickly.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconSize":20,"iconColor":"#10b981"} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:24px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Round-the-clock Availability</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Security Monitoring</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"check-circle","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="check-circle"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading">Regular Updates &amp; Backups</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/tab --></div></div>
<!-- /wp:airo-wp/tabs --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->',
);
