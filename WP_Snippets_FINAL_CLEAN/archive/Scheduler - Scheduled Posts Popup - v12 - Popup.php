/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/084__id-144__post-scheduled-posts-popup-5.php
 * Display name: POST - Scheduled posts popup 5
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 144
 * Online modified: 2026-01-19 09:40:29
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: POST - Scheduled posts popup (2 variantes)
 * Version: v5
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/089__id-149__post-scheduled-posts-popup-6.php
 * Is family latest: non
 * Archive reasons: version-history-older
 * Features: ajax, footer-injection
 * Dependances probables: jQuery
 * Hooks WP: wp_footer, wp_ajax_clm_handle_patreon_poll_v5, wp_ajax_nopriv_clm_handle_patreon_poll_v5, wp_ajax_clm_get_patreon_poll_results_v5, wp_ajax_nopriv_clm_get_patreon_poll_results_v5
 * Fonctions clefs: clm_patreon_poll_v5_widget_and_script, clm_handle_patreon_poll_v5_callback, clm_get_patreon_poll_results_v5_callback
 * Actions AJAX: clm_handle_patreon_poll_v5, clm_get_patreon_poll_results_v5
 * Lignes / octets (brut): 243 / 13893
 * Hash code normalise (sha256): 676dca76fb755356862b546a78a07449fe0fddf93706549bcbe4c16fc2552184
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-scheduled-posts-popup__v005.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-scheduled-posts-popup__v005.php
 * Resume fonctionnalites: interactions AJAX, UI frontend (CSS/HTML), automatisation date/programmation, 5 hook(s) WP, 3 fonction(s) clef
 * Features detectees: ajax, scheduler-date, css-ui, footer-head-injection, svg-ui
 * Dependances probables: jQuery, WordPress AJAX
 * Hooks WP: wp_footer, wp_ajax_clm_handle_patreon_poll_v5, wp_ajax_nopriv_clm_handle_patreon_poll_v5, wp_ajax_clm_get_patreon_poll_results_v5, wp_ajax_nopriv_clm_get_patreon_poll_results_v5
 * Fonctions clefs: clm_patreon_poll_v5_widget_and_script, clm_handle_patreon_poll_v5_callback, clm_get_patreon_poll_results_v5_callback
 * Actions AJAX: clm_handle_patreon_poll_v5, clm_get_patreon_poll_results_v5
 * Selecteurs / IDs: #E2E8F0, #3182CE, #DD6B20, #38A169, #805AD5, #D53F8C, #clm-patreon-widget-v5, #clm-poll-modal-overlay-v5, #clm-poll-view-v5, #clm-stats-view-v5, #clm-modal-close-v5, #clm-poll-question-text-v5 … (+2)
 * APIs WP detectees: add_action, wp_create_nonce, admin_url, wp_enqueue_script, wp_add_inline_script, wp_send_json_error, get_option, wp_send_json_success
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 256 / 14728
 * Empreinte code (sha256): 23d27481bbe62e6e2a9788edd6d1a864052914120e3e7bfd57053e8461ebae1d
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-scheduled-posts-popup__v005.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-scheduled-posts-popup__v005.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: frontend_ui_widget
 * Domaine: post-front
 * Confiance: high
 * Scores (top): scheduler_posts=16, frontend_ui_widget=8, rest_ajax_integration=6, post_footer_ui=5
 * Raisons principales: scheduler-date, scheduled, schedule, popup
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Titre: Jauge d'articles programmés (V5 - Multi-niveaux)
 * Description: Affiche une jauge multi-niveaux. Chaque 100 articles, un nouveau cercle de couleur se superpose.
 */

// == PARTIE 1: AFFICHE LE WIDGET, LA MODALE ET INJECTE LE SCRIPT ==

add_action('wp_footer', 'clm_patreon_poll_v5_widget_and_script');

function clm_patreon_poll_v5_widget_and_script() {
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

    // --- CSS (adapté pour v5) ---
    echo <<<CSS
    <style>
        #clm-patreon-widget-v5 { position: fixed; bottom: 20px; right: 200px; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; align-items: center; padding: 5px 15px 5px 8px; z-index: 9999; transition: transform 0.3s ease; cursor: pointer; }
        #clm-patreon-widget-v5:hover { transform: translateY(-3px); }
        .clm-modal-overlay-v5 { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); z-index: 10000; display: none; align-items: center; justify-content: center; animation: clm-fadeIn-v5 0.3s ease-out; }
        .clm-modal-content-v5 { background: #fff; border-radius: 16px; padding: 35px 45px; width: 90%; max-width: 480px; text-align: center; position: relative; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: scale(0.95); animation: clm-scaleIn-v5 0.3s ease-out forwards; }
        .clm-modal-content-v5 h2 { margin-top: 0; font-size: 24px; font-weight: 700; color: #1A202C; }
        .clm-modal-content-v5 p { margin-bottom: 30px; color: #4A5568; font-size: 17px; line-height: 1.6; }
        .clm-modal-close-v5 { position: absolute; top: 15px; right: 20px; font-size: 30px; color: #A0AEC0; cursor: pointer; line-height: 1; transition: color 0.2s; }
        .clm-modal-close-v5:hover { color: #4A5568; }
        .clm-modal-buttons-v5 { display: flex; justify-content: center; gap: 15px; }
        .clm-modal-buttons-v5 button { border: none; border-radius: 10px; padding: 14px 24px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        #clm-poll-yes-v5 { background-color: #3182CE; color: white; }
        #clm-poll-yes-v5:hover { background-color: #2B6CB0; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(49, 130, 206, 0.3); }
        #clm-poll-no-v5 { background-color: #E2E8F0; color: #2D3748; }
        #clm-poll-no-v5:hover { background-color: #CBD5E0; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
        #clm-stats-question-v5 { font-size: 16px; color: #718096; margin-bottom: 20px; padding: 15px; background-color: #F7FAFC; border-radius: 8px; font-style: italic; }
        .clm-stats-graph-v5 { margin-top: 20px; }
        .clm-stats-bar-v5 { height: 30px; line-height: 30px; color: white; font-weight: bold; text-align: left; padding-left: 12px; border-radius: 6px; white-space: nowrap; overflow: hidden; margin-bottom: 10px; transition: width 0.6s cubic-bezier(0.25, 1, 0.5, 1); }
        .clm-stats-bar-v5.yes { background-color: #38A169; }
        .clm-stats-bar-v5.no { background-color: #E53E3E; }
        .clm-stats-label-v5 { text-align: right; font-size: 14px; color: #718096; margin-top: -8px; margin-bottom: 18px; }
        @keyframes clm-fadeIn-v5 { from { opacity: 0; } to { opacity: 1; } }
        @keyframes clm-scaleIn-v5 { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    </style>
CSS;

    // --- HTML du Widget (avec SVG multi-niveaux) et de la Modale ---
    echo <<<HTML
    <div id="clm-patreon-widget-v5" role="button" tabindex="0" aria-label="Sondage Patreon">
        <div style="position: relative; width: {$viewbox_size}px; height: {$viewbox_size}px;">
            <svg style="width: 100%; height: 100%; transform: rotate(-90deg);" viewBox="0 0 {$viewbox_size} {$viewbox_size}">
                <circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_fond};" cx="{$center}" cy="{$center}" r="{$radius}"></circle>
                {$svg_circles}
            </svg>
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 14px; font-weight: 700; color: #2D3748;">{$count}</div>
        </div>
        <div style="display: flex; flex-direction: column; margin-left: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;"><span style="font-size: 15px; font-weight: 600; color: #1A202C;">Articles programmés</span><span style="font-size: 12px; color: #718096; margin-top: 2px;">Jusqu'au {$last_post_date_formatted}</span></div>
    </div>

    <div id="clm-poll-modal-overlay-v5" class="clm-modal-overlay-v5">
        <div class="clm-modal-content-v5">
            <span id="clm-modal-close-v5" class="clm-modal-close-v5">&times;</span>
            <div id="clm-poll-view-v5" style="display: none;">
                <h2>Sondage Patreon</h2>
                <p id="clm-poll-question-text-v5">êtes-vous prêt(e) à me soutenir sur Patreon pour lire mes articles en avance ?</p>
                <div class="clm-modal-buttons-v5">
                    <button id="clm-poll-yes-v5">Oui, je suis intéressé(e)</button>
                    <button id="clm-poll-no-v5">Non, je ne suis pas intéressé(e)</button>
                </div>
            </div>
            <div id="clm-stats-view-v5" style="display: none;">
                <h2>Résultats du sondage</h2>
                <p id="clm-stats-question-v5"></p>
                <div class="clm-stats-graph-v5">
                    <div id="clm-stats-bar-yes-v5" class="clm-stats-bar-v5 yes" style="width: 0%;"></div>
                    <div id="clm-stats-label-yes-v5" class="clm-stats-label-v5">0%</div>
                    <div id="clm-stats-bar-no-v5" class="clm-stats-bar-v5 no" style="width: 0%;"></div>
                    <div id="clm-stats-label-no-v5" class="clm-stats-label-v5">0%</div>
                </div>
                <p id="clm-stats-total-v5" style="font-size: 14px; color: #718096; margin-top: 10px; margin-bottom: 0;">Total de 0 votes.</p>
            </div>
        </div>
    </div>
HTML;

    // --- JavaScript (adapté pour v5) ---
    $vote_nonce = wp_create_nonce('clm-patreon-poll-nonce-v5');
    $results_nonce = wp_create_nonce('clm-patreon-results-nonce-v5');
    $ajax_url = admin_url('admin-ajax.php');

    $javascript_code = <<<JS
    jQuery(document).ready(function($) {
        const widget = $('#clm-patreon-widget-v5');
        const overlay = $('#clm-poll-modal-overlay-v5');
        const pollView = $('#clm-poll-view-v5');
        const statsView = $('#clm-stats-view-v5');
        const closeModalBtn = $('#clm-modal-close-v5');
        
        const openModal = () => { overlay.css('display', 'flex').hide().fadeIn(300); };
        const closeModal = () => { overlay.fadeOut(300); };

        widget.on('click', function(e) {
            e.preventDefault();
            openModal();

            if (localStorage.getItem('clmPatreonPollAnswered-v5')) {
                statsView.show();
                pollView.hide();
                
                const questionText = $('#clm-poll-question-text-v5').text();
                $('#clm-stats-question-v5').text(questionText);

                $('#clm-stats-bar-yes-v5, #clm-stats-bar-no-v5').css('width', '0%');

                $.ajax({
                    url: '{$ajax_url}',
                    type: 'POST',
                    data: { action: 'clm_get_patreon_poll_results_v5', _ajax_nonce: '{$results_nonce}' },
                    success: function(response) {
                        if(response.success) {
                            const yes = response.data.yes || 0;
                            const no = response.data.no || 0;
                            const total = yes + no;
                            const yes_percent = total > 0 ? Math.round((yes / total) * 100) : 0;
                            const no_percent = total > 0 ? (100 - yes_percent) : 0;

                            $('#clm-stats-bar-yes-v5').css('width', yes_percent + '%').text(yes_percent > 15 ? 'Oui (' + yes_percent + ' %)' : '');
                            $('#clm-stats-label-yes-v5').text(yes + ' votes');
                            $('#clm-stats-bar-no-v5').css('width', no_percent + '%').text(no_percent > 15 ? 'Non (' + no_percent + ' %)' : '');
                            $('#clm-stats-label-no-v5').text(no + ' votes');
                            $('#clm-stats-total-v5').text('Total de ' + total + ' votes.');
                        }
                    }
                });
            } else {
                pollView.show();
                statsView.hide();
            }
        });

        $('#clm-poll-yes-v5, #clm-poll-no-v5').on('click', function() {
            const vote = $(this).attr('id') === 'clm-poll-yes-v5' ? 'yes' : 'no';
            
            $.ajax({
                url: '{$ajax_url}',
                type: 'POST',
                data: { action: 'clm_handle_patreon_poll_v5', _ajax_nonce: '{$vote_nonce}', vote: vote },
                success: function(response) {
                    if(response.success) {
                        localStorage.setItem('clmPatreonPollAnswered-v5', 'true');
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
add_action('wp_ajax_clm_handle_patreon_poll_v5', 'clm_handle_patreon_poll_v5_callback');
add_action('wp_ajax_nopriv_clm_handle_patreon_poll_v5', 'clm_handle_patreon_poll_v5_callback');

function clm_handle_patreon_poll_v5_callback() {
    check_ajax_referer('clm-patreon-poll-nonce-v5');
    if (!isset($_POST['vote']) || !in_array($_POST['vote'], ['yes', 'no'])) {
        wp_send_json_error('Vote invalide.', 400);
    }
    $vote = sanitize_text_field($_POST['vote']);
    $results = get_option('clm_patreon_poll_results_v5', ['yes' => 0, 'no' => 0]);
    $results[$vote]++;
    update_option('clm_patreon_poll_results_v5', $results);
    wp_send_json_success('Vote enregistré.');
}

// == PARTIE 3: GÈRE LA RÉCUPÉRATION DES STATS ==
add_action('wp_ajax_clm_get_patreon_poll_results_v5', 'clm_get_patreon_poll_results_v5_callback');
add_action('wp_ajax_nopriv_clm_get_patreon_poll_results_v5', 'clm_get_patreon_poll_results_v5_callback');

function clm_get_patreon_poll_results_v5_callback() {
    check_ajax_referer('clm-patreon-results-nonce-v5');
    $results = get_option('clm_patreon_poll_results_v5', ['yes' => 0, 'no' => 0]);
    wp_send_json_success($results);
}

