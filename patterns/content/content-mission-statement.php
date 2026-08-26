<?php
/**
 * Title: Mission Statement Section
 * Slug: airo-wp/content/content-mission-statement
 * Categories: airo-wp-content
 * Description: A two-column mission statement section with heading, description, icon list, and image
 * Keywords: mission, statement, about, nonprofit, charity, purpose
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Mission Statement Section', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-mission-statement","name":"Mission Statement Section"},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp","dsgoAnimationDuration":700} -->
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

<!-- wp:airo-wp/icon-button {"url":"#about","icon":"arrow-right","iconPosition":"end","iconGap":"8px","backgroundColor":"contrast","textColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"margin":{"top":"var:preset|spacing|30"}},"border":{"radius":"50px"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left" style="margin-top:var(--wp--preset--spacing--30)"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-base-color has-contrast-background-color has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:50px;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--40)" href="#about" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Learn About Our Work</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"16px"}}} -->
<figure class="wp-block-image size-large has-custom-border"><img src="{{dsgo:placeholder-landscape}}" alt="Children receiving education" style="border-radius:16px"/></figure>
<!-- /wp:image --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
