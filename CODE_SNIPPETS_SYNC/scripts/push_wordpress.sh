#!/usr/bin/env bash
set -euo pipefail

# One-command push:
# 1) build JSON from canonical snippets
# 2) push to Code Snippets REST API on WordPress
#
# Required env vars:
#   WP_SITE_URL
#   WP_SYNC_USER
#   WP_APP_PASSWORD
#
# Optional:
#   DRY_RUN=1
# Args:
#   --dry-run
#   --verbose

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
IMPORT_JSON="CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json"

cd "$ROOT_DIR"

DRY_RUN_FLAG=0
VERBOSE_FLAG=0
for arg in "$@"; do
  case "$arg" in
    --dry-run) DRY_RUN_FLAG=1 ;;
    --verbose) VERBOSE_FLAG=1 ;;
  esac
done

if [[ -z "${WP_SITE_URL:-}" || -z "${WP_SYNC_USER:-}" || -z "${WP_APP_PASSWORD:-}" ]]; then
  echo "Variables requises manquantes: WP_SITE_URL, WP_SYNC_USER, WP_APP_PASSWORD" >&2
  exit 1
fi

php CODE_SNIPPETS_SYNC/scripts/build_code_snippets_import.php \
  --snippets-dir=WP_Snippets_FINAL_CLEAN/canonical \
  --out="$IMPORT_JSON"

ARGS=(
  "--site=${WP_SITE_URL}"
  "--user=${WP_SYNC_USER}"
  "--app-password=${WP_APP_PASSWORD}"
  "--import-json=${IMPORT_JSON}"
)

if [[ "${DRY_RUN:-0}" == "1" || "$DRY_RUN_FLAG" == "1" ]]; then
  ARGS+=("--dry-run")
fi

if [[ "$VERBOSE_FLAG" == "1" ]]; then
  ARGS+=("--verbose")
fi

php CODE_SNIPPETS_SYNC/scripts/push_code_snippets_rest.php "${ARGS[@]}"
