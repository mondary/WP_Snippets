# Changelog

## [2026.09.04] - 2026-09-04
### Added
- `FRONTEND 📊 CURSOR - Live Cursors - 2026.09.3` — curseurs live partagés sur tout le site (polling AJAX), confettis visibles par tous, badge « N en ligne · X vues » avec dropdown des visiteurs et de leur page, noms WP pour les connectés
- `FRONTEND 📊 STATS - Site Stats Page - 2026.09.2` — page standalone `/statistiques/` (header du site conservé), graphiques SVG style crayonné cumul/jour, navigation par année, tracking des vues en table dédiée, import CSV via Outils → Importer Stats

### Fixed
- `TOOL 📊 TRACKING - Super Tracker - 2026.09.1` — double `<?php` qui cassait tout le tracking + audit sécurité : suppression de 3 trackers suspects (datapulse.com, histogram-analytics.com, swilty.com)

## [0.10] - 2026-07-20
### Added
- Initial project scaffold
