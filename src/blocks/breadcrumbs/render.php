<?php
/**
 * Breadcrumbs Block - Server-side Rendering
 *
 * @package airo-wp
 * @since 1.0.0
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content (unused for dynamic blocks).
 * @param WP_Block $block      Block instance.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'airowp_render_breadcrumbs' ) ) {
	/**
	 * Render the Breadcrumbs block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Inner block content.
	 * @param WP_Block $block      Block instance.
	 * @return string|void Rendered breadcrumb markup or no output.
	 */
	function airowp_render_breadcrumbs( $attributes, $content, $block ) {
		// Check if we should hide breadcrumbs on homepage.
		if ( ! empty( $attributes['hideOnHome'] ) && is_front_page() ) {
			return '';
		}

		// Get breadcrumb trail (function defined in includes/breadcrumbs-functions.php).
		$trail = airowp_get_breadcrumb_trail( $block, $attributes );

		// If no breadcrumbs, return empty.
		if ( empty( $trail ) ) {
			return '';
		}

		// Get separator (function defined in includes/breadcrumbs-functions.php).
		$separator = airowp_get_breadcrumb_separator( $attributes );

		// Build wrapper classes.
		$classes = array( 'airo-wp-breadcrumbs' );
		$allowed = array( 'center', 'right' );
		$justify = isset( $attributes['contentJustification'] ) ? $attributes['contentJustification'] : 'left';
		if ( in_array( $justify, $allowed, true ) ) {
			$classes[] = 'is-content-justification-' . $justify;
		}

		// Build wrapper attributes.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class'      => implode( ' ', $classes ),
				'aria-label' => __( 'Breadcrumb', 'airo-wp' ),
			)
		);

		// Output directly (WordPress captures echo'd output from render callbacks).
		?>
		<nav <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( ! empty( $attributes['prefixText'] ) ) : ?>
				<span class="airo-wp-breadcrumbs__prefix"><?php echo esc_html( $attributes['prefixText'] ); ?></span>
			<?php endif; ?>

			<ol class="airo-wp-breadcrumbs__list">
				<?php foreach ( $trail as $index => $item ) : ?>
					<li class="<?php echo esc_attr( 'airo-wp-breadcrumbs__item' . ( ! empty( $item['is_current'] ) ? ' airo-wp-breadcrumbs__item--current' : '' ) ); ?>">
						<?php if ( ! empty( $item['url'] ) && ( empty( $item['is_current'] ) || ! empty( $attributes['linkCurrent'] ) ) ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>" class="airo-wp-breadcrumbs__link"<?php echo ! empty( $item['is_current'] ) ? ' aria-current="page"' : ''; ?>>
								<?php echo esc_html( $item['title'] ); ?>
							</a>
						<?php else : ?>
							<span class="airo-wp-breadcrumbs__text" aria-current="page">
								<?php echo esc_html( $item['title'] ); ?>
							</span>
						<?php endif; ?>
					</li>

					<?php if ( $index < count( $trail ) - 1 ) : ?>
						<li class="airo-wp-breadcrumbs__separator" aria-hidden="true"><?php echo esc_html( $separator ); ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ol>
			</nav>
			<?php
			airowp_render_breadcrumbs_schema( $trail, $block );
	}
}

if ( ! function_exists( 'airowp_render_breadcrumbs_schema' ) ) {
	/**
	 * Print the BreadcrumbList JSON-LD for the page being viewed.
	 *
	 * Rendered server-side so crawlers that don't run JavaScript still see it.
	 * Only a trail for the queried post (or, on an archive, the archive itself) describes this page — a breadcrumbs
	 * block inside a Query loop describes its item, not the page — and only
	 * the first one prints, so a header and a footer copy don't both emit it.
	 *
	 * @param array    $trail Breadcrumb items.
	 * @param WP_Block $block Block instance.
	 */
	function airowp_render_breadcrumbs_schema( array $trail, $block ) {
		$post_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0;

		if ( did_action( 'airowp_breadcrumbs_schema_printed' ) ) {
			return;
		}

		// Singular: the trail must be for the queried post. Archives and the
		// posts index: the trail describes the requested page itself.
		$describes_page = is_singular()
			? get_queried_object_id() === $post_id
			: airowp_breadcrumb_is_page_level_view( $block ) && ( is_archive() || ( is_home() && ! is_front_page() ) );

		if ( ! $describes_page ) {
			return;
		}

		/**
		 * Whether the Breadcrumbs block prints BreadcrumbList JSON-LD.
		 *
		 * SEO plugins such as Yoast SEO and Rank Math print their own; return
		 * false to leave it to them.
		 *
		 * @since 2.8.3
		 *
		 * @param bool  $enabled Default true.
		 * @param array $trail   Breadcrumb items (title, url).
		 */
		if ( ! apply_filters( 'airowp_breadcrumbs_schema_enabled', true, $trail ) ) {
			return;
		}

		$schema = airowp_get_breadcrumb_schema( $trail );
		$json   = $schema ? wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : false;

		if ( false === $json ) {
			return;
		}

		/**
		 * Fires once the breadcrumb JSON-LD has been printed for this request.
		 */
		do_action( 'airowp_breadcrumbs_schema_printed' );

		// Same guard as SchemaOutput::render(): a `</script>` inside a title
		// would otherwise close the element early.
		echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() output with script-closing sequences neutralised.
	}
}

airowp_render_breadcrumbs( $attributes, $content, $block );
