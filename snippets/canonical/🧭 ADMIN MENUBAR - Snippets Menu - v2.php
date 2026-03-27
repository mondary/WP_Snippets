/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/031__id-58__admin-snippets-menu.php
 * Display name: ADMIN - Snippets Menu
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 58
 * Online modified: 2025-03-07 14:42:01
 * Online revision: 7
 * Exact duplicate group: non
 * Version family: ADMIN - Snippets Menu (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/031__id-58__admin-snippets-menu.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 29 / 994
 * Hash code normalise (sha256): 1fc5f41a410cc5140435792a99672e3b94c52d55c1d3e146979fdcd55b41754a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-snippets-menu__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-snippets-menu__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: 1 hook(s) WP
 * Features detectees: admin-menubar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: aucun
 * APIs WP detectees: add_action, add_node, admin_url
 * Signatures contenu: html-markup
 * Lignes / octets: 41 / 1467
 * Empreinte code (sha256): 7c5829b3e70ed4ff889faed33a8189c42fc25f915cb9108bae29a065dcd8cc52
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-snippets-menu__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-snippets-menu__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=18, admin_ui_settings=4
 * Raisons principales: admin-menubar, menubar, admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Ajoute un bouton avec une icône de ciseaux dans la barre d'administration
 * menant vers la page des snippets WPCode
 */

add_action('admin_bar_menu', function($admin_bar) {
    $admin_bar->add_node([
        'id'    => 'wpcode_snippets',
        'title' => '<span class="ab-icon dashicons dashicons-editor-code"></span>',
        'href'  => admin_url('admin.php?page=snippets&status=active'),
        'meta'  => [
            'title' => 'Accéder aux snippets',
        ],
    ]);
}, 100);
