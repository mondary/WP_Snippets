# Changelog

## [2026.10.8] - 2026-10-08
### Added
- `ADMIN 🧰 DETECT - Duplicates Posts - v1` — page « 🔍 Doublons » sous Articles : détecte les articles traitant du même sujet par similarité de titre (tokens normalisés FR : accents, pluriels et mots vides ignorés) et/ou URLs externes communes du contenu (query strings normalisées, liens internes exclus, URLs omniprésentes écartées), regroupe paires et triplons (composantes connexes) avec écart de dates et « pourquoi » (score titre, URLs communes), actions Modifier / Voir / Corbeille avec retour sur la page, filtres méthode / seuil (60 % par défaut) / période (12 mois par défaut) / statuts. Aucune suppression automatique.

### Changed
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
