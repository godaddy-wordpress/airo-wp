#!/bin/sh
# Provision ephemeral WordPress for the Plugin Check suite. Sources the shared
# provision-wp.sh, then installs Plugin Check BEFORE our zip (reversed order
# caused persistent "database tables unavailable" errors during PCP activation).
# Prints WP_DIR=<path> as the last line; run-plugin-check.mjs parses it.
set -e

REPO_ROOT="$(cd "$(dirname "$0")/../../.." && pwd)"

. "$REPO_ROOT/tests/e2e/lib/provision-wp.sh"
provision_wp

wp plugin install /opt/plugin-check.zip --activate --path="$WP_DIR" --allow-root
wp plugin install "$REPO_ROOT/builds/airo-wp-test.zip" --activate --path="$WP_DIR" --allow-root

echo "WP_DIR=$WP_DIR"
