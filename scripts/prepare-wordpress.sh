#!/bin/bash
# Trouver la racine du projet
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
while [ "$SCRIPT_DIR" != "/" ] && [ ! -d "$SCRIPT_DIR/.agent" ] && [ ! -f "$SCRIPT_DIR/go.mod" ]; do
    SCRIPT_DIR="$(dirname "$SCRIPT_DIR")"
done
cd "$SCRIPT_DIR"

IMPORT_JSON="$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json"
OUTPUT_JSON="$SCRIPT_DIR/.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/imports/WORDPRESS-N-N1.json"

echo "🎯 Préparation JSON WordPress (n + n-1)..."
echo ""

python3 - <<PYTHON
import json
import re

with open('$IMPORT_JSON', 'r') as f:
    data = json.load(f)

snippets = data['snippets']

# Grouper par nom de base
groups = {}
for idx, snippet in enumerate(snippets):
    name = snippet['name']

    # Extraire version
    match = re.search(r' - [vV]?(\d+)(?:\.php)?$', name)
    if match:
        version = int(match.group(1))
        base_name = re.sub(r' - [vV]?\d+\.php$', '', name)

        if base_name not in groups:
            groups[base_name] = []
        groups[base_name].append((version, idx, snippet))

kept = []
removed = []

for base_name, files in sorted(groups.items()):
    # Trier par version décroissante
    files.sort(key=lambda x: x[0], reverse=True)

    if len(files) >= 2:
        # Garder n (active) et n-1 (inactive)
        files[0][2]['active'] = True   # n
        files[1][2]['active'] = False  # n-1
        kept.append(files[0][2])
        kept.append(files[1][2])

        # Marquer les autres pour suppression
        for i in range(2, len(files)):
            removed.append(files[i][2]['name'])
    elif len(files) == 1:
        # Seulement une version, la garder active
        files[0][2]['active'] = True
        kept.append(files[0][2])

data['snippets'] = kept
data['generator'] = 'WordPress Import (n + n-1 strategy)'

with open('$OUTPUT_JSON', 'w') as f:
    json.dump(data, f, indent=2, ensure_ascii=False)

active_count = sum(1 for s in kept if s.get('active', False))

print(f"📊 Statistiques:")
print(f"   Snippets conservés: {len(kept)}")
print(f"   Snippets supprimés: {len(removed)}")

if removed:
    print()
    print(f"🗑️  Snippets supprimés:")
    for name in removed:
        print(f"   - {name}")

print()
print(f"✅ Snippets actifs: {active_count} / {len(kept)}")
print()
print(f"📄 JSON généré: WORDPRESS-N-N1.json")
PYTHON
