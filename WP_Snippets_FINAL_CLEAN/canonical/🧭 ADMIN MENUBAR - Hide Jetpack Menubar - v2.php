/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/039__id-92__admin-hide-jetpack-menubar.php
 * Display name: ADMIN - Hide Jetpack menubar
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 92
 * Online modified: 2025-03-13 16:12:10
 * Online revision: 3
 * Exact duplicate group: non
 * Version family: ADMIN - Hide Jetpack menubar (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/039__id-92__admin-hide-jetpack-menubar.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: jetpack, admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_jetpack_stats_styles, remove_jetpack_stats_menu
 * Lignes / octets (brut): 40 / 1373
 * Hash code normalise (sha256): 46b5486b6b66fbdc8e8262e402f1f4a94c28d074c6e456f9c03a5a939719dfe0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-hide-jetpack-menubar__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-hide-jetpack-menubar__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 3 hook(s) WP, 2 fonction(s) clef
 * Features detectees: admin-menubar, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_jetpack_stats_styles, remove_jetpack_stats_menu
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 52 / 1977
 * Empreinte code (sha256): 6225a6cbd0e68bf186afedb75d79a7490deaaa09629b1b548857ced859e18f49
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-hide-jetpack-menubar__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-hide-jetpack-menubar__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-menubar, menubar, admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Hide Jetpack stats from WordPress admin bar (both frontend and backend)
 * Description: This script removes the Jetpack stats menu item from the WordPress admin bar everywhere
 * Version: 1.0
 */

// Add CSS to both admin and frontend
add_action('admin_head', 'hide_jetpack_stats_styles');
add_action('wp_head', 'hide_jetpack_stats_styles');

function hide_jetpack_stats_styles() {
    echo '<style>
        /* Hide Jetpack stats menu item from admin bar */
        #wp-admin-bar-jetpack-stats, #wp-admin-bar-stats {
            display: none !important;
        }
    </style>';
}

// Remove the menu item from the admin bar programmatically (both frontend and backend)
add_action('admin_bar_menu', 'remove_jetpack_stats_menu', 999);

function remove_jetpack_stats_menu($wp_admin_bar) {
    $wp_admin_bar->remove_node('jetpack-stats');
    $wp_admin_bar->remove_node('stats');
}
