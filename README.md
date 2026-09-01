# Airo WP AI Builder

[![CI](https://github.com/godaddy-wordpress/airo-wp/actions/workflows/ci.yml/badge.svg)](https://github.com/godaddy-wordpress/airo-wp/actions/workflows/ci.yml)

**Airo WP AI Builder** is a WordPress plugin that exposes your site to AI assistants via the [Model Context Protocol (MCP)](https://modelcontextprotocol.io/). It also bundles a curated set of Gutenberg block patterns built on the Twenty Twenty-Five (tt5) theme, giving any connected AI a ready-made vocabulary for assembling pages.

## What's included

- **MCP Server** — Registers an MCP-compatible endpoint. Tools cover posts, pages, media, templates, navigation menus, and global styles.
- **Block Patterns** — Curated Gutenberg patterns for the Twenty Twenty-Five theme, sourced from [DesignSetGo](https://wordpress.org/plugins/designsetgo/) (deferred automatically when DesignSetGo is active).
- **AI-agnostic** — Connect Claude, GPT, Gemini, or any MCP-compatible client. No specific AI is bundled or required.

## Roadmap (v1)

- **Site generation** — build a complete WordPress site from a single prompt
- **Conversational setup** — add pages, swap images, and update layouts through plain-English chat
- **WooCommerce storefront** — auto-configure a storefront with AI-generated products from a single prompt

## Plugin information

| | |
|---|---|
| **Plugin name** | Airo WP AI Builder |
| **Text domain** | `airo-wp` |
| **Version** | 0.3.5 |
| **Requires WordPress** | 6.9+ |
| **Requires PHP** | 7.4+ |
| **License** | [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html) |
| **Author** | [GoDaddy](https://www.godaddy.com) |
| **Repository** | [godaddy-wordpress/airo-wp](https://github.com/godaddy-wordpress/airo-wp) |

## Installation

**Do not install by downloading the repository source.** `vendor/` and `dist/` are
generated at build time and are not committed, so a source checkout or GitHub's
"Download ZIP" produces a plugin that loads nothing and registers no blocks. Use one
of these instead:

**From a release (recommended)** — every release attaches a built, installable zip:

1. Download `airo-wp-<version>.zip` from [Releases](https://github.com/godaddy-wordpress/airo-wp/releases).
2. In wp-admin, go to **Plugins → Add New → Upload Plugin** and upload it.
3. Activate **Airo WP AI Builder**.

**Automatic updates from this repository** — install
[Git Updater](https://git-updater.com/), which reads the `GitHub Plugin URI`,
`Primary Branch` and `Release Asset` headers in `airo-wp.php` and offers each new
release as a normal WordPress update. It installs the attached build, not the source
zipball. Pre-releases are flagged as such on GitHub, so Git Updater does not offer
them as updates.

**From source, for development** — clone the repository and build it; see
[CONTRIBUTORS.md](CONTRIBUTORS.md).

## Contributing

See **[CONTRIBUTORS.md](CONTRIBUTORS.md)** for environment setup, architecture, coding standards, and pull request expectations.

## License

GPLv2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
