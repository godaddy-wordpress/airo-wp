<?php
/**
 * Dynamic Query — first-paint render (v2.6 restructure).
 *
 * The block is now a pure container. Flow:
 *   1. Find the airo-wp/query-results child in parsed innerBlocks.
 *   2. Run the WP_Query once via airowp_query_render() using the
 *      parent's query attrs + the results child's presentation attrs
 *      (columns, tagName, groupBy…). Populate the state registry so
 *      pagination / no-results siblings can read totalPages / totalItems.
 *   3. Stash the rendered items HTML in $GLOBALS keyed by queryId so the
 *      query-results block's render.php picks it up when WordPress walks
 *      the tree and renders children.
 *   4. Render each child block in tree order via WP_Block so filters,
 *      pagination, and no-results appear exactly where the author placed
 *      them — no more "always at the bottom" behaviour.
 *
 * @package airo-wp
 * @since 2.6.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Ignored — we re-render children manually so
 *                             the results block can splice in its pre-
 *                             rendered items HTML via a global.
 * @param WP_Block $block      Block instance (carries parsed_block).
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/render-helpers.php';

if ( function_exists( 'wp_enqueue_script_module' ) ) {
	wp_enqueue_script_module( 'airo-wp-query-view-script-module' );
}

$airowp_page = max( 1, absint( get_query_var( 'paged' ) ) );
if ( 1 === $airowp_page ) {
	$airowp_page = max( 1, absint( get_query_var( 'page' ) ) );
}

$airowp_query_id = isset( $attributes['queryId'] ) ? sanitize_key( (string) $attributes['queryId'] ) : '';

$airowp_parsed_children = isset( $block->parsed_block['innerBlocks'] )
	? (array) $block->parsed_block['innerBlocks']
	: array();

// Block wrapper attrs carry native-supports classes / styles / anchor id +
// author-supplied className. We append IAPI attrs inline so the region
// lives on a single element, giving view.js a clean swap target.
$airowp_wrapper_attrs = get_block_wrapper_attributes(
	array(
		'class' => 'airo-wp-query airo-wp-query-region airo-wp-query--source-' . sanitize_key( (string) ( $attributes['source'] ?? 'posts' ) ),
	)
);

echo airowp_query_render_container( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- assembled from WP_Block->render() output + esc_attr()-escaped parts.
	(array) $attributes,
	$airowp_parsed_children,
	$airowp_page,
	$airowp_query_id,
	$airowp_wrapper_attrs,
	(array) ( $block->context ?? array() ),
	class_exists( 'airo-wp\\Blocks\\Query\\RefreshSource' ) ? \airo-wp\Blocks\Query\RefreshSource::current_content_post_id() : 0
);
