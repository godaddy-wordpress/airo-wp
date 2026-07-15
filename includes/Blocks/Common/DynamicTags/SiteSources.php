<?php
/**
 * Dynamic Tags — site-family sources.
 *
 * @package airo-wp
 * @since   2.2.0
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Blocks\Common\DynamicTags;

defined( 'ABSPATH' ) || exit;
/**
 * Registers the `airo-wp/site-*` binding sources.
 */
class SiteSources {

	/**
	 * Registers site-family sources.
	 *
	 * @param Registry $registry Metadata registry.
	 */
	public static function register( Registry $registry ) {
		if ( ! function_exists( 'airowp_register_bindings_source' ) ) {
			return;
		}

		self::register_one(
			$registry,
			'airo-wp/site-title',
			__( 'Site title', 'airo-wp' ),
			array( 'text' ),
			static function ( $_args, $_block, $_attr ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found,Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- required by register_block_bindings_source() callback signature
				$value = get_bloginfo( 'name' );
				return '' === $value ? null : (string) $value;
			}
		);

		self::register_one(
			$registry,
			'airo-wp/site-tagline',
			__( 'Site tagline', 'airo-wp' ),
			array( 'text' ),
			static function ( $_args, $_block, $_attr ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found,Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- required by register_block_bindings_source() callback signature
				$value = get_bloginfo( 'description' );
				return '' === $value ? null : (string) $value;
			}
		);

		self::register_one(
			$registry,
			'airo-wp/site-url',
			__( 'Site URL', 'airo-wp' ),
			array( 'url' ),
			static function ( $_args, $_block, $_attr ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found,Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- required by register_block_bindings_source() callback signature
				$url = home_url( '/' );
				return $url ? (string) $url : null;
			}
		);

		self::register_one(
			$registry,
			'airo-wp/site-logo',
			__( 'Site logo', 'airo-wp' ),
			array( 'image' ),
			static function ( $args, $_block, $_attr ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- required by register_block_bindings_source() callback signature
				$logo_id = (int) get_option( 'site_logo' );
				if ( ! $logo_id ) {
					$logo_id = (int) get_theme_mod( 'custom_logo' );
				}
				if ( ! $logo_id ) {
					return null;
				}
				$subkey = isset( $args['subkey'] ) ? (string) $args['subkey'] : 'url';
				$size   = isset( $args['size'] ) ? (string) $args['size'] : 'full';
				return ImageResolver::resolve_subvalue( $logo_id, $subkey, $size );
			},
			array(
				'subkey' => array(
					'type' => 'string',
					'enum' => array( 'url', 'id', 'alt', 'width', 'height' ),
				),
				'size'   => array( 'type' => 'string' ),
			)
		);
	}

	/**
	 * Registers one source with both core Bindings and our metadata registry.
	 *
	 * @param Registry $registry    Metadata registry.
	 * @param string   $slug        Binding source slug.
	 * @param string   $label       Display label.
	 * @param string[] $returns     Return types.
	 * @param callable $callback    Value callback.
	 * @param array    $args_schema Optional arg schema.
	 */
	private static function register_one( Registry $registry, $slug, $label, array $returns, callable $callback, array $args_schema = array() ) {
		airowp_register_bindings_source(
			$slug,
			$callback,
			array( 'label' => $label )
		);
		$registry->register_source(
			$slug,
			array(
				'label'   => $label,
				'group'   => 'site',
				'returns' => $returns,
				'args'    => $args_schema,
			)
		);
	}
}
