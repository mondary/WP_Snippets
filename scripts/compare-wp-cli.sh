#!/usr/bin/env python3
import json
import os
import subprocess
import requests
from requests.auth import HTTPBasicAuth
import sys

# Charger les credentials
try:
    with open('.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/secrets/wp-sync.env') as f:
        for line in f:
            if '=' in line and not line.strip().startswith('#'):
                key, value = line.strip().split('=', 1)
                os.environ[key] = value
except Exception as e:
    print(f"❌ Erreur chargement credentials: {e}")
    sys.exit(1)

wp_url = os.environ.get('WP_SITE_URL', '').rstrip('/')
wp_user = os.environ.get('WP_SYNC_USER', '')
wp_pass = os.environ.get('WP_APP_PASSWORD', '')

if not wp_url or not wp_user or not wp_pass:
    print("❌ Credentials WordPress manquants")
    sys.exit(1)

print("🔗 Connexion à WordPress...")
print(f"   Site: {wp_url}")
print()

auth = HTTPBasicAuth(wp_user, wp_pass)

try:
    endpoint = f"{wp_url}/wp-json/code-snippets/v1/snippets?per_page=100"
    response = requests.get(endpoint, auth=auth, timeout=15)

    if response.status_code != 200:
        print(f"❌ Erreur API WordPress: HTTP {response.status_code}")
        try:
            print(f"   Response: {response.text[:200]}")
        except:
            pass
        sys.exit(1)

    data = json.loads(response.text)

    # Gérer les différents formats de réponse
    if isinstance(data, list):
        wp_snippets = data
    elif isinstance(data, dict) and 'snippets' in data:
        wp_snippets = data['snippets']
    elif isinstance(data, dict) and isinstance(data.get('items'), list):
        wp_snippets = data['items']
    else:
        print("❌ Format de réponse WordPress non reconnu")
        sys.exit(1)

    # Extraire les snippets ACTIFS seulement
    wp_active = {}
    wp_inactive = {}

    for s in wp_snippets:
        name = s.get('name', '')
        active = s.get('active', False)
        status = s.get('status', 'unknown')

        if active:
            wp_active[name] = {
                'status': status,
                'id': s.get('id'),
                'modified': s.get('modified')
            }
        else:
            wp_inactive[name] = {
                'status': status
            }

    print(f"📊 WordPress: {len(wp_active)} actifs, {len(wp_inactive)} inactifs")
    print()

    # Snippets locaux
    local_snippets = {}
    for file in sorted(os.listdir('snippets/canonical')):
        if file.endswith('.php'):
            try:
                with open(f"snippets/canonical/{file}", 'r') as f:
                    content = f.read()
                    name = None
                    for line in content.split('\n')[:20]:
                        if 'Display name:' in line:
                            name = line.split(':', 1)[1].strip()
                            break
                    if name:
                        local_snippets[name] = file
            except:
                pass

    print(f"💾 Local: {len(local_snippets)} snippets")
    print()

    # Comparaison
    print("=" * 70)
    print("ANALYSE DES SNIPPETS")
    print("=" * 70)
    print()

    print("🟢 Snippets ACTIFS sur WordPress (à GARDER - ne pas supprimer) :")
    for name in sorted(wp_active.keys()):
        # Chercher correspondance locale (insensible à la casse et variations)
        found = None
        for local_name in local_snippets.keys():
            if name.lower().replace(' ', '').replace('-', '').replace('+', '') == local_name.lower().replace(' ', '').replace('-', '').replace('+', ''):
                found = local_name
                break

        status = wp_active[name]['status']
        wp_id = wp_active[name].get('id', 'N/A')

        if found:
            print(f"  ✓ {name}")
            print(f"     Local: {found}")
            print(f"     Status: {status}, ID: {wp_id}")
        else:
            print(f"  ⚠️  {name}")
            print(f"     Status: {status}, ID: {wp_id}")
            print(f"     ❌ PAS DE CORRESPONDANCE LOCALE")
        print()

    print("-" * 70)
    print()

    print("🟠 Snippets LOCAUX à archiver (PAS ACTIFS sur WordPress) :")
    archived = []
    kept = []

    for local_name in sorted(local_snippets.keys()):
        # Chercher sur WordPress (insensible aux variations)
        found_wp = None
        for wp_name in wp_active.keys():
            if local_name.lower().replace(' ', '').replace('-', '').replace('+', '') == wp_name.lower().replace(' ', '').replace('-', '').replace('+', ''):
                found_wp = wp_name
                break

        if found_wp:
            kept.append(local_name)
        else:
            archived.append(local_name)

    if archived:
        for name in archived:
            print(f"  📦 {name}")

    if kept:
        print()
        print("✅ Snippets LOCAUX à CONSERVER (actifs sur WordPress) :")
        for name in kept:
            print(f"  ✓ {name}")

    print()
    print("=" * 70)
    print(f"📊 RÉSUMÉ:")
    print(f"   WordPress actifs: {len(wp_active)}")
    print(f"   WordPress inactifs: {len(wp_inactive)}")
    print(f"   Local total: {len(local_snippets)}")
    print(f"   À archiver: {len(archived)}")
    print(f"   À conserver: {len(kept)}")
    print("=" * 70)

except requests.exceptions.RequestException as e:
    print(f"❌ Erreur connexion WordPress: {e}")
    print("   Vérifie ta connexion internet")
except Exception as e:
    print(f"❌ Erreur: {e}")
    import traceback
    traceback.print_exc()
