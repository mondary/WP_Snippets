#!/usr/bin/env python3
import os
import re

# Liste des 18 snippets actifs sur WordPress
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

# Mapping manuel WordPress -> Local Display name
wp_to_local_mapping = {
    "ADMIN SETTINGS - Fusion Outils+Reglages": "ADMIN - Outils + reglages (tri alphabetique)",
    "ADMIN SETTINGS - Menu Order Alpha Settings - v2": "ADMIN - Menu order alpha +settings",
    "ADMIN SETTINGS - Show Active Plugins First - v2": "ADMIN - Show active plugins first",
    "TAGS - Post Already Existing Tags - v3": "POST - Already existing tags",
    "SCHEDULER - Admin Download Images - v1": "ADMIN - Download images",
    "ADMIN COLUMNS - List Add Featured Images - v3": "ADMIN - List Add featured images",
    "SEARCH - Post Search Auto - v4 (big opti ?)": "POST - Search auto 🟢",
    "ADMIN MENUBAR - Schedule Calendar Drag 14h - v10": "ADMIN - Schedule Calendar [DRAG+14h] [2 MOIS] 📆",
}

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

print("=" * 70)
print("COMPARAISON SNIPPETS WORDPRESS ACTIFS vs LOCAL")
print("=" * 70)
print()

# Snippets WordPress avec correspondance locale connue
matched_wp = set(wp_to_local_mapping.values())
unmatched_wp = [wp for wp in wp_active if wp not in wp_to_local_mapping]

print(f"📊 Local: {len(local_snippets)} snippets")
print(f"🌐 WordPress actifs: {len(wp_active)} snippets")
print(f"✅ Correspondances trouvées: {len(wp_to_local_mapping)}")
print()

to_keep = []
to_archive = []

# Snippets à garder (ceux qui correspondent à WordPress actifs)
for local_name, file in local_snippets.items():
    if local_name in matched_wp:
        wp_name = [wp for wp, loc in wp_to_local_mapping.items() if loc == local_name][0]
        to_keep.append((local_name, file, wp_name))
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
print("🔍 SNIPPETS WORDPRESS ACTIFS SANS CORRESPONDANCE LOCALE")
print("=" * 70)
print("(Ces snippets sont actifs sur WordPress mais n'existent pas en local)")
print()
for wp_name in unmatched_wp:
    print(f"  ⚠️  {wp_name}")

print()
print("=" * 70)
print(f"📊 RÉSUMÉ:")
print(f"   À conserver: {len(to_keep)}")
print(f"   À archiver: {len(to_archive)}")
print(f"   WordPress sans correspondance locale: {len(unmatched_wp)}")
print("=" * 70)
