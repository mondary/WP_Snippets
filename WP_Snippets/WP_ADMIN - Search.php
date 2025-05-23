<?php
// Ajoute une barre de recherche dans la barre de menu admin de WordPress
add_action('admin_bar_menu', 'ajouter_barre_recherche_admin', 35);

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
add_action('wp_head', function() {
    echo '<style>
        #wpadminbar #wp-admin-bar-recherche_admin {
            padding: 0;
            margin-left: 8px;
        }
        #wpadminbar #wp-admin-bar-recherche_admin:hover {
            background: none;
        }
        #wpadminbar #wp-admin-bar-recherche_admin .ab-item {
            padding: 0 !important;
            height: auto !important;
        }
        #wpadminbar #searchform input,
        #wpadminbar #searchform input:focus {
            background: #2c3338 !important;
            color: #fff !important;
            width: 140px !important;
            height: 28px !important;
            margin: 2px 0 !important;
            padding: 0 6px !important;
            border: none !important;
            border-radius: 3px !important;
            font-size: 11px !important;
            line-height: 26px !important;
            outline: none;
            box-sizing: border-box !important;
    </style>';
});

// Ajoute le même style CSS pour l'admin
add_action('admin_head', function() {
    echo '<style>
        #wpadminbar #wp-admin-bar-recherche_admin {
            padding: 0;
            margin-left: 8px;
        }
        #wpadminbar #wp-admin-bar-recherche_admin:hover {
            background: none;
        }
        #wpadminbar #wp-admin-bar-recherche_admin .ab-item {
            padding: 0 !important;
            height: auto !important;
        }
        #wpadminbar #searchform input,
        #wpadminbar #searchform input:focus {
            background: #2c3338 !important;
            color: #fff !important;
            width: 140px !important;
            height: 24px !important;
            margin: 2px 0 !important;
            padding: 0 6px !important;
            border: none !important;
            border-radius: 3px !important;
            font-size: 11px !important;
            line-height: 24px !important;
            outline: none;
            box-sizing: border-box !important;
    </style>';
});
?>
