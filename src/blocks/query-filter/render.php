<?php
/**
 * Dynamic Query — Filter sibling block.
 *
 * One block, six filterKind variations. Wires to the parent query via
 * queryId (from context) + URL params. All controls live inside a
 * <form method="get"> so no-JS submission falls back cleanly to a
 * server-rendered filter; with JS, the IAPI store intercepts.
 *
 * @package airo-wp
 * @since 2.1.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Serialized innerBlocks (unused — no inner blocks).
 * @param WP_Block $block      Block instance (provides context).
 */

defined( 'ABSPATH' ) || exit;

// Shared with the Query block (per-Query URL keys, chip and Reset links).
require_once dirname( __DIR__ ) . '/query/filter-links.php';

// ---------------------------------------------------------------------------
// Helper renderers (one per filterKind) — defined FIRST so the dispatcher
// below can call them on the first render (conditional function defs only
// become callable after the if-block executes).
// ---------------------------------------------------------------------------

if ( ! function_exists( 'airowp_query_filter_collect_term_scope' ) ) :

	/**
	 * Collect the term IDs a query's taxQuery allows (or forbids) for one taxonomy.
	 *
	 * Walks the same nested clause/group shape the server builder understands
	 * (see airowp_build_tax_query_entry()), gathering leaf clauses that
	 * name this taxonomy. `IN` narrows the offer list; `NOT IN` removes from
	 * it. Clauses for other taxonomies are ignored — they constrain which
	 * posts match, not which terms of *this* taxonomy are worth offering.
	 *
	 * Deliberately shallow about boolean structure: an `OR` group could in
	 * principle make an `IN` clause non-binding, so the narrowing here is a
	 * best effort at "do not advertise what this query cannot return" rather
	 * than an exact solve. Erring toward a shorter list is the safer side —
	 * the alternative is what shipped, which advertised everything.
	 *
	 * @param array  $entry    A taxQuery, group, or leaf clause.
	 * @param string $taxonomy Taxonomy slug the filter renders.
	 * @param array  $allowed  Accumulator for allowed term IDs (by reference).
	 * @param array  $blocked  Accumulator for forbidden term IDs (by reference).
	 * @return void
	 */
	function airowp_query_filter_collect_term_scope( array $entry, $taxonomy, array &$allowed, array &$blocked ) {
		if ( isset( $entry['clauses'] ) && is_array( $entry['clauses'] ) ) {
			foreach ( $entry['clauses'] as $child ) {
				if ( is_array( $child ) ) {
					airowp_query_filter_collect_term_scope( $child, $taxonomy, $allowed, $blocked );
				}
			}
			return;
		}

		if ( empty( $entry['taxonomy'] ) || empty( $entry['terms'] ) ) {
			return;
		}
		if ( sanitize_key( (string) $entry['taxonomy'] ) !== $taxonomy ) {
			return;
		}

		$terms    = array_filter( array_map( 'absint', (array) $entry['terms'] ) );
		$operator = isset( $entry['operator'] ) ? strtoupper( (string) $entry['operator'] ) : 'IN';

		if ( 'NOT IN' === $operator ) {
			$blocked = array_merge( $blocked, $terms );
			return;
		}
		// IN and AND both mean "only these terms are in play".
		$allowed = array_merge( $allowed, $terms );
	}

endif;

if ( ! function_exists( 'airowp_query_filter_current_value' ) ) :

	/**
	 * A filter's current value for its Query: the Query's own scoped key if
	 * present (even empty, which clears an inherited bare value), else the
	 * bare key. See airowp_query_owned_request_params().
	 *
	 * @param string $param_name Bare URL parameter name.
	 * @param string $query_id   Sanitized queryId this filter belongs to.
	 * @return string|string[] Unslashed, unsanitized scalar or flat list of scalars; '' when absent.
	 */
	function airowp_query_filter_current_value( $param_name, $query_id ) {
		$owned = airowp_query_owned_request_params( $query_id );
		return isset( $owned[ $param_name ] ) ? airowp_query_scalar_param( wp_unslash( $owned[ $param_name ]['value'] ) ) : '';
	}

endif;

if ( ! function_exists( 'airowp_query_filter_field' ) ) :

	/**
	 * The name, id and carried-over hidden inputs for a filter's form field.
	 *
	 * The field writes the scoped key only when the page holds several
	 * Queries. The hidden inputs keep the rest of the URL (other Queries'
	 * filters, `page_id`) through a no-JS submission.
	 *
	 * @param string $param_name Bare URL parameter name.
	 * @param string $query_id   Sanitized queryId this filter belongs to.
	 * @param bool   $is_array   Whether the field submits `name[]`.
	 * @return array{name:string,id:string,hidden:string}
	 */
	function airowp_query_filter_field( $param_name, $query_id, $is_array = false ) {
		$scoped = $param_name . '__' . $query_id;
		$multi  = airowp_query_page_is_multi();
		$name   = $multi ? $scoped : $param_name;
		// With several Queries the bare key stays for the others; with one,
		// the field replaces it.
		$hidden = airowp_query_preserved_inputs( $query_id, $multi ? array( $scoped ) : array( $param_name, $scoped ) );
		// An unticked list must still override a bare value this Query inherited.
		if ( $is_array && $multi ) {
			$hidden .= sprintf( '<input type="hidden" name="%s[]" value="" />', esc_attr( $scoped ) );
		}
		return array(
			'name'   => $name,
			'id'     => 'airo-wp-filter-' . sanitize_html_class( $scoped ),
			'hidden' => $hidden,
		);
	}

endif;

if ( ! function_exists( 'airowp_query_filter_render_search' ) ) :

	/**
	 * Render the search-input variation.
	 *
	 * @param string $wrapper     Pre-computed get_block_wrapper_attributes() string.
	 * @param string $param_name  URL parameter name (usually 'q').
	 * @param string $label       Optional visible label.
	 * @param string $placeholder Input placeholder text.
	 * @param string $query_id    Sanitized queryId this filter belongs to.
	 */
	function airowp_query_filter_render_search( $wrapper, $param_name, $label, $placeholder, $query_id = '' ) {
		// $param_name is already sanitize_key()'d at the call site. Coerce array
		// GET (?q[]=value) to a scalar so the input doesn't render "Array".
		$raw     = airowp_query_filter_current_value( $param_name, $query_id );
		$raw     = is_array( $raw ) ? ( isset( $raw[0] ) ? $raw[0] : '' ) : $raw;
		$current = sanitize_text_field( (string) $raw );

		$field      = airowp_query_filter_field( $param_name, $query_id );
		$input_id   = $field['id'];
		$aria_label = $label ? '' : ' aria-label="' . esc_attr__( 'Search', 'airo-wp' ) . '"';

		printf(
			'<form %1$s method="get" action="" role="search" data-wp-on--submit="actions.setFilter">%9$s%2$s<div class="airo-wp-query-filter__search-row"><input type="search" id="%7$s" name="%3$s" value="%4$s" placeholder="%5$s" class="airo-wp-query-filter__search-input"%8$s /><button type="submit" class="airo-wp-query-filter__submit">%6$s</button></div></form>',
			$wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output + appended data-wp-context JSON (sanitized values + wp_json_encode with JSON_HEX_APOS).
			$label ? '<label for="' . esc_attr( $input_id ) . '" class="airo-wp-query-filter__label">' . esc_html( $label ) . '</label>' : '',
			esc_attr( $field['name'] ),
			esc_attr( $current ),
			esc_attr( $placeholder ? $placeholder : __( 'Search…', 'airo-wp' ) ),
			esc_html__( 'Search', 'airo-wp' ),
			esc_attr( $input_id ),
			$aria_label, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr__ used inside.
			$field['hidden'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr() per input in airowp_query_preserved_inputs().
		);
	}

endif;

if ( ! function_exists( 'airowp_query_filter_render_sort' ) ) :

	/**
	 * Render the sort-dropdown variation.
	 *
	 * @param string $wrapper    Pre-computed wrapper attributes string.
	 * @param string $param_name URL parameter name (usually 'sort').
	 * @param string $label      Optional visible label.
	 * @param array  $options    Array of {value, label} option definitions.
	 * @param string $query_id   Sanitized queryId this filter belongs to.
	 */
	function airowp_query_filter_render_sort( $wrapper, $param_name, $label, array $options, $query_id = '' ) {
		$raw = airowp_query_filter_current_value( $param_name, $query_id );
		// Coerce array GET (?sort[]=value) to a scalar so `selected()` compares
		// a string, not the literal "Array".
		$raw     = is_array( $raw ) ? ( isset( $raw[0] ) ? $raw[0] : '' ) : $raw;
		$current = sanitize_text_field( (string) $raw );

		$opts_html = '';
		foreach ( $options as $opt ) {
			$val        = isset( $opt['value'] ) ? (string) $opt['value'] : '';
			$opt_label  = isset( $opt['label'] ) ? (string) $opt['label'] : $val;
			$opts_html .= sprintf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $val ),
				selected( $current, $val, false ),
				esc_html( $opt_label )
			);
		}

		$field      = airowp_query_filter_field( $param_name, $query_id );
		$select_id  = $field['id'];
		$aria_label = $label ? '' : ' aria-label="' . esc_attr__( 'Sort', 'airo-wp' ) . '"';

		printf(
			'<form %1$s method="get" action="">%9$s%2$s<select id="%7$s" name="%3$s" class="airo-wp-query-filter__sort" data-wp-on--change="actions.setFilter"%8$s><option value="">%4$s</option>%5$s</select><noscript><button type="submit" class="airo-wp-query-filter__nojs-submit">%6$s</button></noscript></form>',
			$wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$label ? '<label for="' . esc_attr( $select_id ) . '" class="airo-wp-query-filter__label">' . esc_html( $label ) . '</label>' : '',
			esc_attr( $field['name'] ),
			esc_html__( 'Default order', 'airo-wp' ),
			$opts_html, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each option is escaped per-attribute above.
			esc_html__( 'Apply filter', 'airo-wp' ),
			esc_attr( $select_id ),
			$aria_label, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr__ used inside.
			$field['hidden'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr() per input in airowp_query_preserved_inputs().
		);
	}

endif;

if ( ! function_exists( 'airowp_query_filter_render_select' ) ) :

	/**
	 * Render the taxonomy single-select dropdown variation.
	 *
	 * @param string $wrapper         Pre-computed wrapper attributes string.
	 * @param string $param_name      URL parameter name.
	 * @param string $label           Optional visible label.
	 * @param string $filter_taxonomy Taxonomy slug.
	 * @param bool   $show_counts     Whether to append (N) counts to option labels.
	 * @param array  $active_filters  Current active filter state for intersection counts.
	 * @param string $post_type       Optional post-type scope for counts.
	 * @param array  $term_include    Term IDs the parent query allows, if any.
	 * @param array  $term_exclude    Term IDs the parent query excludes, if any.
	 * @param string $query_id        Sanitized queryId this filter belongs to.
	 */
	function airowp_query_filter_render_select( $wrapper, $param_name, $label, $filter_taxonomy, $show_counts = false, $active_filters = array(), $post_type = '', $term_include = array(), $term_exclude = array(), $query_id = '' ) {
		if ( ! taxonomy_exists( $filter_taxonomy ) ) {
			return;
		}
		$term_args = array(
			'taxonomy'   => $filter_taxonomy,
			'hide_empty' => false,
		);
		// Narrowed by the parent query's own taxQuery, so the filter cannot
		// offer a term the query has already excluded.
		if ( ! empty( $term_include ) ) {
			$term_args['include'] = array_values( array_unique( $term_include ) );
		}
		if ( ! empty( $term_exclude ) ) {
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- a NOT IN clause on the parent query is exactly an exclusion; the list is the author's own small set, not user input.
			$term_args['exclude'] = array_values( array_unique( $term_exclude ) );
		}
		$terms = get_terms( $term_args );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		$raw = airowp_query_filter_current_value( $param_name, $query_id );
		// Coerce array GET to scalar for the single-select variation.
		$raw     = is_array( $raw ) ? ( isset( $raw[0] ) ? $raw[0] : '' ) : $raw;
		$current = sanitize_title( (string) $raw );

		// Resolve counts if requested.
		$counts = array();
		if ( $show_counts && class_exists( '\airo-wp\Blocks\Query\FilterIndex' ) ) {
			$term_ids = array_map(
				function ( $t ) {
					return (string) $t->term_id;
				},
				$terms
			);
			$counts   = \airo-wp\Blocks\Query\FilterIndex::count_for_options(
				$filter_taxonomy,
				$term_ids,
				$active_filters,
				$post_type
			);
		}

		$opts_html = '';
		foreach ( $terms as $term ) {
			// Browsers strip HTML from <option>, so append the count as plain text —
			// a <span> wrapper would render literally in Firefox and be stripped in
			// Chrome/Safari.
			$label_text = $term->name;
			if ( $show_counts && isset( $counts[ (string) $term->term_id ] ) ) {
				$label_text .= ' (' . (int) $counts[ (string) $term->term_id ] . ')';
			}
			$opts_html .= sprintf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $term->slug ),
				selected( $current, $term->slug, false ),
				esc_html( $label_text )
			);
		}

		$field      = airowp_query_filter_field( $param_name, $query_id );
		$select_id  = $field['id'];
		$aria_label = $label ? '' : ' aria-label="' . esc_attr( $filter_taxonomy ) . '"';

		printf(
			'<form %1$s method="get" action="">%9$s%2$s<select id="%7$s" name="%3$s" class="airo-wp-query-filter__select" data-wp-on--change="actions.setFilter"%8$s><option value="">%4$s</option>%5$s</select><noscript><button type="submit" class="airo-wp-query-filter__nojs-submit">%6$s</button></noscript></form>',
			$wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$label ? '<label for="' . esc_attr( $select_id ) . '" class="airo-wp-query-filter__label">' . esc_html( $label ) . '</label>' : '',
			esc_attr( $field['name'] ),
			esc_html__( 'All', 'airo-wp' ),
			$opts_html, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- each option is escaped above.
			esc_html__( 'Apply filter', 'airo-wp' ),
			esc_attr( $select_id ),
			$aria_label, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr used inside.
			$field['hidden'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr() per input in airowp_query_preserved_inputs().
		);
	}

endif;

if ( ! function_exists( 'airowp_query_filter_render_checkbox' ) ) :

	/**
	 * Render the taxonomy checkbox-list variation.
	 *
	 * @param string $wrapper         Pre-computed wrapper attributes string.
	 * @param string $param_name      URL parameter name (e.g. filter_category).
	 * @param string $label           Optional legend label.
	 * @param string $filter_taxonomy Taxonomy slug.
	 * @param bool   $show_counts     Whether to append (N) counts to option labels.
	 * @param array  $active_filters  Current active filter state for intersection counts.
	 * @param string $post_type       Optional post-type scope for counts.
	 * @param string $orientation     Layout direction for the checkbox list.
	 * @param string $style           Visual style variant.
	 * @param array  $term_include    Term IDs the parent query allows, if any.
	 * @param array  $term_exclude    Term IDs the parent query excludes, if any.
	 * @param string $query_id        Sanitized queryId this filter belongs to.
	 */
	function airowp_query_filter_render_checkbox( $wrapper, $param_name, $label, $filter_taxonomy, $show_counts = false, $active_filters = array(), $post_type = '', $orientation = 'vertical', $style = 'default', $term_include = array(), $term_exclude = array(), $query_id = '' ) {
		if ( ! taxonomy_exists( $filter_taxonomy ) ) {
			return;
		}
		$term_args = array(
			'taxonomy'   => $filter_taxonomy,
			'hide_empty' => false,
		);
		// Narrowed by the parent query's own taxQuery, so the filter cannot
		// offer a term the query has already excluded.
		if ( ! empty( $term_include ) ) {
			$term_args['include'] = array_values( array_unique( $term_include ) );
		}
		if ( ! empty( $term_exclude ) ) {
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- a NOT IN clause on the parent query is exactly an exclusion; the list is the author's own small set, not user input.
			$term_args['exclude'] = array_values( array_unique( $term_exclude ) );
		}
		$terms = get_terms( $term_args );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		// Support both ?filter_category[]=slug and ?filter_category=slug,slug.
		$raw_input    = airowp_query_filter_current_value( $param_name, $query_id );
		$selected_raw = is_array( $raw_input )
			? array_map( 'sanitize_title', $raw_input )
			: array_filter( array_map( 'sanitize_title', explode( ',', (string) $raw_input ) ) );

		// Resolve per-option counts from the filter index if requested.
		$counts = array();
		if ( $show_counts && class_exists( '\airo-wp\Blocks\Query\FilterIndex' ) ) {
			$term_ids = array_map(
				function ( $t ) {
					return (string) $t->term_id;
				},
				$terms
			);
			$counts   = \airo-wp\Blocks\Query\FilterIndex::count_for_options(
				$filter_taxonomy,
				$term_ids,
				$active_filters,
				$post_type
			);
		}

		$field = airowp_query_filter_field( $param_name, $query_id, true );

		$items_html = '';
		foreach ( $terms as $term ) {
			$checked    = in_array( $term->slug, $selected_raw, true ) ? 'checked' : '';
			$name_label = esc_html( $term->name );
			if ( $show_counts && isset( $counts[ (string) $term->term_id ] ) ) {
				$name_label .= ' <span class="airo-wp-query-filter__count">(' . (int) $counts[ (string) $term->term_id ] . ')</span>';
			}
			$items_html .= sprintf(
				'<label class="airo-wp-query-filter__checkbox-item"><input type="checkbox" name="%1$s[]" value="%2$s" %3$s data-wp-on--change="actions.toggleFilter" /><span>%4$s</span></label>',
				esc_attr( $field['name'] ),
				esc_attr( $term->slug ),
				esc_attr( $checked ),
				$name_label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html() on term->name + our own <span> markup.
			);
		}

		// Fix 4: noscript submit so no-JS users can apply checkbox filters.
		$airowp_nojs_btn = '<noscript><button type="submit" class="airo-wp-query-filter__nojs-submit">' . esc_html__( 'Apply filter', 'airo-wp' ) . '</button></noscript>';
		$list_class_parts     = array( 'airo-wp-query-filter__checkbox-list' );
		if ( 'horizontal' === $orientation ) {
			$list_class_parts[] = 'is-horizontal';
		}
		if ( in_array( $style, array( 'pill', 'underline' ), true ) ) {
			// `is-style-pill` / `is-style-underline` → SCSS hides the native
			// checkbox and styles the label as a pill or underlined tab. The
			// input stays in the DOM so keyboard/screen-reader users still
			// toggle filters the same way.
			$list_class_parts[] = 'is-style-' . $style;
			// Pill + underline variants always read better as a horizontal row.
			if ( 'horizontal' !== $orientation ) {
				$list_class_parts[] = 'is-horizontal';
			}
		}
		$list_class = implode( ' ', $list_class_parts );
		if ( $label ) {
			printf(
				'<form %1$s method="get" action="">%6$s<fieldset class="airo-wp-query-filter__fieldset"><legend class="airo-wp-query-filter__label">%2$s</legend><div class="%5$s">%3$s</div></fieldset>%4$s</form>',
				$wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html( $label ),
				$items_html, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- per-field escaped above.
				$airowp_nojs_btn, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html() used inside.
				esc_attr( $list_class ),
				$field['hidden'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr() per input in airowp_query_preserved_inputs().
			);
		} else {
			printf(
				'<form %1$s method="get" action="">%5$s<div class="%4$s">%2$s</div>%3$s</form>',
				$wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$items_html, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$airowp_nojs_btn, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_html() used inside.
				esc_attr( $list_class ),
				$field['hidden'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_attr() per input in airowp_query_preserved_inputs().
			);
		}
	}

endif;

// ---------------------------------------------------------------------------
// Dispatcher — runs on every block render. Lives at the bottom of the file so
// every helper above is defined by the time we invoke one.
// ---------------------------------------------------------------------------

$airowp_query_id = isset( $block->context['airo-wp/queryId'] )
	? sanitize_key( (string) $block->context['airo-wp/queryId'] )
	: '';

if ( '' === $airowp_query_id ) {
	return;
}

$airowp_filter_kind        = isset( $attributes['filterKind'] ) ? sanitize_key( (string) $attributes['filterKind'] ) : 'checkbox';
$airowp_filter_param       = isset( $attributes['paramName'] ) ? sanitize_key( (string) $attributes['paramName'] ) : '';
$airowp_filter_label       = isset( $attributes['label'] ) ? (string) $attributes['label'] : '';
$airowp_filter_placeholder = isset( $attributes['placeholder'] ) ? (string) $attributes['placeholder'] : '';
$airowp_filter_taxonomy    = isset( $attributes['taxonomy'] ) ? sanitize_key( (string) $attributes['taxonomy'] ) : 'category';
$airowp_filter_orientation = ( isset( $attributes['orientation'] ) && 'horizontal' === $attributes['orientation'] ) ? 'horizontal' : 'vertical';
// Default `filterStyle` is `default` (classic checkboxes) to preserve the
// look of pre-existing saved blocks that have no `filterStyle` attribute.
// New inserts through the inserter variation opt into `underline` explicitly
// — see the variation attributes in src/blocks/query-filter/variations.js.
$airowp_filter_style = isset( $attributes['filterStyle'] ) && in_array( $attributes['filterStyle'], array( 'pill', 'underline' ), true )
	? $attributes['filterStyle']
	: 'default';

// Post-type scope for counts: only non-empty when the parent query targets a
// specific post type (source === 'posts'). Users/terms sources leave it empty
// so the count query runs unscoped (those rows carry post_type='' anyway).
$airowp_query_source    = isset( $block->context['airo-wp/querySource'] )
	? sanitize_key( (string) $block->context['airo-wp/querySource'] )
	: 'posts';
$airowp_query_post_type = '';
if ( 'posts' === $airowp_query_source && isset( $block->context['airo-wp/queryPostType'] ) ) {
	$airowp_query_post_type = sanitize_key( (string) $block->context['airo-wp/queryPostType'] );
}

// Term scope: a filter must not offer a term its own query has already ruled
// out. The parent query's `taxQuery` reaches us through context; clauses for
// THIS taxonomy narrow the option list, so a query restricted to four
// categories stops advertising every other category on the site — each of
// which would have shown a non-zero count and then returned no results,
// because the count comes from the index and knows nothing about the query.
$airowp_term_include = array();
$airowp_term_exclude = array();
if ( isset( $block->context['airo-wp/queryTaxQuery'] ) ) {
	airowp_query_filter_collect_term_scope(
		(array) $block->context['airo-wp/queryTaxQuery'],
		$airowp_filter_taxonomy,
		$airowp_term_include,
		$airowp_term_exclude
	);
}

// Whether to show (N) counts next to filter options (default: true).
$airowp_show_counts = ! isset( $attributes['showCounts'] ) || (bool) $attributes['showCounts'];

// Extract active filters from $_GET so count queries respect the current
// filter state. On the REST-refresh path, $_GET has been overlaid by the
// REST controller with the incoming params, so this is always up-to-date.
// Resolved for THIS query (airowp_query_owned_request_params()), so a
// sibling query's scoped selection never skews these counts, while a bare
// key — which filters this query too — still does.
$airowp_active_filters = array();
foreach ( airowp_query_owned_request_params( $airowp_query_id ) as $airowp_k => $airowp_entry ) {
	if ( 0 !== strpos( (string) $airowp_k, 'filter_' ) ) {
		continue;
	}
	$airowp_vals = array_filter( array_map( 'sanitize_text_field', (array) airowp_query_scalar_param( wp_unslash( $airowp_entry['value'] ) ) ), 'strlen' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by the array_map().
	if ( $airowp_vals ) {
		$airowp_active_filters[ $airowp_k ] = array_values( $airowp_vals );
	}
}

// Re-key active_filters to use the bare taxonomy slug (strip "filter_" prefix)
// because FilterIndex::count_for_options() expects filter keys, not URL param names.
$airowp_active_filters_by_key = array();
foreach ( $airowp_active_filters as $airowp_param_key => $airowp_param_values ) {
	$airowp_filter_key = 'filter_' === substr( $airowp_param_key, 0, 7 )
		? substr( $airowp_param_key, 7 )
		: $airowp_param_key;

	$airowp_active_filters_by_key[ $airowp_filter_key ] = $airowp_param_values;
}

// Translate taxonomy slugs in active_filters_by_key to term IDs.
//
// URL params carry taxonomy slugs (e.g. filter_category=news), but the filter
// index stores term IDs (integers as strings). Without this translation, the
// intersection subquery in count_for_options() looks for filter_value='news'
// but the index has filter_value='42', so all cross-filter counts collapse to 0.
//
// Meta filters store their value verbatim — no translation needed.
if ( class_exists( '\airo-wp\Blocks\Query\FilterRegistry' ) ) {
	$airowp_registered_filters = \airo-wp\Blocks\Query\FilterRegistry::all();
	foreach ( $airowp_active_filters_by_key as $airowp_fk => $airowp_fv ) {
		$airowp_filter_config = $airowp_registered_filters[ $airowp_fk ] ?? null;
		if ( ! $airowp_filter_config || 'taxonomy' !== ( $airowp_filter_config['type'] ?? '' ) ) {
			continue; // Meta or unknown — values are already in the correct format.
		}
		// Keep the loop variable distinct from $airowp_filter_taxonomy, which holds
		// this block's own taxonomy and is needed later for the is_available()
		// check and the render-dispatch switch.
		$airowp_iter_taxonomy = (string) ( $airowp_filter_config['source'] ?? '' );
		if ( '' === $airowp_iter_taxonomy ) {
			continue;
		}
		$airowp_translated = array();
		// URL values are always term slugs (that is what the controls write and
		// what the query matches on), and `slug,slug` is a documented form.
		// A numeric slug such as "2024" is a slug, never a term ID: reading it
		// as an ID would count against whichever unrelated term has that ID.
		$airowp_slugs = array();
		foreach ( (array) $airowp_fv as $airowp_raw_value ) {
			foreach ( explode( ',', (string) $airowp_raw_value ) as $airowp_piece ) {
				$airowp_piece = sanitize_title( $airowp_piece );
				if ( '' !== $airowp_piece ) {
					$airowp_slugs[] = $airowp_piece;
				}
			}
		}
		foreach ( array_unique( $airowp_slugs ) as $airowp_slug ) {
			$airowp_term = get_term_by( 'slug', $airowp_slug, $airowp_iter_taxonomy );
			if ( $airowp_term instanceof \WP_Term ) {
				$airowp_translated[] = (string) $airowp_term->term_id;
			}
		}
		$airowp_active_filters_by_key[ $airowp_fk ] = $airowp_translated;
	}
	unset( $airowp_registered_filters, $airowp_filter_config, $airowp_iter_taxonomy, $airowp_translated, $airowp_slugs, $airowp_raw_value, $airowp_piece, $airowp_slug, $airowp_term );
}

// Only render counts when the filter is indexed AND showCounts is enabled.
$airowp_counts_enabled = $airowp_show_counts
	&& class_exists( '\airo-wp\Blocks\Query\FilterIndex' )
	&& \airo-wp\Blocks\Query\FilterIndex::is_available( $airowp_filter_taxonomy );

$airowp_filter_wrapper_attrs = array(
	'class'                 => 'airo-wp-query-filter airo-wp-query-filter--' . esc_attr( $airowp_filter_kind ),
	'data-wp-interactive'   => 'airo-wp/query',
	'data-airo-wp-query-id'    => $airowp_query_id,
	'data-airo-wp-filter-kind' => $airowp_filter_kind,
	'data-airo-wp-param'       => $airowp_filter_param,
);
// Present when controls may write per-Query keys; view.js writes them only
// once it also sees another Query on the page.
if ( airowp_query_scoping_enabled() ) {
	$airowp_filter_wrapper_attrs['data-airo-wp-scoped'] = '1';
}
$airowp_filter_wrapper = get_block_wrapper_attributes( $airowp_filter_wrapper_attrs );
// Seed IAPI context so `getContext()` inside setFilter / toggleFilter /
// removeActiveFilter / resetAll resolves ctx.queryId. Appended
// outside get_block_wrapper_attributes() because that helper runs esc_attr()
// on values, which would mangle JSON quotes.
// JSON_HEX_APOS defends the single-quoted attribute boundary against a
// stray apostrophe in any future context value (today all four are quote-
// free: sanitize_key / false / esc_url_raw / a nonce).
$airowp_filter_wrapper .= sprintf(
	" data-wp-context='%s'",
	wp_json_encode(
		array(
			'queryId' => $airowp_query_id,
			'busy'    => false,
			'restUrl' => esc_url_raw( rest_url( 'airo-wp/v1/query/render' ) ),
			'nonce'   => function_exists( 'airowp_query_rest_nonce' ) ? airowp_query_rest_nonce() : ( is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '' ), // Visitors get none: a stale one on a cached page is rejected by core.
		),
		JSON_HEX_APOS
	)
);

switch ( $airowp_filter_kind ) {
	case 'search':
		airowp_query_filter_render_search( $airowp_filter_wrapper, $airowp_filter_param, $airowp_filter_label, $airowp_filter_placeholder, $airowp_query_id );
		break;
	case 'sort':
		$airowp_sort_options = isset( $attributes['sortOptions'] ) ? (array) $attributes['sortOptions'] : array();
		airowp_query_filter_render_sort( $airowp_filter_wrapper, $airowp_filter_param, $airowp_filter_label, $airowp_sort_options, $airowp_query_id );
		break;
	case 'select':
		airowp_query_filter_render_select( $airowp_filter_wrapper, $airowp_filter_param, $airowp_filter_label, $airowp_filter_taxonomy, $airowp_counts_enabled, $airowp_active_filters_by_key, $airowp_query_post_type, $airowp_term_include, $airowp_term_exclude, $airowp_query_id );
		break;
	case 'active':
		airowp_query_filter_render_active( $airowp_filter_wrapper, $airowp_filter_label, $airowp_query_id );
		break;
	case 'reset':
		airowp_query_filter_render_reset( $airowp_filter_wrapper, $airowp_filter_label, $airowp_query_id );
		break;
	case 'checkbox':
	default:
		airowp_query_filter_render_checkbox( $airowp_filter_wrapper, $airowp_filter_param, $airowp_filter_label, $airowp_filter_taxonomy, $airowp_counts_enabled, $airowp_active_filters_by_key, $airowp_query_post_type, $airowp_filter_orientation, $airowp_filter_style, $airowp_term_include, $airowp_term_exclude, $airowp_query_id );
		break;
}
