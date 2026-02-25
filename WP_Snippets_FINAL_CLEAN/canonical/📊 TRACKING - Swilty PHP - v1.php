/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/046__id-103__tracker-swilty-php.php
 * Display name: TRACKER - Swilty (php)
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 103
 * Online modified: 2025-05-05 09:37:16
 * Online revision: 4
 * Exact duplicate group: non
 * Version family: TRACKER - Swilty (php) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/046__id-103__tracker-swilty-php.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: ajouter_swilty_pixel_code
 * Lignes / octets (brut): 19 / 691
 * Hash code normalise (sha256): af7d5706d3ea1501517d1eb5e3a382e3b89c1aff1d9749ea98710c65601242f6
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__tracker-swilty-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-swilty-php__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: tracking-analytics, css-ui, footer-head-injection
 * Dependances probables: Service analytics externe
 * Hooks WP: wp_head
 * Fonctions clefs: ajouter_swilty_pixel_code
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 32 / 1285
 * Empreinte code (sha256): 0cf0d8ebb213e56fd51a2d1b9d220eda2847a8a96346371f3de366c91021d4d4
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__tracker-swilty-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-swilty-php__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: aucun
 * Domaine: tracking
 * Confiance: high
 * Scores (top): tracking_analytics=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: tracker, analytics, swilty
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function ajouter_swilty_pixel_code() {
    echo "\n<!-- Swilty Pixel Code for https://swilty.com/ -->\n";
    echo '<script defer src="https://swilty.com/pixel/g7n1qazlbK4Scr88"></script>' . "\n";
    echo "<!-- END Swilty Pixel Code -->\n";
}
add_action('wp_head', 'ajouter_swilty_pixel_code');
