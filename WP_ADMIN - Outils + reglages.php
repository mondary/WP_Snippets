<?php
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
