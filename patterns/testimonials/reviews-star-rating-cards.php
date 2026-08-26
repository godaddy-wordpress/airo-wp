<?php
/**
 * Title: Reviews with Star Rating
 * Slug: airo-wp/testimonials/reviews-star-rating-cards
 * Categories: airo-wp-testimonials
 * Description: Customer reviews in cards, each headed by a Star Rating block with a review count
 * Keywords: reviews, ratings, stars, testimonials, customers
 */

defined( 'ABSPATH' ) || exit;

/*
 * The older `testimonials-rating-cards` pattern draws its stars as five
 * separate Icon blocks, which can only ever show whole stars and tells a
 * screen reader nothing. This one uses a single Star Rating block per card:
 * one block that carries the value, renders half stars, reads out as a
 * sentence, and can be connected to a dynamic source per card.
 */

return array(
	'title'      => __( 'Reviews with Star Rating', 'airo-wp' ),
	'categories' => array( 'airo-wp-testimonials' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base","metadata":{"categories":["airo-wp-testimonials"],"patternName":"airo-wp/testimonials/reviews-star-rating-cards","name":"Reviews with Star Rating"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">What Reviewers Say</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">Verified reviews, scored out of five</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"style":{"spacing":{"blockGap":"var:preset|spacing|40","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--40);column-gap:var(--wp--preset--spacing--40)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px","width":"1px","color":"#e2e8f0"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-border-color has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-color:#e2e8f0;border-width:1px;border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/star-rating {"rating":5,"iconSize":22,"ratingColor":"#f59e0b","showValue":true,"showCount":true,"ratingCount":86,"countTemplate":"(%s reviews)","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--30)">"This product has completely transformed how we work. The interface is intuitive and the features are exactly what we needed. Highly recommended!"</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:image {"width":"48px","height":"48px","scale":"cover","sizeSlug":"thumbnail","className":"is-style-rounded"} -->
<figure class="wp-block-image size-thumbnail is-resized is-style-rounded"><img src="{{dsgo:placeholder-avatar}}" alt="Amanda Peters" style="object-fit:cover;width:48px;height:48px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-weight:600">Amanda Peters</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#64748b"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#64748b">Marketing Director</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px","width":"1px","color":"#e2e8f0"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-border-color has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-color:#e2e8f0;border-width:1px;border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/star-rating {"rating":4.5,"iconSize":22,"ratingColor":"#f59e0b","showValue":true,"showCount":true,"ratingCount":142,"countTemplate":"(%s reviews)","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--30)">"The customer support is outstanding. Every time I had a question, they responded quickly and resolved my issues efficiently. Great experience overall."</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:image {"width":"48px","height":"48px","scale":"cover","sizeSlug":"thumbnail","className":"is-style-rounded"} -->
<figure class="wp-block-image size-thumbnail is-resized is-style-rounded"><img src="{{dsgo:placeholder-avatar}}" alt="David Kim" style="object-fit:cover;width:48px;height:48px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-weight:600">David Kim</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#64748b"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#64748b">Software Engineer</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"16px","width":"1px","color":"#e2e8f0"}},"backgroundColor":"base","dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-border-color has-base-background-color has-background has-airo-wp-animation airo-wp-animation-fadeInUp" style="border-color:#e2e8f0;border-width:1px;border-radius:16px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/star-rating {"rating":5,"iconSize":22,"ratingColor":"#f59e0b","showValue":true,"showCount":true,"ratingCount":63,"countTemplate":"(%s reviews)","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"}}}} /-->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<p style="margin-bottom:var(--wp--preset--spacing--30)">"We have seen a 40% increase in productivity since implementing this solution. The ROI has been incredible and the team loves using it daily."</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:image {"width":"48px","height":"48px","scale":"cover","sizeSlug":"thumbnail","className":"is-style-rounded"} -->
<figure class="wp-block-image size-thumbnail is-resized is-style-rounded"><img src="{{dsgo:placeholder-avatar}}" alt="Sarah Mitchell" style="object-fit:cover;width:48px;height:48px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}},"fontSize":"small"} -->
<p class="has-small-font-size" style="font-weight:600">Sarah Mitchell</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"color":{"text":"#64748b"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#64748b">Operations Manager</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
