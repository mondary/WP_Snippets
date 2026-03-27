/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/034__id-74__admin-hide-commentsmenu.php
 * Display name: ADMIN -  Hide CommentsMenu
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 74
 * Online modified: 2025-03-13 16:12:25
 * Online revision: 4
 * Exact duplicate group: oui (baa008d20358…, 2 membres)
 * Canonical exact group ID: 96
 * Version family: DUP ADMIN -  Hide CommentsMenu (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/034__id-74__admin-hide-commentsmenu.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, admin_bar_menu
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 43 / 1334
 * Hash code normalise (sha256): baa008d20358bfa97122f26e7703353fc20f47a398ee4526a050df729868834c
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-hide-commentsmenu__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-hide-commentsmenu__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 2 hook(s) WP
 * Features detectees: admin-menubar, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, admin_bar_menu
 * Fonctions clefs: aucun
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 56 / 1931
 * Empreinte code (sha256): dc02c620dc9928001b5ca4ec85509b4e4f1de46b4da08ed93fe35835a04be33d
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-hide-commentsmenu__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-hide-commentsmenu__v2__src-wp_snippets_online_current.php
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
 * Hide WordPress Comments Menu from Admin Bar
 * Description: This script removes the Comments menu item from the WordPress admin bar
 * Version: 1.0
 */

add_action('admin_head', function() {
    echo '<style>
        /* Hide Comments menu item from admin bar */
        #wp-admin-bar-comments {
            display: none !important;
        }
        
        /* Hide Comments menu item from admin sidebar */
        #menu-comments {
            display: none !important;
        }
        
        /* Hide Comments submenu items if they exist */
        .wp-submenu a[href*="edit-comments"] {
            display: none !important;
        }
    </style>';
});

// Also remove the menu item from the admin bar programmatically
add_action('admin_bar_menu', function($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
}, 999);