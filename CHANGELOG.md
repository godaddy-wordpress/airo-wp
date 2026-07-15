# Changelog

## 0.2.5

- Hardened permission checks on four MCP tools

## 0.2.4

- Bundled block dynamic tags so shipped blocks resolve dynamic content correctly
- Corrected block class renames, docblocks, and patch paths in synced block source

## 0.2.3

- Renamed plugin to "Airo WP AI Builder" (WordPress.org trademark compliance)
- Fixed PHP syntax error: ABSPATH guard now placed after namespace declaration
- Fixed inline style in form submission meta box to use wp_add_inline_style()
- Scrubbed remote image URLs (Unsplash, picsum) from block patterns and src/blocks
- Corrected Plugin URI and Description header in plugin file
- Added external services documentation for Cloudflare Turnstile and Google Maps

## 0.2.2

- Fixed readme short description exceeding the 150-character limit

## 0.2.1

- Renamed plugin display name to "Airo WP"

## 0.2.0

- Added MCP server with tools for posts, pages, media, templates, navigation, and global styles
- Added DesignSetGo blocks and patterns with automated sync pipeline
- Added website management tools (site info, plugins, themes)
- Added release tooling with version bump, build pipeline, and distributable zip packaging
- E2e and plugin-check tests now validate the distributable build artifact
