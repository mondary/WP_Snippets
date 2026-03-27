
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_ADMIN - Outils + reglages.php
 * Display name: WP_ADMIN - Outils + reglages
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Outils + reglages (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Outils + reglages.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: merge_tools_into_settings
 * Lignes / octets (brut): 34 / 1055
 * Hash code normalise (sha256): d279500c27c28c241ad44a05e3e0f09658f55e6a2ab4096d20bcecb27c8d7750
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-outils-reglages__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-outils-reglages__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_ui_settings
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_ui_settings=12
 * Raisons principales: settings, outils, reglages
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

DMIN - Outils + reglages.php
 * Display name: WP_ADMIN - Outils + reglages
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Outils + reglages (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Outils + reglages.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: merge_tools_into_settings
 * Lignes / octets (brut): 34 / 1055
 * Hash code normalise (sha256): d279500c27c28c241ad44a05e3e0f09658f55e6a2ab4096d20bcecb27c8d7750
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Plugin pour fusionner les entrées "Outils" et "Réglages" dans le menu admin WordPress.
 */

add_action('admin_menu', 'merge_tools_into_settings', 999);

function merge_tools_into_settings() {
    global $submenu;

    // Vérifier si "Outils" et "Réglages" existent dans le menu
    if (!isset($submenu['tools.php']) || !isset($submenu['options-general.php'])) {
        return;
    }

    // Récupérer toutes les sous-entrées de "Outils"
    $tools_submenu = $submenu['tools.php'];

    // Ajouter "Outils" lui-même en tant qu'entrée sous "Réglages"
    $tools_main_item = [
        'Outils',        // Titre
        'manage_options', // Capacité requise
        'tools.php',      // URL de redirection
    ];
    $submenu['options-general.php'][] = $tools_main_item;

    // Ajouter toutes les sous-entrées de "Outils" sous "Réglages"
    foreach ($tools_submenu as $item) {
        $submenu['options-general.php'][] = $item;
    }

    // Supprimer complètement "Outils" du menu principal
    remove_menu_page('tools.php');
}
