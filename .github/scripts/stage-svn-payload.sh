#!/usr/bin/env bash
#
# Stages the distributable plugin zip as a directory tree for SVN, and verifies
# the staged tree matches the archive exactly.
#
# The zip is the canonical artifact: it is built from package.json "files" by
# tests/e2e/setup/build-zip.mjs, which is the same artifact the pre-release
# workflow runs Plugin Check and the e2e matrix against. Staging by unzipping
# that archive — rather than assembling a second file list — is what makes the
# bytes published to WordPress.org identical to the bytes CI tested.
#
# Usage: stage-svn-payload.sh <zip> <staging-dir>
#
# Writes payload=<dir> to $GITHUB_OUTPUT when set. Always prints a summary.

set -euo pipefail

ZIP="${1:?usage: stage-svn-payload.sh <zip> <staging-dir>}"
STAGING="${2:?usage: stage-svn-payload.sh <zip> <staging-dir>}"

if [ ! -f "$ZIP" ]; then
	echo "::error::Archive not found: ${ZIP}" >&2
	exit 1
fi

# The archive wraps everything in a single directory named after the plugin.
# Derive it rather than assume, so a change in archive layout fails loudly here
# instead of publishing a wrongly-nested tree to WordPress.org.
ROOTS=$(unzip -Z1 "$ZIP" | awk -F/ 'NF > 1 { print $1 }' | sort -u)
ROOT_COUNT=$(printf '%s\n' "$ROOTS" | grep -c . || true)

if [ "$ROOT_COUNT" -ne 1 ]; then
	echo "::error::Expected exactly one top-level directory in ${ZIP}, found ${ROOT_COUNT}: $(printf '%s ' $ROOTS)" >&2
	exit 1
fi

ROOT="$ROOTS"

rm -rf "$STAGING"
mkdir -p "$STAGING"
unzip -q "$ZIP" -d "$STAGING"

PAYLOAD="${STAGING}/${ROOT}"

if [ ! -d "$PAYLOAD" ]; then
	echo "::error::Staging did not produce ${PAYLOAD}" >&2
	exit 1
fi

# Compare the archive's file list against what landed on disk. unzip guarantees
# per-file byte fidelity, so the thing worth asserting is that the staged tree
# has neither gained nor lost entries.
archive_manifest=$(unzip -Z1 "$ZIP" | grep -v '/$' | sort)
staged_manifest=$(cd "$STAGING" && find . -type f | sed 's|^\./||' | sort)

if [ "$archive_manifest" != "$staged_manifest" ]; then
	echo "::error::Staged tree does not match ${ZIP}" >&2
	diff <(printf '%s\n' "$archive_manifest") <(printf '%s\n' "$staged_manifest") >&2 || true
	exit 1
fi

file_count=$(printf '%s\n' "$staged_manifest" | wc -l | tr -d ' ')
byte_size=$(du -sk "$PAYLOAD" | awk '{print $1}')

echo "Payload staged from $(basename "$ZIP")"
echo "  root:  ${PAYLOAD}"
echo "  files: ${file_count}"
echo "  size:  ${byte_size} KiB"
echo "  top-level entries:"
( cd "$PAYLOAD" && ls -A1 | sed 's/^/    /' )

if [ -n "${GITHUB_OUTPUT:-}" ]; then
	echo "payload=${PAYLOAD}" >> "$GITHUB_OUTPUT"
fi
