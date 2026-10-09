# Changelog

## [2026.10.13] - 2026-10-09
### Changed
- `FRONTEND 📄 PAGINATION - Jump Select - v2` — retour UX après première utilisation : le jumper remplace désormais les **points de suspension** de la pagination (entre les premiers et derniers numéros, et non plus en bout de rangée), et la drop list laisse place à un **hybride « pill ⋯ + popover »** : clic sur le pill → popover compact avec saisie « Page __ / N » (validée par Entrée ou « Aller », clamp 1..N) et raccourcis −50 / −10 / +10 / +50 pour l'exploration. Rationale : à ~298 pages, scroller une dropdown native est pénible sur desktop, la saisie seule est pénible sur mobile et ne sert pas l'exploration ; l'hybride couvre les deux en un clic. Popover en `position:fixed` (jamais clippé), fermable par ESC (retour focus) ou clic extérieur, focus automatique dans l'input, style aux variables Kadence. v1 conservée dans `snippets/canonical/`.

### Fixed
- Déploiement de la v2 (**#432 active**, v1 #430 désactivée, test #433 en corbeille) : la « validation » d'activation du plugin Code Snippets a renvoyé un **faux négatif** (`rest_cannot_activate`, « le code n'a pas passé la validation ») alors que le code s'exécute sans la moindre erreur sur la home (HTTP 200, jumper servi) — contourné par création/mise à jour directe avec `active: true` (l'endpoint REST de collection n'applique pas la validation sandbox). À retenir aussi : suffixer les fonctions par version (`clm_jump_select_footer_v2`) car le plugin exécute le snippet à l'activation et une v1 active redéclarerait la même fonction (fatal « Cannot redeclare ») ; un update REST sans champ `active` désactive le snippet ; un snippet en corbeille garde son nom et capte les upserts par nom (renommer les doublons trashed avant de re-pousser).

## [2026.10.12] - 2026-10-08
### Added
- `FRONTEND 📄 PAGINATION - Jump Select - v1` — un menu déroulant « Page N » greffé dans la pagination Kadence (home, archives, recherche) : avec ~298 pages, on saute à n'importe laquelle d'un seul geste au lieu d'être limité aux liens « 1 2 3 … 298 ». Le `<select>` porte la classe `page-numbers` du thème (style pill, survol et rayon hérités), JS vanilla sans jQuery ni requête serveur, URL cible reconstruite depuis un lien `/page/N/` existant (page 1 = base sans segment `/page/`), compatible cache LiteSpeed. Marqueur de version en `title` sur le select (pas de titre de page sur ce snippet).

- Déploiement : `Jump Select v1` **en ligne (#430 actif)**, vérifié sur `/`, `/page/2/`, `/page/42/` et `/page/298/`. Au passage, `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.16` déployé (#431 actif, #398 « 2026.09.15 » désactivé) : le code en ligne datait d'avant le 16/09 (badge/dropdown revenus, compteur FAB cassé), séquelle du push périmé ci-dessous.

### Fixed
- **Incident sync** — un push lancé avec un chemin relatif a résolu `WORDPRESS-N-N1.json` contre la racine du toolkit `.agent/` : c'est un snapshot de septembre qui a été poussé. 12 snippets recréés puis mis à la corbeille (#418-429, dont Google News Button v3 brièvement actif en doublon du FAB) ; 3 codes rétrogradés puis restaurés depuis le canonique (Futur Menubar #261, Duplicate Post Page #289, Live Cursors → remplacé par #431). État final vérifié : 67 snippets dont 55 hors corbeille (32 actifs), plus aucun écart de code avec `snippets/canonical/`.
- `scripts/sync-wordpress.sh` — le push passe désormais `--endpoint` explicite (`/wp-json/code-snippets/v1/snippets`) : la découverte automatique tombait sur `/cloud/snippets` qui renvoie HTTP 400 depuis la dernière mise à jour du plugin Code Snippets ; un échec du binaire PHP est désormais fatal (le script affichait « ✅ Synchronisation terminée ! » même après un crash `dump-loader` Herd). Rappel : toujours passer un chemin **absolu** au `--import-json` (les chemins relatifs résolvent contre le toolkit `.agent/`, pas la racine du dépôt).
- `scripts/prepare-wordpress.sh` — comparaison des versions datées corrigée : `2026.09.11` était jugée plus récente que `2026.10.1` (`int("2026101") < int("20260911")`), ce qui inversait n/n-1 pour les familles datées ; désormais tri sur `AAAA*10000 + MM*100 + JJ`.

## [2026.10.11] - 2026-10-08
### Added
- `FRONTEND 📊 STATS - Site Stats Page - 2026.10.1` — ajout au dépôt du snippet actif de statistiques du site (`/statistiques/`), avec graphiques annuels, vues/visiteurs agrégés et import CSV dans Outils.

## [2026.10.10] - 2026-10-08
### Fixed
- `.gitignore` — les exports Umami CSV à la racine (`/umami-*.csv`) sont ignorés pour éviter d’ajouter les données analytiques brutes aux commits. Le dossier `store/umami-reference/` reste ignoré.

## [2026.10.9] - 2026-10-08
### Added
- `ADMIN 🧰 DETECT - Duplicates Posts - v6` — bouton « 🔍 Doublons » ajouté directement à la liste des articles ; le lien « Calendrier annuel » y est présenté comme un vrai bouton. Déployé en ligne (#416 actif, #414 désactivé). Le bouton d’export RAG était déjà présent.

### Changed
- `MEDIA 🖼️ IMAGES - Orphans - v5` — barre des vues de la Médiathèque allégée : libellés courts (« Orphelins », « Utilisées », « Image mise en avant », « Dans le contenu »), boutons d’action compacts à la taille WordPress, sans gras ni style primaire. Déployé en ligne (#417 actif ; #415 v4 désactivé) ; v4 archivée localement.
- Six snippets inactifs mis à la corbeille en ligne (#339, #347, #349, #353, #359, #360) ; snapshots des six fichiers locaux homonymes ajoutés à `snippets/archive/`. Archivage local de Duplicates v5 et Orphans v3/v4. État en ligne : 53 snippets (43 actifs / 10 inactifs).

## [2026.10.8] - 2026-10-08
### Added
- `ADMIN 🧰 DETECT - Duplicates Posts - v1` — page « 🔍 Doublons » sous Articles : détecte les articles traitant du même sujet par similarité de titre (tokens normalisés FR : accents, pluriels et mots vides ignorés) et/ou URLs externes communes du contenu (query strings normalisées, liens internes exclus, URLs omniprésentes écartées), regroupe paires et triplons (composantes connexes) avec écart de dates et « pourquoi » (score titre, URLs communes), actions Modifier / Voir / Corbeille avec retour sur la page, filtres méthode / seuil (60 % par défaut) / période (12 mois par défaut) / statuts. Aucune suppression automatique.

### Changed
- Fin de l'audit sync : **Orphans v4 déployée en ligne (#415, active ; v3 désactivée)** — libellés en français (filtres « Orphelins uniquement / Utilisées uniquement / … », colonne « Utilisée dans », notices), « Analyser l'usage » et « Recalculer la taille » en véritables boutons WP distincts des filtres. Corbeille en ligne : `Preview 13 - v5` (#226, page disparue — fichier canonique archivé) et `Search Auto - v4` (#354). Search Auto : #277 renommé « POST 🔎 SEARCH - Auto - v4 » — le diff ligne à ligne prouve que son code était **identique** au canonique (0 ligne absente ; l'ancien « 70,8 % » était un artefact des gros en-têtes), idem pour External Links #223 renommé. En ligne : 56 snippets (44 actifs / 12 inactifs).
- Poursuite de l'audit sync : corbeille en ligne de 13 snippets supplémentaires (11 inactifs homonymes des canoniques : RAG Export #343, Super Tracker #393, Articles publiés & manqués #332, Orphans #348, Missing Featured #346, Player fullscreen #351, Menu Order Alpha #345, External Links #355, Preview 13 #361, Social Ego #358, Scroll To Top #357 + 2 Media Size v2 en double actif #228/#288) et renommage en ligne de 11 actifs vers leur nom canonique. Enquête « 3× taille de la médiathèque » : causée par les 2 Media Size + la taille intégrée d'Orphans v3 — résolue par la corbeille des 2 Media Size. Cas #277 Search Auto (code divergent à 70 %) laissé en attente de décision, #354 conservé. État final en ligne : 58 snippets (44 actifs / 14 inactifs).
- Audit sync WordPress : renommage en ligne de 4 snippets actifs vers leur nom canonique après vérification du code (`#261` Futur Menubar v4, `#403` FAB Hub Flottant v19, `#404` Close Sidebars, `#414` Duplicates Posts v5 nettoyé de son espace parasite). Suppression des 3 coquilles vides canoniques (`Active Plugins First - v2`, `TAGS - Already Existing - v3`, `Download Images - v1`) dont le code réel n'existe qu'en ligne (`#206`, `#210`, `#211` — à rapatrier). Corbeille en ligne des 26 inactifs déjà archivés localement.
- `ADMIN 🧰 DETECT - Duplicates Posts - v2` — défauts revus après premier usage : méthode « Titres seulement » (au lieu de Titres + URLs), seuil 70 % avec paliers de 5 de 70 à 100 %, période 12 mois conservée ; miniature de l'image mise en avant devant chaque titre ; clic sur un titre ouvre l'article dans un nouvel onglet pour comparaison face à face (bouton Voir retiré) ; libellé « Titres + URLs (large) » et légende explicative des méthodes. v1 conservée dans `snippets/canonical/`.
- `ADMIN 🧰 DETECT - Duplicates Posts - v3` — présentation revue : vignettes 72 px au format standard (plus d'arrondis ni de marges parasites), ordre des colonnes Titre > Statut > Date > Écart > Pourquoi > Actions, clic sur un titre ouvre l'article dans une fenêtre dédiée 1050×950 (au lieu d'un simple onglet), libellé du menu « Doublons 🔍 » (emoji à droite), nouveau filtre « Écart max » (7 jours / 1 mois / 3 mois / 6 mois) qui ne garde que les groupes dont un article est proche de sa référence. v2 conservée dans `snippets/canonical/`.
- `ADMIN 🧰 DETECT - Duplicates Posts - v4` — suppression en AJAX (endpoint `wp_ajax_clm_dup_trash`, nonce par article) : plus de rechargement ni de saut en haut de page après « Corbeille », la ligne disparaît sur place et le groupe se replie quand il reste un seul article ; actions groupées avec case à cocher par article, sélection par groupe, bouton « ⚡ Pré-cocher les doublons » (coche tout sauf la référence de chaque groupe), « Décocher tout » et « 🗑 Corbeille (n) » avec progression et bilan. Le lien « Corbeille » individuel passe aussi en AJAX (l'URL `admin-post.php` reste en repli sans JavaScript). v3 conservée dans `snippets/canonical/`.
- `ADMIN 🧰 DETECT - Duplicates Posts - v5` — groupes triés du plus petit au plus grand puis par score décroissant (les paires précises en premier, les gros groupes type faux positif statistique relégués en bas) ; option « Même jour » dans le filtre « Écart max » (écart 0 = deux articles programmés le même jour) ; dates affichées en `AAAA/MM/JJ HH:MM` ; écart recalculé en jours calendaires (deux articles d'une même journée, même à cheval sur minuit, = 0 jour). v4 conservée dans `snippets/canonical/`.
- Archivage : `ADMIN 🧰 DETECT - Duplicates Posts - v1` → `v4` déplacées dans `snippets/archive/`, seule la `v5` reste dans `snippets/canonical/`.

### Fixed
- Scripts de sync WordPress (`clean-sync.sh`, `prepare-wordpress.sh`, `sync-wordpress.sh`, `compare-wordpress-local.sh`, `compare-wp-cli.sh`) — chemins cassés depuis le déménagement du toolkit : `.agent/-pkwpsyncsnippets/` → `.agent/skills/pk/-pk-wpsyncsnippets/` pour les scripts et secrets, et les JSON d'import/deploy reviennent dans `CODE_SNIPPETS_SYNC/imports/` du dépôt. README : remplacement de `pull_active_snippets.php` (disparu) par `extract_code_snippets_export_to_files.php` (extraction depuis un export Code Snippets).

## [2026.10.1] - 2026-10-01
### Fixed
- `FRONTEND 📊 STATS - Site Stats Page - 2026.10.1` — les futures vues sont enregistrées après affichage visible dans le navigateur, sur toutes les pages publiques plutôt que sur chaque requête PHP d'article ; fenêtre anti-rechargement de 15 secondes par visiteur/page, contrôle d'origine et cookie visiteur journalier. Aucun recalcul des journées passées, aucun import Umami automatique.

## [2026.09.24] - 2026-09-24
### Added
- `ADMIN 📅 SCHEDULER - Calendar - v38` — les articles **publiés ne sont plus jamais déplacés** : le rééquilibrage déclenché après chaque drag & drop ne réécrit plus que `future`/`draft`/`pending` (les créneaux des publiés restent occupés tels quels), et les heures déjà passées du jour (11h, 12h…) redeviennent utilisables. Corrige le bug où un article publié (ex. 13h) basculait `publish` → `future` vers un créneau futur. Déployé sur mondary.design (#405).
- `ADMIN 📅 SCHEDULER - Editor Next Free Slot - v5` — construite sur le fix manuel du 22/09 (le forçage ne vise plus que les validations `publish`) et complétée : purge de la réservation `_clm_editor_reserved_slot` quand un planifié passe en publié via le cron (`future_to_publish`), garde-fou qui ignore tout article déjà publié, et endpoint + garde JS qui lèvent la réservation dès que l'utilisateur choisit lui-même sa date (ex. 11h passée) — le choix manuel prime sur le créneau auto. Déployé (#406).

### Changed
- Snippet `Default Next Date 10-14 Priority` désactivé en prod (troisième acteur horaire redondant avec l'Editor v5).
- Archivage : `Calendar v36/v37` et `Editor Next Free Slot v3/v4` déplacés dans `snippets/archive/` ; l'ancienne version en ligne recréée comme entrées inactives (#408, #409) pour retrouver l'historique.
- Le snippet `MEDIA ▶️ VIDEO - Autoplay Loop Muted - v1` rejoint le dépôt.

### Fixed
- Dépôt public : `VERSION` supprimé (le `CHANGELOG.md` fait foi), section Changelog retirée des README FR/EN (doublon, avec icône `icon.png` ajoutée en tête), et exports analytics Umami dépubliés (`store/umami-reference/` contenaient des données visiteurs brutes : sessions, géolocalisation, gclid/fbclid).

## [2026.09.08] - 2026-09-08
### Added
- `FRONTEND 🌸 FAB - Hub Flottant - v1` — un seul bouton flottant (bas droite) regroupant 8 fonctionnalités auparavant éparpillées sur les 4 coins de l'écran : Google News, Flux RSS (nouveau), Newsletter (ancre Jetpack), Diaporama articles (overlay plein écran repris de News Diaporama v3, endpoint REST conservé), Articles programmés (panneau jauge + date, repris de Scheduled Posts Popup v14 sans le sondage Patreon), Statistiques (lien `/statistiques/` + vues de l'année + compteur live), Ko-fi (nouveau, handle de Social Ego v2) et Retour en haut. Déploiement du menu en éventail « pétales » (2 rayons alternés, ressort + stagger), étiquettes, voile cliquable, ESC, `prefers-reduced-motion`. Absorbe aussi le positionnement/masquage GTranslate (ex Google News Button v3) et masque le `#kt-scroll-up` Kadence. À activer puis délier : Google News Button v3, News Diaporama v3, Scroll To Top v2.
- `FRONTEND 🌸 FAB - Hub Flottant - v4` — remplacement du menu pétales par une barre d'actions sticky, compacte sur desktop et défilable horizontalement sur mobile, avec pictogrammes Font Awesome et libellés lisibles.

### Changed
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.16` — badge « N en ligne · X vues » et dropdown supprimés (remplacés par l'entrée Statistiques du FAB Hub Flottant) : ce script alimente désormais le compteur `#fab-live-count` du hub via `window.__clmLiveN`. Curseurs live et confettis inchangés.
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.11` — versions empilées sur deux lignes alignées à droite (« Stats v… » au-dessus, « Cursor v… » en dessous).
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.10` — la page stats devient l'unique endroit où lire les versions : « Stats v2026.09.10 · Cursor vX » en haut à droite, la version du curseur étant lue dynamiquement dans la table `snippets` (snippet actif).
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.15` — plus aucune version affichée côté curseur (tooltip supprimé).
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.9` — le libellé version porte le nom du script (« Stats v2026.09.9 » en haut à droite) : plus de confusion possible avec les numéros de version du curseur live (un « 2026.09.8 » a existé des deux côtés).
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.14` — version gardée uniquement en tooltip du badge (badge et dropdown revenus sans version, trop chargés).
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.8` — `ss-version` déplacé en haut à droite du contenu (il était fixed en bas à droite, quasi illisible) : `position:absolute;top:1.25rem;right:1.5rem`, taille et contraste remontés.

### Added
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.13` — numéro de version visible sur la page : suffixe `v2026.09.13` dans le badge « N en ligne · X vues », ligne « Live Cursors v… » en pied du dropdown et tooltip du badge (parité avec le `ss-version` de la page stats).
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.12` — vrais noms quand possible : compte WP connecté > nom du cookie de commentaire (`wp_get_current_commenter()`), sinon « Anonyme N » unique assigné côté serveur (compteur persistant `clm_anon_n`, mémorisé en localStorage). Purge des anciens faux prénoms aléatoires, nom validé côté serveur à chaque heartbeat (non spoofable). Déployé sur mondary.design (snippet #391 actif, #389 v11 désactivé).

### Fixed
- `scripts/prepare-wordpress.sh` — les versions datées (`2026.09.12`) ne matchaient pas le regex ` - \d+$` et les snippets concernés étaient silencieusement exclus du JSON WordPress ; désormais versions datées groupées/triées comme les `vXX`, et snippets sans version conservés actifs.
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.7` — correction UI : alignement de la largeur du dropdown sur celle du badge déclencheur (`width: 100%`) pour une esthétique cohérente.
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.3` — correction layout graphique : augmentation de la viewBox SVG (900x350) pour empêcher le débordement horizontal des années et vertical des axes. UX : inversion de l'ordre des tabs pour rendre la vue « Jour » par défaut.
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.6` — correction : les liens du dropdown n'étaient pas cliquables à cause du gap de 6px qui cassait le survol. Ajout d'un pont transparent (`::after`) pour maintenir l'état `:hover` entre le badge et le dropdown.
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.4` — correction : le dropdown du badge « N en ligne » restait vide au survol : `updateDropdown()` était défini mais jamais appelé (version en ligne 2026.09.2). v4 unifie le rendu dans `applyCursors()` (render + count + dropdown à chaque réponse), purge les curseurs partis et envoie la page visitée. Déployé sur mondary.design (snippet #327 « LIVE - cursor »).

## [2026.09.04] - 2026-09-04
### Added
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.3` — curseurs live partagés sur tout le site (polling AJAX), confettis visibles par tous, badge « N en ligne · X vues » avec dropdown des visiteurs et de leur page, noms WP pour les connectés
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.2` — page standalone `/statistiques/` (header du site conservé), graphiques SVG style crayonné cumul/jour, navigation par année, tracking des vues en table dédiée, import CSV via Outils → Importer Stats

### Fixed
- `TOOL 📊 TRACKING - Super Tracker - 2026.09.1` — double `<?php` qui cassait tout le tracking + audit sécurité : suppression de 3 trackers suspects (datapulse.com, histogram-analytics.com, swilty.com)

## [0.10] - 2026-07-20
### Added
- Initial project scaffold
