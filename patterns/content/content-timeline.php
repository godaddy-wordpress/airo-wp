<?php
/**
 * Title: Timeline History
 * Slug: airo-wp/content/content-timeline
 * Categories: airo-wp-content
 * Description: Vertical timeline showing company history or process steps
 * Keywords: timeline, history, milestones, process, steps
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Timeline History', 'airo-wp' ),
	'categories'    => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'       => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}},"backgroundColor":"base","metadata":{"categories":["airo-wp-content"],"patternName":"airo-wp/content/content-timeline","name":"Timeline History"}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--70);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/section {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|60"},"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="margin-bottom:var(--wp--preset--spacing--60);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:heading {"textAlign":"center","fontSize":"x-large"} -->
<h2 class="wp-block-heading has-text-align-center has-x-large-font-size">Our Journey</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"medium"} -->
<p class="has-text-align-center has-medium-font-size">Key milestones that shaped who we are today</p>
<!-- /wp:paragraph -->

<!-- wp:airo-wp/timeline -->
<div class="wp-block-airo-wp-timeline airo-wp-timeline airo-wp-timeline--vertical airo-wp-timeline--layout-alternating airo-wp-timeline--marker-circle airo-wp-timeline--animate" style="--airo-wp-timeline-line-color:var(--wp--preset--color--contrast, #e5e7eb);--airo-wp-timeline-line-thickness:2px;--airo-wp-timeline-connector-style:solid;--airo-wp-timeline-marker-size:16px;--airo-wp-timeline-marker-color:var(--wp--preset--color--primary, #2563eb);--airo-wp-timeline-marker-border-color:var(--wp--preset--color--primary, #2563eb);--airo-wp-timeline-item-spacing:2rem;--airo-wp-timeline-animation-duration:600ms" data-animate="true" data-animation-duration="600" data-stagger-delay="100"><div class="airo-wp-timeline__line" aria-hidden="true"></div><div class="airo-wp-timeline__items"><!-- wp:airo-wp/timeline-item {"date":"2018","title":"Company Founded","uniqueId":"timeline-item-timeline-item-1"} -->
<div class="wp-block-airo-wp-timeline-item airo-wp-timeline-item"><div class="airo-wp-timeline-item__marker" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="var(--wp--preset--color--primary, #2563eb)" stroke="var(--wp--preset--color--primary, #2563eb)" stroke-width="2"></circle></svg></div><div class="airo-wp-timeline-item__wrapper"><span class="airo-wp-timeline-item__date">2018</span><h3 class="airo-wp-timeline-item__title">Company Founded</h3><div class="airo-wp-timeline-item__content"><!-- wp:paragraph {"placeholder":"Add timeline content…"} -->
<p>Started in a small garage with just two founders and a vision to transform the industry.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/timeline-item -->

<!-- wp:airo-wp/timeline-item {"date":"2020","title":"Series A Funding","uniqueId":"timeline-item-timeline-item-3"} -->
<div class="wp-block-airo-wp-timeline-item airo-wp-timeline-item"><div class="airo-wp-timeline-item__marker" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="var(--wp--preset--color--primary, #2563eb)" stroke="var(--wp--preset--color--primary, #2563eb)" stroke-width="2"></circle></svg></div><div class="airo-wp-timeline-item__wrapper"><span class="airo-wp-timeline-item__date">2020</span><h3 class="airo-wp-timeline-item__title">Series A Funding</h3><div class="airo-wp-timeline-item__content"><!-- wp:paragraph {"placeholder":"Add timeline content…"} -->
<p>Raised $10M in Series A funding to accelerate product development and expand our team.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/timeline-item -->

<!-- wp:airo-wp/timeline-item {"date":"2023","title":"Global Expansion","uniqueId":"timeline-item-timeline-item-5"} -->
<div class="wp-block-airo-wp-timeline-item airo-wp-timeline-item"><div class="airo-wp-timeline-item__marker" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="10" fill="var(--wp--preset--color--primary, #2563eb)" stroke="var(--wp--preset--color--primary, #2563eb)" stroke-width="2"></circle></svg></div><div class="airo-wp-timeline-item__wrapper"><span class="airo-wp-timeline-item__date">2023</span><h3 class="airo-wp-timeline-item__title">Global Expansion</h3><div class="airo-wp-timeline-item__content"><!-- wp:paragraph {"placeholder":"Add timeline content…"} -->
<p>Opened offices in London, Tokyo, and Sydney, reaching customers in over 100 countries.</p>
<!-- /wp:paragraph --></div></div></div>
<!-- /wp:airo-wp/timeline-item --></div></div>
<!-- /wp:airo-wp/timeline --></div></div>
<!-- /wp:airo-wp/section --></div></div>
<!-- /wp:airo-wp/section -->',
);
