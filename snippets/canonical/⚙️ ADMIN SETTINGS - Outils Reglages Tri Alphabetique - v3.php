/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/035__id-75__admin-outils-reglages-tri-alphabetique.php
 * Display name: ADMIN - Outils + reglages (tri alphabetique)
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 75
 * Online modified: 2025-03-07 14:38:20
 * Online revision: 14
 * Exact duplicate group: oui (da65b3aa1704…, 2 membres)
 * Canonical exact group ID: 97
 * Version family: DUP ADMIN - Outils + reglages (tri alphabetique) (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/035__id-75__admin-outils-reglages-tri-alphabetique.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: merge_tools_into_settings
 * Lignes / octets (brut): 53 / 1747
 * Hash code normalise (sha256): da65b3aa17044572130eb7838907ebd840d14e53f4fe64fdd8d0df34e6e1123e
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-outils-reglages-tri-alphabetique__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-outils-reglages-tri-alphabetique__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: admin-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: merge_tools_into_settings
 * APIs WP detectees: add_action
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 66 / 2407
 * Empreinte code (sha256): e68f737fa4b8f5a452ef53d3e9462b7d7c42d75df05d5dc3f79cebc8a2c1ac36
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-outils-reglages-tri-alphabetique__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-outils-reglages-tri-alphabetique__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_ui_settings
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_ui_settings=16
 * Raisons principales: admin-ui, settings, outils, reglages
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Plugin pour fusionner les entrées "Outils" et "Réglages" dans le menu admin WordPress
 * et trier les éléments par ordre alphabétique.
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

    // Trier les sous-menus par ordre alphabétique
    usort($submenu['options-general.php'], function($a, $b) {
        return strcmp($a[0], $b[0]);
    });

    // Supprimer complètement "Outils" du menu principal
    remove_menu_page('tools.php');
}
