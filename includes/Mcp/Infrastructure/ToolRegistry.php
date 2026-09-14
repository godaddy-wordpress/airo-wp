<?php
/**
 * MCP tool registry.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure;

defined( 'ABSPATH' ) || exit;

use GoDaddy\WordPress\Plugins\AiroWp\Container;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site\SiteInfo;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Site\UpdateSiteOptions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\ActivatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\DeactivatePlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\GetPlugin;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\ListPlugins;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Plugins\UpdatePlugin;
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
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigations;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\GetNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\CreateNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\UpdateNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\DeleteNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigationRevisions;

/**
 * The canonical list of MCP tools, and the only place it is written down.
 *
 * Two things need this list: AbilitiesApiProxy, which calls register() on every
 * tool, and Mcp\Package, which passes the tool IDs to create_server(). Those used
 * to keep separate hand-maintained copies — a 52-parameter constructor on one side
 * and a 52-entry TOOL_ID array on the other — so adding a tool meant editing both
 * and a tool registered as an ability but missing from the server list (or the
 * reverse) was an easy and silent mistake.
 *
 * Adding a tool is now one line in {@see self::TOOLS}.
 */
final class ToolRegistry {

	/**
	 * Tool classes, in registration order.
	 *
	 * @var string[]
	 */
	public const TOOLS = array(
		SiteInfo::class,
		UpdateSiteOptions::class,
		ActivatePlugin::class,
		DeactivatePlugin::class,
		GetPlugin::class,
		ListPlugins::class,
		UpdatePlugin::class,
		ActivateTheme::class,
		GetThemes::class,
		SwitchTheme::class,
		CreatePost::class,
		UpdatePost::class,
		DeletePost::class,
		ListPosts::class,
		GetPost::class,
		GetPostByOptionName::class,
		ListPostRevisions::class,
		RestorePostRevision::class,
		UpdatePostImageAltText::class,
		CreatePageDraft::class,
		PublishPageDraft::class,
		DiscardPageDraft::class,
		GetPageDraftStatus::class,
		ListPageRevisions::class,
		GetPageRevision::class,
		DeletePageRevision::class,
		DeleteMedia::class,
		GetAllMedia::class,
		GetMediaById::class,
		ListMedia::class,
		UpdateMediaMeta::class,
		UploadImage::class,
		ListTemplates::class,
		UpdateTemplate::class,
		DeleteTemplate::class,
		ListTemplateParts::class,
		UpdateTemplatePart::class,
		DeleteTemplatePart::class,
		ListTemplateRevisions::class,
		ListTemplatePartRevisions::class,
		GetGlobalStyles::class,
		ListGlobalStyles::class,
		ListGlobalStylesRevisions::class,
		UpdateGlobalStyles::class,
		GetBlockTypes::class,
		GetBlockPatterns::class,
		ListNavigations::class,
		GetNavigation::class,
		CreateNavigation::class,
		UpdateNavigation::class,
		DeleteNavigation::class,
		ListNavigationRevisions::class,
	);

	/**
	 * Container used to resolve tool instances.
	 *
	 * @var Container
	 */
	private Container $container;

	/**
	 * Resolved tool instances, or null before the first all() call.
	 *
	 * @var array<object>|null
	 */
	private ?array $instances = null;

	/**
	 * Constructor.
	 *
	 * @param Container $container Plugin container.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Resolve every tool.
	 *
	 * Instances are memoised, though the container caches resolutions anyway.
	 *
	 * @return array<object> Tool instances in registration order.
	 */
	public function all(): array {
		if ( null === $this->instances ) {
			$instances = array();

			foreach ( self::TOOLS as $tool_class ) {
				$instances[] = $this->container->get( $tool_class );
			}

			$this->instances = $instances;
		}

		return $this->instances;
	}

	/**
	 * The TOOL_ID of every tool.
	 *
	 * Reads the constant off each class name, so this does not instantiate anything —
	 * create_server() only needs the IDs.
	 *
	 * @return string[] Tool IDs in registration order.
	 */
	public function tool_ids(): array {
		$ids = array();

		foreach ( self::TOOLS as $tool_class ) {
			$ids[] = $tool_class::TOOL_ID;
		}

		return $ids;
	}
}
