#!/usr/bin/env bash
#
# Syncs plugin-directory artwork (banner, icon, screenshots) to WordPress.org's
# SVN assets/ directory. Nothing else is touched.
#
# 10up/action-wordpress-plugin-asset-update is deliberately not used: it has no
# dry-run mode, and it always writes trunk. Even its most conservative setting
# (IGNORE_OTHER_FILES=true) copies readme.txt into trunk, which can move
# "Stable tag" and therefore what the auto-update channel serves; by default it
# rsyncs the whole working tree into trunk, which for this plugin would publish
# unbuilt source. The image mime-type handling below is borrowed from it.
#
# trunk is never fetched: the working copy is checked out at --depth empty and
# only assets/ is populated, so "trunk untouched" is structural rather than
# merely asserted. It is still fingerprinted before and after as a backstop.
#
# Usage: svn-sync-assets.sh <assets-dir> <svn-root> <slug> <dry-run>
#   dry-run: literal "true" or "false"
#
# Env: SVN_USERNAME, SVN_PASSWORD  (required only when dry-run is false)

set -euo pipefail

ASSETS_SRC="${1:?usage: svn-sync-assets.sh <assets-dir> <svn-root> <slug> <dry-run>}"
SVN_ROOT="${2:?missing svn-root}"
SLUG="${3:?missing slug}"
DRY_RUN="${4:?missing dry-run flag}"

PLUGIN_URL="${SVN_ROOT}/${SLUG}"
TRUNK_URL="${PLUGIN_URL}/trunk"

if [ ! -d "$ASSETS_SRC" ]; then
	echo "::error::Assets directory not found: ${ASSETS_SRC}" >&2
	exit 1
fi

asset_count=$(find "$ASSETS_SRC" -type f | wc -l | tr -d ' ')

if [ "$asset_count" -eq 0 ]; then
	echo "::error::Assets directory is empty: ${ASSETS_SRC}" >&2
	exit 1
fi

if ! command -v svn >/dev/null 2>&1; then
	echo "::error::svn is not installed. Subversion is not preinstalled on GitHub-hosted runners." >&2
	exit 1
fi

if command -v sha256sum >/dev/null 2>&1; then
	sha256() { sha256sum | awk '{print $1}'; }
elif command -v shasum >/dev/null 2>&1; then
	sha256() { shasum -a 256 | awk '{print $1}'; }
else
	echo "::error::Neither sha256sum nor shasum is available." >&2
	exit 1
fi

fingerprint_trunk() {
	if out=$(svn cat "${TRUNK_URL}/readme.txt" --non-interactive 2>/dev/null); then
		printf '%s' "$out" | sha256
	else
		printf 'absent'
	fi
}

WORK=$(mktemp -d)
trap 'rm -rf "$WORK"' EXIT

echo "Assets:  ${ASSETS_SRC} (${asset_count} files)"
echo "Target:  ${PLUGIN_URL}/assets"

before=$(fingerprint_trunk)
echo "Trunk fingerprint before: ${before}"

# --depth empty then populating only assets/ means trunk never enters the
# working copy, so it cannot be modified even by accident.
svn checkout --depth empty --non-interactive "$PLUGIN_URL" "$WORK/svn" >/dev/null
svn update --set-depth infinity --non-interactive "$WORK/svn/assets" >/dev/null

if [ -d "$WORK/svn/trunk" ]; then
	echo "::error::trunk was fetched into the working copy; refusing to continue." >&2
	exit 1
fi

rsync -rc --delete "${ASSETS_SRC}/" "$WORK/svn/assets/"

cd "$WORK/svn"

svn add "assets" --force --non-interactive > /dev/null
# Stage deletions for anything rsync removed.
svn status assets | awk '/^!/ {print $2}' | while read -r gone; do
	svn delete --force --non-interactive "$gone" > /dev/null
done

# Screenshots otherwise force-download instead of displaying in the browser.
for ext in png:image/png jpg:image/jpeg gif:image/gif svg:image/svg+xml; do
	glob="${ext%%:*}"
	mime="${ext##*:}"
	if find assets -maxdepth 1 -name "*.${glob}" -print -quit | grep -q .; then
		svn propset svn:mime-type "$mime" assets/*."${glob}" >/dev/null || true
	fi
done

echo "Pending changes:"
svn status assets | sed 's/^/  /'

if [ -z "$(svn status assets)" ]; then
	echo "No asset changes to publish."
	exit 0
fi

if [ "$DRY_RUN" != "false" ]; then
	echo "Dry run: nothing committed."
	exit 0
fi

: "${SVN_USERNAME:?SVN_USERNAME must be set for a real sync}"
: "${SVN_PASSWORD:?SVN_PASSWORD must be set for a real sync}"

svn commit assets \
	-m "Update plugin directory assets" \
	--no-auth-cache \
	--non-interactive \
	--username "$SVN_USERNAME" \
	--password "$SVN_PASSWORD"

after=$(fingerprint_trunk)
echo "Trunk fingerprint after:  ${after}"

if [ "$before" != "$after" ]; then
	echo "::error::trunk changed during an assets-only sync (${before} -> ${after})." >&2
	exit 1
fi

echo "Assets synced; trunk unchanged."
