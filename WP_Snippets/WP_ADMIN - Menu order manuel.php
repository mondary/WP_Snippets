<?php
/**
 * Script pour réorganiser les entrées du menu d'administration WordPress.
 */

add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'custom_menu_order');

function custom_menu_order($menu_ord) {
    // Définissez l'ordre souhaité des éléments de menu ici
    $new_menu_order = array(
        'index.php',          // Tableau de bord
        'edit.php',           // Tous les articles
        'upload.php',         // Média
        'edit.php?post_type=page', // Pages
        'edit-comments.php',  // Commentaires
        'themes.php',         // Apparence
        'plugins.php',        // Extensions
        'users.php',          // Utilisateurs
        'tools.php',          // Outils
        'options-general.php' // Réglages
    );

    return $new_menu_order;
}
?>
