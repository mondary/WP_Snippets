# WP_Snippets

[🇫🇷 FR](README.md) · [🇬🇧 EN](README_en.md)

✨ Collection de snippets WordPress orientée productivité admin, publication et workflows éditoriaux.

## ✅ Fonctionnalités
- Base de snippets prête à l’usage dans `snippets/canonical/`.
- Historique et variantes dans `snippets/archive/`.
- Workflow de sync WordPress via `CODE_SNIPPETS_SYNC/`.
- Nouveau snippet d’export RAG: un fichier Markdown par article (ZIP).
- **Calendrier V26** avec featured images, drag & drop, réallocation brouillons dès aujourd'hui et vérification des articles planifiés (créneaux 10h, 14h, 11h, 12h, 13h). La réallocation démarre désormais à **aujourd'hui** et respecte la capacité partagée publish+future+draft (max `articles_per_day` par jour). Les créneaux déjà passés sont automatiquement filtrés.
- **Calendrier V28** — Ajout d'une **notification flottante permanente** en haut à droite de l'admin WordPress qui indique en temps réel combien d'articles manquent pour atteindre l'objectif du jour (5 par défaut). Si 3 articles sont prévus, la notif affiche « Manque 2 articles — 3/5 prévus » avec lien direct vers le calendrier. La notif est présente sur **toutes les pages admin**, peut être repliée en pastille, pulse si quota non atteint, et s'auto-refresh toutes les 60s (ou au retour sur l'onglet).
- **Sous-menu « Articles planifiés »** dans la barre latérale gauche, sous le menu Articles, avec badge du nombre d'articles planifiés.
- **Détection des articles sans image mise en avant** — filtre dans la liste, sous-menu « Sans image » avec compteur, et page dédiée listant les articles publiés sans featured image.
- **FAB stack `v19`** — un bouton flottant latéral (droite par défaut) déploie au survol une colonne d'items à largeur unique : Google News, ligne « Articles programmés » **cliquable vers le futur site** (mini-jauge 18px + nombre + date), Statistiques (compteur live), Ko-fi, Newsletter (lien `/newsletter/`), « Lire en diaporama » (déclenche l'overlay de News Diaporama v3 — son bouton play bleu est masqué) et le switcher GTranslate intégré à la géométrie exacte des items. **Burger en icônes officielles iconmonstr** (`layer-multiple-alt-filled` ↔ `cube-filled`, **30px**) qui **se morphent réellement via GSAP MorphSVGPlugin** (gratuit sur cdnjs, 3.13.0) à chaque ouverture/fermeture — `mdhub_render` en wp_footer priorité 99 pour s'exécuter après le chargement de GSAP ; **animation garantie dans tous les cas** : avec GSAP, morph du path (0.7 s, ease élastique) + rotation 180° du badge ; sans GSAP (optimiseurs WP Rocket/Autoptimize/Rocket Loader), spin CSS avec échange de forme à mi-course — plus jamais de saut sec ; **poignée ⇄ collée au burger** pour basculer gauche↔droite. **Toast promotionnel large** (min 300px, z-index au-dessus du GTranslate mais sous la stack déployée, plus de fond blanc sur le wrapper GT) : tirage **sans répétition** (Fisher-Yates persisté) — cliquable, auto-masqué après 10 s, fermable (silence 30 min). Le hub délègue chaque élément à son script dédié : News Diaporama v3, Scroll To Top v2, Live Cursors, Site Stats Page, Social Ego v2, Futur Menubar v4. Voir `snippets/canonical/FRONTEND 🌸 FAB - Hub Flottant - v17.php`.
- **Futur site `v4`** — aperçu des articles programmés sur `/?future_site=1` (home avec les articles `future` inclus + bandeau « 🚀 version spoiler »). Accès : admins **ou** comptes avec un rôle premium (`$FS_PREMIUM_ROLES`, ajustable + filtre `fs_futur_premium_roles`). Les non-abonnés voient un **paywall** avec CTA vers `/abonnement/`. Admin : sous-menu Articles > Futur site + icône fusée dans la barre d'admin. Entrée publique : ligne « Articles programmés » du FAB v19. Voir `snippets/canonical/🧭 ADMIN MENUBAR - Futur Menubar - v4.php`.

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

### Calendrier V28 (Schedule Calendar)
- Fichier: `snippets/canonical/ADMIN 📅 SCHEDULER - Calendar - v28.php`
- UI: Menu bar « Calendrier » dans l'admin WordPress + badge de version dans le titre.
- **Notification flottante permanente** en haut à droite de l'admin WordPress (toutes pages admin), indique en temps réel le quota d'articles du jour (objectif 5 par défaut). Ex: « Manque 2 articles — 3/5 prévus » avec lien direct vers le calendrier. La notif peut être repliée en pastille (persistant par navigateur), pulse si quota non atteint, et s'auto-refresh toutes les 60s.
- **Featured images** en miniature dans les cartes (bordure rouge + 🖼️ si absente).
- **Vue mensuelle stable** : navigation mois précédent/suivant, option `+1 mois` / `Année complète`.
- **Drag & Drop** : reprogrammer les articles par glisser-dépose, rebalance automatique du jour.
- **Créneaux prioritaires `10h, 14h, 11h, 12h, 13h`** : 1er article → 10h, 2e → 14h, puis 11h/12h/13h.
- **Réallocation brouillons** : bouton dédié + choix du nombre d'articles/jour (1 à 5). Par défaut: **Planifiés + brouillons** avec **5 articles / jour**, compactés dès aujourd'hui. Les articles avec image mise en avant sont traités avant ceux sans image. Les créneaux déjà pris ou passés sont filtrés automatiquement.
- **Capacité partagée** — le total publish + future + draft ne dépasse jamais `articles_per_day` par jour, **y compris pour aujourd'hui**. Un jour avec 3 publiés et `2/jour` n'acceptera qu'aucun brouillon. Un jour avec 0 publié et `2/jour` acceptera 2 brouillons (10h, 14h).
- **Boîte de résultats détaillée** avec sections diagnostic: placement des brouillons (ID + date cible) et occupation des 6 prochains jours à partir d'aujourd'hui.
- **Barre de statut** sous le header, en pleine largeur.
- **Filtres** : recherche par titre, filtrage par catégorie, sélection mois/année, détection des doublons.

### Créneau automatique dans l'éditeur
- Fichier: `snippets/canonical/ADMIN 📅 SCHEDULER - Editor Next Free Slot - v3.php`
- À l'ouverture d'un nouvel article, brouillon ou article planifié dans Gutenberg, remplace la date « immédiatement » par le prochain créneau libre du calendrier.
- Créneaux et occupation: `10h, 14h, 11h, 12h, 13h`; articles publiés, planifiés et brouillons sont pris en compte. Les créneaux déjà passés sont ignorés.
- Le sélecteur de date affiche des points sous les jours occupés : rouge pour les brouillons, vert pour les publiés et bleu pour les planifiés.

### Détection des articles sans image mise en avant
- Fichier: `snippets/canonical/ADMIN 🧰 DETECT - Missing Featured Images - v1.php`
- UI: sous-menu « Sans image » dans la colonne latérale gauche, sous le menu Articles (badge rouge = nombre d'articles sans image).
- Filtre « Avec / Sans image mise en avant » dans la liste des articles (`edit.php`).
- Page dédiée listant tous les articles publiés sans featured image, avec liens Modifier/Voir.

### Articles planifiés (Sous-menu)
- Fichier: `snippets/canonical/🧭 ADMIN MENUBAR - Scheduled Posts Submenu - v1.php`
- UI: sous-menu « Articles planifiés » dans la colonne latérale gauche, sous le menu Articles.
- Affiche un badge avec le nombre d'articles planifiés.
- Redirection propre vers `edit.php?post_status=future&post_type=post&orderby=date&order=asc`.

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

- 2026-09-09: **FAB Hub Flottant `v19`** — burger **toujours animé** : avec GSAP, morph du path (0,7 s, ease élastique `back.out(1.7)`) + rotation 180° du badge (killTweensOf contre les conflits de tweens rapides) ; **sans GSAP** (optimiseurs), spin CSS `rotate(90deg) scale(0.72)` avec échange du path à mi-course — l'échange n'est plus un saut sec mais porté par la rotation.
- 2026-09-09: **FAB Hub Flottant `v18`** — morph **résistant aux optimiseurs** : si un plugin décale GSAP après le script inline (defer/delay de WP Rocket, Autoptimize, Rocket Loader…), un échange instantané layer↔cube prend le relais puis GSAP resynchronise l'état courant dès qu'il charge (polling 100 ms, 20 s max) — plus jamais « aucune animation ».
- 2026-09-09: **FAB Hub Flottant `v17`** — icône burger ramenée à **30px** (la 46px débordait du bouton) et **fix du morph muet** : le script inline s'exécutait en `wp_footer` priorité 10, avant le chargement de GSAP (scripts priorité 20) — `window.gsap` était toujours undefined au démarrage, donc aucun morphing ; `mdhub_render` passe en **priorité 99** (après les scripts). Vérifié en navigateur : path layer `m2.394…` ⇄ path cube `m21 7.702…` (les officiels iconmonstr).
- 2026-09-09: **FAB Hub Flottant `v16`** — le burger utilise les **icônes officielles iconmonstr** `layer-multiple-alt-filled` (fermé) et `cube-filled` (ouvert), **vrai morphing** via GSAP **MorphSVGPlugin** (gratuit sur cdnjs en 3.13.0, chargé en footer avec repli statique si GSAP absent) à chaque ouverture/fermeture de la stack. Remplace les losanges/cube dessinés à la main de la v15 (jugés « cassés »).
- 2026-09-09: **FAB Hub Flottant `v15`** — icônes du burger **presque deux fois plus grandes** (46px sur bouton 52px, 40px en mobile, `flex: 0 0 auto` contre un shrink flex mystérieux) et **losanges de la stack redessinés** : plus épais (demi-hauteur 3.3), espacement régulier de 1.2, alignement vertical centré — l'icône fermée n'est plus « cassée ». Cube légèrement agrandi pour équilibrer.
- 2026-09-09: **FAB Hub Flottant `v14`** — republication sous nouveau numéro de la v13 modifiée après installation : **burger animé stack→cube** (trois couches, géométrie d'après iconmonstr layer-multiple-alt-filled, crossfadent à ressort vers un cube isométrique d'après iconmonstr cube-filled, zéro Font Awesome sur le FAB) ; **toast élargi** (min-width 300px, 1-2 lignes au lieu de 4-5) ; **z-index réglés** : toast au-dessus du switcher GTranslate (fond blanc du wrapper supprimé, il masquait le toast) et **stack déployée toujours au-dessus du toast** (petals z-index 10).
- 2026-09-09: **FAB Hub Flottant `v12`** — **toast redessiné** : carte blanche avec pastille colorée aux couleurs de la feature + label (Google News, Futur site, Newsletter, Diaporama, Ko-fi), apparition avec rebond ; **tirage sans répétition** : ordre de Fisher-Yates persisté en localStorage (`mdhubToastOrder`/`mdhubToastCursor`), les 5 features défilent avant qu'un nouveau mélange recommence ; **burger changé** : le `fa-bars` devient un « + » qui pivote en croix (45°) quand la stack est ouverte. Conserve : poignée ⇄ collée au burger, ligne « Articles programmés » cliquable vers le futur site, GTranslate aligné, masquages.
- 2026-09-09: **FAB Hub Flottant `v11`** — poignée de bascule **recollée au burger** (chevauchement -8px, picto remplacé par `fa-right-left`) et **toast promotionnel aléatoire** : une feature tirée au hasard à chaque page (Google News, articles programmés/futur site, newsletter, diaporama, Ko-fi) apparaît à côté du FAB côté intérieur après ~3,5 s — cliquable (le diaporama déclenche l'overlay via `#np-fab`), auto-masquée après 10 s, fermable avec silence de 30 min (localStorage `mdhubToastMuted`). Affichage fiable via `setTimeout` (pas de `requestAnimationFrame`, tributaire de la visibilité de l'onglet).
- 2026-09-09: **FAB Hub Flottant `v10` + Futur site `v4`** — la ligne « Articles programmés » de la stack devient cliquable et rouvre l'accès au **futur site** (`/?future_site=1` : home avec les articles `future` inclus + bandeau « 🚀 version spoiler »), disparu avec l'archivage de Futur Menubar v3. **Futur Menubar `v4`** (nouveau canonique, base v3) : le garde-fou n'est plus admin-only — accès admins **ou** rôle premium (`$FS_PREMIUM_ROLES`, ajustable + filtre `fs_futur_premium_roles`) ; les non-abonnés voient un **paywall** (CTA `/abonnement/` + retour au présent) au lieu d'une 404 ; sous-menu Articles > Futur site + icône fusée admin bar conservés. Stack v10 : ligne « Articles programmés » cliquable (jauge 18px + nombre + date), switcher GTranslate aligné sur la géométrie des items (padding/drapeau 18px/texte 12px — v9), diaporama porté par News Diaporama v3 (bouton play masqué, item « Lire en diaporama »), Newsletter vers `/newsletter/`, masquages Google News Button v3 et `.wppk-floating-newsletter-btn`. Flux RSS et Retour en haut retirés de la stack.
- 2026-09-09: **FAB Hub Flottant `v9`** — stack latérale repliable (FAB + poignée chevron droite↔gauche mémorisée, déploiement au survol/tap/focus, items à largeur unique). **Rebasage sur les scripts dédiés** : le diaporama (endpoint REST, overlay) reste porté par News Diaporama v3 (active) dont le bouton play bleu `#np-fab` est masqué et remplacé par l'item « Lire en diaporama » de la stack (item auto-masqué si v3 inactive), le retour-haut reste porté par Scroll To Top v2 (`#kt-scroll-up` plus masqué), le compteur live par Live Cursors (`#fab-live-count`), la page stats par Site Stats Page. **v9** : alignement typographique complet de la stack — le switcher GTranslate (`.gt_switcher_wrapper`) adopté dans la stack est plaqué sur la géométrie exacte des items (padding 10px, drapeau 18px sur la colonne icône, texte 12px/650 aligné sur les labels, ligne 40px, hover uniforme, fond blanc du contrat GTranslate White v2) et la mini-jauge « Articles programmés » passe de 30 à 18px (même colonne que le drapeau et les icônes, chiffre 8px, ligne 40px). **v8** : masquage du bouton flottant newsletter `.wppk-floating-newsletter-btn` (HTML+CSS injecté côté WP hors repo — ni Jetpack ni snippet local). Seul Google News Button v3 est remplacé. Flux RSS et Retour en haut retirés de la stack.
- 2026-09-08: **Barre d'actions `v4`** — le FAB pétales est remplacé par une barre sticky compacte, lisible sur desktop et défilable sur mobile ; **Cursors `2026.09.16`** — badge haut droite supprimé, compteur live délégué au hub. **Stats `2026.09.11` + Cursors `2026.09.15`** — versions empilées sur deux lignes alignées à droite, uniquement sur la page statistiques (version curseur lue dynamiquement depuis le snippet actif) ; plus rien sur le badge curseur. **Cursors `2026.09.12`** — vrais noms quand possible : compte WP connecté, sinon nom du cookie de commentaire ; à défaut « Anonyme N » unique assigné côté serveur et mémorisé en localStorage (fini les faux prénoms aléatoires, plus de collisions).
- 2026-09-04: **Live Cursors `2026.09.3`** — nouveau snippet : curseurs live partagés sur tout le site (polling AJAX), confettis cliquables visibles par tous, noms WP pour les connectés / aléatoires sinon, badge « N en ligne · X vues » avec dropdown listant chaque visiteur et sa page. **Site Stats Page `2026.09.2`** — page standalone `/statistiques/` avec header du site, graphiques SVG style crayonné (cumul/jour), navigation par année, import CSV (Outils → Importer Stats), tracking des vues en table dédiée. **Super Tracker `2026.09.1`** — correctif du double `<?php` qui cassait le tracking + audit sécurité : suppression de 3 trackers suspects (datapulse, histogram-analytics, swilty).
- 2026-07-16: **Détection articles sans featured image `v1`** — nouveau snippet `ADMIN 🧰 DETECT - Missing Featured Images - v1.php` : filtre dans la liste des articles (Avec/Sans image), sous-menu « Sans image » avec compteur, page dédiée listant les articles publiés sans image mise en avant.
- 2026-07-16: **Media Orphans `v3`** — fusion du snippet taille médiathèque (plus de doublon), UI allégée (filtres dans dropdown uniquement, suppression du mur de boutons), liens colonne Used In fiabilisés (fallback `get_permalink` + titre sans lien), analyse du plus récent au plus ancien (DESC). Token unifié pour Analyze Usage et Recalculer la taille. `v2` + `Admin Size v2` archivés.
- 2026-07-11: Calendrier `v26` — la réallocation des brouillons démarre désormais à **aujourd'hui** au lieu de J+1. La capacité partagée publish+future+draft est respectée pour tous les jours **y compris aujourd'hui**. Les créneaux déjà passés sont filtrés. Ordre prioritaire `[10,14,11,12,13]` unifié pour tous les jours. Debug occupation étendu à 6 jours depuis aujourd'hui. `v24` et `v25` archivées.
- 2026-06-16: Calendrier `v23` — la réallocation tient compte des articles **publiés** pour calculer la capacité d'un jour (`clm_normalize_future_posts_schedule` et `clm_compact_future_posts` interrogent désormais la DB pour exclure les créneaux publiés). Mode par défaut changé à **Planifiés + brouillons**. Diagnostics ajoutés au dialogue de résultats (placement détaillé + occupation). `v22` archivée.
- 2026-06-16: Nouveau snippet **« Articles planifiés »** — sous-menu dans la colonne latérale gauche sous Articles, avec badge de comptage et redirection propre vers les articles planifiés triés par date ascendante.
- 2026-06-16: Calendrier `v21` — fix de la popup de résultat vide (sections déjà construites re-construites). Barre de statut désormais sticky sous le header (reste visible au scroll). Dialog corps scroll corrigé (flex: 1 + min-height: 0).
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
