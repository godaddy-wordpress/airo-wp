<?php
/**
 * Title: Education Stats Counters
 * Slug: airo-wp/content/content-education-stats
 * Categories: airo-wp-content
 * Description: A dark statistics bar showing key education platform metrics like students, courses, instructors, and satisfaction rate
 * Keywords: stats, counters, education, students, courses, metrics
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'      => __( 'Education Stats Counters', 'airo-wp' ),
	'categories' => array( 'airo-wp-content' ),
	'viewportWidth' => 1200,
	'content'    => '<!-- wp:airo-wp/section {"style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-airo-wp-section alignfull airo-wp-stack" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--30)"><div class="airo-wp-stack__inner" style="max-width:var(--wp--style--global--content-size, 1140px);margin-left:auto;margin-right:auto"><!-- wp:airo-wp/counter-group {"columns":4,"animationDuration":2000,"animationDelay":200,"animationEasing":"easeOutExpo","className":"airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-2"} -->
<div class="wp-block-airo-wp-counter-group airo-wp-counter-group airo-wp-counter-group-cols-4 airo-wp-counter-group-cols-tablet-2 airo-wp-counter-group-cols-mobile-2" style="align-self:stretch;--airo-wp-counter-columns-desktop:4;--airo-wp-counter-columns-tablet:2;--airo-wp-counter-columns-mobile:1;--airo-wp-counter-gap:32px" data-animation-duration="2000" data-animation-delay="200" data-animation-easing="easeOutExpo" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter-group__inner airo-wp-counter-group__inner--align-center"><!-- wp:airo-wp/counter {"uniqueId":"edu-1","endValue":100000,"suffix":"+","label":"Students Enrolled"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="edu-1" style="text-align:center" data-start-value="0" data-end-value="100000" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Students Enrolled</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"edu-2","endValue":500,"suffix":"+","label":"Expert Courses"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="edu-2" style="text-align:center" data-start-value="0" data-end-value="500" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Expert Courses</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"edu-3","endValue":150,"suffix":"+","label":"Certified Instructors"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="edu-3" style="text-align:center" data-start-value="0" data-end-value="150" data-decimals="0" data-prefix="" data-suffix="+" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Certified Instructors</div></div>
<!-- /wp:airo-wp/counter -->

<!-- wp:airo-wp/counter {"uniqueId":"edu-4","endValue":95,"suffix":"%","label":"Satisfaction Rate"} -->
<div class="wp-block-airo-wp-counter airo-wp-counter" id="edu-4" style="text-align:center" data-start-value="0" data-end-value="95" data-decimals="0" data-prefix="" data-suffix="%" data-duration="2" data-delay="0" data-easing="easeOutQuad" data-use-grouping="true" data-separator="," data-decimal="."><div class="airo-wp-counter__content icon-top"><div class="airo-wp-counter__number"><span class="airo-wp-counter__value">0</span></div></div><div class="airo-wp-counter__label">Satisfaction Rate</div></div>
<!-- /wp:airo-wp/counter --></div></div>
<!-- /wp:airo-wp/counter-group --></div></div>
<!-- /wp:airo-wp/section -->',
);
