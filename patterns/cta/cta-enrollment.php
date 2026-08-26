<?php
/**
 * Title: Enrollment CTA
 * Slug: airo-wp/cta/cta-enrollment
 * Categories: airo-wp-cta
 * Description: A centered call-to-action encouraging course enrollment with heading, description, and sign-up button
 * Keywords: cta, enrollment, signup, education, courses, learning
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Enrollment CTA', 'airo-wp' ),
	'categories' => array( 'airo-wp-cta' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"contrast","textColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-color has-contrast-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size" style="font-style:normal;font-weight:700">Start Your Learning Journey Today</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Join our community of learners and unlock your potential. Get unlimited access to all courses with our subscription plans.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/row {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|20","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-top:var(--wp--preset--spacing--40);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:center;flex-wrap:wrap;gap:var(--wp--preset--spacing--20)"><!-- wp:airo-wp/icon-button {"url":"#signup","icon":"arrow-right","iconPosition":"end","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"8px"},"color":{"background":"#3b82f6","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-text-color has-background airo-wp-icon-button--has-icon airo-wp-icon-button--icon-end" style="border-radius:8px;color:#ffffff;background-color:#3b82f6;gap:8px;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#signup" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="arrow-right"></span><span class="airo-wp-icon-button__text">Get Started Free</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/icon-button {"url":"#pricing","icon":"","iconPosition":"none","iconGap":"8px","className":"has-text-color has-background","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"8px","width":"2px","color":"#ffffff"},"color":{"background":"transparent","text":"#ffffff"}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left has-text-color has-background"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button has-border-color has-text-color has-background" style="border-color:#ffffff;border-width:2px;border-radius:8px;color:#ffffff;background-color:transparent;padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--50)" href="#pricing" target="_self"><span class="airo-wp-icon-button__text">View Pricing</span></a></div>
<!-- /wp:airo-wp/icon-button --></div></div>
<!-- /wp:airo-wp/row --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->',
);
