<?php
/**
 * Title: Volunteer Signup CTA
 * Slug: airo-wp/cta/cta-volunteer-signup
 * Categories: airo-wp-cta
 * Description: A two-column call-to-action with volunteer information on one side and signup form on the other
 * Keywords: cta, volunteer, signup, nonprofit, charity, form
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Volunteer Signup CTA', 'airo-wp' ),
	'categories' => array( 'airo-wp-cta' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--80);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2,"style":{"spacing":{"blockGap":"var:preset|spacing|70","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"center"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:center;row-gap:var(--wp--preset--spacing--70);column-gap:var(--wp--preset--spacing--70)"><!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"3px"},"color":{"text":"#10b981"}},"fontSize":"small"} -->
<p class="has-text-color has-small-font-size" style="color:#10b981;letter-spacing:3px;text-transform:uppercase">Get Involved</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"style":{"typography":{"fontStyle":"normal","fontWeight":"700"},"spacing":{"margin":{"top":"var:preset|spacing|10"}}},"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size" style="margin-top:var(--wp--preset--spacing--10);font-style:normal;font-weight:700">Ways to Make a Difference</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">There are many ways to support our mission beyond financial donations. Your time, skills, and voice can make a tremendous impact.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/accordion {"className":"airo-wp-accordion\\u002d\\u002dicon-chevron","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-accordion airo-wp-accordion airo-wp-accordion--icon-right airo-wp-accordion--border-between airo-wp-accordion--icon-chevron" style="margin-top:var(--wp--preset--spacing--30);--airo-wp-accordion-open-bg:;--airo-wp-accordion-open-text:;--airo-wp-accordion-hover-bg:;--airo-wp-accordion-hover-text:;--airo-wp-accordion-gap:0.5rem" data-allow-multiple="false" data-icon-style="chevron"><div class="airo-wp-accordion__items"><!-- wp:airo-wp/accordion-item {"title":"Volunteer Your Time","isOpen":true,"uniqueId":"accordion-item-gnr58j9ru"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--open" data-initially-open="true"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="true" aria-controls="accordion-item-gnr58j9ru-panel" id="accordion-item-gnr58j9ru-header"><span class="airo-wp-accordion-item__title">Volunteer Your Time</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-gnr58j9ru-header" id="accordion-item-gnr58j9ru-panel"><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Join our volunteer network and make a hands-on difference. We offer both local and international volunteer opportunities, from community events to field missions.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item -->

<!-- wp:airo-wp/accordion-item {"title":"Become a Monthly Donor","uniqueId":"accordion-item-hbvzkpaxd"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--closed" data-initially-open="false"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="false" aria-controls="accordion-item-hbvzkpaxd-panel" id="accordion-item-hbvzkpaxd-header"><span class="airo-wp-accordion-item__title">Become a Monthly Donor</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-hbvzkpaxd-header" id="accordion-item-hbvzkpaxd-panel" hidden><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Monthly giving provides sustainable support that allows us to plan long-term projects. Even $10/month can provide school supplies for a child for an entire year.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item -->

<!-- wp:airo-wp/accordion-item {"title":"Start a Fundraiser","uniqueId":"accordion-item-dtm880dum"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--closed" data-initially-open="false"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="false" aria-controls="accordion-item-dtm880dum-panel" id="accordion-item-dtm880dum-header"><span class="airo-wp-accordion-item__title">Start a Fundraiser</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-dtm880dum-header" id="accordion-item-dtm880dum-panel" hidden><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Celebrate your birthday, run a marathon, or host an event to raise funds for our cause. We provide all the tools and support you need to succeed.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item -->

<!-- wp:airo-wp/accordion-item {"title":"Corporate Partnerships","uniqueId":"accordion-item-6n0si8m1l"} -->
<div class="wp-block-airo-wp-accordion-item airo-wp-accordion-item airo-wp-accordion-item--closed" data-initially-open="false"><div class="airo-wp-accordion-item__header"><button type="button" class="airo-wp-accordion-item__trigger airo-wp-accordion-item__trigger--icon-right" aria-expanded="false" aria-controls="accordion-item-6n0si8m1l-panel" id="accordion-item-6n0si8m1l-header"><span class="airo-wp-accordion-item__title">Corporate Partnerships</span><span class="airo-wp-accordion-item__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M4.427 6.427l3.396 3.396a.25.25 0 00.354 0l3.396-3.396A.25.25 0 0011.396 6H4.604a.25.25 0 00-.177.427z"></path></svg></span></button></div><div class="airo-wp-accordion-item__panel" role="region" aria-labelledby="accordion-item-6n0si8m1l-header" id="accordion-item-6n0si8m1l-panel" hidden><div class="airo-wp-accordion-item__content"><!-- wp:paragraph -->
<p>Partner with us to make a larger impact. We offer sponsorship opportunities, employee engagement programs, and cause-related marketing partnerships.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/accordion-item --></div></div>
<!-- /wp:airo-wp/accordion --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="border-radius:12px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"level":3,"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="margin-bottom:var(--wp--preset--spacing--30)">Sign Up to Volunteer</h3>
<!-- /wp:heading -->

<!-- wp:airo-wp/form-builder {"formId":"volunteer-form","fieldSpacing":"1.5rem","inputHeight":"44px","inputPadding":"0.75rem","submitButtonPaddingVertical":"0.75rem","submitButtonPaddingHorizontal":"2rem","submitButtonHeight":"44px","className":"airo-wp-form"} -->
<div class="wp-block-airo-wp-form-builder airo-wp-form-builder airo-wp-form-builder--align-left airo-wp-form" style="--airo-wp-form-field-spacing:1.5rem;--airo-wp-form-input-height:44px;--airo-wp-form-input-padding:0.75rem" data-form-id="volunteer-form" data-ajax-submit="true" data-success-message="Thank you! Your form has been submitted successfully." data-error-message="There was an error submitting the form. Please try again."><form class="airo-wp-form" method="post" novalidate><div class="airo-wp-form__fields"><!-- wp:airo-wp/form-text-field {"fieldName":"field_3d375a5f","label":"Full Name","placeholder":"Enter your full name","required":true} -->
<div class="wp-block-airo-wp-form-text-field airo-wp-form-field airo-wp-form-field--text" style="flex-basis:100%;max-width:100%"><label for="field-field_3d375a5f" class="airo-wp-form-field__label">Full Name<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="text" id="field-field_3d375a5f" name="field_3d375a5f" class="airo-wp-form-field__input" placeholder="Enter your full name" required aria-required="true" data-field-type="text"/></div>
<!-- /wp:airo-wp/form-text-field -->

<!-- wp:airo-wp/form-email-field {"fieldName":"field_5213fa62","label":"Email Address","placeholder":"Enter your email","required":true} -->
<div class="wp-block-airo-wp-form-email-field airo-wp-form-field airo-wp-form-field--email" style="flex-basis:100%;max-width:100%"><label for="field-field_5213fa62" class="airo-wp-form-field__label">Email Address<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="email" id="field-field_5213fa62" name="field_5213fa62" class="airo-wp-form-field__input" placeholder="Enter your email" required aria-required="true" data-field-type="email"/></div>
<!-- /wp:airo-wp/form-email-field -->

<!-- wp:airo-wp/form-select-field {"fieldName":"select-662eef03","label":"Area of Interest","options":[{"value":"education","label":"Education Programs"},{"value":"water","label":"Clean Water Projects"},{"value":"healthcare","label":"Healthcare Initiatives"},{"value":"economic","label":"Economic Empowerment"},{"value":"events","label":"Local Events \\u0026 Fundraising"}]} -->
<div class="wp-block-airo-wp-form-select-field airo-wp-form-field airo-wp-form-field--select" style="flex-basis:100%;max-width:100%"><label for="field-select-662eef03" class="airo-wp-form-field__label">Area of Interest</label><select id="field-select-662eef03" name="select-662eef03" class="airo-wp-form-field__select" data-field-type="select"><option value="">-- Select an option --</option><option value="education">Education Programs</option><option value="water">Clean Water Projects</option><option value="healthcare">Healthcare Initiatives</option><option value="economic">Economic Empowerment</option><option value="events">Local Events &amp; Fundraising</option></select></div>
<!-- /wp:airo-wp/form-select-field --></div><input type="text" name="dsg_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"/><input type="hidden" name="dsg_form_id" value="volunteer-form"/><div class="airo-wp-form__footer"><button type="submit" class="airo-wp-form__submit wp-element-button" style="min-height:44px;padding-top:0.75rem;padding-bottom:0.75rem;padding-left:2rem;padding-right:2rem">Submit Application</button></div><div class="airo-wp-form__message" role="status" aria-live="polite" aria-atomic="true" style="display:none"></div></form></div>
<!-- /wp:airo-wp/form-builder --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
