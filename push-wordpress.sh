#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SECRETS_ENV="${ROOT_DIR}/CODE_SNIPPETS_SYNC/secrets/wp-sync.env"

if [[ ! -f "$SECRETS_ENV" ]]; then
  echo "Config manquante: CODE_SNIPPETS_SYNC/secrets/wp-sync.env" >&2
  echo "Copie d'abord CODE_SNIPPETS_SYNC/secrets/wp-sync.env.example -> wp-sync.env" >&2
  exit 1
fi

# shellcheck source=/dev/null
set -a
source "$SECRETS_ENV"
set +a

exec "${ROOT_DIR}/CODE_SNIPPETS_SYNC/scripts/push_wordpress.sh" "$@"

