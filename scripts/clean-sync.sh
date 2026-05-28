#!/bin/bash
# Trouver la racine du projet (remonte jusqu'à trouver .agent ou go.mod)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
while [ "$SCRIPT_DIR" != "/" ] && [ ! -d "$SCRIPT_DIR/.agent" ] && [ ! -f "$SCRIPT_DIR/go.mod" ]; do
    SCRIPT_DIR="$(dirname "$SCRIPT_DIR")"
done
cd "$SCRIPT_DIR"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   Clean & Sync - Version Management"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Étape 1 : Archivage local
echo "📦 Étape 1/3 : Archivage des anciennes versions"
bash "$SCRIPT_DIR/scripts/archive-old-versions.sh"
echo ""

# Étape 2 : Build JSON standard
echo "🔨 Étape 2/3 : Génération JSON standard"
php "$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/scripts/build_code_snippets_import.php" \
    --snippets-dir=snippets/canonical \
    --out="$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json"
echo ""

# Étape 3 : Préparer JSON WordPress (n + n-1)
echo "🎯 Étape 3/3 : Préparation WordPress (n + n-1)"
bash "$SCRIPT_DIR/scripts/prepare-wordpress.sh"
echo ""

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "   ✅ Préparation terminée"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

canonical_count=$(find "$SCRIPT_DIR/snippets/canonical" -name "*.php" 2>/dev/null | wc -l | tr -d ' ')
archive_count=$(find "$SCRIPT_DIR/snippets/archive" -name "*.php" 2>/dev/null | wc -l | tr -d ' ')

echo "📁 État des dossiers :"
echo "   canonical/ : $canonical_count snippets (versions actives)"
echo "   archive/   : $archive_count snippets (historique complet)"
echo ""

echo "📋 JSON prêt pour WordPress :"
echo "   WORDPRESS-N-N1.json"
echo ""

# Vérifier si les credentials existent
if [ -f "$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/secrets/wp-sync.env" ]; then
    echo "💡 Pour synchroniser :"
    echo "   cd scripts && ./sync-wordpress.sh"
    echo "   Ou : ./sync-wp"
else
    echo "⚠️  Credentials WordPress non configurés :"
    echo "   .agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/secrets/wp-sync.env"
fi
echo ""
