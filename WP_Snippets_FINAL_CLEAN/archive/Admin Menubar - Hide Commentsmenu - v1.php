
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - Hide Comments Menu.php
 * Display name: WP_ADMIN - Hide Comments Menu
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (baa008d20358…, 2 membres)
 * Canonical exact group ID: 96
 * Version family: DUP ADMIN -  Hide CommentsMenu (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide Comments Menu.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, admin_bar_menu
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 30 / 847
 * Hash code normalise (sha256): baa008d20358bfa97122f26e7703353fc20f47a398ee4526a050df729868834c
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-hide-commentsmenu__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-hide-commentsmenu__v001.php
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

e: WP_ADMIN - Hide Comments Menu
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (baa008d20358…, 2 membres)
 * Canonical exact group ID: 96
 * Version family: DUP ADMIN -  Hide CommentsMenu (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide Comments Menu.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, admin_bar_menu
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 30 / 847
 * Hash code normalise (sha256): baa008d20358bfa97122f26e7703353fc20f47a398ee4526a050df729868834c
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

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