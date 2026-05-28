#!/bin/bash
# Script de synchronisation WordPress sans secrets hardcoded
# Les credentials sont chargés depuis le fichier sécurisé

# Trouver la racine du projet
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
while [ "$SCRIPT_DIR" != "/" ] && [ ! -d "$SCRIPT_DIR/.agent" ] && [ ! -f "$SCRIPT_DIR/go.mod" ]; do
    SCRIPT_DIR="$(dirname "$SCRIPT_DIR")"
done
cd "$SCRIPT_DIR"

# Vérifier que le JSON existe
JSON_FILE="$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/imports/WORDPRESS-N-N1.json"
if [ ! -f "$JSON_FILE" ]; then
    echo "❌ JSON introuvable: $JSON_FILE"
    echo "Exécutez d'abord: ./clean-sync"
    exit 1
fi

# Charger les credentials depuis le fichier sécurisé
SECRETS_FILE="$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/secrets/wp-sync.env"
if [ ! -f "$SECRETS_FILE" ]; then
    echo "❌ Fichier de credentials introuvable: $SECRETS_FILE"
    echo "Créez-le à partir de l'exemple."
    exit 1
fi

# Charger les variables d'environnement
set -a
source "$SECRETS_FILE"
set +a

# Vérifier les variables requises
if [ -z "${WP_SITE_URL:-}" ] || [ -z "${WP_SYNC_USER:-}" ] || [ -z "${WP_APP_PASSWORD:-}" ]; then
    echo "❌ Variables manquantes dans wp-sync.env"
    exit 1
fi

echo "🚀 Synchronisation vers WordPress..."
echo "   Site: ${WP_SITE_URL}"
echo ""

# Exécuter le sync
php "$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/scripts/push_code_snippets_rest.php" \
    --site="${WP_SITE_URL}" \
    --user="${WP_SYNC_USER}" \
    --app-password="${WP_APP_PASSWORD}" \
    --import-json="${JSON_FILE}"

echo ""
echo "✅ Synchronisation terminée !"
