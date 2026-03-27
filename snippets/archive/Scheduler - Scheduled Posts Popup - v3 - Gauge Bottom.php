/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/078__id-138__post-schedule-posts-gauge-bottom.php
 * Display name: POST - Schedule posts gauge bottom
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 138
 * Online modified: 2025-09-22 15:24:39
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: POST - Schedule posts gauge bottom (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/078__id-138__post-schedule-posts-gauge-bottom.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: display_scheduled_posts_gauge
 * Lignes / octets (brut): 143 / 5121
 * Hash code normalise (sha256): b59a2f8698e88460c5fc25c90fb3c7e7643ae2ac073198bc855d404d400ba851
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__post-schedule-posts-gauge-bottom__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-schedule-posts-gauge-bottom__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: scheduler-date, css-ui, footer-head-injection, svg-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: display_scheduled_posts_gauge
 * Selecteurs / IDs: #2B6CB0, #E2E8F0
 * APIs WP detectees: add_action, get_option
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 156 / 5744
 * Empreinte code (sha256): d7ce06f72c475121e922acb5b314cc2814a40fb035a012e86efe5d76f325281b
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__post-schedule-posts-gauge-bottom__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-schedule-posts-gauge-bottom__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): scheduler_posts=16, frontend_ui_widget=6, post_footer_ui=5
 * Raisons principales: scheduler-date, scheduled, schedule, gauge
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Affiche une jauge visuelle pour le nombre d'articles programmés.
 * La jauge apparaît en bas à droite de l'écran sur tout le site.
 */
add_action('wp_footer', 'display_scheduled_posts_gauge');

function display_scheduled_posts_gauge() {
    // Arguments pour la requête WP_Query : on cherche les articles programmés
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'future',
        'posts_per_page' => -1, // On les prend tous pour les compter
    );

    $scheduled_posts_query = new WP_Query($args);
    $count = $scheduled_posts_query->found_posts;

    // On n'affiche la jauge que s'il y a au moins 1 article programmé
    if ($count > 0) {

        // --- Récupération de la date du dernier article ---
        $last_post_query_args = array(
            'post_type'      => 'post',
            'post_status'    => 'future',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC', // Le plus récent est le plus loin dans le futur
        );
        $last_post_query = new WP_Query($last_post_query_args);
        $last_post_date_formatted = '';
        if ($last_post_query->have_posts()) {
            $last_post = $last_post_query->posts[0];
            // Formate la date en français (ex: 22 septembre 2025)
            $last_post_date_formatted = date_i18n(get_option('date_format'), strtotime($last_post->post_date));
        }

        // --- Configuration de la jauge ---
        $goal = 100; // Objectif de 100 articles
        $color_progression = '#2B6CB0';
        $color_fond = '#E2E8F0';
        $progress = min($count / $goal, 1);
        $radius = 28;
        $circumference = 2 * M_PI * $radius;
        $offset = $circumference * (1 - $progress);

        // Injection du CSS pour le nouveau widget
        echo <<<CSS
        <style>
            #scheduled-posts-widget {
                position: fixed;
                bottom: 20px;
                right: 200px;
                background-color: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                padding: 10px 20px 10px 10px;
                z-index: 9999;
                transition: transform 0.3s ease;
            }
            .gauge-container {
                position: relative;
                width: 40px;
                height:40px;
            }
            .gauge-svg {
                width: 100%;
                height: 100%;
                transform: rotate(-90deg);
            }
            .gauge-bg, .gauge-progress {
                fill: none;
                stroke-width: 7;
            }
            .gauge-bg { stroke: {$color_fond}; }
            .gauge-progress {
                stroke: {$color_progression};
                stroke-linecap: round;
                stroke-dasharray: {$circumference};
                stroke-dashoffset: {$offset};
                transition: stroke-dashoffset 1.5s ease-out;
            }
            .gauge-text-inner {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 18px;
                font-weight: 700;
                color: #2D3748;
            }
            .info-container {
                display: flex;
                flex-direction: column;
                margin-left: 15px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            }
            .info-label {
                font-size: 16px;
                font-weight: 600;
                color: #1A202C;
            }
            .info-date {
                font-size: 13px;
                color: #718096;
                margin-top: 2px;
            }
        </style>
CSS;

        // Injection de l'HTML du widget
        echo <<<HTML
        <div id="scheduled-posts-widget">
            <div class="gauge-container">
                <svg class="gauge-svg" viewBox="0 0 60 60">
                    <circle class="gauge-bg" cx="30" cy="30" r="{$radius}"></circle>
                    <circle class="gauge-progress" cx="30" cy="30" r="{$radius}"></circle>
                </svg>
                <div class="gauge-text-inner">{$count}</div>
            </div>
            <div class="info-container">
                <span class="info-label">Articles programmés</span>
                <span class="info-date">Jusqu'au {$last_post_date_formatted}</span>
            </div>
        </div>
HTML;
    }
}

