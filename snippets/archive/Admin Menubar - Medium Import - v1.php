/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/041__id-94__wp-medium-import.php
 * Display name: WP - Medium import
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 94
 * Online modified: 2025-03-20 15:53:44
 * Online revision: 2
 * Exact duplicate group: non
 * Version family: WP - Medium import (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/041__id-94__wp-medium-import.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu, admin_bar_menu
 * Fonctions clefs: import_to_medium_menu_page
 * Lignes / octets (brut): 50 / 1591
 * Hash code normalise (sha256): f3598aff17a4e34574f7969f4fe20d5479f892df9a1b47e58cf7b16da4910aeb
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__wp-medium-import__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__wp-medium-import__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, automatisation date/programmation, 2 hook(s) WP, 1 fonction(s) clef
 * Features detectees: admin-menubar, admin-ui, scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu, admin_bar_menu
 * Fonctions clefs: import_to_medium_menu_page
 * APIs WP detectees: add_action, add_menu_page, is_single, add_node, admin_url
 * Signatures contenu: html-markup
 * Lignes / octets: 63 / 2170
 * Empreinte code (sha256): 24b89357af37b23c46946289b072cbfadce3ffea593cc4d214df5110ca0da26a
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__wp-medium-import__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__wp-medium-import__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=18, scheduler_posts=8, admin_ui_settings=4
 * Raisons principales: admin-menubar, menubar, admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Plugin Name: Import to Medium Menu
 * Description: Ajoute un lien dans la barre d'administration pour importer un article sur Medium.
 * Version: 1.0
 * Author: Clement Mondary
 */

add_action('admin_menu', function() {
    add_menu_page(
        'Importer sur Medium', // Titre de la page
        'Importer sur Medium', // Titre du menu
        'manage_options', // Capacité requise
        'import-to-medium', // Slug de la page
        'import_to_medium_menu_page' // Fonction de rappel
    );
});

function import_to_medium_menu_page() {
    if (isset($_GET['post_id'])) {
        $post_id = intval($_GET['post_id']);
        import_post_to_medium($post_id);
        echo '<div class="updated"><p>Article importé sur Medium avec succès!</p></div>';
    } else {
        echo '<div class="error"><p>Aucun article sélectionné.</p></div>';
    }
}

add_action('admin_bar_menu', function($wp_admin_bar) {
    if (is_single()) {
        global $post;
        $wp_admin_bar->add_node(array(
            'id' => 'import-to-medium',
            'title' => 'Importer sur Medium',
            'href' => admin_url('admin.php?page=import-to-medium&post_id=' . $post->ID)
        ));
    }
}, 100);