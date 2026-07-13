<?php
/**
 * Title: Sponsor Logos Bar
 * Slug: airo-wp/content/content-sponsor-logos
 * Categories: airo-wp-content
 * Description: A centered sponsor logo display bar for events and conferences
 * Keywords: sponsors, logos, partners, event, conference, bar
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Sponsor Logos Bar', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"align":"center","style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#6b7280"}},"fontSize":"small"} -->
<p class="has-text-align-center has-text-color has-small-font-size" style="color:#6b7280;letter-spacing:3px;text-transform:uppercase">Our Sponsors</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|60","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--60)"><!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#9ca3af"}},"fontSize":"large"} -->
<p class="has-text-color has-large-font-size" style="color:#9ca3af;font-style:normal;font-weight:700;letter-spacing:2px">TECHCORP</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#9ca3af"}},"fontSize":"large"} -->
<p class="has-text-color has-large-font-size" style="color:#9ca3af;font-style:normal;font-weight:700;letter-spacing:2px">CLOUDIFY</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#9ca3af"}},"fontSize":"large"} -->
<p class="has-text-color has-large-font-size" style="color:#9ca3af;font-style:normal;font-weight:700;letter-spacing:2px">DATAFLOW</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#9ca3af"}},"fontSize":"large"} -->
<p class="has-text-color has-large-font-size" style="color:#9ca3af;font-style:normal;font-weight:700;letter-spacing:2px">NEXUSAI</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"2px"},"color":{"text":"#9ca3af"}},"fontSize":"large"} -->
<p class="has-text-color has-large-font-size" style="color:#9ca3af;font-style:normal;font-weight:700;letter-spacing:2px">SCALABLE</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section -->',
);
