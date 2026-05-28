#!/usr/bin/env python3
import os
import shutil
from pathlib import Path

# Mapping WordPress -> Local Display name (snippets actifs sur WordPress)
wp_active_local_names = {
    "ADMIN - Outils + reglages (tri alphabetique)",
    "ADMIN - Menu order alpha +settings",
    "ADMIN - Show active plugins first",
    "POST - Already existing tags",
    "ADMIN - Download images",
    "ADMIN - List Add featured images",
    "POST - Search auto 🟢",
    "ADMIN - Schedule Calendar [DRAG+14h] [2 MOIS] 📆",
}

canonical = Path("snippets/canonical")
archive = Path("snippets/archive")

# Créer le dossier archives
archive.mkdir(parents=True, exist_ok=True)

print("=" * 70)
print("ARCHIVAGE DES SNIPPETS INACTIFS WORDPRESS")
print("=" * 70)
print()

# Trouver tous les snippets locaux
local_snippets = {}
for file in sorted(canonical.glob("*.php")):
    try:
        with open(file, 'r') as f:
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

# Séparer à garder vs à archiver
to_keep = []
to_archive = []

for display_name, file_path in local_snippets.items():
    if display_name in wp_active_local_names:
        to_keep.append((display_name, file_path))
    else:
        to_archive.append((display_name, file_path))

print(f"📊 Analyse:")
print(f"   Total local: {len(local_snippets)}")
print(f"   À conserver: {len(to_keep)}")
print(f"   À archiver: {len(to_archive)}")
print()

# Archiver
print("=" * 70)
print("📦 ARCHIVAGE EN COURS...")
print("=" * 70)
print()

archived_count = 0
for display_name, file_path in to_archive:
    dest = archive / file_path.name
    print(f"📦 {file_path.name}")
    print(f"   Display: {display_name}")
    if dest.exists():
        print(f"   ⚠️  Déjà dans archive - remplacement")
        dest.unlink()
    shutil.move(str(file_path), str(dest))
    archived_count += 1
    print()

print()
print("=" * 70)
print("✅ SNIPPETS CONSERVÉS (actifs WordPress)")
print("=" * 70)
for display_name, file_path in sorted(to_keep, key=lambda x: x[0]):
    print(f"  ✓ {file_path.name}")
    print(f"     {display_name}")
    print()

print()
print("=" * 70)
print(f"📊 RÉSUMÉ:")
print(f"   Archivés: {archived_count}")
print(f"   Conservés: {len(to_keep)}")
print("=" * 70)
