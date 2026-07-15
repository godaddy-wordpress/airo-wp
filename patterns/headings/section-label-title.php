<?php
/**
 * Title: Section Label + Title
 * Slug: airo-wp/headings/section-label-title
 * Categories: airo-wp-headings
 * Description: Section heading with a small uppercase label and large title using the Advanced Heading block, followed by a description
 * Keywords: heading, typography, label, section, title, uppercase, advanced
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Section Label + Title', 'airo-wp' ),
	'categories'    => array( 'airo-wp-headings' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"metadata":{"categories":["airo-wp-headings"],"patternName":"airo-wp/headings/section-label-title","name":"Section Label + Title"}} --><div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/advanced-heading {"level":2,"style":{"spacing":{"blockGap":"0.5em"}},"fontSize":"x-large"} --><div class="wp-block-airo-wp-advanced-heading airo-wp-advanced-heading has-x-large-font-size"><h2 class="airo-wp-advanced-heading__inner" style="--airo-wp-segment-gap:0.5em"><!-- wp:airo-wp/heading-segment {"style":{"typography":{"fontWeight":"600","textTransform":"uppercase","letterSpacing":"2px"}},"textColor":"accent-3","fontSize":"small"} --><span class="wp-block-airo-wp-heading-segment airo-wp-heading-segment has-accent-3-color has-text-color has-small-font-size" style="font-weight:600;letter-spacing:2px;text-transform:uppercase"><span class="airo-wp-heading-segment__text">Our Services</span></span><!-- /wp:airo-wp/heading-segment --><!-- wp:airo-wp/heading-segment {"style":{"typography":{"fontWeight":"700"}}} --><span class="wp-block-airo-wp-heading-segment airo-wp-heading-segment" style="font-weight:700"><span class="airo-wp-heading-segment__text">What we do best</span></span><!-- /wp:airo-wp/heading-segment --></h2></div><!-- /wp:airo-wp/advanced-heading --><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"textColor":"contrast-2","fontSize":"medium"} --><p class="has-contrast-2-color has-text-color has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">We combine strategy, design, and technology to create digital experiences that drive real results for your business.</p><!-- /wp:paragraph --><!-- wp:airo-wp/divider {"thickness":1,"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} --><div class="wp-block-airo-wp-divider airo-wp-divider airo-wp-divider--solid" style="margin-top:var(--wp--preset--spacing--40)"><div class="airo-wp-divider__container" style="width:100%"><div class="airo-wp-divider__line" style="height:1px"></div></div></div><!-- /wp:airo-wp/divider --></div></div><!-- /wp:airo-wp/section -->',
);
