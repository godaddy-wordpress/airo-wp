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
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\ActivatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\DeactivatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\GetPlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\ListPlugins;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\UpdatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site\SiteInfo;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site\UpdateSiteOptions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes\ActivateTheme;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes\GetThemes;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Themes\SwitchTheme;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\CreatePost;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\UpdatePost;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\DeletePost;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\ListPosts;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\GetPost;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\GetPostByOptionName;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\ListPostRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\RestorePostRevision;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Posts\UpdatePostImageAltText;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\CreatePageDraft;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\PublishPageDraft;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\DiscardPageDraft;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\GetPageDraftStatus;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\ListPageRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\GetPageRevision;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Pages\DeletePageRevision;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\DeleteMedia;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\GetAllMedia;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\GetMediaById;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\ListMedia;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\UpdateMediaMeta;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Media\UploadImage;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\ListTemplates;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\UpdateTemplate;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\DeleteTemplate;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\ListTemplateParts;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\UpdateTemplatePart;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\DeleteTemplatePart;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\ListTemplateRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Templates\ListTemplatePartRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\GetGlobalStyles;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\ListGlobalStyles;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\ListGlobalStylesRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\UpdateGlobalStyles;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\GetBlockTypes;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\GlobalStyles\GetBlockPatterns;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\CreateNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\DeleteNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\GetNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigationRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigations;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\UpdateNavigation;
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

		$proxy = $container->get( AbilitiesApiProxy::class );
		$proxy->setup();

		$draft_service = $container->get( DraftPageService::class );
		add_action( 'before_delete_post', array( $draft_service, 'cleanup_draft_meta' ) );

		// Initialise own McpAdapter instance. Internally hooks rest_api_init:15
		// to fire mcp_adapter_init for REST requests only.
		$adapter = McpAdapter::instance();

		// Create the 'airo-wp' MCP server when OUR adapter fires mcp_adapter_init.
		// The identity check prevents responding to mcp-adapter-initializer's adapter
		// if both plugins are active (they share the same action name but are separate
		// Strauss-prefixed instances).
		add_action(
			'mcp_adapter_init',
			static function ( $fired_adapter ) use ( $adapter ) {
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
					array(
						SiteInfo::TOOL_ID,
						UpdateSiteOptions::TOOL_ID,
						ActivatePlugin::TOOL_ID,
						DeactivatePlugin::TOOL_ID,
						GetPlugin::TOOL_ID,
						ListPlugins::TOOL_ID,
						UpdatePlugin::TOOL_ID,
						ActivateTheme::TOOL_ID,
						GetThemes::TOOL_ID,
						SwitchTheme::TOOL_ID,
						CreatePost::TOOL_ID,
						UpdatePost::TOOL_ID,
						DeletePost::TOOL_ID,
						ListPosts::TOOL_ID,
						GetPost::TOOL_ID,
						GetPostByOptionName::TOOL_ID,
						ListPostRevisions::TOOL_ID,
						RestorePostRevision::TOOL_ID,
						UpdatePostImageAltText::TOOL_ID,
						CreatePageDraft::TOOL_ID,
						PublishPageDraft::TOOL_ID,
						DiscardPageDraft::TOOL_ID,
						GetPageDraftStatus::TOOL_ID,
						ListPageRevisions::TOOL_ID,
						GetPageRevision::TOOL_ID,
						DeletePageRevision::TOOL_ID,
						DeleteMedia::TOOL_ID,
						GetAllMedia::TOOL_ID,
						GetMediaById::TOOL_ID,
						ListMedia::TOOL_ID,
						UpdateMediaMeta::TOOL_ID,
						UploadImage::TOOL_ID,
						ListTemplates::TOOL_ID,
						UpdateTemplate::TOOL_ID,
						DeleteTemplate::TOOL_ID,
						ListTemplateParts::TOOL_ID,
						UpdateTemplatePart::TOOL_ID,
						DeleteTemplatePart::TOOL_ID,
						ListTemplateRevisions::TOOL_ID,
						ListTemplatePartRevisions::TOOL_ID,
						GetGlobalStyles::TOOL_ID,
						ListGlobalStyles::TOOL_ID,
						ListGlobalStylesRevisions::TOOL_ID,
						UpdateGlobalStyles::TOOL_ID,
						GetBlockTypes::TOOL_ID,
						GetBlockPatterns::TOOL_ID,
						ListNavigations::TOOL_ID,
						GetNavigation::TOOL_ID,
						CreateNavigation::TOOL_ID,
						UpdateNavigation::TOOL_ID,
						DeleteNavigation::TOOL_ID,
						ListNavigationRevisions::TOOL_ID,
					),
					array(),  // Resources.
					array(),  // Prompts.
					'is_user_logged_in'
				);
			}
		);
	}
}
