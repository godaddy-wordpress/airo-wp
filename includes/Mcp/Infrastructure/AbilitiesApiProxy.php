<?php
/**
 * Abilities API proxy.
 *
 * @package airo-wp
 */

declare(strict_types=1);

namespace GoDaddy\WordPress\Plugins\AiroWp\Mcp\Infrastructure;

defined( 'ABSPATH' ) || exit;

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
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\CreateNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\DeleteNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\GetNavigation;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigationRevisions;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\ListNavigations;
use GoDaddy\WordPress\Plugins\AiroWp\Mcp\Tools\Navigation\UpdateNavigation;

/**
 * Abstracts native (WP 6.9+) vs bundled (<6.9) Abilities API hook variants.
 *
 * Hooks register_tools() onto both known Abilities API action names:
 *  - wp_abilities_api_init — WordPress 6.9+ core Abilities API; also fired
 *                            by wordpress/abilities-api v0.4.0 (bundled)
 *  - abilities_api_init    — forward-compat listener; reserved in case a
 *                            future bundled variant adopts this hook name
 */
class AbilitiesApiProxy {

	/**
	 * Tools to register on boot.
	 *
	 * @var array<object>
	 */
	private array $tools;

	/**
	 * Constructor.
	 *
	 * @param SiteInfo                  $site_info                 Site info tool.
	 * @param UpdateSiteOptions         $update_site_options       Update site options tool.
	 * @param ActivatePlugin            $activate_plugin           Activate plugin tool.
	 * @param DeactivatePlugin          $deactivate_plugin         Deactivate plugin tool.
	 * @param GetPlugin                 $get_plugin                Get plugin tool.
	 * @param ListPlugins               $list_plugins              List plugins tool.
	 * @param UpdatePlugin              $update_plugin             Update plugin tool.
	 * @param ActivateTheme             $activate_theme            Activate theme tool.
	 * @param GetThemes                 $get_themes                Get themes tool.
	 * @param SwitchTheme               $switch_theme              Switch theme tool.
	 * @param CreatePost                $create_post               Create post tool.
	 * @param UpdatePost                $update_post               Update post tool.
	 * @param DeletePost                $delete_post               Delete post tool.
	 * @param ListPosts                 $list_posts                List posts tool.
	 * @param GetPost                   $get_post                  Get post tool.
	 * @param GetPostByOptionName       $get_post_by_option_name   Get post by option name tool.
	 * @param ListPostRevisions         $list_post_revisions       List post revisions tool.
	 * @param RestorePostRevision       $restore_post_revision     Restore post revision tool.
	 * @param UpdatePostImageAltText    $update_post_image_alt_text Update post image alt text tool.
	 * @param CreatePageDraft           $create_page_draft         Create page draft tool.
	 * @param PublishPageDraft          $publish_page_draft        Publish page draft tool.
	 * @param DiscardPageDraft          $discard_page_draft        Discard page draft tool.
	 * @param GetPageDraftStatus        $get_page_draft_status     Get page draft status tool.
	 * @param ListPageRevisions         $list_page_revisions       List page revisions tool.
	 * @param GetPageRevision           $get_page_revision         Get page revision tool.
	 * @param DeletePageRevision        $delete_page_revision      Delete page revision tool.
	 * @param DeleteMedia               $delete_media              Delete media tool.
	 * @param GetAllMedia               $get_all_media             Get all media tool.
	 * @param GetMediaById              $get_media_by_id           Get media by ID tool.
	 * @param ListMedia                 $list_media                List media tool.
	 * @param UpdateMediaMeta           $update_media_meta         Update media meta tool.
	 * @param UploadImage               $upload_image                  Upload image tool.
	 * @param ListTemplates             $list_templates                List templates tool.
	 * @param UpdateTemplate            $update_template               Update template tool.
	 * @param DeleteTemplate            $delete_template               Delete template tool.
	 * @param ListTemplateParts         $list_template_parts           List template parts tool.
	 * @param UpdateTemplatePart        $update_template_part          Update template part tool.
	 * @param DeleteTemplatePart        $delete_template_part          Delete template part tool.
	 * @param ListTemplateRevisions     $list_template_revisions       List template revisions tool.
	 * @param ListTemplatePartRevisions $list_template_part_revisions  List template part revisions tool.
	 * @param GetGlobalStyles           $get_global_styles             Get global styles tool.
	 * @param ListGlobalStyles          $list_global_styles            List global styles tool.
	 * @param ListGlobalStylesRevisions $list_global_styles_revisions  List global styles revisions tool.
	 * @param UpdateGlobalStyles        $update_global_styles          Update global styles tool.
	 * @param GetBlockTypes             $get_block_types               Get block types tool.
	 * @param GetBlockPatterns          $get_block_patterns            Get block patterns tool.
	 * @param ListNavigations           $list_navigations              List navigations tool.
	 * @param GetNavigation             $get_navigation                Get navigation tool.
	 * @param CreateNavigation          $create_navigation             Create navigation tool.
	 * @param UpdateNavigation          $update_navigation             Update navigation tool.
	 * @param DeleteNavigation          $delete_navigation             Delete navigation tool.
	 * @param ListNavigationRevisions   $list_navigation_revisions     List navigation revisions tool.
	 */
	public function __construct(
		SiteInfo $site_info,
		UpdateSiteOptions $update_site_options,
		ActivatePlugin $activate_plugin,
		DeactivatePlugin $deactivate_plugin,
		GetPlugin $get_plugin,
		ListPlugins $list_plugins,
		UpdatePlugin $update_plugin,
		ActivateTheme $activate_theme,
		GetThemes $get_themes,
		SwitchTheme $switch_theme,
		CreatePost $create_post,
		UpdatePost $update_post,
		DeletePost $delete_post,
		ListPosts $list_posts,
		GetPost $get_post,
		GetPostByOptionName $get_post_by_option_name,
		ListPostRevisions $list_post_revisions,
		RestorePostRevision $restore_post_revision,
		UpdatePostImageAltText $update_post_image_alt_text,
		CreatePageDraft $create_page_draft,
		PublishPageDraft $publish_page_draft,
		DiscardPageDraft $discard_page_draft,
		GetPageDraftStatus $get_page_draft_status,
		ListPageRevisions $list_page_revisions,
		GetPageRevision $get_page_revision,
		DeletePageRevision $delete_page_revision,
		DeleteMedia $delete_media,
		GetAllMedia $get_all_media,
		GetMediaById $get_media_by_id,
		ListMedia $list_media,
		UpdateMediaMeta $update_media_meta,
		UploadImage $upload_image,
		ListTemplates $list_templates,
		UpdateTemplate $update_template,
		DeleteTemplate $delete_template,
		ListTemplateParts $list_template_parts,
		UpdateTemplatePart $update_template_part,
		DeleteTemplatePart $delete_template_part,
		ListTemplateRevisions $list_template_revisions,
		ListTemplatePartRevisions $list_template_part_revisions,
		GetGlobalStyles $get_global_styles,
		ListGlobalStyles $list_global_styles,
		ListGlobalStylesRevisions $list_global_styles_revisions,
		UpdateGlobalStyles $update_global_styles,
		GetBlockTypes $get_block_types,
		GetBlockPatterns $get_block_patterns,
		ListNavigations $list_navigations,
		GetNavigation $get_navigation,
		CreateNavigation $create_navigation,
		UpdateNavigation $update_navigation,
		DeleteNavigation $delete_navigation,
		ListNavigationRevisions $list_navigation_revisions
	) {
		$this->tools = array(
			$site_info,
			$update_site_options,
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
			$list_navigation_revisions,
		);
	}

	/**
	 * Register hooks for both Abilities API variants.
	 */
	public function setup(): void {
		add_action( 'wp_abilities_api_categories_init', array( $this, 'register_categories' ) );
		add_action( 'wp_abilities_api_init', array( $this, 'register_tools' ) );
		add_action( 'abilities_api_init', array( $this, 'register_tools' ) );
	}

	/**
	 * Register ability categories required by our tools.
	 *
	 * The wp_register_ability_category() function is a WP 6.9+ core function. On WP 6.8 the
	 * Abilities API is loaded as a standalone package that provides the same
	 * function. The function_exists() guard ensures graceful degradation when
	 * neither source is available.
	 */
	public function register_categories(): void {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		wp_register_ability_category(
			'site-management',
			array(
				'label'       => __( 'Site Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing the WordPress site.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'plugin-management',
			array(
				'label'       => __( 'Plugin Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress plugins.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'theme-management',
			array(
				'label'       => __( 'Theme Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress themes.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'content-management',
			array(
				'label'       => __( 'Content Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress content.', 'airo-wp' ),
			)
		);

		wp_register_ability_category(
			'media-management',
			array(
				'label'       => __( 'Media Management', 'airo-wp' ),
				'description' => __( 'Abilities for managing WordPress media.', 'airo-wp' ),
			)
		);
	}

	/**
	 * Call register() on each tool.
	 */
	public function register_tools(): void {
		foreach ( $this->tools as $tool ) {
			$tool->register();
		}
	}
}
