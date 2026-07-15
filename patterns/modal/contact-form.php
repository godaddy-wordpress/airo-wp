<?php
/**
 * Title: Contact Form Modal
 * Slug: airo-wp/modal/contact-form
 * Categories: airo-wp-modal
 * Description: Contact form in a modal with trigger button
 * Keywords: modal, contact, form, popup, email
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Contact Form Modal', 'airo-wp' ),
	'categories' => array( 'airo-wp-modal' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:group {"metadata":{"categories":["airo-wp-modal"],"patternName":"airo-wp/modal/contact-form","name":"Contact Form Modal"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:airo-wp/icon-button {"text":"Contact Us","url":"#airo-wp-modal-contact","icon":"envelope","iconGap":"8px"} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon" style="gap:8px" href="#airo-wp-modal-contact" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="envelope"></span><span class="airo-wp-icon-button__text">Contact Us</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/modal {"modalId":"airo-wp-modal-contact","animationType":"slide-up","overlayOpacity":75} -->
<div id="airo-wp-modal-contact" role="dialog" aria-modal="true" aria-label="Modal" aria-hidden="true" data-airo-wp-modal="true" data-modal-id="airo-wp-modal-contact" data-animation-type="slide-up" data-animation-duration="300" data-close-on-backdrop="true" data-close-on-esc="true" data-disable-body-scroll="true" data-allow-hash-trigger="true" data-update-url-on-open="false" data-auto-trigger-type="none" data-auto-trigger-delay="0" data-auto-trigger-frequency="always" data-cookie-duration="7" data-exit-intent-sensitivity="medium" data-exit-intent-min-time="5" data-exit-intent-exclude-mobile="true" data-scroll-depth="50" data-scroll-direction="down" data-time-on-page="30" data-gallery-group-id="" data-gallery-index="0" data-show-gallery-navigation="true" data-navigation-style="arrows" data-navigation-position="sides" class="wp-block-airo-wp-modal airo-wp-modal"><div class="airo-wp-modal__backdrop" style="background-color:#000000;opacity:0.75" aria-hidden="true"></div><div class="airo-wp-modal__dialog"><div class="airo-wp-modal__content" style="border-style:none;border-width:0px;width:600px;max-width:90vw"><!-- wp:heading -->
<h2 class="wp-block-heading">Get in Touch</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Have a question? We\'d love to hear from you.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/form-builder {"formId":"b375c2c9","submitButtonText":"Send Message","successMessage":"Thank you! Your message has been sent."} -->
<div class="wp-block-airo-wp-form-builder airo-wp-form-builder airo-wp-form-builder--align-left" style="--airo-wp-form-field-spacing:1.5rem;--airo-wp-form-input-height:44px;--airo-wp-form-input-padding:0.75rem;--airo-wp-form-label-color:;--airo-wp-form-border-color:#d1d5db;--airo-wp-form-field-bg:" data-form-id="b375c2c9" data-ajax-submit="true" data-success-message="Thank you! Your message has been sent." data-error-message="There was an error submitting the form. Please try again." data-submit-text="Send Message"><form class="airo-wp-form" method="post" novalidate><div class="airo-wp-form__fields"><!-- wp:airo-wp/form-text-field {"fieldName":"name","label":"Name","placeholder":"Your name","required":true} -->
<div class="wp-block-airo-wp-form-text-field airo-wp-form-field airo-wp-form-field--text" style="flex-basis:100%;max-width:100%"><label for="field-name" class="airo-wp-form-field__label">Name<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="text" id="field-name" name="name" class="airo-wp-form-field__input" placeholder="Your name" required aria-required="true" data-field-type="text"/></div>
<!-- /wp:airo-wp/form-text-field -->

<!-- wp:airo-wp/form-email-field {"fieldName":"email","placeholder":"your@email.com","required":true} -->
<div class="wp-block-airo-wp-form-email-field airo-wp-form-field airo-wp-form-field--email" style="flex-basis:100%;max-width:100%"><label for="field-email" class="airo-wp-form-field__label">Email<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="email" id="field-email" name="email" class="airo-wp-form-field__input" placeholder="your@email.com" required aria-required="true" data-field-type="email"/></div>
<!-- /wp:airo-wp/form-email-field -->

<!-- wp:airo-wp/form-textarea-field {"fieldName":"message","placeholder":"Your message...","required":true,"className":"wp-block-airo-wp-form-textarea"} -->
<div class="wp-block-airo-wp-form-textarea-field airo-wp-form-field airo-wp-form-field--textarea wp-block-airo-wp-form-textarea" style="flex-basis:100%;max-width:100%"><label for="field-message" class="airo-wp-form-field__label">Message<span class="airo-wp-form-field__required" aria-label="required">*</span></label><textarea id="field-message" name="message" class="airo-wp-form-field__textarea" placeholder="Your message..." required rows="4" aria-required="true" data-field-type="textarea"></textarea></div>
<!-- /wp:airo-wp/form-textarea-field --></div><input type="text" name="dsg_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"/><input type="hidden" name="dsg_form_id" value="b375c2c9"/><div class="airo-wp-form__footer"><button type="submit" class="airo-wp-form__submit wp-element-button" style="min-height:44px;padding-top:0.75rem;padding-bottom:0.75rem;padding-left:2rem;padding-right:2rem">Send Message</button></div><div class="airo-wp-form__message" role="status" aria-live="polite" aria-atomic="true" style="display:none"></div></form></div>
<!-- /wp:airo-wp/form-builder --><button class="airo-wp-modal__close airo-wp-modal__close--inside-top-right" style="width:24px;height:24px" type="button" aria-label="Close modal"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></button></div></div></div>
<!-- /wp:airo-wp/modal --></div>
<!-- /wp:group -->',
);
