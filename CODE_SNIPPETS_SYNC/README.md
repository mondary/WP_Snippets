# CODE_SNIPPETS_SYNC

Dossier unique pour le workflow de sync `WordPress Code Snippets`.

## Le fichier a importer maintenant
- `imports/IMPORT-WORDPRESS.json`

## Dossiers
- `scripts/` : scripts de build / compare / extract
- `imports/` : JSONs prets a importer dans WordPress
- `exports/` : mets ici tes exports JSON bruts depuis WordPress
- `manifests/` : manifest d'exclusions / overrides
- `reports/` : rapports de comparaison

## Commandes utiles (depuis la racine du repo)
- Generer l'import depuis les snippets canonique:
  - `php CODE_SNIPPETS_SYNC/scripts/build_code_snippets_import.php --snippets-dir=WP_Snippets_FINAL_CLEAN/canonical --out=CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json`
- Comparer un export WordPress avec un dossier local:
  - `php CODE_SNIPPETS_SYNC/scripts/compare_code_snippets_export.php --export=CODE_SNIPPETS_SYNC/exports/mon-export.json --snippets-dir=WP_Snippets_FINAL_CLEAN/canonical --report=CODE_SNIPPETS_SYNC/reports/compare-report.json`
- Extraire un export WP en fichiers PHP:
  - `php CODE_SNIPPETS_SYNC/scripts/extract_code_snippets_export_to_files.php --export=CODE_SNIPPETS_SYNC/exports/mon-export.json --out-dir=CODE_SNIPPETS_SYNC/exports/extracted-online-current`
