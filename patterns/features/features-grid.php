<?php
/**
 * Title: Features Grid
 * Slug: airo-wp/features/features-grid
 * Categories: airo-wp-features
 * Description: A clean 3-column feature grid with cards and icons
 * Keywords: features, grid, cards, icons, minimal
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Features Grid', 'airo-wp' ),
	'categories' => array( 'airo-wp-features' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"metadata":{"categories":["airo-wp-features"],"patternName":"airo-wp/features/features-grid","name":"Features Grid"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:700">Everything You Need</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Powerful features to help you build better websites, faster.</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-3 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(3, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--30);column-gap:var(--wp--preset--spacing--30)"><!-- wp:airo-wp/card {"layoutPreset":"minimal","title":"Lightning Fast","bodyText":"Optimized for performance with lazy loading, minimal CSS, and efficient JavaScript.","visualStyle":"shadow","showImage":false,"showCta":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-shadow" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Lightning Fast</h3><p class="airo-wp-card__body">Optimized for performance with lazy loading, minimal CSS, and efficient JavaScript.</p></div></div></div>
<!-- /wp:airo-wp/card -->

<!-- wp:airo-wp/card {"layoutPreset":"minimal","title":"Fully Responsive","bodyText":"Every block adapts beautifully to any screen size, from mobile to desktop.","visualStyle":"shadow","showImage":false,"showCta":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-shadow" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Fully Responsive</h3><p class="airo-wp-card__body">Every block adapts beautifully to any screen size, from mobile to desktop.</p></div></div></div>
<!-- /wp:airo-wp/card -->

<!-- wp:airo-wp/card {"layoutPreset":"minimal","title":"Accessible","bodyText":"Built with WCAG guidelines in mind, ensuring your site is usable by everyone.","visualStyle":"shadow","showImage":false,"showCta":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-shadow" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Accessible</h3><p class="airo-wp-card__body">Built with WCAG guidelines in mind, ensuring your site is usable by everyone.</p></div></div></div>
<!-- /wp:airo-wp/card -->

<!-- wp:airo-wp/card {"layoutPreset":"minimal","title":"Easy to Customize","bodyText":"Adjust colors, spacing, typography, and more using the familiar WordPress interface.","visualStyle":"shadow","showImage":false,"showCta":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-shadow" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Easy to Customize</h3><p class="airo-wp-card__body">Adjust colors, spacing, typography, and more using the familiar WordPress interface.</p></div></div></div>
<!-- /wp:airo-wp/card -->

<!-- wp:airo-wp/card {"layoutPreset":"minimal","title":"Theme Compatible","bodyText":"Works seamlessly with any WordPress theme that supports the block editor.","visualStyle":"shadow","showImage":false,"showCta":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-shadow" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Theme Compatible</h3><p class="airo-wp-card__body">Works seamlessly with any WordPress theme that supports the block editor.</p></div></div></div>
<!-- /wp:airo-wp/card -->

<!-- wp:airo-wp/card {"layoutPreset":"minimal","title":"Regular Updates","bodyText":"Constantly improved with new features, bug fixes, and WordPress compatibility.","visualStyle":"shadow","showImage":false,"showCta":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}}} -->
<div class="wp-block-airo-wp-card airo-wp-card airo-wp-card--minimal airo-wp-card--style-shadow" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-card__inner"><div class="airo-wp-card__content "><h3 class="airo-wp-card__title">Regular Updates</h3><p class="airo-wp-card__body">Constantly improved with new features, bug fixes, and WordPress compatibility.</p></div></div></div>
<!-- /wp:airo-wp/card --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
