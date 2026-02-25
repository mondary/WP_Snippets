
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - Hide AllInOneSEO.php
 * Display name: WP_ADMIN - Hide AllInOneSEO
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (67887017f371…, 2 membres)
 * Canonical exact group ID: 130
 * Version family: DUP ADMIN - Hide AllineoneSEO (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide AllInOneSEO.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_aioseo_styles, remove_aioseo_menu
 * Lignes / octets (brut): 31 / 958
 * Hash code normalise (sha256): 67887017f371954796f05b899337de45bed9312be96058b6751cee27712ea685
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-hide-allineoneseo__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-hide-allineoneseo__v001.php
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

neSEO.php
 * Display name: WP_ADMIN - Hide AllInOneSEO
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (67887017f371…, 2 membres)
 * Canonical exact group ID: 130
 * Version family: DUP ADMIN - Hide AllineoneSEO (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide AllInOneSEO.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_aioseo_styles, remove_aioseo_menu
 * Lignes / octets (brut): 31 / 958
 * Hash code normalise (sha256): 67887017f371954796f05b899337de45bed9312be96058b6751cee27712ea685
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Hide All in One SEO menu from WordPress admin bar (both frontend and backend)
 * Description: This script removes the All in One SEO menu item from the WordPress admin bar everywhere
 * Version: 1.1
 */

// Add CSS to both admin and frontend
add_action('admin_head', 'hide_aioseo_styles');
add_action('wp_head', 'hide_aioseo_styles');

function hide_aioseo_styles() {
    echo '<style>
        /* Hide All in One SEO menu item from admin bar */
        #wp-admin-bar-aioseo-main {
            display: none !important;
        }
        
        /* Hide All in One SEO submenu items if they exist */
        .wp-submenu a[href*="aioseo"] {
            display: none !important;
        }
    </style>';
}

// Remove the menu item from the admin bar programmatically (both frontend and backend)
add_action('admin_bar_menu', 'remove_aioseo_menu', 999);

function remove_aioseo_menu($wp_admin_bar) {
    $wp_admin_bar->remove_node('aioseo-main');
}