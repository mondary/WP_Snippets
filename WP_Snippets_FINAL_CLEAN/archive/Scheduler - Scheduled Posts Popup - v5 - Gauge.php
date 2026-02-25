
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - Scheduled Posts Gauge 2.php
 * Display name: WP_POST - Scheduled Posts Gauge 2
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (693133efb220…, 2 membres)
 * Canonical exact group ID: 163
 * Version family: WP_POST - Scheduled Posts Gauge (6 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_POST - Scheduled Posts Gauge 6.php
 * Is family latest: non
 * Archive reasons: exact-duplicate, version-history-older
 * Features: ajax, footer-injection
 * Dependances probables: jQuery
 * Hooks WP: wp_footer, wp_ajax_clm_handle_patreon_poll
 * Fonctions clefs: clm_patreon_poll_widget_and_script, clm_handle_patreon_poll_callback
 * Actions AJAX: clm_handle_patreon_poll
 * Lignes / octets (brut): 129 / 5590
 * Hash code normalise (sha256): 693133efb220eb72bab4727e250d087c5cedcae7b31c6bfd4eaaac5eb22ab480
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-scheduled-posts-gauge__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-scheduled-posts-gauge__v002.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: scheduler_posts
 * Clusters secondaires: rest_ajax_integration
 * Domaine: post-front
 * Confiance: high
 * Scores (top): scheduler_posts=12, rest_ajax_integration=6, post_footer_ui=5, frontend_ui_widget=2
 * Raisons principales: scheduled, schedule, gauge
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

d8c79e7609a95448709797
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - Scheduled Posts Gauge 2.php
 * Display name: WP_POST - Scheduled Posts Gauge 2
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (693133efb220…, 2 membres)
 * Canonical exact group ID: 163
 * Version family: WP_POST - Scheduled Posts Gauge (6 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_POST - Scheduled Posts Gauge 6.php
 * Is family latest: non
 * Archive reasons: exact-duplicate, version-history-older
 * Features: ajax, footer-injection
 * Dependances probables: jQuery
 * Hooks WP: wp_footer, wp_ajax_clm_handle_patreon_poll
 * Fonctions clefs: clm_patreon_poll_widget_and_script, clm_handle_patreon_poll_callback
 * Actions AJAX: clm_handle_patreon_poll
 * Lignes / octets (brut): 129 / 5590
 * Hash code normalise (sha256): 693133efb220eb72bab4727e250d087c5cedcae7b31c6bfd4eaaac5eb22ab480
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Titre: Jauge d'articles programmés avec sondage Patreon
 * Description: Affiche une jauge cliquable et stocke les réponses à un sondage. Conçu pour le plugin Code Snippets.
 */

// == PARTIE 1: AFFICHE LE WIDGET ET INJECTE LE SCRIPT ==

add_action('wp_footer', 'clm_patreon_poll_widget_and_script');

function clm_patreon_poll_widget_and_script() {
    // --- Récupération des données ---
    $query_args = [
        'post_type'      => 'post',
        'post_status'    => 'future',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    $query = new WP_Query($query_args);
    $count = $query->found_posts;

    if ($count <= 0) return; // Ne rien afficher si aucun article n'est programmé

    $last_post_date_formatted = $query->have_posts() ? date_i18n(get_option('date_format'), strtotime($query->posts[0]->post_date)) : '';

    // --- Paramètres de la jauge ---
    $goal = 100; $color_progression = '#2B6CB0'; $color_fond = '#E2E8F0';
    $progress = min($count / $goal, 1);
    $radius = 16; $stroke_width = 6; $viewbox_size = 40; $center = $viewbox_size / 2;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference * (1 - $progress);

    // --- Injection du CSS (avec des noms de classe uniques pour éviter les conflits) ---
    echo <<<CSS
    <style>
        #clm-patreon-widget { position: fixed; bottom: 20px; right: 200px; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; align-items: center; padding: 5px 15px 5px 8px; z-index: 9999; transition: transform 0.3s ease; cursor: pointer; }
        #clm-patreon-widget:hover { transform: translateY(-3px); }
    </style>
CSS;

    // --- Injection de l'HTML ---
    echo <<<HTML
    <div id="clm-patreon-widget" role="button" tabindex="0" aria-label="Sondage Patreon">
        <div style="position: relative; width: {$viewbox_size}px; height: {$viewbox_size}px;">
            <svg style="width: 100%; height: 100%; transform: rotate(-90deg);" viewBox="0 0 {$viewbox_size} {$viewbox_size}">
                <circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_fond};" cx="{$center}" cy="{$center}" r="{$radius}"></circle>
                <circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_progression}; stroke-linecap: round; stroke-dasharray: {$circumference}; stroke-dashoffset: {$offset}; transition: stroke-dashoffset 1.5s ease-out;" cx="{$center}" cy="{$center}" r="{$radius}"></circle>
            </svg>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 14px; font-weight: 700; color: #2D3748;">{$count}</div>
        </div>
        <div style="display: flex; flex-direction: column; margin-left: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
            <span style="font-size: 15px; font-weight: 600; color: #1A202C;">Articles programmés</span>
            <span style="font-size: 12px; color: #718096; margin-top: 2px;">Jusqu'au {$last_post_date_formatted}</span>
        </div>
    </div>
HTML;

    // --- Injection du script jQuery (méthode la plus fiable) ---
    $ajax_nonce = wp_create_nonce('clm-patreon-poll-nonce');
    $ajax_url = admin_url('admin-ajax.php');

    $javascript_code = <<<JS
    jQuery(document).ready(function($) {
        const widget = $('#clm-patreon-widget');
        if (widget.length === 0) return;

        widget.on('click', function(e) {
            e.preventDefault();

            if (localStorage.getItem('clmPatreonPollAnswered')) {
                alert('Merci d\'avoir déjà répondu à notre sondage !');
                return;
            }

            const userResponse = confirm("Seriez-vous prêt à payer via Patreon pour avoir nos articles en avance ?");
            const vote = userResponse ? 'yes' : 'no';

            $.ajax({
                url: '{$ajax_url}',
                type: 'POST',
                data: {
                    action: 'clm_handle_patreon_poll',
                    _ajax_nonce: '{$ajax_nonce}',
                    vote: vote
                },
                success: function(response) {
                    if(response.success) {
                        alert("Merci pour votre réponse !");
                        localStorage.setItem('clmPatreonPollAnswered', 'true');
                    } else {
                        alert("Une erreur s'est produite: " + response.data);
                    }
                },
                error: function(error) {
                    console.error('Erreur AJAX:', error);
                    alert('Une erreur technique est survenue.');
                }
            });
        });
    });
JS;

    wp_enqueue_script('jquery');
    wp_add_inline_script('jquery', $javascript_code);
}

// == PARTIE 2: GÈRE LA RÉPONSE AJAX ==

add_action('wp_ajax_clm_handle_patreon_poll', 'clm_handle_patreon_poll_callback');

function clm_handle_patreon_poll_callback() {
    check_ajax_referer('clm-patreon-poll-nonce');

    if (!isset($_POST['vote']) || !in_array($_POST['vote'], ['yes', 'no'])) {
        wp_send_json_error('Vote invalide.', 400);
    }

    $vote = sanitize_text_field($_POST['vote']);
    $results = get_option('clm_patreon_poll_results', ['yes' => 0, 'no' => 0]);

    $results[$vote]++;

    update_option('clm_patreon_poll_results', $results);

    wp_send_json_success('Vote enregistré avec succès.');
}

?>