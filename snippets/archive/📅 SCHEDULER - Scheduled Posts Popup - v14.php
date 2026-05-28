/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/089__id-149__post-scheduled-posts-popup-6.php
 * Display name: POST - Scheduled posts popup 6
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 149
 * Online modified: 2026-01-19 09:41:07
 * Online revision: 1
 * Exact duplicate group: oui (eafa93c79814…, 2 membres)
 * Canonical exact group ID: 112
 * Version family: POST - Scheduled posts popup (2 variantes)
 * Version: v14
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/089__id-149__post-scheduled-posts-popup-6.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, version-family-latest, protected-online-active
 * Features: ajax, footer-injection
 * Dependances probables: jQuery
 * Hooks WP: wp_footer, wp_ajax_clm_handle_patreon_poll_v6, wp_ajax_nopriv_clm_handle_patreon_poll_v6, wp_ajax_clm_get_patreon_poll_results_v6, wp_ajax_nopriv_clm_get_patreon_poll_results_v6
 * Fonctions clefs: clm_patreon_poll_v6_widget_and_script, clm_handle_patreon_poll_v6_callback, clm_get_patreon_poll_results_v6_callback
 * Actions AJAX: clm_handle_patreon_poll_v6, clm_get_patreon_poll_results_v6
 * Lignes / octets (brut): 254 / 14304
 * Hash code normalise (sha256): eafa93c798144ad5ba09d0b8be818f287f4a3534538335bb344dc0ab429ac9a8
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-scheduled-posts-popup-6__v6__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-scheduled-posts-popup-6__v6__src-wp_snippets_online_current.php
 * Resume fonctionnalites: interactions AJAX, UI frontend (CSS/HTML), automatisation date/programmation, 5 hook(s) WP, 3 fonction(s) clef
 * Features detectees: ajax, scheduler-date, css-ui, footer-head-injection, svg-ui
 * Dependances probables: jQuery, WordPress AJAX
 * Hooks WP: wp_footer, wp_ajax_clm_handle_patreon_poll_v6, wp_ajax_nopriv_clm_handle_patreon_poll_v6, wp_ajax_clm_get_patreon_poll_results_v6, wp_ajax_nopriv_clm_get_patreon_poll_results_v6
 * Fonctions clefs: clm_patreon_poll_v6_widget_and_script, clm_handle_patreon_poll_v6_callback, clm_get_patreon_poll_results_v6_callback
 * Actions AJAX: clm_handle_patreon_poll_v6, clm_get_patreon_poll_results_v6
 * Selecteurs / IDs: #E2E8F0, #3182CE, #DD6B20, #38A169, #805AD5, #D53F8C, #clm-patreon-widget-v6, #clm-poll-modal-overlay-v6, #clm-poll-view-v6, #clm-stats-view-v6, #clm-modal-close-v6, #clm-poll-question-text-v6 … (+2)
 * APIs WP detectees: add_action, wp_create_nonce, admin_url, wp_enqueue_script, wp_add_inline_script, wp_send_json_error, get_option, wp_send_json_success
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 268 / 15261
 * Empreinte code (sha256): 819a5054e48a9ca7ffc6b3e38bd276b7e3da1d65d77f73b5afc0981f56ffa8fa
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-scheduled-posts-popup-6__v6__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-scheduled-posts-popup-6__v6__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: frontend_ui_widget
 * Domaine: post-front
 * Confiance: high
 * Scores (top): scheduler_posts=16, frontend_ui_widget=8, rest_ajax_integration=6, post_footer_ui=5
 * Raisons principales: scheduler-date, scheduled, schedule, popup
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Titre: Jauge d'articles programmés (V6 - Multi-niveaux)
 * Description: Affiche une jauge multi-niveaux. Chaque 100 articles, un nouveau cercle de couleur se superpose.
 */

// == PARTIE 1: AFFICHE LE WIDGET, LA MODALE ET INJECTE LE SCRIPT ==

add_action('wp_footer', 'clm_patreon_poll_v6_widget_and_script');

function clm_patreon_poll_v6_widget_and_script() {
    // --- Récupération des données ---
    $query_args = [
        'post_type' => 'post', 'post_status' => 'future', 'posts_per_page' => -1, // On récupère tout pour un compte précis
    ];
    $query = new WP_Query($query_args);
    $count = $query->found_posts;

    if ($count <= 0) return;

    // On cherche la date du dernier article programmé
    $last_post_query_args = [
        'post_type' => 'post', 'post_status' => 'future', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC',
    ];
    $last_post_query = new WP_Query($last_post_query_args);
    $last_post_date_formatted = $last_post_query->have_posts() ? date_i18n('j F Y', strtotime($last_post_query->posts[0]->post_date)) : '';

    // --- NOUVEAU: Logique pour la jauge multi-niveaux ---
    $goal_per_level = 100;
    $color_fond = '#E2E8F0';
    $level_colors = ['#3182CE', '#DD6B20', '#38A169', '#805AD5', '#D53F8C']; // Bleu, Orange, Vert, Violet, Rose

    $radius = 16; $stroke_width = 6; $viewbox_size = 40; $center = $viewbox_size / 2;
    $circumference = 2 * M_PI * $radius;

    $levels_to_draw = floor(($count - 1) / $goal_per_level);
    $progress_on_current_level = ($count % $goal_per_level) / $goal_per_level;
    if ($progress_on_current_level == 0 && $count > 0) {
        $progress_on_current_level = 1; // Si on est à 100, 200, etc., le dernier cercle est plein
    }

    $svg_circles = '';
    // Dessiner les cercles pleins des niveaux précédents
    for ($i = 0; $i < $levels_to_draw; $i++) {
        $color = $level_colors[$i % count($level_colors)];
        $svg_circles .= "<circle style=\"fill: none; stroke-width: {$stroke_width}; stroke: {$color}; stroke-linecap: round; stroke-dasharray: {$circumference}; stroke-dashoffset: 0;\" cx=\"{$center}\" cy=\"{$center}\" r=\"{$radius}\"></circle>";
    }

    // Dessiner le cercle du niveau actuel
    $current_level_color = $level_colors[$levels_to_draw % count($level_colors)];
    $offset = $circumference * (1 - $progress_on_current_level);
    $svg_circles .= "<circle style=\"fill: none; stroke-width: {$stroke_width}; stroke: {$current_level_color}; stroke-linecap: round; stroke-dasharray: {$circumference}; stroke-dashoffset: {$offset}; transition: stroke-dashoffset 1.5s ease-out;\" cx=\"{$center}\" cy=\"{$center}\" r=\"{$radius}\"></circle>";

    // --- CSS (adapté pour v6) ---
    echo <<<CSS
    <style>
        #clm-patreon-widget-v6 { position: fixed; bottom: 20px; right: 200px; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; align-items: center; padding: 5px 15px 5px 8px; z-index: 9999; transition: transform 0.3s ease, opacity 0.3s ease; cursor: pointer; }
        #clm-patreon-widget-v6:hover { transform: translateY(-3px); }
        #clm-patreon-widget-v6.clm-widget-hidden-v6 { opacity: 0; transform: translateY(12px); pointer-events: none; }
        .clm-modal-overlay-v6 { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); z-index: 10000; display: none; align-items: center; justify-content: center; animation: clm-fadeIn-v6 0.3s ease-out; }
        .clm-modal-content-v6 { background: #fff; border-radius: 16px; padding: 35px 45px; width: 90%; max-width: 480px; text-align: center; position: relative; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: scale(0.95); animation: clm-scaleIn-v6 0.3s ease-out forwards; }
        .clm-modal-content-v6 h2 { margin-top: 0; font-size: 24px; font-weight: 700; color: #1A202C; }
        .clm-modal-content-v6 p { margin-bottom: 30px; color: #4A5568; font-size: 17px; line-height: 1.6; }
        .clm-modal-close-v6 { position: absolute; top: 15px; right: 20px; font-size: 30px; color: #A0AEC0; cursor: pointer; line-height: 1; transition: color 0.2s; }
        .clm-modal-close-v6:hover { color: #4A5568; }
        .clm-modal-buttons-v6 { display: flex; justify-content: center; gap: 15px; }
        .clm-modal-buttons-v6 button { border: none; border-radius: 10px; padding: 14px 24px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        #clm-poll-yes-v6 { background-color: #3182CE; color: white; }
        #clm-poll-yes-v6:hover { background-color: #2B6CB0; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(49, 130, 206, 0.3); }
        #clm-poll-no-v6 { background-color: #E2E8F0; color: #2D3748; }
        #clm-poll-no-v6:hover { background-color: #CBD5E0; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        #clm-stats-question-v6 { font-size: 16px; color: #718096; margin-bottom: 20px; padding: 15px; background-color: #F7FAFC; border-radius: 8px; font-style: italic; }
        .clm-stats-graph-v6 { margin-top: 20px; }
        .clm-stats-bar-v6 { height: 30px; line-height: 30px; color: white; font-weight: bold; text-align: left; padding-left: 12px; border-radius: 6px; white-space: nowrap; overflow: hidden; margin-bottom: 10px; transition: width 0.6s cubic-bezier(0.25, 1, 0.5, 1); }
        .clm-stats-bar-v6.yes { background-color: #38A169; }
        .clm-stats-bar-v6.no { background-color: #E53E3E; }
        .clm-stats-label-v6 { text-align: right; font-size: 14px; color: #718096; margin-top: -8px; margin-bottom: 18px; }
        @keyframes clm-fadeIn-v6 { from { opacity: 0; } to { opacity: 1; } }
        @keyframes clm-scaleIn-v6 { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    </style>
CSS;

    // --- HTML du Widget (avec SVG multi-niveaux) et de la Modale ---
    echo <<<HTML
    <div id="clm-patreon-widget-v6" role="button" tabindex="0" aria-label="Sondage Patreon">
        <div style="position: relative; width: {$viewbox_size}px; height: {$viewbox_size}px;">
            <svg style="width: 100%; height: 100%; transform: rotate(-90deg);" viewBox="0 0 {$viewbox_size} {$viewbox_size}">
                <circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_fond};" cx="{$center}" cy="{$center}" r="{$radius}"></circle>
                {$svg_circles}
            </svg>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 14px; font-weight: 700; color: #2D3748;">{$count}</div>
        </div>
        <div style="display: flex; flex-direction: column; margin-left: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;"><span style="font-size: 15px; font-weight: 600; color: #1A202C;">Articles programmés</span><span style="font-size: 12px; color: #718096; margin-top: 2px;">Jusqu'au {$last_post_date_formatted}</span></div>
    </div>

    <div id="clm-poll-modal-overlay-v6" class="clm-modal-overlay-v6">
        <div class="clm-modal-content-v6">
            <span id="clm-modal-close-v6" class="clm-modal-close-v6">&times;</span>
            <div id="clm-poll-view-v6" style="display: none;">
                <h2>Sondage Patreon</h2>
                <p id="clm-poll-question-text-v6">êtes-vous prêt(e) à me soutenir sur Patreon pour lire mes articles en avance ?</p>
                <div class="clm-modal-buttons-v6">
                    <button id="clm-poll-yes-v6">Oui, je suis intéressé(e)</button>
                    <button id="clm-poll-no-v6">Non, je ne suis pas intéressé(e)</button>
                </div>
            </div>
            <div id="clm-stats-view-v6" style="display: none;">
                <h2>Résultats du sondage</h2>
                <p id="clm-stats-question-v6"></p>
                <div class="clm-stats-graph-v6">
                    <div id="clm-stats-bar-yes-v6" class="clm-stats-bar-v6 yes" style="width: 0%;"></div>
                    <div id="clm-stats-label-yes-v6" class="clm-stats-label-v6">0%</div>
                    <div id="clm-stats-bar-no-v6" class="clm-stats-bar-v6 no" style="width: 0%;"></div>
                    <div id="clm-stats-label-no-v6" class="clm-stats-label-v6">0%</div>
                </div>
                <p id="clm-stats-total-v6" style="font-size: 14px; color: #718096; margin-top: 10px; margin-bottom: 0;">Total de 0 votes.</p>
            </div>
        </div>
    </div>
HTML;

    // --- JavaScript (adapté pour v6) ---
    $vote_nonce = wp_create_nonce('clm-patreon-poll-nonce');
    $results_nonce = wp_create_nonce('clm-patreon-results-nonce');
    $ajax_url = admin_url('admin-ajax.php');

    $javascript_code = <<<JS
    jQuery(document).ready(function($) {
        const widget = $('#clm-patreon-widget-v6');
        const overlay = $('#clm-poll-modal-overlay-v6');
        const pollView = $('#clm-poll-view-v6');
        const statsView = $('#clm-stats-view-v6');
        const closeModalBtn = $('#clm-modal-close-v6');
        
        const openModal = () => { overlay.css('display', 'flex').hide().fadeIn(300); };
        const closeModal = () => { overlay.fadeOut(300); };
        const handleScroll = () => {
            if (window.scrollY > 10) {
                widget.addClass('clm-widget-hidden-v6');
            } else {
                widget.removeClass('clm-widget-hidden-v6');
            }
        };

        handleScroll();
        $(window).on('scroll', handleScroll);

        widget.on('click', function(e) {
            e.preventDefault();
            openModal();

            if (localStorage.getItem('clmPatreonPollAnsweredV6')) {
                statsView.show();
                pollView.hide();
                
                const questionText = $('#clm-poll-question-text-v6').text();
                $('#clm-stats-question-v6').text(questionText);

                $('#clm-stats-bar-yes-v6, #clm-stats-bar-no-v6').css('width', '0%');

                $.ajax({
                    url: '{$ajax_url}',
                    type: 'POST',
                    data: { action: 'clm_get_patreon_poll_results_v6', _ajax_nonce: '{$results_nonce}' },
                    success: function(response) {
                        if(response.success) {
                            const yes = response.data.yes || 0;
                            const no = response.data.no || 0;
                            const total = yes + no;
                            const yes_percent = total > 0 ? Math.round((yes / total) * 100) : 0;
                            const no_percent = total > 0 ? (100 - yes_percent) : 0;

                            $('#clm-stats-bar-yes-v6').css('width', yes_percent + '%').text(yes_percent > 15 ? 'Oui (' + yes_percent + ' %)' : '');
                            $('#clm-stats-label-yes-v6').text(yes + ' votes');
                            $('#clm-stats-bar-no-v6').css('width', no_percent + '%').text(no_percent > 15 ? 'Non (' + no_percent + ' %)' : '');
                            $('#clm-stats-label-no-v6').text(no + ' votes');
                            $('#clm-stats-total-v6').text('Total de ' + total + ' votes.');
                        }
                    }
                });
            } else {
                pollView.show();
                statsView.hide();
            }
        });

        $('#clm-poll-yes-v6, #clm-poll-no-v6').on('click', function() {
            const vote = $(this).attr('id') === 'clm-poll-yes-v6' ? 'yes' : 'no';
            
            $.ajax({
                url: '{$ajax_url}',
                type: 'POST',
                data: { action: 'clm_handle_patreon_poll_v6', _ajax_nonce: '{$vote_nonce}', vote: vote },
                success: function(response) {
                    if(response.success) {
                        localStorage.setItem('clmPatreonPollAnsweredV6', 'true');
                        closeModal();
                        setTimeout(() => widget.trigger('click'), 350);
                    }
                }
            });
        });

        closeModalBtn.on('click', closeModal);
        overlay.on('click', function(e) {
            if ($(e.target).is(overlay)) {
                closeModal();
            }
        });
    });
JS;

    wp_enqueue_script('jquery');
    wp_add_inline_script('jquery', $javascript_code);
}

// == PARTIE 2: GÈRE LA RÉPONSE AJAX POUR LE VOTE ==
add_action('wp_ajax_clm_handle_patreon_poll_v6', 'clm_handle_patreon_poll_v6_callback');
add_action('wp_ajax_nopriv_clm_handle_patreon_poll_v6', 'clm_handle_patreon_poll_v6_callback');

function clm_handle_patreon_poll_v6_callback() {
    check_ajax_referer('clm-patreon-poll-nonce');
    if (!isset($_POST['vote']) || !in_array($_POST['vote'], ['yes', 'no'])) {
        wp_send_json_error('Vote invalide.', 400);
    }
    $vote = sanitize_text_field($_POST['vote']);
$results = get_option('clm_patreon_poll_results', ['yes' => 0, 'no' => 0]);
    $results[$vote]++;
    update_option('clm_patreon_poll_results', $results);
    wp_send_json_success('Vote enregistré.');
}

// == PARTIE 3: GÈRE LA RÉCUPÉRATION DES STATS ==
add_action('wp_ajax_clm_get_patreon_poll_results_v6', 'clm_get_patreon_poll_results_v6_callback');
add_action('wp_ajax_nopriv_clm_get_patreon_poll_results_v6', 'clm_get_patreon_poll_results_v6_callback');

function clm_get_patreon_poll_results_v6_callback() {
    check_ajax_referer('clm-patreon-results-nonce');
    $results = get_option('clm_patreon_poll_results', ['yes' => 0, 'no' => 0]);
    wp_send_json_success($results);
}

