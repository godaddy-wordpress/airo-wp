# Changelog

## 0.4.1

- The download no longer carries test-only helper classes that were never used at runtime, making the plugin slightly smaller

## 0.4.0

- Images can again be uploaded from raw file data, not only from a URL — an image with no publicly reachable address could not be uploaded at all
- Plugin tools now reject an empty plugin slug up front instead of failing partway through
- Navigation and page-revision tools describe the fields they return again, so a client knows the shape of the response

## 0.3.6

- AI clients can now authenticate with a WordPress Application Password sent in the `Authorization` header (HTTPS required)
- Documented how to connect a client: the endpoint, creating a credential, and the capability each tool needs
- Fixed the MCP endpoint returning 401 on GoDaddy sites that have not been published yet
- Fixed a release fault where a successful publish could report failure and skip the plugin-directory artwork
- Release publishing now verifies credentials before building and explains authentication failures

## 0.3.5

- Reduced the download by 41%: uncompiled block sources are no longer shipped inside the plugin. Only the compiled output users actually run is included
- The build is now a hard precondition of packaging, so a skipped or stale asset build fails the release instead of producing a plugin that registers nothing

## 0.3.4

- Added Git Updater support: `GitHub Plugin URI`, `Primary Branch` and `Release Asset` plugin headers, so the plugin can update itself from this repository's GitHub Releases
- Every release now attaches a built, installable zip to its GitHub Release — the repository source alone is not installable, because `vendor/` and `dist/` are generated at build time
- WordPress.org publishing is now dispatched against a version tag rather than a release branch, so a published release is always exactly what CI tested

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
