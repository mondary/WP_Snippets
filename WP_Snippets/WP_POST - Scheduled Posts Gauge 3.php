<?php
/**
 * Titre: Jauge d'articles programmés avec sondage Patreon (V2)
 * Description: Affiche une jauge cliquable. Ouvre une modale pour un sondage ou pour afficher les résultats avec un graphique.
 */

// == PARTIE 1: AFFICHE LE WIDGET, LA MODALE ET INJECTE LE SCRIPT ==

add_action('wp_footer', 'clm_patreon_poll_v2_widget_and_script');

function clm_patreon_poll_v2_widget_and_script() {
    // --- Récupération des données (inchangé) ---
    $query_args = [
        'post_type' => 'post', 'post_status' => 'future', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC',
    ];
    $query = new WP_Query($query_args);
    $count = $query->found_posts;

    if ($count <= 0) return;

    $last_post_date_formatted = $query->have_posts() ? date_i18n(get_option('date_format'), strtotime($query->posts[0]->post_date)) : '';

    // --- Paramètres de la jauge (inchangé) ---
    $goal = 100; $color_progression = '#2B6CB0'; $color_fond = '#E2E8F0';
    $progress = min($count / $goal, 1);
    $radius = 16; $stroke_width = 6; $viewbox_size = 40; $center = $viewbox_size / 2;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference * (1 - $progress);

    // --- NOUVEAU: CSS pour la modale et le graphique ---
    echo <<<CSS
    <style>
        #clm-patreon-widget { position: fixed; bottom: 20px; right: 200px; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; align-items: center; padding: 5px 15px 5px 8px; z-index: 9999; transition: transform 0.3s ease; cursor: pointer; }
        #clm-patreon-widget:hover { transform: translateY(-3px); }
        
        /* CSS pour l'overlay de la modale */
        .clm-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 10000; display: none; align-items: center; justify-content: center; animation: clm-fadeIn 0.3s ease; }
        
        /* CSS pour le contenu de la modale */
        .clm-modal-content { background: #fff; border-radius: 10px; padding: 30px 40px; width: 90%; max-width: 450px; text-align: center; position: relative; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .clm-modal-content h2 { margin-top: 0; font-size: 22px; color: #1A202C; }
        .clm-modal-content p { margin-bottom: 25px; color: #4A5568; font-size: 16px; }
        .clm-modal-close { position: absolute; top: 10px; right: 15px; font-size: 28px; color: #718096; cursor: pointer; line-height: 1; }
        
        /* CSS pour les boutons de la modale */
        .clm-modal-buttons button { border: none; border-radius: 8px; padding: 12px 20px; font-size: 16px; cursor: pointer; transition: all 0.2s; margin: 0 5px; }
        #clm-poll-yes { background-color: #3182CE; color: white; }
        #clm-poll-yes:hover { background-color: #2B6CB0; transform: translateY(-1px); }
        #clm-poll-no { background-color: #E2E8F0; color: #2D3748; }
        #clm-poll-no:hover { background-color: #CBD5E0; transform: translateY(-1px); }

        /* CSS pour le graphique */
        .clm-stats-graph { margin-top: 20px; }
        .clm-stats-bar { height: 28px; line-height: 28px; color: white; font-weight: bold; text-align: left; padding-left: 10px; border-radius: 5px; white-space: nowrap; overflow: hidden; margin-bottom: 8px; transition: width 0.5s ease-in-out; }
        .clm-stats-bar.yes { background-color: #38A169; }
        .clm-stats-bar.no { background-color: #E53E3E; }
        .clm-stats-label { text-align: right; font-size: 13px; color: #718096; margin-top: -5px; margin-bottom: 15px; }
        @keyframes clm-fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
CSS;

    // --- HTML du Widget (inchangé) et de la Modale (NOUVEAU) ---
    echo <<<HTML
    <!-- Le widget -->
    <div id="clm-patreon-widget" role="button" tabindex="0" aria-label="Sondage Patreon">
        <div style="position: relative; width: {$viewbox_size}px; height: {$viewbox_size}px;"><svg style="width: 100%; height: 100%; transform: rotate(-90deg);" viewBox="0 0 {$viewbox_size} {$viewbox_size}"><circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_fond};" cx="{$center}" cy="{$center}" r="{$radius}"></circle><circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_progression}; stroke-linecap: round; stroke-dasharray: {$circumference}; stroke-dashoffset: {$offset}; transition: stroke-dashoffset 1.5s ease-out;" cx="{$center}" cy="{$center}" r="{$radius}"></circle></svg><div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 14px; font-weight: 700; color: #2D3748;">{$count}</div></div>
        <div style="display: flex; flex-direction: column; margin-left: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;"><span style="font-size: 15px; font-weight: 600; color: #1A202C;">Articles programmés</span><span style="font-size: 12px; color: #718096; margin-top: 2px;">Jusqu'au {$last_post_date_formatted}</span></div>
    </div>

    <!-- La modale (cachée par défaut) -->
    <div id="clm-poll-modal-overlay" class="clm-modal-overlay">
        <div class="clm-modal-content">
            <span id="clm-modal-close" class="clm-modal-close">&times;</span>
            
            <div id="clm-poll-view" style="display: none;">
                <h2>Sondage Patreon</h2>
                <p>Seriez-vous prêt(e) à nous soutenir sur Patreon pour lire nos articles en avance ?</p>
                <div class="clm-modal-buttons">
                    <button id="clm-poll-yes">Oui, je suis intéressé(e)</button>
                    <button id="clm-poll-no">Non, pas pour l'instant</button>
                </div>
            </div>

            <div id="clm-stats-view" style="display: none;">
                <h2>Résultats du sondage</h2>
                <div class="clm-stats-graph">
                    <div id="clm-stats-bar-yes" class="clm-stats-bar yes" style="width: 0%;"></div>
                    <div id="clm-stats-label-yes" class="clm-stats-label">0%</div>
                    <div id="clm-stats-bar-no" class="clm-stats-bar no" style="width: 0%;"></div>
                    <div id="clm-stats-label-no" class="clm-stats-label">0%</div>
                </div>
                <p id="clm-stats-total" style="font-size: 14px; color: #718096; margin-bottom: 0;">Total de 0 votes.</p>
            </div>
        </div>
    </div>
HTML;

    // --- JavaScript mis à jour pour gérer la modale ---
    $vote_nonce = wp_create_nonce('clm-patreon-poll-nonce');
    $results_nonce = wp_create_nonce('clm-patreon-results-nonce');
    $ajax_url = admin_url('admin-ajax.php');

    $javascript_code = <<<JS
    jQuery(document).ready(function($) {
        const widget = $('#clm-patreon-widget');
        const overlay = $('#clm-poll-modal-overlay');
        const pollView = $('#clm-poll-view');
        const statsView = $('#clm-stats-view');
        const closeModalBtn = $('#clm-modal-close');
        
        const openModal = () => { overlay.css('display', 'flex').hide().fadeIn(200); };
        const closeModal = () => { overlay.fadeOut(200); };

        widget.on('click', function(e) {
            e.preventDefault();
            openModal();

            if (localStorage.getItem('clmPatreonPollAnswered')) {
                statsView.show();
                pollView.hide();
                
                $('#clm-stats-bar-yes, #clm-stats-bar-no').css('width', '0%'); // Reset before loading

                $.ajax({
                    url: '{$ajax_url}',
                    type: 'POST',
                    data: { action: 'clm_get_patreon_poll_results', _ajax_nonce: '{$results_nonce}' },
                    success: function(response) {
                        if(response.success) {
                            const yes = response.data.yes || 0;
                            const no = response.data.no || 0;
                            const total = yes + no;
                            const yes_percent = total > 0 ? Math.round((yes / total) * 100) : 0;
                            const no_percent = total > 0 ? 100 - yes_percent : 0;

                            $('#clm-stats-bar-yes').css('width', yes_percent + '%').text(yes_percent > 15 ? 'Oui' : '');
                            $('#clm-stats-label-yes').text(yes_percent + '% (' + yes + ' votes)');
                            $('#clm-stats-bar-no').css('width', no_percent + '%').text(no_percent > 15 ? 'Non' : '');
                            $('#clm-stats-label-no').text(no_percent + '% (' + no + ' votes)');
                            $('#clm-stats-total').text('Total de ' + total + ' votes.');
                        }
                    }
                });
            } else {
                pollView.show();
                statsView.hide();
            }
        });

        $('#clm-poll-yes, #clm-poll-no').on('click', function() {
            const vote = $(this).attr('id') === 'clm-poll-yes' ? 'yes' : 'no';
            
            $.ajax({
                url: '{$ajax_url}',
                type: 'POST',
                data: { action: 'clm_handle_patreon_poll', _ajax_nonce: '{$vote_nonce}', vote: vote },
                success: function(response) {
                    if(response.success) {
                        localStorage.setItem('clmPatreonPollAnswered', 'true');
                        closeModal();
                        setTimeout(() => widget.trigger('click'), 300);
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
add_action('wp_ajax_clm_handle_patreon_poll', 'clm_handle_patreon_poll_callback');
add_action('wp_ajax_nopriv_clm_handle_patreon_poll', 'clm_handle_patreon_poll_callback');

function clm_handle_patreon_poll_callback() {
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
add_action('wp_ajax_clm_get_patreon_poll_results', 'clm_get_patreon_poll_results_callback');
add_action('wp_ajax_nopriv_clm_get_patreon_poll_results', 'clm_get_patreon_poll_results_callback');

function clm_get_patreon_poll_results_callback() {
    check_ajax_referer('clm-patreon-results-nonce');
    $results = get_option('clm_patreon_poll_results', ['yes' => 0, 'no' => 0]);
    wp_send_json_success($results);
}

?>