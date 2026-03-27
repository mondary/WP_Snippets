/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/082__id-142__tracker-lytlix.php
 * Display name: TRACKER - Lytlix
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 142
 * Online modified: 2025-09-26 16:58:35
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: TRACKER - Lytlix (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/082__id-142__tracker-lytlix.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_litlyx_script
 * Lignes / octets (brut): 18 / 625
 * Hash code normalise (sha256): b8d225f46314c44c08d7e5caaa98e2e7f384eecd45b94f500f200ea120947f90
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: tracker-lytlix__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/tracker-lytlix__v001.php
 * Resume fonctionnalites: tracking / analytics, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: tracking-analytics, css-ui, footer-head-injection
 * Dependances probables: Service analytics externe
 * Hooks WP: wp_head
 * Fonctions clefs: inject_litlyx_script
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 31 / 1179
 * Empreinte code (sha256): 8ef6d3e0f2c8d04d18832938e309b288a54b5cc5d79c78db35d90bb4e33e7058
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: tracker-lytlix__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/tracker-lytlix__v001.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: aucun
 * Domaine: tracking
 * Confiance: high
 * Scores (top): tracking_analytics=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: tracker, analytics, lytlix
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function inject_litlyx_script() {
    echo '<script defer data-workspace="68d64f57d549f0b9f9874518" 
    src="https://cdn.jsdelivr.net/npm/litlyx-js@latest/browser/litlyx.js"></script>';
}
add_action("wp_head", "inject_litlyx_script");
