# CODE_SNIPPETS_SYNC

Dossier unique pour le workflow de sync `WordPress Code Snippets`.

## Push direct (1 commande)
1. Cree ton fichier local de secrets:
   - copie `secrets/wp-sync.env.example` vers `secrets/wp-sync.env`
2. Renseigne `WP_SITE_URL`, `WP_SYNC_USER`, `WP_APP_PASSWORD`
3. Lance depuis la racine du repo:
   - `./push-wordpress.sh --dry-run` (test)
   - `./push-wordpress.sh` (push reel)

## Statut (teste)
- Push direct **teste et fonctionnel** sur `mondary.design`
- Cas OVH/WAF observe:
  - `/wp-json` peut renvoyer `403` avec le `User-Agent` curl par defaut
  - le script force un `User-Agent` navigateur-like (corrige ce point)
  - `PATCH`/`PUT` peuvent etre bloques (`403`) sur certaines routes REST
  - le script tombe alors en fallback `POST` sur la route item (`/snippets/{id}`), ce qui fonctionne avec `Code Snippets`

## Le fichier d'import (si tu veux encore importer a la main)
- `imports/IMPORT-WORDPRESS.json`

## Dossiers
- `scripts/` : scripts de build / compare / extract
- `imports/` : JSONs prets a importer dans WordPress
- `exports/` : mets ici tes exports JSON bruts depuis WordPress
- `manifests/` : manifest d'exclusions / overrides
- `reports/` : rapports de comparaison

## Commandes utiles (depuis la racine du repo)
- Push direct (wrapper racine, lit `CODE_SNIPPETS_SYNC/secrets/wp-sync.env`)
  - `./push-wordpress.sh --dry-run`
  - `./push-wordpress.sh`
- Generer l'import depuis les snippets canonique:
  - `php CODE_SNIPPETS_SYNC/scripts/build_code_snippets_import.php --snippets-dir=WP_Snippets_FINAL_CLEAN/canonical --out=CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json`
- Comparer un export WordPress avec un dossier local:
  - `php CODE_SNIPPETS_SYNC/scripts/compare_code_snippets_export.php --export=CODE_SNIPPETS_SYNC/exports/mon-export.json --snippets-dir=WP_Snippets_FINAL_CLEAN/canonical --report=CODE_SNIPPETS_SYNC/reports/compare-report.json`
- Extraire un export WP en fichiers PHP:
  - `php CODE_SNIPPETS_SYNC/scripts/extract_code_snippets_export_to_files.php --export=CODE_SNIPPETS_SYNC/exports/mon-export.json --out-dir=CODE_SNIPPETS_SYNC/exports/extracted-online-current`

## Notes (push direct)
- Le push direct utilise l'API REST de `Code Snippets` (plugin free).
- Auth recommandee: `Application Password` WordPress (pas ton mot de passe normal).
- Strategie d'upsert: matching par **nom exact** du snippet (create/update).
- Si la route REST n'est pas exposee sur ton site, le script te le dira (et on fera un petit plugin bridge).
- Les secrets sont lus depuis `CODE_SNIPPETS_SYNC/secrets/wp-sync.env` (dossier ignore par Git).
- Le `--dry-run` valide la connexion, detecte les snippets distants, et affiche ce qui serait cree/mis a jour sans ecrire.
- Le push genere d'abord `imports/IMPORT-WORDPRESS.json`, puis pousse via REST.
