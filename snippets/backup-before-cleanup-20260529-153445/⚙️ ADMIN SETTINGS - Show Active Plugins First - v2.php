/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/admin/008__id-13__admin-show-active-plugins-first.php
 * Display name: ADMIN - Show active plugins first
 * Scope: admin
 * Online snippet: oui
 * Online active: oui
 * Online ID: 13
 * Online modified: 2024-12-06 15:55:09
 * Online revision: 3
 * Exact duplicate group: oui (5bc010cb8f1f…, 2 membres)
 * Canonical exact group ID: 77
 * Version family: DUP ADMIN - Show active plugins first (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/admin/008__id-13__admin-show-active-plugins-first.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: views_plugins
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 46 / 1464
 * Hash code normalise (sha256): 5bc010cb8f1f09f49681f18deef4814d6299a5faff81f5c2d238c5823736a56a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__admin__admin-show-active-plugins-first__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-show-active-plugins-first__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: 1 hook(s) WP
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: views_plugins
 * Fonctions clefs: aucun
 * APIs WP detectees: add_filter, is_network_admin, get_option
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 59 / 2028
 * Empreinte code (sha256): a579ab5bf8d1717e60899923fbf28b32d9aa8a43a929e65bf0e586b113cf9f35
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__admin__admin-show-active-plugins-first__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-show-active-plugins-first__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_ui_settings
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: low
 * Scores (top): admin_ui_settings=4
 * Raisons principales: plugins
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Trie la liste des plugins pour afficher d'abord les plugins actifs.
 */

add_filter('views_plugins', function($views) {
    global $wp_list_table;

    if (!is_network_admin() && isset($wp_list_table->items)) {
        $all_plugins = $wp_list_table->items;
        $active_plugins = get_option('active_plugins');

        $reordered_plugins = array();

        // Add active plugins first
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            if (in_array($plugin_file, $active_plugins)) {
                $reordered_plugins[$plugin_file] = $plugin_data;
            }
        }

        // Add inactive plugins
        foreach ($all_plugins as $plugin_file => $plugin_data) {
            if (!in_array($plugin_file, $active_plugins)) {
                $reordered_plugins[$plugin_file] = $plugin_data;
            }
        }

        $wp_list_table->items = $reordered_plugins;
    }

    return $views;
});