#!/usr/bin/env python3
import json
import subprocess
import os

# Charger les credentials
try:
    with open('.agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/secrets/wp-sync.env') as f:
        for line in f:
            if '=' in line and not line.strip().startswith('#'):
                key, value = line.strip().split('=', 1)
                os.environ[key] = value
except:
    pass

# Récupérer les snippets depuis WordPress
wp_url = os.environ.get('WP_SITE_URL', '')
wp_user = os.environ.get('WP_SYNC_USER', '')
wp_pass = os.environ.get('WP_APP_PASSWORD', '')

if not wp_url or not wp_user or not wp_pass:
    print("❌ Credentials WordPress manquants")
    exit(1)

import requests
from requests.auth import HTTPBasicAuth

auth = HTTPBasicAuth(wp_user, wp_pass)

try:
    # Endpoint WordPress snippets
    endpoint = f"{wp_url}/wp-json/code-snippets/v1/snippets"
    response = requests.get(endpoint, auth=auth, timeout=10)

    if response.status_code == 200:
        wp_snippets = json.loads(response.text)

        # Si c'est une liste simple
        if isinstance(wp_snippets, list):
            wp_names = {s['name'] for s in wp_snippets}
        else:
            wp_names = set()

    else:
        print(f"❌ Erreur API WordPress: HTTP {response.status_code}")
        wp_names = set()

except Exception as e:
    print(f"❌ Erreur connexion WordPress: {e}")
    wp_names = set()

# Snippets locaux dans canonical/
local_snippets = set()
for file in os.listdir('snippets/canonical'):
    if file.endswith('.php'):
        # Extraire le nom du Display name
        try:
            with open(f'snippets/canonical/{file}', 'r') as f:
                content = f.read()
                for line in content.split('\n')[:50]:
                    if 'Display name:' in line:
                        name = line.split(':', 1)[1].strip()
                        local_snippets.add(name)
                        break
        except:
            pass

print("=" * 60)
print("COMPARATIF SNIPPETS WORDPRESS vs LOCAL")
print("=" * 60)
print()

print(f"🌐 WordPress : {len(wp_names)} snippets")
print(f"💾 Local (canonical/) : {len(local_snippets)} snippets")
print()

print("🟢 Présents sur LES DEUX côtés :")
for name in sorted(local_snippets & wp_names):
    print(f"  ✓ {name}")
print()

print("🟡 Présents SEULEMENT sur WordPress :")
for name in sorted(wp_names - local_snippets):
    print(f"  + {name}")
print()

print("🟠 Présents SEULEMENT en LOCAL (à pousser) :")
for name in sorted(local_snippets - wp_names):
    print(f"  - {name}")
