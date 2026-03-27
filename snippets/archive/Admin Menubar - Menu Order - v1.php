/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/040__id-93__admin-menu-order.php
 * Display name: ADMIN - Menu order
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 93
 * Online modified: 2025-03-13 16:29:08
 * Online revision: 5
 * Exact duplicate group: non
 * Version family: ADMIN - Menu order (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/040__id-93__admin-menu-order.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: search-ui, admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: custom_reorder_admin_bar_menu
 * Lignes / octets (brut): 47 / 1332
 * Hash code normalise (sha256): 5879e4c35889f24ff35be9935c32890ef872baf4a21345f6602050db448bbffa
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-menu-order__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-menu-order__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: interface de recherche, automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: admin-menubar, search-ui, scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: custom_reorder_admin_bar_menu
 * APIs WP detectees: add_action, get_nodes, add_node
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 59 / 1887
 * Empreinte code (sha256): 5338a3407320169479629d44defded191d65b6d8730dd66de571aa6b2aad1356
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-menu-order__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-menu-order__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: search_ui
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=18, search_ui=10, scheduler_posts=8
 * Raisons principales: admin-menubar, menubar, admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Reorder WordPress admin bar menu items
 * Description: This script reorders specific menu items in the WordPress admin bar
 * Version: 1.0
 */

add_action('admin_bar_menu', 'custom_reorder_admin_bar_menu', 999);

function custom_reorder_admin_bar_menu($wp_admin_bar) {
    // Get all existing nodes
    $nodes = $wp_admin_bar->get_nodes();

    // Define the desired order with priorities
    $order = [
        'searchform' => 10,
        'wp-statistic-menu' => 20,
        'iawp_admin_bar_button' => 30,
        'google-site-kit' => 40,
        'customize' => 50,
        'new-content' => 60,
        'edit' => 70,
        'wpcode_snippets' => 80
    ];

    // Update priorities for each node
    foreach ($order as $id => $priority) {
        if (isset($nodes[$id])) {
            $node = $nodes[$id];
            $node->priority = $priority;
            $wp_admin_bar->add_node($node);
        }
    }
}
