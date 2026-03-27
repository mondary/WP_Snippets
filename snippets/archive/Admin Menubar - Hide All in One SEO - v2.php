/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/033__id-73__admin-hide-allineoneseo.php
 * Display name: ADMIN - Hide AllineoneSEO
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 73
 * Online modified: 2025-03-07 13:57:06
 * Online revision: 4
 * Exact duplicate group: oui (67887017f371…, 2 membres)
 * Canonical exact group ID: 130
 * Version family: DUP ADMIN - Hide AllineoneSEO (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/033__id-73__admin-hide-allineoneseo.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_aioseo_styles, remove_aioseo_menu
 * Lignes / octets (brut): 44 / 1445
 * Hash code normalise (sha256): 67887017f371954796f05b899337de45bed9312be96058b6751cee27712ea685
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-hide-allineoneseo__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-allineoneseo__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 3 hook(s) WP, 2 fonction(s) clef
 * Features detectees: admin-menubar, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, wp_head, admin_bar_menu
 * Fonctions clefs: hide_aioseo_styles, remove_aioseo_menu
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 57 / 2062
 * Empreinte code (sha256): b09130b07aa8de23290b12fa88ed1da64d4485d3bd14622a453ca44dd9ad9826
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-hide-allineoneseo__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-allineoneseo__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-menubar, menubar, admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

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