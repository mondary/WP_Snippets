
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
});

add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
});

add_action('wp_head', 'analytics_menu_styles');
add_action('admin_head', 'analytics_menu_styles');

function analytics_menu_styles() {
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
        /* Menu separators */
        #wpadminbar #wp-admin-bar-custom_menu .separator {
            height: 1px;
            margin: 6px 8px;
            background: rgba(255, 255, 255, 0.2);
        }
    </style>';
}

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
    // Groupe Analytics & Stats
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
		'Swilty' => array(
    'url' => 'https://swilty.com/dashboard',
    'icon' => 'fa-bolt'
),

        'Counter' => array(
            'url' => 'https://counter.dev/dashboard.html',
            'icon' => 'fa-clock'
        ),
        'Cronitor' => array(
            'url' => 'https://cronitor.io/app/monitors/MzFC18?env=production&sort=-created&time=7d',
            'icon' => 'fa-eye'
        ),
        'Google News' => array(
            'url' => 'https://news.google.com/search?q=site%3Amondary.design&hl=fr&gl=FR&ceid=FR%3Af',
            'icon' => 'fa-newspaper'
        ),
        'Google Search Console' => array(
            'url' => 'https://search.google.com/search-console/sitemaps?resource_id=https%3A%2F%2Fmondary.design%2F&hl=fr',
            'icon' => 'fa-search'
        ),

        'separator1' => array(
            'separator' => true
        ),
        // Groupe Publication
        'Collaborator' => array(
            'url' => 'https://collaborator.pro/creator/article/view?id=328636',
            'icon' => 'fa-file-lines'
        ),
        'Google AdSense' => array(
            'url' => 'https://www.google.com/adsense/new/u/0/pub-1824217780734986/home',
            'icon' => 'fa-dollar-sign'
        ),
        'AdSense Paiements' => array(
            'url' => 'https://www.google.com/adsense/new/u/0/pub-1824217780734986/payments/?place=TRANSACTIONS_SERVICE',
            'icon' => 'fa-money-bill'
        ),
        'separator2' => array(
            'separator' => true
        ),
        // Groupe Abonnés & Social
        'Jetpack Subscribers' => array(
            'url' => 'https://cloud.jetpack.com/subscribers/194725933?site=194725933',
            'icon' => 'fa-users'
        ),
        'Jetpack Subscribers Stats' => array(
            'url' => 'https://wordpress.com/stats/subscribers/mondary.design',
            'icon' => 'fa-chart-bar'
        ),
        'WordPress Reader' => array(
            'url' => 'https://wordpress.com/reader/feeds/119173277',
            'icon' => 'fa-rss'
        ),
        'separator3' => array(
            'separator' => true
        ),
        // Groupe Domaines & Hébergement
        'Squarespace' => array(
            'url' => 'https://account.squarespace.com/domains',
            'icon' => 'fa-globe'
        ),
        'Porkbun' => array(
            'url' => 'https://porkbun.com/account/domainsSpeedy',
            'icon' => 'fa-server'
        ),
        'OVH Manager' => array(
            'url' => 'https://www.ovh.com/manager/#/hub',
            'icon' => 'fa-cloud'
        )
    );

    foreach ($links as $title => $link) {
        if (isset($link['separator'])) {
            $wp_admin_bar->add_node(array(
                'id'    => $title,
                'title' => '<div class="separator"></div>',
                'parent' => 'custom_menu',
            ));
        } else {
            $wp_admin_bar->add_node(array(
                'id'    => sanitize_title($title),
                'title' => '<i class="fas ' . $link['icon'] . '"></i> ' . $title,
                'href'  => $link['url'],
                'meta'  => array('target' => '_blank'), // Ouvre dans un nouvel onglet
                'parent' => 'custom_menu', // Définit le parent pour le sous-menu
            ));
        }
    }
}
add_action('admin_bar_menu', 'custom_admin_bar_menu', 100);

