<?php
/**
 * Title: Non-profit / Charity Homepage
 * Slug: airo-wp/homepage/homepage-nonprofit
 * Categories: airo-wp-homepage
 * Description: An inspiring non-profit homepage with impact statistics, causes, volunteer signup, and donation calls-to-action
 * Keywords: homepage, nonprofit, charity, donation, volunteer, cause, foundation
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Non-profit / Charity Homepage', 'airo-wp' ),
	'categories' => array( 'airo-wp-homepage' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:cover {"url":"{{dsgo:placeholder-landscape-wide}}","alt":"Volunteers helping community","dimRatio":60,"overlayColor":"contrast","minHeight":650,"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-cover alignfull" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30);min-height:650px"><img class="wp-block-cover__image-background" alt="Volunteers helping community" src="{{dsgo:placeholder-landscape-wide}}" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-60 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeIn","dsgoAnimationDuration":800} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeIn" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeIn" data-airo-wp-animation-duration="800"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"4px"}},"textColor":"base","fontSize":"small","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInDown"} -->
<p class="has-text-align-center has-base-color has-text-color has-small-font-size has-airo-wp-animation airo-wp-animation-fadeInDown" style="letter-spacing:4px;text-transform:uppercase" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInDown">Making a Difference Since 2010</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontStyle":"normal","fontWeight":"700","lineHeight":"1.1"},"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"textColor":"base","fontSize":"xx-large","dsgoTextRevealEnabled":true,"dsgoTextRevealColor":"#10b981","dsgoTextRevealSplitMode":"words"} -->
<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color has-xx-large-font-size has-airo-wp-text-reveal" style="margin-top:var(--wp--preset--spacing--20);font-style:normal;font-weight:700;line-height:1.1" data-airo-wp-text-reveal-enabled="true" data-airo-wp-text-reveal-color="#10b981" data-airo-wp-text-reveal-split-mode="words" data-airo-wp-text-reveal-transition="150">Together We Can<br>Change Lives</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"textColor":"base","fontSize":"medium"} -->
<p class="has-text-align-center has-base-color has-text-color has-medium-font-size" style="margin-top:var(--wp--preset--spacing--30)">Join our mission to provide hope, resources, and opportunities to communities in need around the world.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"text":"Donate Now","url":"#donate","icon":"heart","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"50px"},"color":{"background":"#10b981","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon" style="border-radius:50px;color:#ffffff;background-color:#10b981;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#donate" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="heart"></span><span class="airo-wp-icon-button__text">Donate Now</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"text":"Get Involved","url":"#volunteer","icon":"","iconPosition":"none","iconGap":"8px","backgroundColor":"transparent","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"50px","width":"2px","color":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-base-color has-transparent-background-color has-text-color has-background" style="border-color:#ffffff;border-width:2px;border-radius:50px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#volunteer" target="_self"><span class="airo-wp-icon-button__text">Get Involved</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:cover -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/counter-group {"columns":4,"animationDuration":2000,"animationDelay":200,"animationEasing":"easeOutExpo","className":"airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-2"} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-2" style="align-self:stretch;--airo-wp-counter-columns-desktop:4;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2000" data-animation-delay="200" data-animation-easing="easeOutExpo" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"np-1","endValue":50000,"suffix":"+","label":"Lives Changed"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="np-1" style="text-align:center" data-start-value="0" data-end-value="50000" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Lives Changed</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"np-2","endValue":25,"label":"Countries Reached"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="np-2" style="text-align:center" data-start-value="0" data-end-value="25" data-decimals="0" data-prefix="" data-suffix="" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Countries Reached</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"np-3","endValue":1000,"suffix":"+","label":"Volunteers"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="np-3" style="text-align:center" data-start-value="0" data-end-value="1000" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Volunteers</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"np-4","endValue":5,"prefix":"$","suffix":"M+","label":"Raised"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="np-4" style="text-align:center" data-start-value="0" data-end-value="5" data-decimals="0" data-prefix="$" data-suffix="M+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Raised</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDuration":700} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-animation airo-wp-animation-fadeInUp" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp" data-airo-wp-animation-duration="700"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#10b981"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#10b981;letter-spacing:3px;text-transform:uppercase">Our Mission</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Empowering Communities, One Life at a Time</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">We believe everyone deserves access to clean water, education, healthcare, and economic opportunity. Through sustainable programs and community partnerships, we work to break the cycle of poverty.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">Our approach focuses on long-term solutions that empower communities to thrive independently. We invest in local leaders, provide training and resources, and measure our impact to ensure every dollar makes a difference.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-button {"text":"Learn About Our Work","url":"#about","icon":"arrow-right","iconPosition":"end","iconGap":"8px","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|30"}},"border":{"radius":"50px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left" style="margin-top:var(--wp--preset--spacing--30)"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-base-color has-contrast-background-color has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:50px;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#about" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn About Our Work</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Children receiving education" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#10b981"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#10b981;letter-spacing:3px;text-transform:uppercase">Our Focus Areas</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Where Your Support Goes</h2>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"desktopColumns":4,"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-4 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(4, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--30);column-gap:var(--wp--preset--spacing--30)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"book","justification":"left","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"zoomIn","dsgoAnimationDuration":500,"style":{"color":{"text":"#10b981"}}} /-->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Education</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--10)">Building schools and providing scholarships to give children the education they deserve.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"users","justification":"left","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"zoomIn","dsgoAnimationDuration":500,"dsgoAnimationDelay":100,"style":{"color":{"text":"#10b981"}}} /-->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Clean Water</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--10)">Installing wells and water systems to provide safe drinking water to communities.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"heart","justification":"left","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"zoomIn","dsgoAnimationDuration":500,"dsgoAnimationDelay":200,"style":{"color":{"text":"#10b981"}}} /-->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Healthcare</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--10)">Funding medical clinics and providing essential healthcare services to underserved areas.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/icon {"icon":"briefcase","justification":"left","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"zoomIn","dsgoAnimationDuration":500,"dsgoAnimationDelay":300,"style":{"color":{"text":"#10b981"}}} /-->

<!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Economic Empowerment</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"small"} -->
<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--10)">Microloans and vocational training to help families build sustainable livelihoods.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#10b981"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#10b981;letter-spacing:3px;text-transform:uppercase">Current Campaign</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Help Us Reach Our Goal</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">We are building 10 new schools in rural communities. Every donation brings us closer to making education accessible to 5,000 more children.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/progress-bar {"barColor":"#10b981"} -->
<div class="wp-block-airo-wp-progress-bar airo-wp-progress-bar airo-wp-progress-bar--animate" data-percentage="75" data-duration="1.5"><div class="airo-wp-progress-bar__label airo-wp-progress-bar__label--top">75%</div><div class="airo-wp-progress-bar__container" style="width:100%;height:20px;background-color:#e5e7eb;border-radius:4px;overflow:hidden;position:relative"><div class="airo-wp-progress-bar__fill " style="width:0%;height:100%;background-color:#10b981;transition:width 1.5s ease-out;border-radius:4px"></div></div></div>
<!-- /wp:airo-wp/progress-bar -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"text":"Donate $25","url":"#donate-25","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"50px","width":"2px","color":"#ffffff"},"color":{"background":"transparent","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:#ffffff;border-width:2px;border-radius:50px;color:#ffffff;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#donate-25" target="_self"><span class="airo-wp-icon-button__text">Donate $25</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"text":"Donate $50","url":"#donate-50","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"50px","width":"2px","color":"#ffffff"},"color":{"background":"transparent","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:#ffffff;border-width:2px;border-radius:50px;color:#ffffff;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#donate-50" target="_self"><span class="airo-wp-icon-button__text">Donate $50</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"text":"Donate $100","url":"#donate-100","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"50px"},"color":{"background":"#10b981","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background" style="border-radius:50px;color:#ffffff;background-color:#10b981;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#donate-100" target="_self"><span class="airo-wp-icon-button__text">Donate $100</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"text":"Custom Amount","url":"#donate-custom","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"50px","width":"2px","color":"#ffffff"},"color":{"background":"transparent","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:#ffffff;border-width:2px;border-radius:50px;color:#ffffff;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#donate-custom" target="_self"><span class="airo-wp-icon-button__text">Custom Amount</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
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
<h3 class="wp-block-heading has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Maria\'s Journey to Self-Sufficiency</h3>
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
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#10b981"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#10b981;letter-spacing:3px;text-transform:uppercase">Get Involved</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Ways to Make a Difference</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">There are many ways to support our mission beyond financial donations. Your time, skills, and voice can make a tremendous impact.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/accordion {"className":"airo-wp-accordion\u002d\u002dicon-chevron","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-accordion airo-wp-accordion airo-wp-accordion--icon-right airo-wp-accordion--border-between airo-wp-accordion--icon-chevron" style="margin-top:var(--wp--preset--spacing--30);--airo-wp-accordion-open-bg:;--airo-wp-accordion-open-text:;--airo-wp-accordion-hover-bg:;--airo-wp-accordion-hover-text:;--airo-wp-accordion-gap:0.5rem" data-allow-multiple="false" data-icon-style="chevron"><div class="airo-wp-accordion__items"><!-- wp:airo-wp/accordion-item {"title":"Volunteer Your Time","isOpen":true,"uniqueId":"accordion-item-gnr58j9ru"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--open" data-initially-open="true"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="true" aria-controls="accordion-item-gnr58j9ru-panel" id="accordion-item-gnr58j9ru-header"><span class="airo-wp-accordion-item__title">Volunteer Your Time</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-gnr58j9ru-header" id="accordion-item-gnr58j9ru-panel"><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Join our volunteer network and make a hands-on difference. We offer both local and international volunteer opportunities, from community events to field missions.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item -->

<!-- wp:airo-wp/accordion-item {"title":"Become a Monthly Donor","uniqueId":"accordion-item-hbvzkpaxd"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--closed" data-initially-open="false"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="false" aria-controls="accordion-item-hbvzkpaxd-panel" id="accordion-item-hbvzkpaxd-header"><span class="airo-wp-accordion-item__title">Become a Monthly Donor</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-hbvzkpaxd-header" id="accordion-item-hbvzkpaxd-panel" hidden><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Monthly giving provides sustainable support that allows us to plan long-term projects. Even $10/month can provide school supplies for a child for an entire year.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item -->

<!-- wp:airo-wp/accordion-item {"title":"Start a Fundraiser","uniqueId":"accordion-item-dtm880dum"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--closed" data-initially-open="false"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="false" aria-controls="accordion-item-dtm880dum-panel" id="accordion-item-dtm880dum-header"><span class="airo-wp-accordion-item__title">Start a Fundraiser</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-dtm880dum-header" id="accordion-item-dtm880dum-panel" hidden><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Celebrate your birthday, run a marathon, or host an event to raise funds for our cause. We provide all the tools and support you need to succeed.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item -->

<!-- wp:airo-wp/accordion-item {"title":"Corporate Partnerships","uniqueId":"accordion-item-6n0si8m1l"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--closed" data-initially-open="false"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="false" aria-controls="accordion-item-6n0si8m1l-panel" id="accordion-item-6n0si8m1l-header"><span class="airo-wp-accordion-item__title">Corporate Partnerships</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-6n0si8m1l-header" id="accordion-item-6n0si8m1l-panel" hidden><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Partner with us to make a larger impact. We offer sponsorship opportunities, employee engagement programs, and cause-related marketing partnerships.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item --></div></div>
<!-- /wp:airo-wp/accordion --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="margin-bottom:var(--wp--preset--spacing--30)">Sign Up to Volunteer</h3>
<!-- /wp:heading -->

<!-- wp:airo-wp/form-builder {"formId":"volunteer-form","fieldSpacing":"1.5rem","inputHeight":"44px","inputPadding":"0.75rem","submitButtonPaddingVertical":"0.75rem","submitButtonPaddingHorizontal":"2rem","submitButtonHeight":"44px","className":"airo-wp-form"} -->
<div class="wp-block-airo-wp-form-builder airo-wp-form-builder airo-wp-form-builder--align-left airo-wp-form" style="--airo-wp-form-field-spacing:1.5rem;--airo-wp-form-input-height:44px;--airo-wp-form-input-padding:0.75rem" data-form-id="volunteer-form" data-ajax-submit="true" data-success-message="Thank you! Your form has been submitted successfully." data-error-message="There was an error submitting the form. Please try again."><form class="airo-wp-form" method="post" novalidate><div class="airo-wp-form__fields"><!-- wp:airo-wp/form-text-field {"fieldName":"field_3d375a5f","label":"Full Name","placeholder":"Enter your full name","required":true} -->
<div class="wp-block-airo-wp-form-text-field airo-wp-form-field airo-wp-form-field--text" style="flex-basis:100%;max-width:100%"><label for="field-field_3d375a5f" class="airo-wp-form-field__label">Full Name<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="text" id="field-field_3d375a5f" name="field_3d375a5f" class="airo-wp-form-field__input" placeholder="Enter your full name" required aria-required="true" data-field-type="text"/></div>
<!-- /wp:airo-wp/form-text-field -->

<!-- wp:airo-wp/form-email-field {"fieldName":"field_5213fa62","label":"Email Address","placeholder":"Enter your email","required":true} -->
<div class="wp-block-airo-wp-form-email-field airo-wp-form-field airo-wp-form-field--email" style="flex-basis:100%;max-width:100%"><label for="field-field_5213fa62" class="airo-wp-form-field__label">Email Address<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="email" id="field-field_5213fa62" name="field_5213fa62" class="airo-wp-form-field__input" placeholder="Enter your email" required aria-required="true" data-field-type="email"/></div>
<!-- /wp:airo-wp/form-email-field -->

<!-- wp:airo-wp/form-select-field {"fieldName":"select-662eef03","label":"Area of Interest","options":[{"value":"education","label":"Education Programs"},{"value":"water","label":"Clean Water Projects"},{"value":"healthcare","label":"Healthcare Initiatives"},{"value":"economic","label":"Economic Empowerment"},{"value":"events","label":"Local Events \u0026 Fundraising"}]} -->
<div class="wp-block-airo-wp-form-select-field airo-wp-form-field airo-wp-form-field--select" style="flex-basis:100%;max-width:100%"><label for="field-select-662eef03" class="airo-wp-form-field__label">Area of Interest</label><select id="field-select-662eef03" name="select-662eef03" class="airo-wp-form-field__select" data-field-type="select"><option value="">-- Select an option --</option><option value="education">Education Programs</option><option value="water">Clean Water Projects</option><option value="healthcare">Healthcare Initiatives</option><option value="economic">Economic Empowerment</option><option value="events">Local Events &amp; Fundraising</option></select></div>
<!-- /wp:airo-wp/form-select-field --></div><input type="text" name="dsg_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"/><input type="hidden" name="dsg_form_id" value="volunteer-form"/><div class="airo-wp-form__footer"><button type="submit" class="airo-wp-form__submit wp-element-button" style="min-height:44px;padding-top:0.75rem;padding-bottom:0.75rem;padding-left:2rem;padding-right:2rem">Submit Application</button></div><div class="airo-wp-form__message" role="status" aria-live="polite" aria-atomic="true" style="display:none"></div></form></div>
<!-- /wp:airo-wp/form-builder --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:700">Every Gift Creates Ripples of Change</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Your donation today will transform lives for generations to come. Together, we can build a world where everyone has the opportunity to thrive.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"text":"Donate Now","url":"#donate","icon":"heart","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"50px"},"color":{"background":"#10b981","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon" style="border-radius:50px;color:#ffffff;background-color:#10b981;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#donate" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="heart"></span><span class="airo-wp-icon-button__text">Donate Now</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->',
);
