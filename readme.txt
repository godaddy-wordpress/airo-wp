=== Airo WP AI Builder ===
Contributors: godaddy
Tags: airo, godaddy, mcp, ai, block-patterns
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.4.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

MCP server and block pattern library for AI-powered site building. Connect any AI client to manage content and assemble pages from curated patterns.

== Description ==

**Airo WP AI Builder** is a WordPress plugin that exposes your site to AI assistants via the [Model Context Protocol (MCP)](https://modelcontextprotocol.io/). It also bundles a curated set of Gutenberg block patterns built on the Twenty Twenty-Five (tt5) theme, giving any connected AI a ready-made vocabulary for assembling pages.

**MCP Server**

Registers an MCP-compatible server endpoint that any AI client can connect to. Tools cover posts, pages, media, templates, navigation menus, and global styles. Clients authenticate with a standard WordPress Application Password — see the FAQ for details.

**Block Patterns**

A curated library of Gutenberg block patterns designed for the Twenty Twenty-Five theme. Patterns are sourced from the DesignSetGo project and automatically deferred when DesignSetGo is active to avoid duplication.

**AI-agnostic**

No specific AI is bundled or required. Connect Claude, GPT, Gemini, or any MCP-compatible assistant of your choice.

**Connecting a client**

The MCP endpoint is `/wp-json/airo-wp/v1/mcp/streamable`. Authentication uses a standard WordPress Application Password, which you create and revoke under Users -> Profile. HTTPS is required, because WordPress does not issue Application Passwords on non-SSL sites. See the FAQ below for the exact header, and the permissions each tool requires.

**Developer highlights:**

* Custom DI container with domain `Package` classes registered on `plugins_loaded`
* Strauss-prefixed vendor code in `dependencies/` to avoid conflicts
* PHPUnit unit tests with Brain Monkey
* PHP 7.4 through 8.3 compatibility

== Roadmap (v1) ==

The following features are planned for the v1 release:

* **Site generation** — build a complete WordPress site from a single prompt
* **Conversational setup** — add pages, swap images, and update layouts through plain-English chat
* **WooCommerce storefront** — auto-configure a storefront with AI-generated products from a single prompt

== Installation ==

1. Upload the `airo-wp` folder to `/wp-content/plugins/`, or install from your organization's deployment pipeline.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. For development, run `composer install` in the plugin directory before activation (see CONTRIBUTORS.md).

== Frequently Asked Questions ==

= What does Airo WP AI Builder do right now? =

It registers an MCP server on your WordPress site and provides a block pattern library based on the Twenty Twenty-Five theme. You connect an AI client of your choice to the MCP endpoint and use it to read and write site content.

= Which AI can I use? =

Any MCP-compatible AI client — Claude, GPT, Gemini, or others. The plugin does not bundle or require a specific AI.

= Where do the block patterns come from? =

Patterns are sourced from the [DesignSetGo plugin](https://wordpress.org/plugins/designsetgo/). If DesignSetGo is active, Airo WP AI Builder defers to it automatically to avoid duplication.

= How do I connect an AI client to the MCP server? =

The endpoint is:

`https://example.com/wp-json/airo-wp/v1/mcp/streamable`

Create a credential under Users -> Profile -> Application Passwords, giving it a name such as "Claude". WordPress shows the generated password once only, so copy it before leaving the screen.

Combine your WordPress username and that password with a colon, base64-encode the result, and send it under the `airowp` authorization scheme:

`Authorization: airowp YWRtaW46YWJjZCBFRkdIIGlqa2wgTU5PUCBxcnN0IFVWV1g=`

Some clients offer only a bearer-token field rather than a full header. For those, prefix the same encoded value with `airowp_` and send it as a bearer token:

`Authorization: Bearer airowp_YWRtaW46YWJjZCBFRkdIIGlqa2wgTU5PUCBxcnN0IFVWV1g=`

Both forms are equivalent. Note that base64 is encoding rather than encryption, so treat the encoded string as being exactly as sensitive as the password itself, and keep it out of shared configuration files.

= Why does the MCP endpoint reject my credential? =

Three common causes:

1. **The site is not served over HTTPS.** WordPress refuses to issue or accept Application Passwords without SSL, so the credential cannot be created in the first place.
2. **The regular account password was used.** The MCP server accepts an Application Password, not your login password.
3. **The username is wrong.** Use the WordPress username, not the email address.

An error mentioning `Mcp-Session-Id` is not an authentication failure — it means the credential was accepted and the request reached the MCP server. The transport is session-based: call `initialize` first, then send the returned session id with every subsequent request. AI clients handle this automatically; it only comes up when calling the endpoint by hand.

= What can a connected AI actually change? =

Whatever the user you created the Application Password for is allowed to change. Authentication only establishes which user the request belongs to; every tool then checks a WordPress capability of its own — `edit_posts` to create a post, `upload_files` to add media, `activate_plugins` to activate a plugin, and so on.

A credential belonging to a Subscriber therefore connects successfully and is refused by every tool that modifies anything. Create the Application Password for a user whose role matches the access you intend to grant, and revoke it under Users -> Profile when it is no longer needed.

= Does this support the MCP specification's OAuth flow? =

Not yet. The MCP specification defines an OAuth 2.1 authorization flow with discovery documents and a consent screen. This plugin does not implement it, so clients that require OAuth discovery cannot connect. The Application Password scheme described above is a configured credential instead. OAuth support is under consideration.

= What PHP versions are supported? =

PHP 7.4 is the minimum. PHP 8.3 is the recommended version for local development. CI validates 7.4, 8.0, 8.1, 8.2, and 8.3.

= Why are dependencies in a `dependencies/` folder? =

Runtime Composer packages are namespace-prefixed with Strauss into `dependencies/` so they do not clash with other plugins' autoloaders.

== Changelog ==

= 0.4.1 =
* The download no longer carries test-only helper classes that were never used at runtime, making the plugin slightly smaller

= 0.4.0 =
* Images can again be uploaded from raw file data, not only from a URL — an image with no publicly reachable address could not be uploaded at all
* Plugin tools now reject an empty plugin slug up front instead of failing partway through
* Navigation and page-revision tools describe the fields they return again, so a client knows the shape of the response

= 0.3.6 =
* AI clients can now authenticate with a WordPress Application Password sent in the Authorization header (HTTPS required)
* Documented how to connect a client: the endpoint, creating a credential, and the capability each tool needs
* Fixed the MCP endpoint returning 401 on GoDaddy sites that have not been published yet
* Fixed a release fault where a successful publish could report failure and skip the plugin-directory artwork
* Release publishing now verifies credentials before building and explains authentication failures

= 0.3.5 =
* Reduced the download by 41% — uncompiled block sources are no longer shipped inside the plugin
* The asset build is now a hard precondition of packaging, so a stale build fails the release instead of shipping a plugin that registers nothing

= 0.3.4 =
* Added Git Updater support so the plugin can update itself from this repository's GitHub Releases
* Every release now attaches a built, installable zip to its GitHub Release
* WordPress.org publishing is now dispatched against a version tag rather than a release branch

= 0.3.3 =
* Maintenance release: documentation corrections and release-tooling fixes only, no user-facing changes

= 0.3.2 =
* Fixed a fatal error that stopped form fields rendering: the procedural render helpers they call were missing from the synced block source
* Fixed the MCP tools list being rejected by strict clients: two tools advertised an invalid input schema, which left every tool unavailable

= 0.3.1 =
* Maintenance release: release-pipeline and test-lane fixes only, no user-facing changes

= 0.3.0 =
* Added block editor extensions (animations, hover effects, sticky headers, dynamic tags, and more) to the bundled block source
* Confirmed compatibility with WordPress 7.1
* Added plugin-directory icon and banner artwork

= 0.2.5 =
* Hardened permission checks on four MCP tools

= 0.2.4 =
* Bundled block dynamic tags so shipped blocks resolve dynamic content correctly
* Corrected block class renames, docblocks, and patch paths in synced block source

= 0.2.3 =
* Renamed plugin to "Airo WP AI Builder" (WordPress.org trademark compliance)
* Fixed PHP syntax error: ABSPATH guard now placed after namespace declaration
* Fixed inline style in form submission meta box to use wp_add_inline_style()
* Scrubbed remote image URLs (Unsplash, picsum) from block patterns and src/blocks
* Corrected Plugin URI and Description header in plugin file
* Added external services documentation for Cloudflare Turnstile and Google Maps

= 0.2.2 =
* Fixed readme short description exceeding the 150-character limit

= 0.2.1 =
* Renamed plugin display name to "Airo WP"

= 0.2.0 =
* Added MCP server with tools for posts, pages, media, templates, navigation, and global styles
* Added DesignSetGo blocks and patterns with automated sync pipeline
* Added website management tools (site info, plugins, themes)
* Added release tooling with version bump, build pipeline, and distributable zip packaging
* E2e and plugin-check tests now validate the distributable build artifact

= 0.1.0 =
* Initial scaffold: bootstrap, DI container, package loader, Strauss build, unit tests, and PHPCS (WPCS + VIP-Go).

== Upgrade Notice ==

= 0.1.0 =
Initial release. Safe to install for development and future Airo features.

== External Services ==

This plugin optionally connects to two third-party services depending on which blocks are used.

**Cloudflare Turnstile** (form spam protection)

Used by the Form block when a Turnstile site key is configured under Settings → Airo WP AI Builder → Integrations.
When a visitor loads a page containing a form block, the Turnstile JavaScript widget is fetched from
`https://challenges.cloudflare.com/`. On form submission, the CAPTCHA token is verified by sending it
to `https://challenges.cloudflare.com/turnstile/v0/siteverify` along with the visitor's IP address.
No data is sent if no Turnstile site key is configured.

- Terms of Service: https://www.cloudflare.com/website-terms/
- Privacy Policy: https://www.cloudflare.com/privacypolicy/

**Google Maps** (map embed block)

Used by the Map block when a Google Maps API key is configured.
When a visitor loads a page containing a map block, the Google Maps JavaScript API is loaded from
`https://maps.googleapis.com/`. The visitor's browser communicates directly with Google Maps servers.
No data is sent if no API key is configured.

- Terms of Service: https://cloud.google.com/maps-platform/terms
- Privacy Policy: https://policies.google.com/privacy
