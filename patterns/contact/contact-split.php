<?php
/**
 * Title: Contact Split
 * Slug: airo-wp/contact/contact-split
 * Categories: airo-wp-contact
 * Description: A light airy contact section with form and contact info
 * Keywords: contact, split, form, info, light
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Contact Split', 'airo-wp' ),
	'categories' => array( 'airo-wp-contact' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"metadata":{"categories":["airo-wp-contact"],"patternName":"airo-wp/contact/contact-split","name":"Contact Split"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/grid {"desktopColumns":2} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-2 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:stretch;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Let us Talk</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"fontSize":"medium"} -->
<p class="has-medium-font-size" style="margin-top:var(--wp--preset--spacing--30)">Have a question or want to work together? We are here to help you bring your ideas to life.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/icon-list {"iconColor":"vivid-cyan-blue","gap":"12px","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-airo-wp-icon-list airo-wp-icon-list airo-wp-icon-list--vertical" style="margin-top:var(--wp--preset--spacing--50);width:100%"><div class="airo-wp-icon-list__items" style="display:flex;flex-direction:column;gap:12px;align-items:flex-start;width:100%"><!-- wp:airo-wp/icon-list-item {"icon":"envelope","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="envelope"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading"><strong>Email us: </strong>hello@example.com</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"phone","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="phone"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading"><strong>Call Us: </strong>123-456-7890</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"location","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="location"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading"><strong>Visit Us:</strong> Address Here</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item -->

<!-- wp:airo-wp/icon-list-item {"icon":"clock","contentGap":8} -->
<div class="wp-block-airo-wp-icon-list-item airo-wp-icon-list-item airo-wp-icon-list-item--icon-left" style="display:flex;flex-direction:row;align-items:flex-start"><div class="airo-wp-icon-list-item__icon airo-wp-lazy-icon airo-wp-icon-list-item__icon--inherit-size" data-icon-name="clock"></div><div class="airo-wp-icon-list-item__content" style="text-align:left;display:flex;flex-direction:column;gap:8px"><!-- wp:heading {"level":4,"placeholder":"List item title…"} -->
<h4 class="wp-block-heading"><strong>Hours:</strong> Mon-Fri: 9AM - 6PM EST</h4>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/icon-list-item --></div></div>
<!-- /wp:airo-wp/icon-list -->

<!-- wp:social-links {"iconColor":"base","iconColorValue":"#FFFFFF","size":"has-normal-icon-size","className":"is-style-default","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<ul class="wp-block-social-links has-normal-icon-size has-icon-color is-style-default" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:social-link {"url":"#","service":"x"} /-->

<!-- wp:social-link {"url":"#","service":"linkedin"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"facebook"} /--></ul>
<!-- /wp:social-links --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size">Send a Message</h3>
<!-- /wp:heading -->

<!-- wp:airo-wp/form-builder {"formId":"cf17bd2a","successMessage":"Thanks! We will be in touch soon.","fieldSpacing":"1.5rem","inputHeight":"44px","inputPadding":"0.75rem","submitButtonPaddingVertical":"0.75rem","submitButtonPaddingHorizontal":"2rem","submitButtonHeight":"44px","className":"airo-wp-form","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"}}} -->
<div class="wp-block-airo-wp-form-builder airo-wp-form-builder airo-wp-form-builder--align-left airo-wp-form" style="margin-top:var(--wp--preset--spacing--40);--airo-wp-form-field-spacing:1.5rem;--airo-wp-form-input-height:44px;--airo-wp-form-input-padding:0.75rem" data-form-id="cf17bd2a" data-ajax-submit="true" data-success-message="Thanks! We will be in touch soon." data-error-message="There was an error submitting the form. Please try again."><form class="airo-wp-form" method="post" novalidate><div class="airo-wp-form__fields"><!-- wp:airo-wp/form-text-field {"fieldName":"field_f5d84e76","label":"First Name","placeholder":"John","required":true,"fieldWidth":"50"} -->
<div class="wp-block-airo-wp-form-text-field airo-wp-form-field airo-wp-form-field--text" style="flex-basis:calc(50% - var(--airo-wp-form-field-spacing, 1.5rem) / 2);max-width:calc(50% - var(--airo-wp-form-field-spacing, 1.5rem) / 2)"><label for="field-field_f5d84e76" class="airo-wp-form-field__label">First Name<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="text" id="field-field_f5d84e76" name="field_f5d84e76" class="airo-wp-form-field__input" placeholder="John" required aria-required="true" data-field-type="text"/></div>
<!-- /wp:airo-wp/form-text-field -->

<!-- wp:airo-wp/form-text-field {"fieldName":"field_409877ae","label":"Last Name","placeholder":"Doe","required":true,"fieldWidth":"50"} -->
<div class="wp-block-airo-wp-form-text-field airo-wp-form-field airo-wp-form-field--text" style="flex-basis:calc(50% - var(--airo-wp-form-field-spacing, 1.5rem) / 2);max-width:calc(50% - var(--airo-wp-form-field-spacing, 1.5rem) / 2)"><label for="field-field_409877ae" class="airo-wp-form-field__label">Last Name<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="text" id="field-field_409877ae" name="field_409877ae" class="airo-wp-form-field__input" placeholder="Doe" required aria-required="true" data-field-type="text"/></div>
<!-- /wp:airo-wp/form-text-field -->

<!-- wp:airo-wp/form-email-field {"fieldName":"field_1956efa8","placeholder":"john@example.com","required":true} -->
<div class="wp-block-airo-wp-form-email-field airo-wp-form-field airo-wp-form-field--email" style="flex-basis:100%;max-width:100%"><label for="field-field_1956efa8" class="airo-wp-form-field__label">Email<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="email" id="field-field_1956efa8" name="field_1956efa8" class="airo-wp-form-field__input" placeholder="john@example.com" required aria-required="true" data-field-type="email"/></div>
<!-- /wp:airo-wp/form-email-field -->

<!-- wp:airo-wp/form-textarea-field {"fieldName":"field_efaa1364","placeholder":"Tell us about your project...","required":true,"className":"wp-block-airo-wp-form-textarea"} -->
<div class="wp-block-airo-wp-form-textarea-field airo-wp-form-field airo-wp-form-field--textarea wp-block-airo-wp-form-textarea" style="flex-basis:100%;max-width:100%"><label for="field-field_efaa1364" class="airo-wp-form-field__label">Message<span class="airo-wp-form-field__required" aria-label="required">*</span></label><textarea id="field-field_efaa1364" name="field_efaa1364" class="airo-wp-form-field__textarea" placeholder="Tell us about your project..." required rows="4" aria-required="true" data-field-type="textarea"></textarea></div>
<!-- /wp:airo-wp/form-textarea-field --></div><input type="text" name="dsg_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"/><input type="hidden" name="dsg_form_id" value="cf17bd2a"/><div class="airo-wp-form__footer"><button type="submit" class="airo-wp-form__submit wp-element-button" style="min-height:44px;padding-top:0.75rem;padding-bottom:0.75rem;padding-left:2rem;padding-right:2rem">Submit</button></div><div class="airo-wp-form__message" role="status" aria-live="polite" aria-atomic="true" style="display:none"></div></form></div>
<!-- /wp:airo-wp/form-builder --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
