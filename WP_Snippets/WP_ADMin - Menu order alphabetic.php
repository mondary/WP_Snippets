<?php
/**
 * Plugin Name: Trier le menu admin WordPress par ordre alphabétique
 * Description: Ce script trie les entrées du menu admin WordPress par ordre alphabétique.
 * Author: Clément
 * Version: 1.0
 */

add_action('admin_menu', 'trier_menu_admin', 999);

function trier_menu_admin() {
    global $menu;

    // Vérifie si le menu existe
    if (is_array($menu)) {
        // Trie le menu par ordre alphabétique en utilisant le titre des éléments
        usort($menu, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });
    }
}
