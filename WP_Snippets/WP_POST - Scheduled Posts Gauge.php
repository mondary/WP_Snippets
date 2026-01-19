<?php

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
                right: 200px; /* Votre position */
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

?>