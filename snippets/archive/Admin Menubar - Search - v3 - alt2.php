
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - Search.php
 * Display name: WP_ADMIN - Search
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (1d00d17fefa8…, 2 membres)
 * Canonical exact group ID: 93
 * Version family: DUP ADMIN - Search (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Search.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: search-ui, admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu, wp_head, admin_head
 * Fonctions clefs: ajouter_barre_recherche_admin
 * Lignes / octets (brut): 79 / 3010
 * Hash code normalise (sha256): 1d00d17fefa8407ab5dcf66c2e495938009b78686d8824f1364dbe4913e74ec7
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-search__v001__alt2.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-search__v001__alt2.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: search_ui
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_menubar=6, search_ui=5
 * Raisons principales: admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

* Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - Search.php
 * Display name: WP_ADMIN - Search
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (1d00d17fefa8…, 2 membres)
 * Canonical exact group ID: 93
 * Version family: DUP ADMIN - Search (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Search.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: search-ui, admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu, wp_head, admin_head
 * Fonctions clefs: ajouter_barre_recherche_admin
 * Lignes / octets (brut): 79 / 3010
 * Hash code normalise (sha256): 1d00d17fefa8407ab5dcf66c2e495938009b78686d8824f1364dbe4913e74ec7
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

// Ajoute une barre de recherche dans la barre de menu admin de WordPress
add_action('admin_bar_menu', 'ajouter_barre_recherche_admin', 35);

function ajouter_barre_recherche_admin($admin_bar) {
    $admin_bar->add_node(array(
        'id'    => 'recherche_admin',
        'title' => '<form role="search" method="get" id="searchform" action="' . esc_url(admin_url('edit.php')) . '" style="display: flex; align-items: center; padding: 0 8px;">
                        <input type="text" value="" name="s" id="s" placeholder="Recherche" style="width: 140px; height: 24px; margin: 2px 0; padding: 0 6px; border: none; border-radius: 3px; background: #2c3338; color: #fff; font-size: 12px; line-height: 24px;"/>
                        <button type="submit" id="searchsubmit" style="display: none;"></button>
                    </form>',
        'meta'  => array(
            'title' => 'Recherche',
        ),
    ));
}

// Ajoute un style CSS pour rendre la barre de recherche toujours visible
add_action('wp_head', function() {
    echo '<style>
        #wpadminbar #wp-admin-bar-recherche_admin {
            padding: 0;
            margin-left: 8px;
        }
        #wpadminbar #wp-admin-bar-recherche_admin:hover {
            background: none;
        }
        #wpadminbar #wp-admin-bar-recherche_admin .ab-item {
            padding: 0 !important;
            height: auto !important;
        }
        #wpadminbar #searchform input,
        #wpadminbar #searchform input:focus {
            background: #2c3338 !important;
            color: #fff !important;
            width: 140px !important;
            height: 28px !important;
            margin: 2px 0 !important;
            padding: 0 6px !important;
            border: none !important;
            border-radius: 3px !important;
            font-size: 11px !important;
            line-height: 26px !important;
            outline: none;
            box-sizing: border-box !important;
    </style>';
});

// Ajoute le même style CSS pour l'admin
add_action('admin_head', function() {
    echo '<style>
        #wpadminbar #wp-admin-bar-recherche_admin {
            padding: 0;
            margin-left: 8px;
        }
        #wpadminbar #wp-admin-bar-recherche_admin:hover {
            background: none;
        }
        #wpadminbar #wp-admin-bar-recherche_admin .ab-item {
            padding: 0 !important;
            height: auto !important;
        }
        #wpadminbar #searchform input,
        #wpadminbar #searchform input:focus {
            background: #2c3338 !important;
            color: #fff !important;
            width: 140px !important;
            height: 24px !important;
            margin: 2px 0 !important;
            padding: 0 6px !important;
            border: none !important;
            border-radius: 3px !important;
            font-size: 11px !important;
            line-height: 24px !important;
            outline: none;
            box-sizing: border-box !important;
    </style>';
});
?>
