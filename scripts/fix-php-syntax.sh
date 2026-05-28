#!/bin/bash
# Script de correction automatique de la syntaxe PHP
# Ajoute <?php aux fichiers qui en manquent

echo "🔧 Correction syntaxe PHP..."
echo ""

FIXED=0
CHECKED=0

for file in snippets/canonical/*.php; do
    ((CHECKED++))
    if ! head -1 "$file" | grep -q "<?php"; then
        echo "Fixing: $(basename "$file")"
        # Créer un fichier temporaire avec <?php ajouté
        { echo "<?php"; cat "$file"; } > "$file.tmp"
        mv "$file.tmp" "$file"
        ((FIXED++))
    fi
done

echo ""
echo "📊 Résultat:"
echo "   Fichiers vérifiés: $CHECKED"
echo "   Fichiers corrigés: $FIXED"
echo ""

# Vérifier la syntaxe
echo "🔍 Vérification syntaxe..."
ERRORS=0
for file in snippets/canonical/*.php; do
    if ! php -l "$file" > /dev/null 2>&1; then
        echo "❌ $(basename "$file")"
        php -l "$file"
        ((ERRORS++))
    fi
done

if [ $ERRORS -eq 0 ]; then
    echo "✅ Tous les fichiers sont corrects !"
else
    echo "❌ $ERRORS fichiers ont encore des erreurs"
fi
