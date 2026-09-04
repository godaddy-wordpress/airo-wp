# Airo WP AI Builder

[![CI](https://github.com/godaddy-wordpress/airo-wp/actions/workflows/ci.yml/badge.svg)](https://github.com/godaddy-wordpress/airo-wp/actions/workflows/ci.yml)

**Airo WP AI Builder** is a WordPress plugin that exposes your site to AI assistants via the [Model Context Protocol (MCP)](https://modelcontextprotocol.io/). It also bundles a curated set of Gutenberg block patterns built on the Twenty Twenty-Five (tt5) theme, giving any connected AI a ready-made vocabulary for assembling pages.

## What's included

- **MCP Server** — Registers an MCP-compatible endpoint. Tools cover posts, pages, media, templates, navigation menus, and global styles. Clients authenticate with a WordPress Application Password — see [Connecting an AI client](#connecting-an-ai-client).
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
| **Version** | 0.3.6 |
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

## Connecting an AI client

### The endpoint

```
https://example.com/wp-json/airo-wp/v1/mcp/streamable
```

**HTTPS is required.** WordPress refuses to issue or accept Application Passwords unless
the site is served over SSL, so on a plain-HTTP site the credential below cannot even be
created.

### Create a credential

Application Passwords are WordPress's own mechanism — no plugin-specific secret is
involved, and you revoke access in the same place you granted it.

1. In wp-admin, go to **Users → Profile**.
2. Scroll to **Application Passwords**, enter a name (for example `Claude`), and select
   **Add New Application Password**.
3. Copy the generated password. WordPress shows it once and never again.

The user you create it for determines what the AI can do — see *Permissions* below.

### Authenticate

Combine the WordPress **username** and the application password with a colon, then
base64-encode the result:

```bash
printf '%s' 'admin:abcd EFGH ijkl MNOP qrst UVWX' | base64
# YWRtaW46YWJjZCBFRkdIIGlqa2wgTU5PUCBxcnN0IFVWV1g=
```

Send that under the `airowp` authorization scheme:

```
Authorization: airowp YWRtaW46YWJjZCBFRkdIIGlqa2wgTU5PUCBxcnN0IFVWV1g=
```

Some clients only offer a bearer-token field rather than a full header value. For those,
prefix the same encoded string with `airowp_` and send it as a bearer token:

```
Authorization: Bearer airowp_YWRtaW46YWJjZCBFRkdIIGlqa2wgTU5PUCBxcnN0IFVWV1g=
```

Both forms are equivalent. The `airowp` marker is what identifies the credential, so a
plain `Bearer` token is left untouched for other authentication schemes.

Keep the application password itself out of shared configuration files and version
control — base64 is encoding, not encryption, so the encoded string is exactly as
sensitive as the password.

### Example: Claude Code

```bash
claude mcp add --transport http airo-wp \
  https://example.com/wp-json/airo-wp/v1/mcp/streamable \
  --header "Authorization: airowp YWRtaW46YWJjZCBFRkdIIGlqa2wgTU5PUCBxcnN0IFVWV1g="
```

### Permissions

Authentication answers *who* the request is from; what it may do is decided separately,
by WordPress capabilities:

- reaching the endpoint at all requires the `read` capability
- every tool then checks its own capability — `edit_posts` to create a post,
  `upload_files` to add media, `activate_plugins` to activate a plugin, and so on

So a credential belonging to a Subscriber connects successfully and is refused by every
tool that changes anything. Create the application password for a user whose role
matches the access you intend to grant, rather than reaching for an administrator by
default.

### Calling it directly

Clients handle this themselves; it only matters if you are testing with `curl`. The
transport is session-based — `initialize` first, then send the returned session id with
every subsequent request:

```bash
# 1. initialize — the session id comes back in the Mcp-Session-Id RESPONSE HEADER
curl -i -X POST "$ENDPOINT" \
  -H 'Content-Type: application/json' \
  -H "Authorization: airowp $CREDENTIAL" \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"curl","version":"1.0"}}}'

# 2. every later call carries it
curl -X POST "$ENDPOINT" \
  -H 'Content-Type: application/json' \
  -H 'Mcp-Session-Id: <value from step 1>' \
  -H "Authorization: airowp $CREDENTIAL" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}'
```

Omitting the session id returns
`{"code":-32600,"message":"Invalid Request: Missing Mcp-Session-Id header"}` — an MCP
protocol error, not an authentication failure.

### A note on OAuth

The MCP specification defines an OAuth 2.1 authorization flow with discovery. This plugin
does not implement it: there is no discovery document, no consent screen and no token
endpoint. The scheme above is a configured credential, so clients that require OAuth
discovery cannot connect yet. Support for it is under consideration.

## Contributing

See **[CONTRIBUTORS.md](CONTRIBUTORS.md)** for environment setup, architecture, coding standards, and pull request expectations.

## License

GPLv2 or later. See [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html).
