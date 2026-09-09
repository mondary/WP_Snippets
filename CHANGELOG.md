# Changelog

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
