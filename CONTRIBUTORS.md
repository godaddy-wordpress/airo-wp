# Contributing to Airo WP

Thank you for contributing to the Airo WordPress plugin. This document explains how to get started, how the project is organized, and what we expect in pull requests.

## What this repository is

Airo WP is a **plugin scaffold**: bootstrap, DI container, package loader, Strauss vendor isolation, tests, and CI. Feature work happens in **domain packages** under `includes/{Domain}/`, not in the root bootstrap file.

## Prerequisites

Before you begin, install:

| Tool | Version |
|------|---------|
| PHP | 7.4+ (use **8.3** locally when possible) |
| Composer | 2.x |
| Git | 2.x |
| WordPress | 6.8+ (local site or container) |

Optional but recommended:

- A local WordPress environment (e.g. site-designer-local or your team's standard stack)
- `phpcs` / `phpcbf` via Composer (included in dev dependencies)

## Getting started

### 1. Clone and install dependencies

```bash
git clone git@github.com:godaddy-wordpress/airo-wp.git
cd airo-wp
composer install
```

This installs dev tools (PHPUnit, Brain Monkey, WPCS, VIPWPCS), downloads the Strauss phar, and wires autoloaders.

### 2. Link the plugin into WordPress

Point your local site's plugins directory at this repo:

```bash
# Example: symlink (adjust paths to your environment)
ln -s "$(pwd)" /path/to/wordpress/wp-content/plugins/airo-wp
```

Or copy the built plugin tree into `wp-content/plugins/airo-wp/`.

### 3. Prefix runtime dependencies (when changing deps or autoload)

```bash
composer build
```

Run this after adding or updating Composer `require` packages, or after changing PSR-4 paths. It runs Strauss to refresh prefixed packages under `dependencies/`, then regenerates `vendor/autoload.php` via Composer. The `dependencies/` directory is gitignored — do not commit it.

### 4. Verify everything passes

With Docker (recommended; caches `vendor` in named volumes):

```bash
make check             # test + lint (recommended before opening a PR)
make test PHP=8.0      # test on a specific version only
make lint              # lint only
make docker-build-all  # build all matrix images locally (optional)
```

CI runs the same commands automatically:
- `.github/workflows/phpunit.yml` — PHPUnit matrix on PHP 7.4–8.3 (PR + push to `main`)
- `.github/workflows/phpcs.yml` — PHPCS on PHP 8.3 (PR + push to `main`)
- `.github/workflows/plugin-check.yml` — WordPress Plugin Check on PHP 8.3, WP latest (PR + push to `main`)

Or locally with Composer (single host PHP only):

```bash
composer test
composer lint
```

Both must pass before you open a pull request. Build images once with `make docker-build` if `airo-wp-test` / `airo-wp-linter` tags are missing.

### 5. Activate in WordPress

In wp-admin → **Plugins**, activate **Airo WP**. The scaffold has no settings screen; activation only boots the container and placeholder `scaffold` package.

## Development commands

| Command | Description |
|---------|-------------|
| `make test` | PHPUnit in Docker (default PHP 8.3) |
| `make test PHP=8.0` | PHPUnit in Docker on a specific version (7.4–8.3) |
| `make lint` | PHPCS in Docker |
| `make check` | test + lint |
| `make docker-build` | Build `airo-wp-test` and `airo-wp-linter` images |
| `make docker-build-all` | Build test images for every PHP version |
| `make plugin-check` | Run WordPress Plugin Check in Docker (PHP 8.3, WP latest) |
| `make plugin-check PHP=8.3 WP=6.8` | Plugin Check against a specific WP version |
| `make docker-build-e2e` | Build `airo-wp-e2e` image (PHP + Node.js + WP-CLI) |
| `make e2e` | Run e2e tests in Docker (PHP 8.3, WP 6.9.4) |
| `make e2e PHP=8.0 WP=6.8` | Run e2e tests against a specific PHP × WP version |
| `make playground` | Run WP Playground in Docker (PHP 8.3, latest WP) |
| `make playground PHP=8.2 WP=6.8` | Playground with specific PHP × WP version |
| `make playground-stop` | Stop the running playground container |
| `make playground-reset` | Stop and remove all playground data (fresh start) |
| `make version-bump TYPE=patch` | Bump plugin version in all files (patch/minor/major) |
| `make package` | Build distributable zip (`builds/airo-wp.zip`) |
| `make build TYPE=patch` | Version bump + full build (patch/minor/major) |
| `composer test` | PHPUnit on host PHP (no version matrix) |
| `composer lint` | PHPCS — WPCS + VIP-Go + PHPCompatibility |
| `composer format` | PHPCBF auto-fix |
| `composer build` | Strauss-prefix runtime deps into `dependencies/` |

## Plugin Check

The [WordPress Plugin Check](https://wordpress.org/plugins/plugin-check/) (PCP) verifies the plugin satisfies WordPress.org requirements (security, performance, accessibility, plugin-review standards).

### Running locally

```bash
# Build the e2e image once (shared with e2e tests)
make docker-build-e2e

# Run Plugin Check (default: PHP 8.3, WP latest)
make plugin-check

# Run against a specific WP version
make plugin-check PHP=8.3 WP=6.8
```

### How it works

1. `tests/e2e/setup/plugin-check-entrypoint.sh` runs inside the container:
   - Builds a distributable zip via `.dev/release/build-zip.sh` (Composer, DSG sync, wp-scripts build, plugin-zip)
   - Downloads WordPress via WP-CLI and sets up SQLite database integration (no MySQL needed)
   - Installs the plugin from the built zip (`wp plugin install builds/airo-wp-test.zip`)
   - Installs the Plugin Check plugin and runs `wp plugin check` with `--require=cli.php` for runtime checks
2. Full results are saved as markdown to `plugin-check-results/full-results.txt` (posted as a sticky PR comment in CI)
3. A filtered run (excluding baseline ignore codes) determines pass/fail
4. The job hard-fails if any ERROR-type finding is detected in the filtered output

### The `.distignore` file

`.distignore` documents which files and directories are excluded from the distributable plugin archive. The plugin-check build step mirrors these exclusions. When adding new dev-only files or directories, add them to `.distignore` to keep them out of the build.

## End-to-end tests

E2e tests run WordPress via [`@wp-playground/cli`](https://github.com/WordPress/playground-tools/tree/trunk/packages/playground-cli) (WASM PHP + bundled SQLite, no separate database) and drive the REST API with [Playwright](https://playwright.dev).

### Prerequisites

- Docker (same requirement as unit tests)
- No local Node.js or PHP needed — everything runs inside the container

### Running locally

```bash
# Build the e2e image once (or after changing .dev/e2e/Dockerfile)
make docker-build-e2e

# Run against the default matrix cell (PHP 8.3, WP 6.9.4)
make e2e

# Run against a specific PHP × WP version
make e2e PHP=8.0 WP=6.8
```

Supported WP versions: `6.8`, `6.9`, `7` (must be `>= 6.8` — the plugin's declared minimum).

On failure, Playwright's HTML report is written to `tests/e2e/playwright-report/` inside the container. Pass `-e PWDEBUG=1` in the `docker run` command to enable step-by-step debugging.

### What the tests cover

| Spec | What it verifies |
|------|-----------------|
| `plugin-activation.spec.ts` | Plugin is present and `active` via `GET /wp/v2/plugins` |
| `mcp-tools.spec.ts` | MCP `tools/list` returns `gd-mcp-get-site-info`; `tools/call` returns site data |

### How it works

1. `entrypoint.sh` runs inside the container:
   - Builds a distributable zip via `.dev/release/build-zip.sh` (Composer, DSG sync, wp-scripts build, plugin-zip)
   - Extracts `builds/airo-wp-test.zip` to a temp directory
   - Copies `tests/e2e/setup/e2e-setup.php` into the @wp-playground/cli server's MU-plugins directory (loaded on WP boot)
   - Starts `@wp-playground/cli` serving the extracted plugin on port 8080
   - Runs `playwright test` with static test credentials
2. All REST calls use `index.php?rest_route=` instead of `/wp-json/` because @wp-playground/cli does not rewrite URLs at the Node level.

### CI

The workflow at `.github/workflows/e2e.yml` is **manual only** (`workflow_dispatch`). It accepts optional `php_version` and `wp_version` inputs (defaults to `all`) and runs the full PHP × WP matrix with `fail-fast: false`. Trigger it from the GitHub Actions tab when you need to verify backward compatibility.

## WP Playground

A local WordPress instance running the plugin with live source binding — useful for quick fixes, prototyping, and manual testing.

### Running

```bash
# Start (first run builds the image and installs deps)
make playground

# Specific PHP and WP versions
make playground PHP=8.2 WP=6.8

# Stop
make playground-stop

# Reset all state (fresh WordPress on next start)
make playground-reset
```

### How it works

- Uses [`@wp-playground/cli`](https://github.com/WordPress/playground-tools/tree/trunk/packages/playground-cli) (WordPress Playground for Node.js) inside Docker
- Plugin source is bind-mounted — edits reflect immediately without restart
- WordPress state (SQLite database, uploads, settings) persists in the `airo-wp-playground-data` Docker volume across container restarts
- Browser auto-opens at `http://localhost:9400`; admin at `http://localhost:9400/wp-admin/`
- The Docker image is built from the `base` stage of `.dev/e2e/Dockerfile` (PHP + Node 24 + Composer, shared with the e2e image)

### Known limitations

- @wp-playground/cli uses PHP-WASM (WebAssembly), not native PHP — step-debugging (Xdebug) is not available
- WordPress loopback requests (Site Health REST API check) time out — this is a PHP-WASM limitation and does not affect normal plugin development
- Plugin source changes are live, but `vendor/` changes require stopping and restarting (`make playground-stop && make playground`)

## Architecture

| Path | Role |
|------|------|
| `airo-wp.php` | Plugin header, constants, autoload, container, `Plugin::instance()` |
| `includes/` | PSR-4 code — `GoDaddy\WordPress\Plugins\AiroWp\` |
| `includes/Packages.php` | Domain package registry |
| `includes/Container.php` | DI facade |
| `functions/` | Global helpers (`functions/index.php` → per-domain files) |
| `dependencies/` | Strauss-prefixed runtime vendors (gitignored; generated at build time) |
| `tests/` | PHPUnit unit tests |

**Autoloading:** Composer's `vendor/autoload.php` loads plugin code (`includes/`, `functions/`) and Strauss-prefixed runtime packages under `dependencies/`. Run `composer install` (or `composer install --no-dev` for releases) before activating the plugin.

## Project structure

```
airo-wp.php              # Plugin header + bootstrap (keep thin)
readme.txt               # WordPress.org-style readme (screens, changelog)
includes/                # All PHP classes (PSR-4)
  Container.php
  Packages.php
  Plugin.php
  Internal/              # Infrastructure (container, etc.)
  {YourDomain}/          # Your feature package
    Package.php
functions/
  index.php              # Composer files autoload entry
  container.php          # AiroWP()
  {your-domain}.php      # Optional global helpers
dependencies/            # Strauss-prefixed vendors (gitignored; generated by composer build)
tests/                   # PHPUnit + Brain Monkey
```

Namespace root: `GoDaddy\WordPress\Plugins\AiroWp\`

## Adding a feature (domain package)

1. **Create the package class**

   `includes/MyFeature/Package.php`:

   ```php
   <?php
   declare(strict_types=1);

   namespace GoDaddy\WordPress\Plugins\AiroWp\MyFeature;

   use GoDaddy\WordPress\Plugins\AiroWp\Container;
   use GoDaddy\WordPress\Plugins\AiroWp\PackageInterface;

   final class Package implements PackageInterface {
       public static function init( Container $container ): void {
           // Register hooks; resolve services from $container.
       }
   }
   ```

2. **Register it** in `includes/Packages.php`:

   ```php
   'my-feature' => MyFeature\Package::class,
   ```

3. **Prefer DI over `new`** — resolve services via `$container->get( SomeService::class )` with constructor or `init()` type hints.

4. **Optional helpers** — add `functions/my-feature.php` and `require_once` it from `functions/index.php`.

5. **Tests** — add `tests/Unit/MyFeature/` with Brain Monkey for WordPress APIs.

6. **Run** `composer test`, `composer lint`, and `composer build` if you changed Composer dependencies (`dependencies/` is regenerated automatically and should not be committed).

## Coding standards

- **PHPCS** with WordPress-Core, WordPress-Extra, WordPress-Docs, **WordPress-VIP-Go**, and PHPCompatibilityWP (`testVersion` 7.4–8.3).
- Run `make lint` (Docker, matches CI) before pushing; `composer lint` works too if Docker is unavailable. `composer format` (or `phpcbf`) fixes many issues automatically.
- Use `declare(strict_types=1);` in new PHP files.
- Add `defined( 'ABSPATH' ) || exit;` to every PHP file for direct file access protection. Place it **after** the `namespace` declaration in namespaced files, or after `declare(strict_types=1)` in non-namespaced files (PHP requires `namespace` to directly follow `declare`).
- Keep `airo-wp.php` free of business logic.
- Write PHP **7.4-compatible** code in `includes/` (no enums/readonly in shared code unless the team agrees to raise the minimum).
- **Brain Monkey** for unit tests; use `Proxies\LegacyProxy` only when a WordPress function is impractical to mock.

## Dependency and autoload rules

- **Runtime** packages go in `composer.json` `require` → Strauss prefixes them into `dependencies/`.
- **Dev-only** tools stay in `require-dev` → `vendor/` only, not shipped.
- Do not commit `vendor/`, `dependencies/`, or `bin/strauss.phar`.
- `dependencies/` is gitignored and generated automatically by `composer build` (or `make test` on first run).

## Pull requests

1. Branch from `main` (or your team's default branch).
2. Keep changes focused; one domain or one infrastructure concern per PR when possible.
3. Ensure CI passes (PHPUnit matrix 7.4–8.3, PHPCS on 8.3).
4. Describe **what** changed and **why** in the PR body.
5. No secrets, `.env` files, or local paths in commits.

## Local docs

Brainstorming specs and implementation plans may live under `docs/superpowers/` on your machine. That directory is **gitignored** and is not part of the plugin distribution.

## Questions

For team-specific process (code review owners, release cadence, Jira tickets), use your GoDaddy / Site Designer team channels. For plugin architecture questions, refer to `README.md` and the inline structure in `includes/Packages.php` and `includes/Container.php`.
