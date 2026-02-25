/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/077__id-137__post-schedule-posts-gauge.php
 * Display name: POST - Schedule posts gauge
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 137
 * Online modified: 2025-09-22 15:18:15
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: POST - Schedule posts gauge (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/077__id-137__post-schedule-posts-gauge.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: display_scheduled_posts_gauge
 * Lignes / octets (brut): 112 / 3931
 * Hash code normalise (sha256): b7e6ea1b15cc95e0b3a0d039a64cdd4a1b93bbcb67ebc5ad769e25f12df4cae2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-schedule-posts-gauge__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-schedule-posts-gauge__v001.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: scheduler-date, css-ui, footer-head-injection, svg-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: display_scheduled_posts_gauge
 * Selecteurs / IDs: #2B6CB0, #E2E8F0
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 125 / 4533
 * Empreinte code (sha256): 66f98ee25a0180eac168d7bc67b9acb7d51c8ecdbc1e55494234f5e9c538cb60
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-schedule-posts-gauge__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-schedule-posts-gauge__v001.php
 * Bucket FINAL: archive
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
        // --- Configuration de la jauge ---
        $goal = 100; // Objectif de 100 articles pour remplir la jauge
        $color_progression = '#2B6CB0'; // Votre couleur
        $color_fond = '#E2E8F0'; // Couleur de fond de la jauge (gris clair)
        
        // Calcul du pourcentage de progression
        $progress = ($count / $goal);
        $progress = min($progress, 1); // On s'assure de ne pas dépasser 100% visuellement

        // Paramètres du cercle SVG
        $radius = 35;
        $circumference = 2 * M_PI * $radius;
        $offset = $circumference * (1 - $progress);

        // Injection du CSS
        echo <<<CSS
        <style>
            #scheduled-posts-gauge {
                position: fixed;
                top: 55px;
                right: 50px;
                width: 50px;
                height: 50px;
                z-index: 9999;
                transition: transform 0.3s ease;
                cursor: pointer;
            }
            #scheduled-posts-gauge:hover {
                transform: scale(1.05);
            }
            .gauge-svg {
                width: 100%;
                height: 100%;
                transform: rotate(-90deg); /* Pour que la jauge commence en haut */
            }
            .gauge-bg {
                fill: none;
                stroke: {$color_fond};
                stroke-width: 8;
            }
            .gauge-progress {
                fill: none;
                stroke: {$color_progression};
                stroke-width: 8;
                stroke-linecap: round; /* Extrémités arrondies */
                stroke-dasharray: {$circumference};
                stroke-dashoffset: {$circumference};
                animation: fillGauge 1.5s ease-out forwards;
                animation-delay: 0.5s;
            }
            @keyframes fillGauge {
                to {
                    stroke-dashoffset: {$offset};
                }
            }
            .gauge-text {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 22px;
                font-weight: 600;
                color: #2D3748;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            }
        </style>
CSS;

        // Injection de l'HTML (SVG)
        echo <<<HTML
        <div id="scheduled-posts-gauge" title="{$count} / {$goal} articles programmés">
            <svg class="gauge-svg" viewBox="0 0 80 80">
                <circle class="gauge-bg" cx="40" cy="40" r="{$radius}"></circle>
                <circle class="gauge-progress" cx="40" cy="40" r="{$radius}"></circle>
            </svg>
            <div class="gauge-text">{$count}</div>
        </div>
HTML;
    }
}

