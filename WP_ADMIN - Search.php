<?php
// Ajoute une barre de recherche dans la barre de menu admin de WordPress
add_action('admin_bar_menu', 'ajouter_barre_recherche_admin', 100);

function ajouter_barre_recherche_admin($admin_bar) {
    $admin_bar->add_node(array(
        'id'    => 'recherche_admin',
        'title' => '<form role="search" method="get" id="searchform" action="' . esc_url(admin_url('edit.php')) . '" style="display: flex; align-items: center; background-color: #333; padding: 5px; border-radius: 5px;">
                        <input type="text" value="" name="s" id="s" placeholder="Recherche" style="width: 80%; height: 16px; border: none; border-radius: 5px; padding: 0; margin-right: 5px; font-size: 12px; line-height: 16px;"/>
                        <button type="submit" id="searchsubmit" style="border: none; background: none; cursor: pointer;">
                            <i class="fas fa-search" style="font-size: 16px; color: white;"></i>
                        </button>
                    </form>',
        'meta'  => array(
            'title' => 'Recherche',
        ),
    ));
}

// Ajoute un style CSS pour rendre la barre de recherche toujours visible
add_action('wp_head', function() {
    echo '<style>
        #wpadminbar {
            position: fixed !important;
            top: 0;
            width: 100%;
            z-index: 9999;
        }
    </style>';
});
?>
