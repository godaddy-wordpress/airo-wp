# Shared native-PHP WordPress provisioning for both e2e suites. POSIX sh,
# meant to be SOURCED. Requires /opt/wp-core and /opt/sqlite-database-integration
# (from scripts/setup/e2e.sh). Contract: provision_wp() creates a fresh
# ephemeral install and sets WP_DIR. Admin credentials: admin/password —
# the @wordpress/e2e-test-utils-playwright RequestUtils defaults.

provision_wp() {
	WP_DIR="$(mktemp -d /tmp/airo-wp-e2e.XXXXXX)"
	cp -a /opt/wp-core/. "$WP_DIR"/

	# SQLite drop-in: plugin files first, then db.php from the plugin's db.copy
	# template (documented manual-install procedure). db.php must exist before
	# wp core install or it tries to reach MySQL.
	cp -a /opt/sqlite-database-integration "$WP_DIR/wp-content/plugins/"
	sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$WP_DIR/wp-content/plugins/sqlite-database-integration#" \
		-e "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#" \
		"$WP_DIR/wp-content/plugins/sqlite-database-integration/db.copy" \
		> "$WP_DIR/wp-content/db.php"

	wp config create --path="$WP_DIR" --dbname=wordpress --dbuser=wordpress \
		--dbpass=wordpress --skip-check --allow-root
	wp config set WP_DEBUG true --raw --path="$WP_DIR" --allow-root
	wp config set WP_DEBUG_DISPLAY true --raw --path="$WP_DIR" --allow-root

	wp core install --path="$WP_DIR" --url="${WP_SITE_URL:-http://localhost:8881}" \
		--title="Airo WP E2E" --admin_user=admin --admin_password=password \
		--admin_email=admin@example.com --skip-email --allow-root
}
