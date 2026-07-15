<?php
/**
 * Title: Quote Highlight
 * Slug: airo-wp/content/content-quote-highlight
 * Categories: airo-wp-content
 * Description: Featured quote or testimonial with large typography
 * Keywords: quote, testimonial, blockquote, highlight, featured
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Quote Highlight', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"color":{"gradient":"linear-gradient(135deg,rgb(30,27,75) 0%,rgb(49,46,129) 100%)"}},"className":"has-airo-wp-parallax","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-quote-highlight","name":"Quote Highlight"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-parallax has-background" style="background:linear-gradient(135deg,rgb(30,27,75) 0%,rgb(49,46,129) 100%);padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"className":"has-airo-wp-text-reveal"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-text-reveal" style="padding-top:0;padding-right:var(--wp--preset--spacing--50);padding-bottom:0;padding-left:var(--wp--preset--spacing--50)"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/icon {"icon":"quote","className":"airo-wp-lazy-icon","textColor":"base","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}},"elements":{"link":{"color":{"text":"var:preset|color|base"}}}}} /-->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"clamp(1.5rem, 4vw, 2.5rem)","lineHeight":"1.4","fontStyle":"italic","fontWeight":"400"}},"textColor":"base"} -->
<p class="has-text-align-center has-base-color has-text-color" style="font-size:clamp(1.5rem, 4vw, 2.5rem);font-style:italic;font-weight:400;line-height:1.4">The best way to predict the future is to create it. Innovation distinguishes between a leader and a follower.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/divider {"thickness":1,"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}}}} -->
<div class="wp-block-airo-wp-divider aligncenter airo-wp-divider airo-wp-divider--solid" style="margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)"><div class="airo-wp-divider__container" style="width:100%"><div class="airo-wp-divider__line" style="height:1px"></div></div></div>
<!-- /wp:airo-wp/divider -->

<!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","orientation":"horizontal","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap"><!-- wp:image {"width":"60px","height":"60px","scale":"cover","sizeSlug":"thumbnail","align":"center","className":"is-style-rounded"} -->
<figure class="wp-block-image aligncenter size-thumbnail is-resized is-style-rounded"><img src="{{dsgo:placeholder-avatar}}" alt="Steve Jobs" style="object-fit:cover;width:60px;height:60px"/></figure>
<!-- /wp:image -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:paragraph {"align":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"600"}},"textColor":"base"} -->
<p class="has-text-align-center has-base-color has-text-color" style="font-style:normal;font-weight:600">Steve Jobs</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center","style":{"color":{"text":"rgba(255,255,255,0.7)"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:rgba(255,255,255,0.7)">Co-founder, Apple Inc.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->',
);
