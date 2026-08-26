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
| **Version** | 0.3.1 |
| **Requires WordPress** | 6.8+ |
| **Requires PHP** | 7.4+ |
| **License** | [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html) |
| **Author** | [GoDaddy](https://www.godaddy.com) |
| **Repository** | [godaddy-wordpress/airo-wp](https://github.com/godaddy-wordpress/airo-wp) |

## Installation

1. Copy or deploy this repository to `wp-content/plugins/airo-wp/`.
2. Activate **Airo WP AI Builder** under **Plugins** in wp-admin.

## Contributing

See **[CONTRIBUTORS.md](CONTRIBUTORS.md)** for environment setup, architecture, coding standards, and pull request expectations.

AI assistants should read **[AGENTS.md](AGENTS.md)** (`CLAUDE.md` symlinks to it) for repository-specific commands and architecture.

## License

GPLv2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
