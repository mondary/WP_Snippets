<?php
// Ajoute une barre de recherche dans la barre de menu admin de WordPress
add_action('admin_bar_menu', 'ajouter_barre_recherche_admin', 999);

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
add_action('admin_head', function() {
    echo '<style>
        #wpadminbar #wp-admin-bar-recherche_admin {
            padding: 0;
            float: right;
            margin-right: 8px;
        }
        #wpadminbar #wp-admin-bar-recherche_admin:hover {
            background: none;
        }
        #wpadminbar #searchform input:focus {
            background: #2c3338;
            color: #fff;
            box-shadow: 0 0 0 1px #2271b1, 0 0 2px 1px rgba(30,140,190,.8);
            outline: none;
        }
    </style>';
});
?>
