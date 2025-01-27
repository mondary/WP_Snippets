<?php
/**
 * Plugin pour ajouter une page de réglages pour réorganiser les entrées du menu d'administration WordPress.
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

// Fonction pour afficher la page de réglages
function menu_order_settings_page() {
    // Vérification des permissions
    if (!current_user_can('manage_options')) {
        return;
    }

    // Enregistrement des options
    if (isset($_POST['menu_order_option'])) {
        update_option('menu_order_option', $_POST['menu_order_option']);
        echo '<div class="updated"><p>Ordre du menu mis à jour.</p></div>';
    }

    // Récupération de l'option actuelle
    $menu_order_option = get_option('menu_order_option', 'normal'); // 'normal' par défaut

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
                    <input type="radio" name="menu_order_option" value="plugins" <?php checked($menu_order_option, 'plugins'); ?> />
                    Afficher uniquement les extensions par ordre alphabétique
                </label>
            </p>
            <input type="submit" class="button button-primary" value="Enregistrer l'Ordre" />
        </form>
    </div>
    <?php
}

// Filtrer l'ordre du menu
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'custom_menu_order');

function custom_menu_order($menu_ord) {
    $menu_order_option = get_option('menu_order_option', 'normal');

    if ($menu_order_option === 'alphabetical') {
        // Tri par ordre alphabétique
        usort($menu_ord, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });
        return $menu_ord;
    }

    if ($menu_order_option === 'plugins') {
        // Si l'option "Afficher uniquement les extensions" est sélectionnée
        $plugins_menu = [];
        foreach ($menu as $item) {
            if (strpos($item[2], 'plugins') !== false) {
                $plugins_menu[] = $item;
            }
        }
        usort($plugins_menu, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });

        // Retourne le menu par défaut suivi des plugins triés
        return array_merge($menu_ord, ['separator'], $plugins_menu);
    }

    return $menu_ord; // Retourne l'ordre par défaut si aucune option n'est définie
}
?>