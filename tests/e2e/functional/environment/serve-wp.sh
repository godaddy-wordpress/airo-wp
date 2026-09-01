#!/bin/sh
# Boot ephemeral WordPress for the functional e2e suite. Invoked by
# playwright.config.ts's webServer.command. Provision (via provision-wp.sh),
# install the packaged zip, set pretty permalinks, exec wp server.
# Installation completes BEFORE the server binds the port — the plain URL
# readiness check in playwright.config.ts is truthful.
set -e

PORT="${WP_E2E_PORT:-8881}"
REPO_ROOT="$(cd "$(dirname "$0")/../../../.." && pwd)"

ZIP="$REPO_ROOT/builds/airo-wp-test.zip"
if [ ! -f "$ZIP" ]; then
	echo "$ZIP missing — build the plugin zip before running the e2e suite" >&2
	exit 1
fi

WP_SITE_URL="http://localhost:$PORT"
. "$REPO_ROOT/tests/e2e/lib/provision-wp.sh"
provision_wp

# wp-cli's server-command router rewrites home/siteurl to the incoming Host
# header on every request, defeating host-dependent tests. Pin both options at
# max priority so the canonical URL is always used regardless of the request host.
mkdir -p "$WP_DIR/wp-content/mu-plugins"
cat > "$WP_DIR/wp-content/mu-plugins/airo-wp-e2e-pin-canonical.php" <<PHP
<?php
// Pin canonical URLs so wp server's host-rewriting doesn't break host-dependent tests.
add_filter( 'pre_option_home', static fn() => '$WP_SITE_URL', PHP_INT_MAX );
add_filter( 'pre_option_siteurl', static fn() => '$WP_SITE_URL', PHP_INT_MAX );
add_filter( 'option_home', static fn() => '$WP_SITE_URL', PHP_INT_MAX );
add_filter( 'option_siteurl', static fn() => '$WP_SITE_URL', PHP_INT_MAX );
PHP

# Disable WordPress auto-updates and pseudo-cron so Plugin_Upgrader never creates
# .maintenance mid-run (which causes 503 responses in concurrent PHP workers).
cat > "$WP_DIR/wp-content/mu-plugins/airo-wp-e2e-disable-updates.php" <<'PHP'
<?php
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'DISABLE_WP_CRON', true );
PHP

wp plugin install "$ZIP" --activate --path="$WP_DIR" --allow-root
# hello-dolly is used by mcp-update-plugin e2e tests; install it explicitly
# because the e2e WordPress image does not bundle it by default.
wp plugin install hello-dolly --path="$WP_DIR" --allow-root
# Plugin_Upgrader creates .maintenance during installs; remove it before
# the server starts so the first test requests don't see a 503.
rm -f "$WP_DIR/.maintenance"
wp rewrite structure '/%postname%/' --path="$WP_DIR" --allow-root
wp rewrite flush --path="$WP_DIR" --allow-root

echo "Airo WP e2e ready: $WP_DIR on port $PORT (log: $WP_DIR/php-server.log)"
PHP_CLI_SERVER_WORKERS=6 exec wp server --host=0.0.0.0 --port="$PORT" \
	--path="$WP_DIR" --allow-root >>"$WP_DIR/php-server.log" 2>&1
