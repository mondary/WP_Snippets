# WP_Snippets (Root)

## Dossiers principaux

- `snippets/`
  - Base de travail des snippets (version finale triée)
  - `canonical/` = snippets à garder / utiliser
  - `archive/` = anciennes versions / variantes

- `CODE_SNIPPETS_SYNC/`
  - Outils de sync import/export pour WordPress `Code Snippets`
  - Fichier d'import actuel : `CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json`

## Workflow simple (maintenant)

1. Modifier les snippets dans `snippets/canonical/`
2. Générer l'import JSON via `CODE_SNIPPETS_SYNC/`
3. Importer `CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json` dans WordPress (`Code Snippets > Import`)

## Snippet media (actuel)

- Snippet canonical: `snippets/canonical/🖼️ MEDIA LIBRARY - Usage Audit - v7.php`
- Fonction: colonne `Used In` + detection `Featured` / `Content` / `Orphan`
- Filtres media: `Orphan only`, `Used only`, `Featured only`, `Content only`, `Not analyzed`
- Action requise apres activation: cliquer `Analyze Usage` dans la barre de vues de `Mediatheque > Liste`

### Convention versioning

- Meme nom de base pour la famille de snippet
- Incrementation `vN` dans le nom de fichier
- Changelog obligatoire en en-tete du fichier
- Versions precedentes deplacees dans `snippets/archive/`

## Plugin

Le plugin WordPress **WP PK Premium** est maintenant dans un repo dedie : `WP_pkpremium`.

## Note

- Les anciens fichiers / scripts / rapports sont conservés dans les dossiers `_ROOT_LEGACY/`.

## Push (recommande)

```bash
bash CODE_SNIPPETS_SYNC/scripts/push_wordpress.sh --dry-run --verbose
```
