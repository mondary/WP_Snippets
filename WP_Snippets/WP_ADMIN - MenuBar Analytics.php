

add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
});

add_action('admin_head', function() {
    echo '<style>
        /* Ensure the Analytics menu icon is clickable */
        #wpadminbar #wp-admin-bar-custom_menu > .ab-item {
            padding: 0 8px !important;
            line-height: 32px !important;
        }
        #wpadminbar #wp-admin-bar-custom_menu .ab-icon {
            padding: 6px 0 !important;
            margin-right: 0 !important;
            top: 0 !important;
        }
        #wpadminbar #wp-admin-bar-custom_menu:hover .ab-icon {
            color: #72aee6;
        }
        /* FontAwesome icons styling */
        #wpadminbar .fa, #wpadminbar .fas {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900;
            font-size: 14px;
            line-height: 1;
            padding: 0 4px 0 0;
        }
    </style>';
});

function custom_admin_bar_menu($wp_admin_bar) {
    // Ajouter un groupe de menu
    $args = array(
        'id'    => 'custom_menu',
        'title' => '<span class="ab-icon dashicons dashicons-chart-bar"></span>',
        'href'  => '#',
        'meta'  => array(
            'class' => 'custom-menu-class',
            'title' => 'Analytics'
        ),
    );
    $wp_admin_bar->add_node($args);

    // Ajouter des sous-menus avec des icônes
    $links = array(
        'Google Analytics' => array(
            'url' => 'https://analytics.google.com/analytics/web',
            'icon' => 'fa-chart-line'
        ),
        'Umami' => array(
            'url' => 'https://eu.umami.is/websites/18410156-63da-42cf-b3bb-474c0d61f208',
            'icon' => 'fa-chart-bar'
        ),
        'DataPulse' => array(
            'url' => 'https://datapulse.app/dashboard',
            'icon' => 'fa-chart-area'
        ),
        'Counter' => array(
            'url' => 'https://counter.dev/dashboard.html',
            'icon' => 'fa-clock'
        ),
        'Cronitor' => array(
            'url' => 'https://cronitor.io/app/monitors/MzFC18?env=production&sort=-created&time=7d',
            'icon' => 'fa-eye'
        ),
        'Collaborator' => array(
            'url' => 'https://collaborator.pro/creator/article/view?id=328636',
            'icon' => 'fa-file-lines'
        ),
    );

    foreach ($links as $title => $link) {
        $wp_admin_bar->add_node(array(
            'id'    => sanitize_title($title),
            'title' => '<i class="fas ' . $link['icon'] . '"></i> ' . $title,
            'href'  => $link['url'],
            'meta'  => array('target' => '_blank'), // Ouvre dans un nouvel onglet
            'parent' => 'custom_menu', // Définit le parent pour le sous-menu
        ));
    }
}
add_action('admin_bar_menu', 'custom_admin_bar_menu', 100);

