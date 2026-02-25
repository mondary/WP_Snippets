# WP_Snippets (Root)

## Dossiers principaux

- `WP_Snippets_FINAL_CLEAN/`
  - Base de travail des snippets (version finale triée)
  - `canonical/` = snippets à garder / utiliser
  - `archive/` = anciennes versions / variantes

- `CODE_SNIPPETS_SYNC/`
  - Outils de sync import/export pour WordPress `Code Snippets`
  - Fichier d'import actuel : `CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json`

## Workflow simple (maintenant)

1. Modifier les snippets dans `WP_Snippets_FINAL_CLEAN/canonical/`
2. Générer l'import JSON via `CODE_SNIPPETS_SYNC/`
3. Importer `CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json` dans WordPress (`Code Snippets > Import`)

## Note

- Les anciens fichiers / scripts / rapports sont conservés dans les dossiers `_ROOT_LEGACY/`.

