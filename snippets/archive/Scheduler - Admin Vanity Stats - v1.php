/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/016__id-25__admin-vanity-stats.php
 * Display name: ADMIN - VANITY stats 🔴
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 25
 * Online modified: 2025-01-22 13:37:11
 * Online revision: 11
 * Exact duplicate group: non
 * Version family: ADMIN - VANITY stats 🔴 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/016__id-25__admin-vanity-stats.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: my_personal_stats_menu, my_personal_stats_page, display_chart
 * Lignes / octets (brut): 103 / 3832
 * Hash code normalise (sha256): 5b3fa45e916a9a99f1b50ecd65a15766c4c7c2675e2fbce953a2f9a718404f9a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-vanity-stats__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-vanity-stats__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, UI frontend (CSS/HTML), automatisation date/programmation, 1 hook(s) WP, 3 fonction(s) clef
 * Features detectees: admin-ui, scheduler-date, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: my_personal_stats_menu, my_personal_stats_page, display_chart
 * Selecteurs / IDs: #f1f1f1, #4CAF50
 * APIs WP detectees: add_action, add_menu_page, wp_get_current_user
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 116 / 4444
 * Empreinte code (sha256): 9394a92e63aca0fb8abc4208a2abe1e0d85fcd96268e8bfc3a1fc524eb81f8b7
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-vanity-stats__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-vanity-stats__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: admin_ui_settings
 * Domaine: global
 * Confiance: medium
 * Scores (top): scheduler_posts=8, admin_ui_settings=4, frontend_ui_widget=2
 * Raisons principales: scheduler-date, schedule
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/*
Plugin Name: My Personal Stats
Plugin URI: https://example.com/my-personal-stats
Description: Affiche des statistiques personnelles sur une page dédiée.
Version: 1.3
Author: Votre Nom
Author URI: https://example.com
*/

// Ajouter la page des statistiques personnelles
add_action('admin_menu', 'my_personal_stats_menu');
function my_personal_stats_menu() {
    add_menu_page(
        'Mes Statistiques Personnelles',
        'Mes Statistiques',
        'manage_options',
        'my-personal-stats',
        'my_personal_stats_page',
        'dashicons-chart-line',
        6
    );
}

// Afficher le contenu de la page des statistiques personnelles
function my_personal_stats_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Récupérer les informations de l'utilisateur connecté
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $user_birthday = '1984-02-11'; // Remplacez par votre date de naissance

    // Récupérer le nombre d'articles publiés par l'utilisateur
    $post_count = count_user_posts($user_id);

    // Calculer les statistiques
    $days_since_registered = floor((time() - strtotime($user->user_registered)) / 86400);
    $age = (new DateTime())->diff(new DateTime($user_birthday))->y;
    $days_since_birth = floor((time() - strtotime($user_birthday)) / 86400);

    // Afficher les statistiques sur la page
    echo '<div class="wrap" style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
    echo '<h1 style="color: #333;">Mes Statistiques Personnelles</h1>';

    // Styles pour les sections
    echo '<style>
        .stat-section {
            margin-bottom: 40px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
        }
        h2 {
            color: #4CAF50;
        }
        .chart-container {
            width: 100%;
            height: 300px;
        }
    </style>';

    // Fonction pour afficher un graphique
    function display_chart($id, $title, $value) {
        echo '<div class="stat-section">';
        echo "<h2>$title</h2>";
        echo "<div id=\"$id\" class=\"chart-container\"></div>";
        echo '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
        echo '<script type="text/javascript">';
        echo 'google.charts.load("current", {packages:["corechart"]});';
        echo 'google.charts.setOnLoadCallback(function() {';
        echo '    var data = google.visualization.arrayToDataTable([["", "Nombre"], ["", ' . $value . ']]);';
        echo '    var options = {title: "' . $title . '", is3D: true, pieHole: 0.4, backgroundColor: "#f1f1f1", colors: ["#4CAF50"]};';
        echo '    var chart = new google.visualization.PieChart(document.getElementById("' . $id . '"));';
        echo '    chart.draw(data, options);';
        echo '});';
        echo '</script>';
        echo '</div>'; // .stat-section
    }

    // Afficher les graphiques
    display_chart('age-chart', 'Mon âge', $age);
    display_chart('post-chart', 'Mes articles publiés', $post_count);
    display_chart('registered-chart', 'Jours depuis la création du compte', $days_since_registered);
    display_chart('birth-chart', 'Jours depuis ma naissance', $days_since_birth);

    echo '</div>'; // .wrap
}
