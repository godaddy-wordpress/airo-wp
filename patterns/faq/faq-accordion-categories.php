<?php
/**
 * Title: FAQ Accordion with Categories
 * Slug: airo-wp/faq/faq-accordion-categories
 * Categories: airo-wp-faq
 * Description: FAQ section with categorized accordion panels
 * Keywords: faq, accordion, categories, questions, answers
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'FAQ Accordion with Categories', 'airo-wp' ),
	'categories'    => array( 'airo-wp-faq' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base-2","metadata":{"categories":["airo-wp-faq"],"patternName":"airo-wp/faq/faq-accordion-categories","name":"FAQ Accordion with Categories"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"className":"has-airo-wp-text-reveal"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-airo-wp-text-reveal" style="margin-bottom:var(--wp--preset--spacing--50);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Frequently Asked Questions</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}},"fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size" style="margin-top:var(--wp--preset--spacing--20)">Find answers to common questions about our services</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/grid {"desktopColumns":2,"tabletColumns":1,"style":{"spacing":{"blockGap":"var:preset|spacing|50","padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"alignItems":"start"} -->
<div class="wp-block-airo-wp-grid alignfull airo-wp-grid airo-wp-grid-cols-2 airo-wp-grid-cols-tablet-1 airo-wp-grid-cols-mobile-1 airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-grid__inner" style="display:grid;grid-template-columns:repeat(2, 1fr);align-items:start;row-gap:var(--wp--preset--spacing--50);column-gap:var(--wp--preset--spacing--50)"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--30);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:airo-wp/icon {"icon":"chat","className":"airo-wp-lazy-icon"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontWeight":"600"}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="font-weight:600">General Questions</h3>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>What services do you offer?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">We offer a comprehensive range of digital services including web design, development, digital marketing, SEO optimization, and ongoing maintenance and support for your website.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>How long does a typical project take?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">Project timelines vary based on complexity. A simple website typically takes 2-4 weeks, while more complex projects with custom functionality may take 6-12 weeks or longer.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>Do you work with clients remotely?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">Yes! We work with clients worldwide through video calls, email, and project management tools. Distance is never an obstacle to delivering exceptional results.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="padding-top:var(--wp--preset--spacing--40);padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--30);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:airo-wp/icon {"icon":"credit-card","className":"airo-wp-lazy-icon"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontWeight":"600"}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="font-weight:600">Pricing &amp; Payment</h3>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>What are your payment terms?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">We typically require 50% deposit upfront with the remaining balance due upon project completion. For larger projects, we can arrange milestone-based payments.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>Do you offer refunds?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">We offer a satisfaction guarantee. If you are not happy with our work, we will revise it until you are satisfied. Refunds are available within the first week if no substantial work has begun.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>What payment methods do you accept?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">We accept all major credit cards, PayPal, bank transfers, and can accommodate other payment methods upon request for international clients.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}},"layout":{"gridColumn":"span 5"}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--30);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:airo-wp/icon {"icon":"settings","className":"airo-wp-lazy-icon"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontWeight":"600"}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="font-weight:600">Technical Support</h3>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>Do you provide ongoing support?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">Yes, we offer various maintenance and support packages to keep your website secure, updated, and running smoothly after launch.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>Will I be able to update my website myself?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">Absolutely! We build on WordPress with user-friendly interfaces. We also provide training so you can confidently manage your content and make updates.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>What if something breaks on my site?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">With our support packages, we monitor your site and address issues quickly. We also maintain regular backups so your data is always protected.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div></div>
<!-- /wp:airo-wp/section -->

<!-- wp:airo-wp/section {"constrainWidth":false,"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"0","left":"0","right":"0"}}},"dsgoAnimationEnabled":true,"dsgoEntranceAnimation":"fadeInUp"} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack airo-wp-no-width-constraint has-airo-wp-animation airo-wp-animation-fadeInUp" style="padding-top:var(--wp--preset--spacing--40);padding-right:0;padding-bottom:0;padding-left:0" data-airo-wp-animation-enabled="true" data-airo-wp-entrance-animation="fadeInUp"><div class="airo-wp-stack__inner"><!-- wp:airo-wp/row {"style":{"spacing":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"},"margin":{"bottom":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-row alignfull airo-wp-flex airo-wp-no-width-constraint" style="margin-bottom:var(--wp--preset--spacing--30);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-flex__inner" style="display:flex;justify-content:left;flex-wrap:wrap"><!-- wp:airo-wp/icon {"icon":"shield","className":"airo-wp-lazy-icon"} /-->

<!-- wp:heading {"level":3,"style":{"typography":{"fontWeight":"600"}},"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size" style="font-weight:600">Security &amp; Privacy</h3>
<!-- /wp:heading --></div></div>
<!-- /wp:airo-wp/row -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>How do you ensure website security?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">We implement industry-standard security measures including SSL certificates, regular updates, secure hosting, firewalls, and malware scanning to protect your site.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>Is my data kept confidential?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">Yes, we take confidentiality seriously. All client data is protected under our privacy policy, and we can sign NDAs for sensitive projects upon request.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|20"},"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}},"border":{"radius":"12px"}},"backgroundColor":"base"} -->
<details class="wp-block-details has-base-background-color has-background" style="border-radius:12px;margin-bottom:var(--wp--preset--spacing--20);padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><summary>Do you comply with GDPR?</summary><!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20"}}}} -->
<p style="margin-top:var(--wp--preset--spacing--20)">Yes, we ensure all websites we build are GDPR compliant with proper cookie notices, privacy policies, and data handling procedures in place.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/grid --></div></div>
<!-- /wp:airo-wp/section -->',
);
