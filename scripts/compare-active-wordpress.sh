#!/usr/bin/env python3
import os
import re

# Liste des 18 snippets actifs sur WordPress (fournie par l'utilisateur)
wp_active = [
    "DIAG snippets !!!!!",
    "ADMIN SETTINGS - Fusion Outils+Reglages",
    "ADMIN SETTINGS - Menu Order Alpha Settings - v2",
    "ADMIN SETTINGS - Show Active Plugins First - v2",
    "TAGS - Post Already Existing Tags - v3",
    "SCHEDULER - Admin Download Images - v1",
    "SUPER TRACKER OPTIMISÉ",
    "ADMIN COLUMNS - List Add Featured Images - v3",
    "SEARCH - Post Search Auto - v4 (big opti ?)",
    "ADMIN MENUBAR - Schedule Calendar Drag 14h - v10",
    "SCHEDULER - Auto Draft To Publish - v2",
    "POST - Disable Auto-Save - v1",
    "ADMIN - Disable Attachment Pages - v1",
    "ADMIN COLUMNS - Add ID to WordPress Lists - v1",
    "SCHEDULER - Media Library Usage Audit - v8",
    "SCHEDULER - RAG Markdown Export - v1",
    "SCHEDULER - Theme Version Scanner - v1",
    "ADMIN - Remove Menu Links - v1"
]

# Normaliser les noms WordPress pour comparaison
wp_active_normalized = {}
for name in wp_active:
    # Version normalisée: lowercase, sans espaces, tirets, plus, emojis, accents
    norm = name.lower()
    norm = re.sub(r'[\s\-+–—]', '', norm)  # espaces, tirets, plus
    norm = re.sub(r'[^\w\?!]', '', norm)   # garder lettres, chiffres, ?, !
    # Remplacer les accents
    norm = norm.replace('é', 'e').replace('è', 'e').replace('ê', 'e')
    norm = norm.replace('à', 'a').replace('â', 'a')
    norm = norm.replace('ô', 'o').replace('î', 'i').replace('ï', 'i')
    wp_active_normalized[norm] = name

print("=" * 70)
print("COMPARAISON SNIPPETS WORDPRESS ACTIFS vs LOCAL")
print("=" * 70)
print()

# Snippets locaux
local_snippets = {}
for file in sorted(os.listdir('snippets/canonical')):
    if file.endswith('.php'):
        try:
            with open(f"snippets/canonical/{file}", 'r') as f:
                content = f.read()
                name = None
                for line in content.split('\n')[:30]:
                    if 'Display name:' in line:
                        name = line.split(':', 1)[1].strip()
                        break
                if name:
                    local_snippets[name] = file
        except:
            pass

print(f"📊 Local: {len(local_snippets)} snippets")
print(f"🌐 WordPress actifs: {len(wp_active)} snippets")
print()

# Comparer
to_keep = []
to_archive = []
unmatched_wp = []

for local_name, file in local_snippets.items():
    # Normaliser le nom local
    norm_local = local_name.lower()
    norm_local = re.sub(r'[\s\-+–—]', '', norm_local)
    norm_local = re.sub(r'[^\w\?!]', '', norm_local)
    norm_local = norm_local.replace('é', 'e').replace('è', 'e').replace('ê', 'e')
    norm_local = norm_local.replace('à', 'a').replace('â', 'a')
    norm_local = norm_local.replace('ô', 'o').replace('î', 'i').replace('ï', 'i')

    # Chercher correspondance WordPress
    found = None
    for wp_norm, wp_orig in wp_active_normalized.items():
        if norm_local == wp_norm or norm_local in wp_norm or wp_norm in norm_local:
            found = wp_orig
            break

    if found:
        to_keep.append((local_name, file, found))
    else:
        to_archive.append((local_name, file))

print("=" * 70)
print("✅ SNIPPETS À CONSERVER (actifs sur WordPress)")
print("=" * 70)
for local, file, wp in sorted(to_keep, key=lambda x: x[0]):
    print(f"  ✓ {local}")
    print(f"     WordPress: {wp}")
    print(f"     Fichier: {file}")
    print()

print("=" * 70)
print(f"📦 SNIPPETS À ARCHIVER ({len(to_archive)} fichiers)")
print("=" * 70)
for local, file in sorted(to_archive, key=lambda x: x[0]):
    print(f"  📦 {local}")
    print(f"     Fichier: {file}")

print()
print("=" * 70)
print("🔍 SNIPPETS WordPress SANS CORRESPONDANCE LOCALE")
print("=" * 70)
kept_wp_names = {wp_match for _, _, wp_match in to_keep}
for wp_name in wp_active:
    if wp_name not in kept_wp_names:
        print(f"  ⚠️  {wp_name}")

print()
print("=" * 70)
print(f"📊 RÉSUMÉ:")
print(f"   À conserver: {len(to_keep)}")
print(f"   À archiver: {len(to_archive)}")
print(f"   WordPress sans correspondance: {len([n for n in wp_active if not any(n == m for _, _, m in to_keep)])}")
print("=" * 70)
