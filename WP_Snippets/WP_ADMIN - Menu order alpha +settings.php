<?php
/**
 * Plugin pour trier le menu admin WordPress avec options et gestion des extensions.
 */

// Ajouter une entrée dans le menu Réglages
add_action('admin_menu', function() {
    add_options_page(
        'Ordre du Menu', // Titre de la page
        'Ordre du Menu', // Titre du menu
        'manage_options', // Capacité requise
        'menu-order-settings', // Slug de la page
        'menu_order_settings_page' // Fonction de rappel
    );
});

// Affichage de la page de réglages
function menu_order_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['menu_order_option'])) {
        update_option('menu_order_option', sanitize_text_field($_POST['menu_order_option']));
        echo '<div class="updated"><p>Ordre du menu mis à jour.</p></div>';
    }

    $menu_order_option = get_option('menu_order_option', 'normal');

    ?>
    <div class="wrap">
        <h1>Réorganiser les Entrées du Menu</h1>
        <form method="post" action="">
            <h2>Options :</h2>
            <p>
                <label>
                    <input type="radio" name="menu_order_option" value="normal" <?php checked($menu_order_option, 'normal'); ?> />
                    Afficher le menu normalement
                </label>
            </p>
            <p>
                <label>
                    <input type="radio" name="menu_order_option" value="alphabetical" <?php checked($menu_order_option, 'alphabetical'); ?> />
                    Afficher le menu par ordre alphabétique
                </label>
            </p>
            <p>
                <label>
                    <input type="radio" name="menu_order_option" value="extensions" <?php checked($menu_order_option, 'extensions'); ?> />
                    Afficher les extensions triées après le menu WordPress par défaut
                </label>
            </p>
            <input type="submit" class="button button-primary" value="Enregistrer l'Ordre" />
        </form>
    </div>
    <?php
}

// Appliquer le tri du menu en fonction des réglages
add_action('admin_menu', 'custom_menu_order', 999);

function custom_menu_order() {
    global $menu;

    $menu_order_option = get_option('menu_order_option', 'normal');

    // Liste des entrées de base WordPress
    $core_menus = [
        'index.php',            // Tableau de bord
        'edit.php',             // Articles
        'upload.php',           // Médias
        'edit.php?post_type=page', // Pages
        'edit-comments.php',    // Commentaires
        'themes.php',           // Apparence
        'plugins.php',          // Extensions
        'users.php',            // Utilisateurs
        'tools.php',            // Outils
        'options-general.php',  // Réglages
    ];

    if ($menu_order_option === 'alphabetical') {
        // Tri alphabétique de tout le menu
        usort($menu, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });
    }

    if ($menu_order_option === 'extensions') {
        // Séparer les entrées de base et les extensions
        $default_menu = [];
        $extensions_menu = [];

        foreach ($menu as $item) {
            if (!empty($item[2]) && in_array($item[2], $core_menus)) {
                $default_menu[] = $item; // Menu de base
            } else {
                $extensions_menu[] = $item; // Extensions
            }
        }

        // Trier les extensions par ordre alphabétique
        usort($extensions_menu, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });

        // Ajouter un séparateur avant les extensions
        $default_menu[] = ['---', 'read', 'separator'];

        // Fusionner les deux parties
        $menu = array_merge($default_menu, $extensions_menu);
    }
}
?>
