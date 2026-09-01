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
npm run test:unit:php
npm run lint
```

Both must pass before you open a pull request. CI runs the same commands automatically.

### 5. Activate in WordPress

The environment boots at `http://localhost:9173` with the plugin active. Admin at `http://localhost:9173/wp-admin/` (user: `admin`, password: `password`).

### 6. Stop the environment when done

```bash
npm run wp-env:stop
```

## Development commands

| Command | Description |
|---------|-------------|
| `npm run test:unit:php` | PHPUnit — PHP unit tests |
| `npm run lint` | PHPCS + ESLint + Stylelint (`lint:php`, `lint:js`, `lint:style`) |
| `npm run format` | Auto-fix PHP, JS and SCSS (`format:php`, `format:js`, `format:css`) |
| `npm run build` | Build JS/CSS assets (wp-scripts) |
| `npm run build:zip` | Build distributable plugin zip (`builds/airo-wp.zip`) |
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

All specs live under `tests/e2e/functional/specs/`.

| Spec | What it verifies |
|------|-----------------|
| `plugin-activation.spec.ts` | Plugin is present and `active` via `GET /wp/v2/plugins` |
| `mcp-server.spec.ts` | MCP endpoint handshake and `tools/list` |
| `blocks-and-patterns.spec.ts` | Registered blocks and patterns are available to the editor |
| `blocks-browser.spec.ts` | Blocks render in the editor without console errors |
| `patterns-browser.spec.ts` | Patterns insert and render correctly |
| `rest/` | Plugin REST routes |
| `tools/` | One spec per MCP tool — `tools/call` request and response shape |

`tools/` is the bulk of the suite and grows with every new MCP tool, so it is listed
by directory rather than enumerated.

## CI

`ci.yml` runs automatically on pull requests and pushes to `main`:

- **PHPCS** — WordPress coding standards
- **PHPUnit** — PHP unit tests
- **Plugin Check (PCP)** — WordPress.org plugin requirements check

All three must pass before merge.

`pre-release.yml` additionally runs on `release/*` pull requests and adds JS/SCSS
lint, the distributable zip build, and the full Playwright E2E matrix across the
supported PHP and WordPress versions. Those run before a release, not on every PR.

## Releasing

Releasing has two distinct stages, and only the first is automatic.

**1. Merge to `main` produces the installable artifact.** The **Release Artifact**
workflow reads the version from `package.json`, builds the distributable zip, checks
it actually contains `vendor/autoload.php` and `dist/blocks/*/block.json`, then
creates tag `v<version>` and a GitHub Release with the zip attached. A version whose
tag already exists is a no-op, not a failure, so an unrelated push to `main` does not
try to re-release.

That artifact matters because `vendor/` and `dist/` are generated at build time and
never committed. GitHub's source zipball is therefore not an installable plugin, and
the attached build is what both manual installs and
[Git Updater](https://git-updater.com/) consume — see the `Release Asset` header in
`airo-wp.php`.

**2. Publishing to WordPress.org is manual.** The **Publish Plugin** workflow is
dispatched by hand against a `v<version>` tag and refuses any other ref. Tags, not
branches: a branch can move after CI went green, so dispatching from one could
publish commits nobody tested. A tag cannot, which also means re-running a publish
republishes byte-identical content.

### Version numbers

| Kind | Form | Example |
|------|------|---------|
| Stable | `x.y.z` | `0.3.0` |
| Beta | `x.y.z-betaN` | `0.3.0-beta1` |
| Release candidate | `x.y.z-rcN` | `0.3.0-rc1` |

The `-betaN` / `-rcN` spelling is WordPress's own convention, and the counter
starts at 1. `version_compare()` ranks them below the release they precede —
`0.3.0-beta1 < 0.3.0-rc1 < 0.3.0` — and the counter compares numerically, so
`beta9 < beta10`. Note that ordering is not what keeps a beta away from existing
installs; `Stable tag` is, as described below.

### Publishing

Dry run first — `dry_run` defaults to `true`, and a dry run reports the exact
Subversion diff without committing anything:

```bash
gh workflow run publish-plugin.yml \
  --repo godaddy-wordpress/airo-wp \
  --ref v0.3.4 \
  -f dry_run=true
```

The tag is the one created by the Release Artifact workflow when the release merged;
`gh release list` shows what is available. Dispatching a ref that is not a `v*` tag
fails immediately, before anything is built.

Read the job summary: it lists the target Subversion tag, the full payload
manifest, and the current `Stable tag` in `trunk`. When it looks right, re-dispatch
with `-f dry_run=false`.

What the workflow does depends on the version:

| Version | `trunk` | Tag | Plugin-directory assets |
|---------|---------|-----|------------------------|
| Stable | updated | `tags/x.y.z` | synced after a successful deploy |
| Pre-release | **untouched** | `tags/x.y.z-betaN` | never |

A pre-release is published as a tag only. `trunk/readme.txt`'s `Stable tag` is what
WordPress.org feeds to the auto-update channel, so leaving `trunk` alone is what
keeps a beta off every existing install. Testers install it from the plugin page's
advanced view.

For a stable release, the payload's `readme.txt` must declare `Stable tag` equal to
the version being published. The workflow refuses to publish otherwise, because
WordPress.org would then serve a different version from the one just released.

### Artwork only

Banner, icon and screenshots live in `.wordpress-org/` and are published to
Subversion's `assets/` directory. They are version-independent and go live the
moment they are committed, so they can be updated without releasing any code:

```bash
gh workflow run publish-plugin.yml \
  --repo godaddy-wordpress/airo-wp \
  --ref v0.3.4 \
  -f sync_assets=true -f dry_run=false
```

`sync_assets` means *artwork only* — no code is built, no tag is created, and
`trunk` is not touched.

If `.github/ASSETS_ARE_PLACEHOLDERS` is present, a real artwork sync is refused and
only dry runs are allowed. It exists to stop unfinished artwork reaching the live
plugin page; remove it once the artwork is real.

### Rollback: read this before publishing

**WordPress.org tags are effectively permanent.** No workflow path unpublishes a
version. Removing a tag requires manual Subversion work, and by then the release
may already have been downloaded and installed.

What you can do:

- **Stable release is bad** — commit `trunk/readme.txt` with `Stable tag` pointing
  back at the previous good version. WordPress.org serves whatever `Stable tag`
  names, so this is the fastest way to stop distributing a bad release. The bad tag
  itself stays published.
- **Pre-release is bad** — much lower stakes: `trunk` was never touched and
  `Stable tag` never pointed at it, so no existing install was ever offered it.
  Publish a higher pre-release (`-beta2`) and move on.

In both cases the real remedy is releasing forward, not retracting. Treat the dry
run as the last point at which a mistake is cheap.

## Architecture

| Path | Role |
|------|------|
| `airo-wp.php` | Plugin header, constants, autoload, container, `Plugin::instance()` |
| `includes/` | PSR-4 code — `GoDaddy\WordPress\Plugins\AiroWp\` |
| `includes/Plugin.php` | Static orchestrator — creates the container, boots domain packages (`Plugin::packages()`) |
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

2. **Register it** in `Plugin::packages()` (`includes/Plugin.php`) — import the class,
   then add it to the returned list:

   ```php
   use GoDaddy\WordPress\Plugins\AiroWp\MyFeature\Package as MyFeaturePackage;

   private static function packages(): array {
       return array(
           RestPackage::class,
           McpPackage::class,
           BlocksPackage::class,
           MyFeaturePackage::class,
       );
   }
   ```

3. **Tests** — add `tests/Unit/MyFeature/` with Brain Monkey for WordPress APIs.

4. **Run** `npm run test:unit:php` and `npm run lint` before opening a PR.

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

## Questions

For team-specific process, use your team's standard channels. For plugin architecture questions, refer to `README.md` and the inline structure in `includes/Plugin.php` and `includes/Container.php`.
