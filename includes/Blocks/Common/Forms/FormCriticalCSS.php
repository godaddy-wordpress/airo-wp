<?php
/**
 * Critical inline CSS for Form Builder conditional fields and steps.
 *
 * The form block stylesheets are deferred (media="print" + onload swap, see
 * Assets::optimize_css_loading()), so a rule that lives in them applies after
 * first paint. The conditional-field pre-hide has to apply before the fields
 * paint, so it is printed inline ahead of every form that needs it. The same
 * holds for multi-step forms: later steps and the submit footer are hidden
 * until the steps script marks the form ready.
 *
 * @package airo-wp
 * @since 2.10.0
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Blocks\Common\Forms;

defined( 'ABSPATH' ) || exit;
/**
 * Form_Critical_CSS.
 */
class FormCriticalCSS {

	/**
	 * Conditional fields are hidden until the view script has applied the
	 * rules, but only when scripting is available: without JavaScript every
	 * field shows and the server drops answers to fields whose rules aren't
	 * met. The pre-hide expires: if the view script never runs (blocked,
	 * errored, failed to load), the fields show after 4s instead of staying
	 * hidden while still required. Browsers that can't animate `display`
	 * don't pre-hide.
	 *
	 * Steps: every step after the first, and the submit footer, are pre-hidden
	 * until the steps script sets `data-airo-wp-steps-ready`; the footer rule uses
	 * `:has()` and is its own rule so an engine without it still applies the
	 * step pre-hide (an unsupported selector voids its whole list).
	 *
	 * `[hidden]` is included because the fields' inline per-block
	 * `display:flex` otherwise beats the UA rule until the deferred form
	 * stylesheet loads, flashing fields the view script has already hidden.
	 */
	const CSS = '.airo-wp-form-field[hidden],[data-airo-wp-step][hidden],.airo-wp-form-steps__nav[hidden],.airo-wp-form-steps__nav [hidden],.airo-wp-form__footer[hidden],.airo-wp-form__submit--inline[hidden],.airo-wp-form-steps__progress[hidden],[data-airo-wp-turnstile-container][hidden]{display:none}'
		. '@media (scripting:enabled){'
		. '.airo-wp-form-builder:not([data-airo-wp-conditions-ready]) .airo-wp-form-field--conditional{animation:airo-wp-conditions-pending 4s}'
		. '.airo-wp-form-builder:not([data-airo-wp-steps-ready]) [data-airo-wp-step]~[data-airo-wp-step]{animation:airo-wp-conditions-pending 4s}'
		. '.airo-wp-form-builder:not([data-airo-wp-steps-ready]) .airo-wp-form__fields:has([data-airo-wp-step])~.airo-wp-form__footer{animation:airo-wp-conditions-pending 4s}'
		. '}'
		. '@keyframes airo-wp-conditions-pending{0%,99.9%{display:none}}';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'render_block_airo-wp/form-builder', array( $this, 'prepend_style' ), 10, 2 );
	}

	/**
	 * Prepend the critical style to every form containing a conditional field or step.
	 *
	 * Not printed once per request: a render whose output never reaches the
	 * page (SEO plugins, REST content) would otherwise consume the flag.
	 * Duplicate identical style tags are harmless.
	 *
	 * @param string $block_content Rendered block HTML.
	 * @param array  $block         Parsed block (unused).
	 * @return string
	 */
	public function prepend_style( $block_content, $block = array() ) {
		unset( $block );

		if (
			! is_string( $block_content )
			|| ( false === strpos( $block_content, 'airo-wp-form-field--conditional' ) && false === strpos( $block_content, 'data-airo-wp-step' ) )
		) {
			return $block_content;
		}

		return '<style class="airo-wp-form-critical">' . self::CSS . '</style>' . $block_content;
	}
}
