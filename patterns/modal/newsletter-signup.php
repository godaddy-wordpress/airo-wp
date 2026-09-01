<?php
/**
 * Title: Newsletter Signup Modal
 * Slug: airo-wp/modal/newsletter-signup
 * Categories: airo-wp-modal
 * Description: Newsletter subscription modal with exit intent trigger and form
 * Keywords: modal, newsletter, signup, subscribe, email
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Newsletter Signup Modal', 'airo-wp' ),
	'categories' => array( 'airo-wp-modal' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:group {"metadata":{"categories":["airo-wp-modal"],"patternName":"airo-wp/modal/newsletter-signup","name":"Newsletter Signup Modal"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:airo-wp/icon-button {"url":"#airo-wp-modal-newsletter","icon":"envelope","iconGap":"8px","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|60","right":"var:preset|spacing|60"}}}} -->
<div class="wp-block-airo-wp-icon-button airo-wp-justify airo-wp-justify--left"><a class="airo-wp-icon-button wp-block-button wp-block-button__link wp-element-button airo-wp-icon-button--has-icon" style="gap:8px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--60)" href="#airo-wp-modal-newsletter" target="_self"><span class="airo-wp-icon-button__icon airo-wp-lazy-icon" data-icon-name="envelope"></span><span class="airo-wp-icon-button__text">Subscribe to Newsletter</span></a></div>
<!-- /wp:airo-wp/icon-button -->

<!-- wp:airo-wp/modal {"modalId":"airo-wp-modal-newsletter","autoTriggerType":"exitIntent","autoTriggerFrequency":"once","cookieDuration":30,"width":"500px","animationType":"slide-up"} -->
<div id="airo-wp-modal-newsletter" role="dialog" aria-modal="true" aria-label="Modal" aria-hidden="true" data-airo-wp-modal="true" data-modal-id="airo-wp-modal-newsletter" data-animation-type="slide-up" data-animation-duration="300" data-close-on-backdrop="true" data-close-on-esc="true" data-disable-body-scroll="true" data-allow-hash-trigger="true" data-update-url-on-open="false" data-auto-trigger-type="exitIntent" data-auto-trigger-delay="0" data-auto-trigger-frequency="once" data-cookie-duration="30" data-exit-intent-sensitivity="medium" data-exit-intent-min-time="5" data-exit-intent-exclude-mobile="true" data-scroll-depth="50" data-scroll-direction="down" data-time-on-page="30" data-gallery-group-id="" data-gallery-index="0" data-show-gallery-navigation="true" data-navigation-style="arrows" data-navigation-position="sides" class="wp-block-airo-wp-modal airo-wp-modal"><div class="airo-wp-modal__backdrop" style="opacity:0.8" aria-hidden="true"></div><div class="airo-wp-modal__dialog"><div class="airo-wp-modal__content" style="border-style:none;border-width:0px;width:500px;max-width:90vw"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Subscribe to Our Newsletter</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Get the latest updates and exclusive content delivered to your inbox.</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/form-builder {"formId":"8ec8a00c","submitButtonAlignment":"center","successMessage":"Thank you for subscribing!","fieldSpacing":"1.5rem","inputHeight":"44px","inputPadding":"0.75rem","submitButtonPaddingVertical":"0.75rem","submitButtonPaddingHorizontal":"2rem","submitButtonHeight":"44px"} -->
<div class="wp-block-airo-wp-form-builder airo-wp-form-builder airo-wp-form-builder--align-center" style="--airo-wp-form-field-spacing:1.5rem;--airo-wp-form-input-height:44px;--airo-wp-form-input-padding:0.75rem" data-form-id="8ec8a00c" data-ajax-submit="true" data-success-message="Thank you for subscribing!" data-error-message="There was an error submitting the form. Please try again."><form class="airo-wp-form" method="post" novalidate><div class="airo-wp-form__fields"><!-- wp:airo-wp/form-email-field {"fieldName":"email","label":"Email Address","placeholder":"your@email.com","required":true} -->
<div class="wp-block-airo-wp-form-email-field airo-wp-form-field airo-wp-form-field--email" style="flex-basis:100%;max-width:100%"><label for="field-email" class="airo-wp-form-field__label">Email Address<span class="airo-wp-form-field__required" aria-label="required">*</span></label><input type="email" id="field-email" name="email" class="airo-wp-form-field__input" placeholder="your@email.com" required aria-required="true" data-field-type="email"/></div>
<!-- /wp:airo-wp/form-email-field --></div><input type="text" name="dsg_website" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden"/><input type="hidden" name="dsg_form_id" value="8ec8a00c"/><div class="airo-wp-form__footer"><button type="submit" class="airo-wp-form__submit wp-element-button" style="min-height:44px;padding-top:0.75rem;padding-bottom:0.75rem;padding-left:2rem;padding-right:2rem">Subscribe</button></div><div class="airo-wp-form__message" role="status" aria-live="polite" aria-atomic="true" style="display:none"></div></form></div>
<!-- /wp:airo-wp/form-builder --><button class="airo-wp-modal__close airo-wp-modal__close--inside-top-right" style="width:24px;height:24px" type="button" aria-label="Close modal"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg></button></div></div></div>
<!-- /wp:airo-wp/modal --></div>
<!-- /wp:group -->',
);
