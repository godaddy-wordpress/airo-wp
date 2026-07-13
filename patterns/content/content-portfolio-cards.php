<?php
/**
 * Title: Portfolio Cards
 * Slug: airo-wp/content/content-portfolio-cards
 * Categories: airo-wp-content
 * Description: Portfolio or case study cards with hover effects
 * Keywords: portfolio, projects, work, case study, gallery
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Portfolio Cards', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"contrast","textColor":"base","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-portfolio-cards","name":"Portfolio Cards"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","textColor":"base","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color has-x-large-font-size">Our Work</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"rgba(255,255,255,0.7)"}},"fontSize":"medium"} -->
<p class="has-text-align-center has-text-color has-medium-font-size" style="color:rgba(255,255,255,0.7)">Selected projects we are proud of</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--40);column-gap:var(--wp--preset--spacing--40)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"radius":"16px"}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/card {"imageUrl":"{{dsgo:placeholder-landscape}}","badgeText":"Web Design","title":"E-commerce Platform","subtitle":"Fashion \u0026amp; Retail","bodyText":"Complete redesign of a fashion e-commerce platform with improved UX.","showCta":false,"className":"airo-wp-card\u002d\u002dhas-hover","textColor":"contrast","style":{"border":{"radius":"16px"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--standard airo-wp-card--style-default airo-wp-card--has-hover has-contrast-color has-text-color has-link-color" style="border-radius:16px"><span class="airo-wp-card__badge airo-wp-card__badge--floating airo-wp-card__badge--top-right" role="status" aria-label="Badge">Web Design</span><div class="airo-wp-card__inner"><div class="airo-wp-card__image-wrapper"><img src="{{dsgo:placeholder-landscape}}" alt="Card image" class="airo-wp-card__image" style="aspect-ratio:16 / 9;object-fit:cover;object-position:50% 50%" loading="lazy" aria-hidden="true"/></div><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">E-commerce Platform</h3><p class="airo-wp-card__subtitle">Fashion &amp; Retail</p><p class="airo-wp-card__body">Complete redesign of a fashion e-commerce platform with improved UX.</p></div></div></div>
<!-- /wp:airo-wp/card --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"radius":"16px"}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/card {"imageUrl":"{{dsgo:placeholder-landscape}}","badgeText":"Mobile App","title":"Fitness Tracker App","subtitle":"Health \u0026amp; Wellness","bodyText":"A comprehensive fitness tracking app with social features and gamification.","showCta":false,"className":"airo-wp-card\u002d\u002dhas-hover","textColor":"contrast","style":{"border":{"radius":"16px"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--standard airo-wp-card--style-default airo-wp-card--has-hover has-contrast-color has-text-color has-link-color" style="border-radius:16px"><span class="airo-wp-card__badge airo-wp-card__badge--floating airo-wp-card__badge--top-right" role="status" aria-label="Badge">Mobile App</span><div class="airo-wp-card__inner"><div class="airo-wp-card__image-wrapper"><img src="{{dsgo:placeholder-landscape}}" alt="Card image" class="airo-wp-card__image" style="aspect-ratio:16 / 9;object-fit:cover;object-position:50% 50%" loading="lazy" aria-hidden="true"/></div><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Fitness Tracker App</h3><p class="airo-wp-card__subtitle">Health &amp; Wellness</p><p class="airo-wp-card__body">A comprehensive fitness tracking app with social features and gamification.</p></div></div></div>
<!-- /wp:airo-wp/card --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"border":{"radius":"16px"}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-radius:16px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/card {"imageUrl":"{{dsgo:placeholder-landscape}}","badgeText":"Branding","title":"Brand Identity System","subtitle":"Visual Design","bodyText":"Complete brand identity including logo, guidelines, and marketing materials.","showCta":false,"className":"airo-wp-card\u002d\u002dhas-hover","textColor":"contrast","style":{"border":{"radius":"16px"},"elements":{"link":{"color":{"text":"var:preset|color|contrast"}}}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--standard airo-wp-card--style-default airo-wp-card--has-hover has-contrast-color has-text-color has-link-color" style="border-radius:16px"><span class="airo-wp-card__badge airo-wp-card__badge--floating airo-wp-card__badge--top-right" role="status" aria-label="Badge">Branding</span><div class="airo-wp-card__inner"><div class="airo-wp-card__image-wrapper"><img src="{{dsgo:placeholder-landscape}}" alt="Card image" class="airo-wp-card__image" style="aspect-ratio:16 / 9;object-fit:cover;object-position:50% 50%" loading="lazy" aria-hidden="true"/></div><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Brand Identity System</h3><p class="airo-wp-card__subtitle">Visual Design</p><p class="airo-wp-card__body">Complete brand identity including logo, guidelines, and marketing materials.</p></div></div></div>
<!-- /wp:airo-wp/card --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
