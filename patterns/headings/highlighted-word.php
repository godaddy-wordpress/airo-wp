<?php
/**
 * Title: Highlighted Word Heading
 * Slug: airo-wp/headings/highlighted-word
 * Categories: airo-wp-headings
 * Description: Heading with a highlighted word using background color and rounded padding
 * Keywords: heading, typography, highlight, badge, background, accent
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Highlighted Word Heading', 'airo-wp' ),
	'categories'    => array( 'airo-wp-headings' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"metadata":{"categories":["airo-wp-headings"],"patternName":"airo-wp/headings/highlighted-word","name":"Highlighted Word Heading"}} --><div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/advanced-heading {"level":2,"textAlign":"center","fontSize":"x-large"} --><div class="wp-block-airo-wp-advanced-heading airo-wp-advanced-heading has-text-align-center has-x-large-font-size"><h2 class="airo-wp-advanced-heading__inner"><!-- wp:airo-wp/heading-segment {"style":{"typography":{"fontWeight":"600"}}} --><span class="wp-block-airo-wp-heading-segment airo-wp-heading-segment" style="font-weight:600"><span class="airo-wp-heading-segment__text">Your next</span></span><!-- /wp:airo-wp/heading-segment --><!-- wp:airo-wp/heading-segment {"style":{"typography":{"fontWeight":"700"},"spacing":{"padding":{"top":"0.1em","right":"0.4em","bottom":"0.1em","left":"0.4em"}},"border":{"radius":"4px"}},"backgroundColor":"accent-3","textColor":"base"} --><span class="wp-block-airo-wp-heading-segment airo-wp-heading-segment has-base-color has-accent-3-background-color has-text-color has-background" style="border-radius:4px;padding-top:0.1em;padding-right:0.4em;padding-bottom:0.1em;padding-left:0.4em;font-weight:700"><span class="airo-wp-heading-segment__text">big idea</span></span><!-- /wp:airo-wp/heading-segment --><!-- wp:airo-wp/heading-segment {"style":{"typography":{"fontWeight":"600"}}} --><span class="wp-block-airo-wp-heading-segment airo-wp-heading-segment" style="font-weight:600"><span class="airo-wp-heading-segment__text">starts here.</span></span><!-- /wp:airo-wp/heading-segment --></h2></div><!-- /wp:airo-wp/advanced-heading --></div></div><!-- /wp:airo-wp/section -->',
);
