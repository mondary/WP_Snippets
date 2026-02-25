
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - Hide Jetpack stats.php
 * Display name: WP_ADMIN - Hide Jetpack stats
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Hide Jetpack stats (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide Jetpack stats.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: jetpack, admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_jetpack_stats_styles, remove_jetpack_stats_menu
 * Lignes / octets (brut): 28 / 939
 * Hash code normalise (sha256): 17d4f46a9d2ad769346d95a6a9b935c0c139fbf88c734884a678604f0064e6b0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-hide-jetpack-stats__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-hide-jetpack-stats__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_menubar=6
 * Raisons principales: admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

* Source path: WP_Snippets/WP_ADMIN - Hide Jetpack stats.php
 * Display name: WP_ADMIN - Hide Jetpack stats
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Hide Jetpack stats (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide Jetpack stats.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: jetpack, admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_jetpack_stats_styles, remove_jetpack_stats_menu
 * Lignes / octets (brut): 28 / 939
 * Hash code normalise (sha256): 17d4f46a9d2ad769346d95a6a9b935c0c139fbf88c734884a678604f0064e6b0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

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
        #wp-admin-bar-wp-statistic-menu > .ab-item, #wp-admin-bar-jetpack-stats, #wp-admin-bar-stats {
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
?>