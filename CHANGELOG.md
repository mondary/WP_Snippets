# Changelog

## [2026.09.08] - 2026-09-08
### Fixed
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.3` — correction layout graphique : augmentation de la viewBox SVG (900x350) pour empêcher le débordement horizontal des années et vertical des axes. UX : inversion de l'ordre des tabs pour rendre la vue « Jour » par défaut.
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.5` — correction : les liens du dropdown n'étaient pas cliquables à cause du gap de 6px qui cassait le survol. Ajout d'un pont transparent (`::after`) pour maintenir l'état `:hover` entre le badge et le dropdown.
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
