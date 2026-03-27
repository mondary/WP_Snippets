/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - MenuBar Analytics.php
 * Display name: WP_ADMIN - MenuBar Analytics
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - MenuBar Analytics (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_ADMIN - MenuBar Analytics.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: search-ui, jetpack, umami, admin-bar, head-injection
 * Dependances probables: Font Awesome
 * Hooks WP: wp_enqueue_scripts, admin_enqueue_scripts, wp_head, admin_head, admin_bar_menu
 * Fonctions clefs: analytics_menu_styles, custom_admin_bar_menu
 * Lignes / octets (brut): 162 / 5776
 * Hash code normalise (sha256): faa02a5d75a6a2b7e4a134ebfdc555602a1631f7f39c7002004fc222f545619a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: admin-menubar-analytics__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-menubar-analytics__v001.php
 * Resume fonctionnalites: tracking / analytics, flux RSS, interface de recherche, UI frontend (CSS/HTML), 5 hook(s) WP, 2 fonction(s) clef
 * Features detectees: rss, tracking-analytics, admin-menubar, search-ui, css-ui, footer-head-injection, cron
 * Dependances probables: Service analytics externe
 * Hooks WP: wp_enqueue_scripts, admin_enqueue_scripts, wp_head, admin_head, admin_bar_menu
 * Fonctions clefs: analytics_menu_styles, custom_admin_bar_menu
 * APIs WP detectees: add_action, wp_enqueue_style, add_node
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 184 / 6691
 * Empreinte code (sha256): 6219b49a8a827e6090b4516d7cf6da7e4f84390e3a5b10f8897ab0394f0b7a8d
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-menubar-analytics__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-menubar-analytics__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: search_ui
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=18, search_ui=10, tracking_analytics=6, rss_feed=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-menubar, menubar, admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

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

