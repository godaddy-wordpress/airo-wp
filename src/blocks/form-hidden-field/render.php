<?php
/**
 * Form Hidden Field - server-side render.
 *
 * Dynamic block: save() returns null, so the field's markup is produced here at
 * render time. No static HTML is stored to diff against — block validation can
 * never fail on this field.
 *
 * @package airo-wp
 * @since 2.5.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content (unused for dynamic blocks).
 * @param WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'airowp_render_form_hidden_field' ) ) {
	/**
	 * Render the Form Hidden Field block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Inner block content.
	 * @param WP_Block $block      Block instance.
	 * @return void
	 */
	function airowp_render_form_hidden_field( $attributes, $content, $block ) {
		$field_name = isset( $attributes['fieldName'] ) ? (string) $attributes['fieldName'] : '';
		$value      = isset( $attributes['value'] ) ? (string) $attributes['value'] : '';

		$inner = '<input type="hidden" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $value ) . '" data-field-type="hidden"/>';

		$wrapper = get_block_wrapper_attributes(
			array(
				'class' => 'airo-wp-form-field airo-wp-form-field--hidden',
			)
		);

		echo '<div ' . $wrapper . '>' . $inner . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

airowp_render_form_hidden_field( $attributes, $content, $block );
