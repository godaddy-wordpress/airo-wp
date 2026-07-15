<?php
/**
 * Title: Header Classic
 * Slug: airo-wp/header/header-classic
 * Categories: airo-wp-header
 * Description: A clean professional header with site logo on the left and navigation on the right
 * Keywords: header, navigation, classic, simple, professional
 * Block Types: core/template-part/header
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Header Classic', 'airo-wp' ),
	'categories' => array( 'airo-wp-header' ),
	'blockTypes' => array( 'core/template-part/header' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"tagName":"header","constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"blockGap":"0"}}} -->
<header class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"bottom":{"color":"var:preset|color|contrast","width":"1px","style":"solid"}}},"metadata":{"categories":["airo-wp-header"],"patternName":"airo-wp/header/header-classic","name":"Header Classic"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="border-bottom-color:var(--wp--preset--color--contrast);border-bottom-style:solid;border-bottom-width:1px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"0","right":"0"},"blockGap":"var(\u002d\u002dwp\u002d\u002dpreset\u002d\u002dspacing\u002d\u002d30)"}},"layout":{"type":"flex","justifyContent":"space-between","verticalAlignment":"center","flexWrap":"nowrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--30);padding-right:0;padding-bottom:var(--wp--preset--spacing--30);padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:nowrap;gap:var(--wp--preset--spacing--30)"><!-- wp:site-logo /-->

<!-- wp:navigation {"overlayBackgroundColor":"base","overlayTextColor":"contrast","layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap"}} /--></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></header>
<!-- /wp:airo-wp/section -->',
);
