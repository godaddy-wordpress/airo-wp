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
		$classes   = array( 'airo-wp-breadcrumbs' );
		$allowed   = array( 'center', 'right' );
		$justify   = isset( $attributes['contentJustification'] ) ? $attributes['contentJustification'] : 'left';
		if ( in_array( $justify, $allowed, true ) ) {
			$classes[] = 'is-content-justification-' . $justify;
		}

		// Build wrapper attributes.
		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class'                 => implode( ' ', $classes ),
				'aria-label'            => __( 'Breadcrumb', 'airo-wp' ),
				'data-airo-wp-breadcrumbs' => wp_json_encode( $trail ),
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
						<?php if ( empty( $item['is_current'] ) || ! empty( $attributes['linkCurrent'] ) ) : ?>
							<a href="<?php echo esc_url( $item['url'] ); ?>" class="airo-wp-breadcrumbs__link">
								<?php echo esc_html( $item['title'] ); ?>
							</a>
						<?php else : ?>
							<span class="airo-wp-breadcrumbs__text">
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
	}
}

airowp_render_breadcrumbs( $attributes, $content, $block );
