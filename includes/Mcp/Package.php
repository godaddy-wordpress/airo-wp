<?php
/**
 * MCP domain package.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Dependencies\WP\MCP\Core\McpAdapter;
use GoDaddy\WordPress\Plugins\AiroWp\Dependencies\WP\MCP\Transport\HttpTransport;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\AbilitiesApiProxy;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\AppPasswordHeaderAuth;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\RouteAccess;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\ToolRegistry;
use GoDaddy\WordPress\Plugins\AiroWp\Services\DraftPageService;
use GoDaddy\WordPress\Plugins\AiroWp\PackageInterface;

/**
 * Boots the MCP domain package.
 *
 * Registers tools via AbilitiesApiProxy (native or bundled Abilities API),
 * initialises the Strauss-prefixed McpAdapter singleton, and creates the
 * 'airo-wp' MCP server on the mcp_adapter_init action.
 *
 * No should_run guard: hooks are cheap; real work only happens on REST requests.
 * Auth providers (JWT, request signing) remain out of scope; RouteAccess only
 * makes core's own Application Password auth work on the MCP route.
 */
final class Package implements PackageInterface {

	/**
	 * Initialise the MCP package.
	 *
	 * @param Container $container Plugin container.
	 */
	public static function init( Container $container ): void {
		// Both register auth filters and must be in place before anything resolves
		// the current user, because WordPress caches that answer for the whole
		// request -- a filter added later is never consulted.
		//
		// They divide the work rather than duplicate it. RouteAccess answers the
		// GoDaddy Launch platform: it lifts the coming-soon REST restriction, and
		// asserts api-request status for the MCP route so that credentials Launch
		// resolves early (notably core's own Basic auth) are still validated.
		// AppPasswordHeaderAuth answers hosted MCP clients, which can send neither
		// Basic auth nor a custom header name, and asserts api-request status only
		// when one of its own credentials is present.
		$container->get( RouteAccess::class )->setup();
		$container->get( AppPasswordHeaderAuth::class )->setup();

		$registry = $container->get( ToolRegistry::class );

		$proxy = $container->get( AbilitiesApiProxy::class );
		$proxy->setup();

		// Resolved before the closure so the server's tool list and the abilities the
		// proxy registers always come from the same source.
		$tool_ids = $registry->tool_ids();

		$draft_service = $container->get( DraftPageService::class );
		add_action( 'before_delete_post', array( $draft_service, 'cleanup_draft_meta' ) );

		// Initialise own McpAdapter instance. Internally hooks rest_api_init:15
		// to fire mcp_adapter_init for REST requests only.
		$adapter = McpAdapter::instance();

		// Create the 'airo-wp' MCP server when OUR adapter fires mcp_adapter_init.
		// The identity check prevents responding to another plugin's adapter if both
		// are active: they share the same action name but are separate Strauss-prefixed
		// instances, so the firing adapter must be verified as ours.
		add_action(
			'mcp_adapter_init',
			static function ( $fired_adapter ) use ( $adapter, $tool_ids ) {
				if ( $fired_adapter !== $adapter ) {
					return;
				}
				$adapter->create_server(
					'airo-wp',
					'airo-wp/v1',
					'mcp/streamable',
					__( 'Airo WP MCP Server', 'airo-wp' ),
					__( 'MCP server for Airo WP tools.', 'airo-wp' ),
					AIRO_WP_VERSION,
					array( HttpTransport::class ),
					null,     // Error handler: NullMcpErrorHandler used by default.
					null,     // Observability handler: NullMcpObservabilityHandler used by default.
					$tool_ids,
					array(),  // Resources.
					array(),  // Prompts.
					'is_user_logged_in'
				);
			}
		);
	}
}
