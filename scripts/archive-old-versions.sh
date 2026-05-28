#!/bin/bash
cd "$(dirname "$0")"

CANONICAL="snippets/canonical"
ARCHIVES="snippets/archive"

echo "🔄 Archivage des anciennes versions..."
echo ""

# Créer le dossier archives si nécessaire
mkdir -p "$ARCHIVES"

# Trouver toutes les familles de snippets et archiver les anciennes versions
python3 - <<'PYTHON'
import os
import re
from pathlib import Path

canonical = Path("snippets/canonical")
archives = Path("snippets/archive")

# Créer le dossier archives
archives.mkdir(parents=True, exist_ok=True)

# Grouper les snippets par nom de base
groups = {}
for file in canonical.glob("*.php"):
    filename = file.name

    # Extraire version si présente
    match = re.search(r' - [vV]?(\d+)\.php$', filename)
    if match:
        version = int(match.group(1))
        base_name = re.sub(r' - [vV]?\d+\.php$', '', filename)

        if base_name not in groups:
            groups[base_name] = []
        groups[base_name].append((version, file))

# Traiter chaque groupe
archived_count = 0
kept_count = 0

for base_name, files in sorted(groups.items()):
    if len(files) <= 1:
        kept_count += 1
        continue

    # Trier par version décroissante
    files.sort(key=lambda x: x[0], reverse=True)

    # Garder seulement la plus récente dans canonical/
    for i, (version, file) in enumerate(files):
        if i == 0:
            kept_count += 1
            print(f"✅ Garde: {file.name}")
        else:
            # Archiver
            dest = archives / file.name
            file.rename(dest)
            print(f"📦 Archive: {file.name}")
            archived_count += 1

print()
print(f"📊 Résultat:")
print(f"   Conservés (canonical/) : {kept_count}")
print(f"   Archivés (archive/)   : {archived_count}")
PYTHON

echo ""
echo "✅ Archivage terminé !"
