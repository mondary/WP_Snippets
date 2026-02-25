/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/079__id-139__post-schedule-posts-gauge-opti.php
 * Display name: POST - Schedule posts gauge opti ✅
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 139
 * Online modified: 2025-09-22 15:27:42
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: POST - Schedule posts gauge opti ✅ (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/079__id-139__post-schedule-posts-gauge-opti.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: display_scheduled_posts_gauge
 * Lignes / octets (brut): 142 / 5226
 * Hash code normalise (sha256): bba69dd460204dc257d3c1eecdd7fc3f7e43606130a1954e5a21602890b7b025
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-schedule-posts-gauge-opti__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-schedule-posts-gauge-opti__v001.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: scheduler-date, css-ui, footer-head-injection, svg-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: display_scheduled_posts_gauge
 * Selecteurs / IDs: #2B6CB0, #E2E8F0
 * APIs WP detectees: add_action, get_option
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 155 / 5847
 * Empreinte code (sha256): 7f43b7e65abdf1d86bf9b4edf8a57d3ed299bd7cb04f1dce70f49868f208e904
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-schedule-posts-gauge-opti__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-schedule-posts-gauge-opti__v001.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): scheduler_posts=16, frontend_ui_widget=6, post_footer_ui=5, performance_optimization=5
 * Raisons principales: scheduler-date, scheduled, schedule, gauge
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Affiche une jauge visuelle pour le nombre d'articles programmés.
 * Le widget apparaît là où défini dans le CSS.
 */
add_action('wp_footer', 'display_scheduled_posts_gauge');

function display_scheduled_posts_gauge() {

    // --- Récupération des informations en une seule requête optimisée ---
    $query_args = array(
        'post_type'      => 'post',
        'post_status'    => 'future',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC', // Le plus récent est le plus loin dans le futur
    );
    $query = new WP_Query($query_args);

    $count = $query->found_posts;

    // On n'affiche la jauge que s'il y a au moins 1 article programmé
    if ($count > 0) {

        $last_post_date_formatted = '';
        if ($query->have_posts()) {
            $last_post = $query->posts[0];
            // Formate la date en français (ex: 22 septembre 2025)
            $last_post_date_formatted = date_i18n(get_option('date_format'), strtotime($last_post->post_date));
        }

        // --- Configuration de la jauge (ajustée pour une taille de 40px) ---
        $goal = 100; // Objectif de 100 articles
        $color_progression = '#2B6CB0';
        $color_fond = '#E2E8F0';
        $progress = min($count / $goal, 1);
        
        // Paramètres du cercle SVG adaptés à la nouvelle taille
        $radius = 16; // Rayon réduit pour un conteneur de 40px
        $stroke_width = 6; // Epaisseur du trait réduite
        $viewbox_size = 40; // Viewbox adaptée
        $center = $viewbox_size / 2;
        $circumference = 2 * M_PI * $radius;
        $offset = $circumference * (1 - $progress);

        // Injection du CSS pour le nouveau widget
        echo <<<CSS
        <style>
            #scheduled-posts-widget {
                position: fixed;
                bottom: 20px; /* Votre position */
                right: 185px; /* Votre position */
                background-color: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                padding: 5px 15px 5px 8px;
                z-index: 9999;
                transition: transform 0.3s ease;
            }
            .gauge-container {
                position: relative;
                width: {$viewbox_size}px; /* Votre taille */
                height: {$viewbox_size}px; /* Votre taille */
            }
            .gauge-svg {
                width: 100%;
                height: 100%;
                transform: rotate(-90deg);
            }
            .gauge-bg, .gauge-progress {
                fill: none;
                stroke-width: {$stroke_width};
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
                font-size: 14px; /* Taille de police réduite */
                font-weight: 700;
                color: #2D3748;
            }
            .info-container {
                display: flex;
                flex-direction: column;
                margin-left: 12px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            }
            .info-label {
                font-size: 15px;
                font-weight: 600;
                color: #1A202C;
            }
            .info-date {
                font-size: 12px;
                color: #718096;
                margin-top: 2px;
            }
        </style>
CSS;

        // Injection de l'HTML du widget
        echo <<<HTML
        <div id="scheduled-posts-widget">
            <div class="gauge-container">
                <svg class="gauge-svg" viewBox="0 0 {$viewbox_size} {$viewbox_size}">
                    <circle class="gauge-bg" cx="{$center}" cy="{$center}" r="{$radius}"></circle>
                    <circle class="gauge-progress" cx="{$center}" cy="{$center}" r="{$radius}"></circle>
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

