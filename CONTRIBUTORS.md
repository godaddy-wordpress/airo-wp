# Contributing to Airo WP

Thank you for contributing to the Airo WordPress plugin. This document explains how to get started, how the project is organized, and what we expect in pull requests.

## What this repository is

Airo WP is a **plugin scaffold**: bootstrap, DI container, package loader, Strauss vendor isolation, tests, and CI. Feature work happens in **domain packages** under `includes/{Domain}/`, not in the root bootstrap file.

## Prerequisites

| Tool | Version | Notes |
|------|---------|-------|
| Node.js | 20+ | Required |
| npm | 10+ | Required |
| Docker | 24+ | Required by the WordPress test environment |
| PHP | 7.4+ | Optional — only needed if running Composer on the host |
| Composer | 2.x | Optional — runs inside the WordPress environment |

## Getting started

### 1. Clone and install Node dependencies

```bash
git clone git@github.com:godaddy-wordpress/airo-wp.git
cd airo-wp
npm ci
```

### 2. Start the WordPress environment

```bash
npm run wp-env:start
```

This starts a Docker-based WordPress instance with the plugin loaded. On first run it also installs Composer inside the environment (takes ~2–3 minutes while images are pulled).

### 3. Install PHP dependencies

```bash
npx wp-env run tests-cli --env-cwd=wp-content/plugins/airo-wp -- composer install --no-interaction --no-progress
```

### 4. Verify everything passes

```bash
npm run test:unit
npm run lint
```

Both must pass before you open a pull request. CI runs the same commands automatically.

### 5. Activate in WordPress

The environment boots at `http://localhost:8881` with the plugin active. Admin at `http://localhost:8881/wp-admin/` (user: `admin`, password: `password`).

### 6. Stop the environment when done

```bash
npm run wp-env:stop
```

## Development commands

| Command | Description |
|---------|-------------|
| `npm run test:unit` | PHPUnit — PHP unit tests |
| `npm run lint` | PHPCS — WordPress coding standards |
| `npm run format` | PHPCBF — auto-fix coding standard issues |
| `npm run build` | Build JS/CSS assets (wp-scripts) |
| `npm run build:zip` | Build distributable plugin zip (`builds/airo-wp-test.zip`) |
| `npm run test:e2e` | Playwright functional end-to-end tests |
| `npm run test:e2e:debug` | Playwright with step debugger |
| `npm run plugin-check` | WordPress Plugin Check (PCP) |
| `npm run wp-env:start` | Start the WordPress environment |
| `npm run wp-env:stop` | Stop the WordPress environment |
| `npm run start` | JS/CSS file watcher |

## Plugin Check

The [WordPress Plugin Check](https://wordpress.org/plugins/plugin-check/) (PCP) verifies the plugin satisfies WordPress.org requirements.

### Running locally

```bash
# Build the distributable zip first
npx wp-env run tests-cli --env-cwd=wp-content/plugins/airo-wp -- composer install --no-dev --no-interaction --no-progress
npm run build:zip

# Run Plugin Check
npm run plugin-check
```

Full results are written to `builds/plugin-check-results.txt`. CI posts them as a sticky PR comment.

After running plugin-check locally, restore dev dependencies:

```bash
npx wp-env run tests-cli --env-cwd=wp-content/plugins/airo-wp -- composer install --no-interaction --no-progress
```

## End-to-end tests

E2e tests run WordPress via the wp-env environment and drive the REST API and browser with [Playwright](https://playwright.dev).

### Running locally

```bash
# Build JS/CSS assets (required — tests interact with compiled blocks)
npm run build

# Install Playwright browser (once)
npx playwright install --with-deps chromium

# Start environment and run tests
npm run wp-env:start
npm run test:e2e
```

Playwright's HTML report is written to `playwright-report/` on failure.

### What the tests cover

| Spec | What it verifies |
|------|-----------------|
| `plugin-activation.spec.ts` | Plugin is present and `active` via `GET /wp/v2/plugins` |
| `mcp-tools.spec.ts` | MCP `tools/list` returns `gd-mcp-get-site-info`; `tools/call` returns site data |

## CI

CI runs automatically on pull requests and pushes to `main`:

- **PHPUnit** — PHP unit tests
- **PHPCS** — WordPress coding standards
- **Functional E2E** — Playwright test suite
- **Plugin Check (PCP)** — WordPress.org plugin requirements check

All four must pass before merge.

## Architecture

| Path | Role |
|------|------|
| `airo-wp.php` | Plugin header, constants, autoload, container, `Plugin::instance()` |
| `includes/` | PSR-4 code — `GoDaddy\WordPress\Plugins\AiroWp\` |
| `includes/Packages.php` | Domain package registry |
| `includes/Container.php` | DI facade |
| `functions/` | Global helpers (`functions/index.php` → per-domain files) |
| `tests/` | PHPUnit unit tests + Playwright e2e tests |

## Adding a feature (domain package)

1. **Create the package class** — `includes/MyFeature/Package.php`:

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

3. **Tests** — add `tests/Unit/MyFeature/` with Brain Monkey for WordPress APIs.

4. **Run** `npm run test:unit` and `npm run lint` before opening a PR.

## Coding standards

- **PHPCS** with WordPress-Core, WordPress-Extra, WordPress-Docs, **WordPress-VIP-Go**, and PHPCompatibilityWP (`testVersion` 7.4–8.3).
- Use `declare(strict_types=1);` in new PHP files.
- Add `defined( 'ABSPATH' ) || exit;` to every PHP file for direct file access protection. Place it **after** the `namespace` declaration in namespaced files, or after `declare(strict_types=1)` in non-namespaced files.
- Write PHP **7.4-compatible** code in `includes/`.

## Pull requests

1. Branch from `main`.
2. Keep changes focused — one domain or one infrastructure concern per PR.
3. Ensure CI passes (all four jobs).
4. Describe **what** changed and **why** in the PR body.
5. No secrets, `.env` files, or local paths in commits.

## Local docs

Brainstorming specs and implementation plans may live under `docs/superpowers/` on your machine. That directory is not part of the plugin distribution.

## Questions

For team-specific process, use your team's standard channels. For plugin architecture questions, refer to `README.md` and the inline structure in `includes/Packages.php` and `includes/Container.php`.
