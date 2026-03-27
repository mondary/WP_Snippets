
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_ADMIN Analytics menu/ADMIN - Analytics menu.php
 * Display name: ADMIN - Analytics menu
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: ADMIN - Analytics menu (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_ADMIN Analytics menu/ADMIN - Analytics menu.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: umami, admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: custom_admin_bar_menu
 * Lignes / octets (brut): 49 / 1672
 * Hash code normalise (sha256): 317a88ee5eeeef067ea4510e284017340508a70931777c7af52b33687082e814
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__admin__admin-analytics-menu__v1__src-a-trier.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__admin__admin-analytics-menu__v1__src-a-trier.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: tracking_analytics
 * Clusters secondaires: admin_menubar
 * Domaine: tracking
 * Confiance: medium
 * Scores (top): tracking_analytics=6, admin_menubar=6
 * Raisons principales: analytics
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

le final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_ADMIN Analytics menu/ADMIN - Analytics menu.php
 * Display name: ADMIN - Analytics menu
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: ADMIN - Analytics menu (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_ADMIN Analytics menu/ADMIN - Analytics menu.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: umami, admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: custom_admin_bar_menu
 * Lignes / octets (brut): 49 / 1672
 * Hash code normalise (sha256): 317a88ee5eeeef067ea4510e284017340508a70931777c7af52b33687082e814
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

function custom_admin_bar_menu($wp_admin_bar) {
    // Ajouter un groupe de menu
    $args = array(
        'id'    => 'custom_menu',
        'title' => 'Analytics',
        'href'  => '#',
        'meta'  => array('class' => 'custom-menu-class'),
    );
    $wp_admin_bar->add_node($args);

    // Ajouter des sous-menus avec des icônes
    $links = array(
        'Google Analytics' => array(
            'url' => 'https://analytics.google.com/analytics/web',
            'icon' => 'dashicons-chart-line'
        ),
        'Umami' => array(
            'url' => 'https://eu.umami.is/websites/18410156-63da-42cf-b3bb-474c0d61f208',
            'icon' => 'dashicons-chart-bar'
        ),
        'DataPulse' => array(
            'url' => 'https://datapulse.app/dashboard',
            'icon' => 'dashicons-chart-area'
        ),
        'Counter' => array(
            'url' => 'https://counter.dev/dashboard.html',
            'icon' => 'dashicons-clock'
        ),
        'Cronitor' => array(
            'url' => 'https://cronitor.io/app/monitors/MzFC18?env=production&sort=-created&time=7d',
            'icon' => 'dashicons-visibility'
        ),
    );

    foreach ($links as $title => $link) {
        $wp_admin_bar->add_node(array(
            'id'    => sanitize_title($title),
            'title' => '<span class="dashicons ' . $link['icon'] . '"></span> ' . $title,
            'href'  => $link['url'],
            'meta'  => array('target' => '_blank'), // Ouvre dans un nouvel onglet
            'parent' => 'custom_menu', // Définit le parent pour le sous-menu
        ));
    }
}
add_action('admin_bar_menu', 'custom_admin_bar_menu', 100);

?>
