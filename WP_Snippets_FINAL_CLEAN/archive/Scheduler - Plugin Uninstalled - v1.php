/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/022__id-40__plugin-uninstalled.php
 * Display name: PLUGIN - uninstalled
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 40
 * Online modified: 2025-01-17 15:01:55
 * Online revision: 8
 * Exact duplicate group: non
 * Version family: PLUGIN - uninstalled (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/022__id-40__plugin-uninstalled.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: deleted_plugin, admin_menu
 * Fonctions clefs: enregistrer_extension_supprimee, ajouter_onglet_extensions_supprimees, afficher_extensions_supprimees
 * Lignes / octets (brut): 93 / 3239
 * Hash code normalise (sha256): 3d091d0956effed89edb9ac24ebc6c5ebbc5663abfc04f0f4c9a7f3f25ff0de5
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__plugin-uninstalled__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__plugin-uninstalled__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, automatisation date/programmation, 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: admin-ui, scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: deleted_plugin, admin_menu
 * Fonctions clefs: enregistrer_extension_supprimee, ajouter_onglet_extensions_supprimees, afficher_extensions_supprimees
 * APIs WP detectees: is_admin, add_action, get_plugins, get_option, add_submenu_page
 * Signatures contenu: html-markup
 * Lignes / octets: 106 / 3901
 * Empreinte code (sha256): e13f91fcf59933f99f6ede7771bcc3c67b526cda3fb8e6d87867c44883998027
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__plugin-uninstalled__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__plugin-uninstalled__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: admin_ui_settings
 * Domaine: global
 * Confiance: medium
 * Scores (top): scheduler_posts=8, admin_ui_settings=4
 * Raisons principales: scheduler-date, schedule
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Initialiser les actions uniquement si nous sommes dans l'interface admin
if (is_admin()) {
    // Surveiller la suppression des extensions
    add_action('deleted_plugin', 'enregistrer_extension_supprimee');

    // Ajouter un onglet pour afficher les extensions supprimées
    add_action('admin_menu', 'ajouter_onglet_extensions_supprimees');
}

// Fonction pour enregistrer les extensions supprimées
function enregistrer_extension_supprimee($plugin_file) {
    $toutes_les_extensions = get_plugins();
    $plugin_details = isset($toutes_les_extensions[$plugin_file]) ? $toutes_les_extensions[$plugin_file] : null;

    if ($plugin_details) {
        $extensions_supprimees = get_option('extensions_supprimees', []);

        $extensions_supprimees[] = [
            'nom' => $plugin_details['Name'],
            'description' => $plugin_details['Description'],
            'version' => $plugin_details['Version'],
            'auteur' => $plugin_details['Author'],
            'date' => current_time('mysql'),
        ];

        update_option('extensions_supprimees', $extensions_supprimees);
    }
}

// Fonction pour ajouter l'onglet
function ajouter_onglet_extensions_supprimees() {
    add_submenu_page(
        'plugins.php',
        'Log des Extensions Supprimées',
        'Extensions Supprimées',
        'manage_options',
        'extensions-supprimees',
        'afficher_extensions_supprimees'
    );
}

// Fonction pour afficher les extensions supprimées
function afficher_extensions_supprimees() {
    $extensions_supprimees = get_option('extensions_supprimees', []);

    echo '<div class="wrap">';
    echo '<h1 style="color:#0073aa;">Log des Extensions Supprimées</h1>';
    echo '<p>Voici une liste des extensions supprimées, enregistrées depuis l’installation de ce snippet.</p>';

    if (empty($extensions_supprimees)) {
        echo '<p>Aucune extension supprimée n’a été enregistrée pour le moment.</p>';
    } else {
        echo '<table class="widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Nom</th>';
        echo '<th>Description</th>';
        echo '<th>Version</th>';
        echo '<th>Auteur</th>';
        echo '<th>Date de Suppression</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($extensions_supprimees as $extension) {
            echo '<tr>';
            echo '<td>' . esc_html($extension['nom']) . '</td>';
            echo '<td>' . esc_html($extension['description']) . '</td>';
            echo '<td>' . esc_html($extension['version']) . '</td>';
            echo '<td>' . esc_html($extension['auteur']) . '</td>';
            echo '<td>' . esc_html($extension['date']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }

    echo '</div>';
}
