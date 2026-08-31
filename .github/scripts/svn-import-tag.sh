#!/usr/bin/env bash
#
# Publishes a pre-release to WordPress.org as a tag only, leaving trunk alone.
#
# trunk/readme.txt's "Stable tag" is what WordPress.org feeds to the auto-update
# channel. A pre-release must not move it, so this never checks out or writes
# trunk: it imports the payload straight into tags/<version> in a single commit.
# That is why the stable lane's deploy action cannot be reused here — it always
# rsyncs into trunk first.
#
# Usage: svn-import-tag.sh <payload-dir> <svn-root> <slug> <version> <dry-run>
#   dry-run: literal "true" or "false"
#
# Env: SVN_USERNAME, SVN_PASSWORD  (required only when dry-run is false)

set -euo pipefail

PAYLOAD="${1:?usage: svn-import-tag.sh <payload-dir> <svn-root> <slug> <version> <dry-run>}"
SVN_ROOT="${2:?missing svn-root}"
SLUG="${3:?missing slug}"
VERSION="${4:?missing version}"
DRY_RUN="${5:?missing dry-run flag}"

TAG_URL="${SVN_ROOT}/${SLUG}/tags/${VERSION}"
TRUNK_URL="${SVN_ROOT}/${SLUG}/trunk"

if [ ! -d "$PAYLOAD" ]; then
	echo "::error::Payload directory not found: ${PAYLOAD}" >&2
	exit 1
fi

file_count=$(find "$PAYLOAD" -type f | wc -l | tr -d ' ')

if [ "$file_count" -eq 0 ]; then
	echo "::error::Payload directory is empty: ${PAYLOAD}" >&2
	exit 1
fi

if ! command -v svn >/dev/null 2>&1; then
	echo "::error::svn is not installed. Subversion is not preinstalled on GitHub-hosted runners; install it before calling this script." >&2
	exit 1
fi

# Fingerprint trunk so we can prove afterwards that it was untouched. Uses
# `svn cat` rather than an HTTP fetch so the same code path works against a
# file:// repository in tests. An absent readme is a legitimate state before the
# first stable release, and must fingerprint stably rather than error.
# sha256sum is coreutils (Linux runners); shasum is Perl (macOS). Pick whichever
# exists so this is runnable both on the runner and locally.
if command -v sha256sum >/dev/null 2>&1; then
	sha256() { sha256sum | awk '{print $1}'; }
elif command -v shasum >/dev/null 2>&1; then
	sha256() { shasum -a 256 | awk '{print $1}'; }
else
	echo "::error::Neither sha256sum nor shasum is available; cannot fingerprint trunk." >&2
	exit 1
fi

fingerprint_trunk() {
	if out=$(svn cat "${TRUNK_URL}/readme.txt" --non-interactive 2>/dev/null); then
		printf '%s' "$out" | sha256
	else
		printf 'absent'
	fi
}

echo "Payload:  ${PAYLOAD} (${file_count} files)"
echo "Tag URL:  ${TAG_URL}"
echo "Trunk:    ${TRUNK_URL} (never written by this lane)"

before=$(fingerprint_trunk)
echo "Trunk fingerprint before: ${before}"

if [ "$DRY_RUN" != "false" ]; then
	echo "Dry run: no import performed. Would run:"
	echo "  svn import '${PAYLOAD}' '${TAG_URL}' -m 'Release ${VERSION} (pre-release)'"
	exit 0
fi

: "${SVN_USERNAME:?SVN_USERNAME must be set for a real import}"
: "${SVN_PASSWORD:?SVN_PASSWORD must be set for a real import}"

echo "Importing ${file_count} files into ${TAG_URL}"

svn import "$PAYLOAD" "$TAG_URL" \
	-m "Release ${VERSION} (pre-release)" \
	--no-auth-cache \
	--non-interactive \
	--username "$SVN_USERNAME" \
	--password "$SVN_PASSWORD"

# Post-conditions: the tag exists, and trunk is byte-identical to before.
if ! svn ls "$TAG_URL" --non-interactive >/dev/null 2>&1; then
	echo "::error::Import reported success but ${TAG_URL} is not readable." >&2
	exit 1
fi

after=$(fingerprint_trunk)
echo "Trunk fingerprint after:  ${after}"

if [ "$before" != "$after" ]; then
	echo "::error::trunk changed during a pre-release publish (${before} -> ${after}). A pre-release must never modify trunk, because trunk's Stable tag drives the auto-update channel." >&2
	exit 1
fi

echo "Imported ${VERSION}; trunk unchanged."
