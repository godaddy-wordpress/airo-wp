# Changelog

## 0.3.3

- Maintenance release: documentation corrections and release-tooling fixes only, no user-facing changes

## 0.3.2

- Fixed a fatal error that stopped form fields rendering: the procedural render helpers they call were missing from the synced block source
- Fixed the MCP tools list being rejected by strict clients: two tools advertised an invalid input schema, which left every tool unavailable

## 0.3.1

- Maintenance release: release-pipeline and test-lane fixes only, no user-facing changes

## 0.3.0

- Added block editor extensions (animations, hover effects, sticky headers, dynamic tags, and more) to the bundled block source
- Confirmed compatibility with WordPress 7.1
- Added plugin-directory icon and banner artwork

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
