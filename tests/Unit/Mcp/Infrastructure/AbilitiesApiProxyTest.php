<?php
/**
 * AbilitiesApiProxy tests.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Tests\Unit\Mcp\Infrastructure;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure\AbilitiesApiProxy;
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
use GoDaddy\WordPress\Plugins\AiroWp\Tests\TestCase;

/**
 * Tests for AbilitiesApiProxy.
 */
final class AbilitiesApiProxyTest extends TestCase {

	/**
	 * Create an AbilitiesApiProxy with all mocked tool dependencies.
	 *
	 * @return AbilitiesApiProxy
	 */
	private function create_proxy(): AbilitiesApiProxy {
		return new AbilitiesApiProxy(
			\Mockery::mock( SiteInfo::class ),
			\Mockery::mock( UpdateSiteOptions::class ),
			\Mockery::mock( ActivatePlugin::class ),
			\Mockery::mock( DeactivatePlugin::class ),
			\Mockery::mock( GetPlugin::class ),
			\Mockery::mock( ListPlugins::class ),
			\Mockery::mock( UpdatePlugin::class ),
			\Mockery::mock( ActivateTheme::class ),
			\Mockery::mock( GetThemes::class ),
			\Mockery::mock( SwitchTheme::class ),
			\Mockery::mock( CreatePost::class ),
			\Mockery::mock( UpdatePost::class ),
			\Mockery::mock( DeletePost::class ),
			\Mockery::mock( ListPosts::class ),
			\Mockery::mock( GetPost::class ),
			\Mockery::mock( GetPostByOptionName::class ),
			\Mockery::mock( ListPostRevisions::class ),
			\Mockery::mock( RestorePostRevision::class ),
			\Mockery::mock( UpdatePostImageAltText::class ),
			\Mockery::mock( CreatePageDraft::class ),
			\Mockery::mock( PublishPageDraft::class ),
			\Mockery::mock( DiscardPageDraft::class ),
			\Mockery::mock( GetPageDraftStatus::class ),
			\Mockery::mock( ListPageRevisions::class ),
			\Mockery::mock( GetPageRevision::class ),
			\Mockery::mock( DeletePageRevision::class ),
			\Mockery::mock( DeleteMedia::class ),
			\Mockery::mock( GetAllMedia::class ),
			\Mockery::mock( GetMediaById::class ),
			\Mockery::mock( ListMedia::class ),
			\Mockery::mock( UpdateMediaMeta::class ),
			\Mockery::mock( UploadImage::class ),
			\Mockery::mock( ListTemplates::class ),
			\Mockery::mock( UpdateTemplate::class ),
			\Mockery::mock( DeleteTemplate::class ),
			\Mockery::mock( ListTemplateParts::class ),
			\Mockery::mock( UpdateTemplatePart::class ),
			\Mockery::mock( DeleteTemplatePart::class ),
			\Mockery::mock( ListTemplateRevisions::class ),
			\Mockery::mock( ListTemplatePartRevisions::class ),
			\Mockery::mock( GetGlobalStyles::class ),
			\Mockery::mock( ListGlobalStyles::class ),
			\Mockery::mock( ListGlobalStylesRevisions::class ),
			\Mockery::mock( UpdateGlobalStyles::class ),
			\Mockery::mock( GetBlockTypes::class ),
			\Mockery::mock( GetBlockPatterns::class ),
			\Mockery::mock( ListNavigations::class ),
			\Mockery::mock( GetNavigation::class ),
			\Mockery::mock( CreateNavigation::class ),
			\Mockery::mock( UpdateNavigation::class ),
			\Mockery::mock( DeleteNavigation::class ),
			\Mockery::mock( ListNavigationRevisions::class )
		);
	}

	/**
	 * Setup() registers all three action hooks.
	 *
	 * Both wp_abilities_api_init (WP 6.9+ native) and abilities_api_init
	 * (bundled pre-6.9) are hooked — they are mutually exclusive at runtime,
	 * so register_tools() fires exactly once regardless of WP version.
	 * wp_abilities_api_categories_init is also registered for category support.
	 */
	public function test_setup_registers_all_hooks(): void {
		$proxy = $this->create_proxy();

		Actions\expectAdded( 'wp_abilities_api_init' )->once()->with( array( $proxy, 'register_tools' ) );
		Actions\expectAdded( 'abilities_api_init' )->once()->with( array( $proxy, 'register_tools' ) );
		Actions\expectAdded( 'wp_abilities_api_categories_init' )->once()->with( array( $proxy, 'register_categories' ) );

		$proxy->setup();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Register_categories() calls wp_register_ability_category for all categories.
	 */
	public function test_register_categories_registers_all_categories(): void {
		$proxy = $this->create_proxy();

		Functions\when( '__' )->returnArg( 1 );
		Functions\when( 'esc_html' )->returnArg( 1 );
		Functions\when( 'did_action' )->justReturn( 0 );
		Functions\when( '_doing_it_wrong' )->justReturn( null );

		$proxy->register_categories();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * Register_tools() calls register() on each injected tool.
	 */
	public function test_register_tools_calls_register_on_each_tool(): void {
		$site_info               = \Mockery::mock( SiteInfo::class );
		$update_site_opts        = \Mockery::mock( UpdateSiteOptions::class );
		$activate_plugin         = \Mockery::mock( ActivatePlugin::class );
		$deactivate_plugin       = \Mockery::mock( DeactivatePlugin::class );
		$get_plugin              = \Mockery::mock( GetPlugin::class );
		$list_plugins            = \Mockery::mock( ListPlugins::class );
		$update_plugin           = \Mockery::mock( UpdatePlugin::class );
		$activate_theme          = \Mockery::mock( ActivateTheme::class );
		$get_themes              = \Mockery::mock( GetThemes::class );
		$switch_theme            = \Mockery::mock( SwitchTheme::class );
		$create_post             = \Mockery::mock( CreatePost::class );
		$update_post             = \Mockery::mock( UpdatePost::class );
		$delete_post             = \Mockery::mock( DeletePost::class );
		$list_posts              = \Mockery::mock( ListPosts::class );
		$get_post                = \Mockery::mock( GetPost::class );
		$get_post_by_option_name = \Mockery::mock( GetPostByOptionName::class );
		$list_post_revisions        = \Mockery::mock( ListPostRevisions::class );
		$restore_post_revision      = \Mockery::mock( RestorePostRevision::class );
		$update_post_image_alt_text = \Mockery::mock( UpdatePostImageAltText::class );
		$create_page_draft          = \Mockery::mock( CreatePageDraft::class );
		$publish_page_draft      = \Mockery::mock( PublishPageDraft::class );
		$discard_page_draft      = \Mockery::mock( DiscardPageDraft::class );
		$get_page_draft_status   = \Mockery::mock( GetPageDraftStatus::class );
		$list_page_revisions     = \Mockery::mock( ListPageRevisions::class );
		$get_page_revision       = \Mockery::mock( GetPageRevision::class );
		$delete_page_revision    = \Mockery::mock( DeletePageRevision::class );
		$delete_media            = \Mockery::mock( DeleteMedia::class );
		$get_all_media           = \Mockery::mock( GetAllMedia::class );
		$get_media_by_id         = \Mockery::mock( GetMediaById::class );
		$list_media              = \Mockery::mock( ListMedia::class );
		$update_media_meta       = \Mockery::mock( UpdateMediaMeta::class );
		$upload_image            = \Mockery::mock( UploadImage::class );
		$list_templates          = \Mockery::mock( ListTemplates::class );
		$update_template         = \Mockery::mock( UpdateTemplate::class );
		$delete_template         = \Mockery::mock( DeleteTemplate::class );
		$list_template_parts     = \Mockery::mock( ListTemplateParts::class );
		$update_template_part    = \Mockery::mock( UpdateTemplatePart::class );
		$delete_template_part    = \Mockery::mock( DeleteTemplatePart::class );
		$list_template_revisions = \Mockery::mock( ListTemplateRevisions::class );
		$list_template_part_revisions = \Mockery::mock( ListTemplatePartRevisions::class );
		$get_global_styles            = \Mockery::mock( GetGlobalStyles::class );
		$list_global_styles           = \Mockery::mock( ListGlobalStyles::class );
		$list_global_styles_revisions = \Mockery::mock( ListGlobalStylesRevisions::class );
		$update_global_styles         = \Mockery::mock( UpdateGlobalStyles::class );
		$get_block_types              = \Mockery::mock( GetBlockTypes::class );
		$get_block_patterns           = \Mockery::mock( GetBlockPatterns::class );
		$list_navigations             = \Mockery::mock( ListNavigations::class );
		$get_navigation               = \Mockery::mock( GetNavigation::class );
		$create_navigation            = \Mockery::mock( CreateNavigation::class );
		$update_navigation            = \Mockery::mock( UpdateNavigation::class );
		$delete_navigation            = \Mockery::mock( DeleteNavigation::class );
		$list_navigation_revisions    = \Mockery::mock( ListNavigationRevisions::class );

		$site_info->shouldReceive( 'register' )->once();
		$update_site_opts->shouldReceive( 'register' )->once();
		$activate_plugin->shouldReceive( 'register' )->once();
		$deactivate_plugin->shouldReceive( 'register' )->once();
		$get_plugin->shouldReceive( 'register' )->once();
		$list_plugins->shouldReceive( 'register' )->once();
		$update_plugin->shouldReceive( 'register' )->once();
		$activate_theme->shouldReceive( 'register' )->once();
		$get_themes->shouldReceive( 'register' )->once();
		$switch_theme->shouldReceive( 'register' )->once();
		$create_post->shouldReceive( 'register' )->once();
		$update_post->shouldReceive( 'register' )->once();
		$delete_post->shouldReceive( 'register' )->once();
		$list_posts->shouldReceive( 'register' )->once();
		$get_post->shouldReceive( 'register' )->once();
		$get_post_by_option_name->shouldReceive( 'register' )->once();
		$list_post_revisions->shouldReceive( 'register' )->once();
		$restore_post_revision->shouldReceive( 'register' )->once();
		$update_post_image_alt_text->shouldReceive( 'register' )->once();
		$create_page_draft->shouldReceive( 'register' )->once();
		$publish_page_draft->shouldReceive( 'register' )->once();
		$discard_page_draft->shouldReceive( 'register' )->once();
		$get_page_draft_status->shouldReceive( 'register' )->once();
		$list_page_revisions->shouldReceive( 'register' )->once();
		$get_page_revision->shouldReceive( 'register' )->once();
		$delete_page_revision->shouldReceive( 'register' )->once();
		$delete_media->shouldReceive( 'register' )->once();
		$get_all_media->shouldReceive( 'register' )->once();
		$get_media_by_id->shouldReceive( 'register' )->once();
		$list_media->shouldReceive( 'register' )->once();
		$update_media_meta->shouldReceive( 'register' )->once();
		$upload_image->shouldReceive( 'register' )->once();
		$list_templates->shouldReceive( 'register' )->once();
		$update_template->shouldReceive( 'register' )->once();
		$delete_template->shouldReceive( 'register' )->once();
		$list_template_parts->shouldReceive( 'register' )->once();
		$update_template_part->shouldReceive( 'register' )->once();
		$delete_template_part->shouldReceive( 'register' )->once();
		$list_template_revisions->shouldReceive( 'register' )->once();
		$list_template_part_revisions->shouldReceive( 'register' )->once();
		$get_global_styles->shouldReceive( 'register' )->once();
		$list_global_styles->shouldReceive( 'register' )->once();
		$list_global_styles_revisions->shouldReceive( 'register' )->once();
		$update_global_styles->shouldReceive( 'register' )->once();
		$get_block_types->shouldReceive( 'register' )->once();
		$get_block_patterns->shouldReceive( 'register' )->once();
		$list_navigations->shouldReceive( 'register' )->once();
		$get_navigation->shouldReceive( 'register' )->once();
		$create_navigation->shouldReceive( 'register' )->once();
		$update_navigation->shouldReceive( 'register' )->once();
		$delete_navigation->shouldReceive( 'register' )->once();
		$list_navigation_revisions->shouldReceive( 'register' )->once();

		$proxy = new AbilitiesApiProxy(
			$site_info,
			$update_site_opts,
			$activate_plugin,
			$deactivate_plugin,
			$get_plugin,
			$list_plugins,
			$update_plugin,
			$activate_theme,
			$get_themes,
			$switch_theme,
			$create_post,
			$update_post,
			$delete_post,
			$list_posts,
			$get_post,
			$get_post_by_option_name,
			$list_post_revisions,
			$restore_post_revision,
			$update_post_image_alt_text,
			$create_page_draft,
			$publish_page_draft,
			$discard_page_draft,
			$get_page_draft_status,
			$list_page_revisions,
			$get_page_revision,
			$delete_page_revision,
			$delete_media,
			$get_all_media,
			$get_media_by_id,
			$list_media,
			$update_media_meta,
			$upload_image,
			$list_templates,
			$update_template,
			$delete_template,
			$list_template_parts,
			$update_template_part,
			$delete_template_part,
			$list_template_revisions,
			$list_template_part_revisions,
			$get_global_styles,
			$list_global_styles,
			$list_global_styles_revisions,
			$update_global_styles,
			$get_block_types,
			$get_block_patterns,
			$list_navigations,
			$get_navigation,
			$create_navigation,
			$update_navigation,
			$delete_navigation,
			$list_navigation_revisions
		);

		$proxy->register_tools();

		$this->addToAssertionCount( 1 );
	}
}
