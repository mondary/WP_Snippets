# WP_Snippets

[🇫🇷 FR](README.md) · [🇬🇧 EN](README_en.md)

✨ Collection de snippets WordPress orientée productivité admin, publication et workflows éditoriaux.

## ✅ Fonctionnalités
- Base de snippets prête à l’usage dans `snippets/canonical/`.
- Historique et variantes dans `snippets/archive/`.
- Workflow de sync WordPress via `CODE_SNIPPETS_SYNC/`.
- Nouveau snippet d’export RAG: un fichier Markdown par article (ZIP).
- **Calendrier V21** avec featured images, drag & drop, réallocation brouillons et vérification des articles planifiés (créneaux 10h, 14h, 11h, 12h, 13h).

## 🧠 Utilisation
1. Ouvrir et éditer les snippets dans `snippets/canonical/`.
2. Importer un snippet dans WordPress (plugin Code Snippets / WPCode).
3. Activer le snippet puis tester dans l’admin WordPress.

### Export RAG (nouveau)
- Fichier: `snippets/canonical/🧰 UTILITIES - Admin Export Posts Markdown RAG - v1.php`
- UI: bouton `Export Markdown (RAG)` dans `wp-admin > Articles`.
- Sortie: `wp-posts-rag-YYYY-MM-DD.zip`
- Contenu ZIP:
  - 1 fichier `.md` par article (`YYYY-MM-DD__slug__id-123.md`)
  - `INDEX.md` (index global des fichiers)
- Métadonnées incluses: date, auteur, catégories, tags, keywords, excerpt, URL, statut, etc.

### Calendrier V21 (Schedule Calendar)
- Fichier: `snippets/canonical/🧭 ADMIN MENUBAR - Schedule Calendar - v21.php`
- UI: Menu bar « Calendrier » dans l'admin WordPress + badge de version dans le titre.
- **Featured images** en miniature dans les cartes (bordure rouge + 🖼️ si absente).
- **Vue mensuelle stable** : navigation mois précédent/suivant, option `+1 mois` / `Année complète`.
- **Drag & Drop** : reprogrammer les articles par glisser-dépose, rebalance automatique du jour.
- **Créneaux prioritaires `10h, 14h, 11h, 12h, 13h`** : 1er article → 10h, 2e → 14h, puis 11h/12h/13h.
- **Réallocation brouillons** : bouton dédié + choix du nombre d'articles/jour (1 à 5). Les brouillons sont replanifiés à partir de J+1 en respectant les créneaux déjà pris.
- **Vérification des articles planifiés** (V21) : au moment de la réallocation, les `future` sont remis sur les bons créneaux ; débordement > 5/jour cascadé vers J+1.
- **Boîte de résultats détaillée** (V21) : remplace le `alert()` natif, affiche sections réallocation + planifiés vérifiés (corrigés, décalés, inchangés), scrollable.
- **Barre de statut** (V21) sous le header, en pleine largeur.
- **Filtres** : recherche par titre, filtrage par catégorie, sélection mois/année, détection des doublons.

## ⚙️ Réglages
- Aucun réglage obligatoire pour la plupart des snippets.
- Pour l’export RAG, serveur PHP avec extension `ZipArchive` requise.

## 🧾 Commandes

### Vérification syntaxe PHP
```bash
php -l "snippets/canonical/🧰 UTILITIES - Admin Export Posts Markdown RAG - v1.php"
```

### Synchronisation WordPress

#### 1. Comparaison WordPress vs Local
Compare les snippets actifs sur WordPress avec les snippets locaux.

```bash
# Comparer les snippets actifs WordPress avec les snippets locaux
python3 scripts/compare-active-wordpress-v2.sh
```

Sortie:
- **Snippets à conserver** : actifs sur WordPress
- **Snippets à archiver** : inactifs sur WordPress
- **Snippets WordPress sans correspondance locale** : à récupérer

#### 2. Archivage des snippets inactifs
Archive les snippets locaux qui ne sont pas actifs sur WordPress.

```bash
# Archiver les snippets inactifs (déplace vers snippets/archive/)
python3 scripts/archive-inactive-wordpress.sh
```

#### 3. Récupération des snippets actifs depuis WordPress
Récupère tous les snippets actifs depuis WordPress et les place dans `snippets/canonical/`.

```bash
# Récupérer les snippets actifs depuis WordPress
source .agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/secrets/wp-sync.env
php .agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/scripts/pull_active_snippets.php \
  --site="${WP_SITE_URL}" \
  --user="${WP_SYNC_USER}" \
  --app-password="${WP_APP_PASSWORD}" \
  --output-dir="snippets/canonical"
```

#### 4. Correction syntaxe PHP
Ajoute les balises `<?php` manquantes aux fichiers PHP.

```bash
./scripts/fix-php-syntax.sh
```

### Workflow complet de synchronisation

```bash
# 1. Comparer WordPress vs local
python3 scripts/compare-active-wordpress-v2.sh

# 2. Archiver les snippets inactifs
python3 scripts/archive-inactive-wordpress.sh

# 3. Récupérer les snippets actifs depuis WordPress
source .agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/secrets/wp-sync.env
php .agent/-pkwpsyncsnippets/CODE_SNIPPETS_SYNC/scripts/pull_active_snippets.php \
  --site="${WP_SITE_URL}" \
  --user="${WP_SYNC_USER}" \
  --app-password="${WP_APP_PASSWORD}" \
  --output-dir="snippets/canonical"

# 4. Nettoyer la syntaxe PHP si nécessaire
./scripts/fix-php-syntax.sh
```

## 📦 Build & Package
- Génération import JSON via `CODE_SNIPPETS_SYNC/`.
- Import WordPress recommandé: `CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json`.

## 🧪 Installation
1. Installer/activer `Code Snippets` (ou WPCode) sur WordPress.
2. Coller/importer le snippet souhaité.
3. Activer puis vérifier dans l’interface admin.

## 🧾 Changelog
- 2026-06-15: Calendrier `v21` — nouvel ordre de créneaux `10h, 14h, 11h, 12h, 13h`, vérification des articles planifiés (cascade J+1 si >5/jour), boîte de résultats détaillée (remplace `alert()`), barre de statut sous le header, badge de version dans le titre. Fichier renommé `Schedule Calendar - v21`. `v19` archivée.
- 2026-06-01: calendrier `v18` conservé en canonical, versions `v11` à `v17` archivées/supprimées selon workflow.
- 2026-06-01: correction snippets cassés par metadata injectées (`Fusion OutilsReglages`, `Admin Media Size v2`) en versions minifiées activables.
- 2026-05-28: ajout du Calendrier V11 avec featured images en miniature (identification visuelle des articles sans image).
- 2026-05-28: ajout des scripts de synchronisation WordPress (compare, archive, pull).
- 2026-05-28: archivage de 34 snippets inactifs, récupération de 23 snippets actifs depuis WordPress.
- 2026-04-29: ajout du snippet `Admin Export Posts Markdown RAG - v1`.
- 2026-04-29: README restructuré (FR/EN) et documentation du flux export RAG.

## 🔗 Liens
- EN README: `README_en.md`
- Snippets actifs: `snippets/canonical/`
