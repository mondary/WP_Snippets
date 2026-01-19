<?php
/**
 * Titre: Jauge d'articles programmés avec sondage Patreon (V4)
 * Description: Affiche une jauge cliquable. Ouvre une modale plus jolie pour un sondage ou pour afficher les résultats (avec la question rappelée) via un graphique.
 */

// == PARTIE 1: AFFICHE LE WIDGET, LA MODALE ET INJECTE LE SCRIPT ==

add_action('wp_footer', 'clm_patreon_poll_v4_widget_and_script');

function clm_patreon_poll_v4_widget_and_script() {
    // --- Récupération des données (inchangé) ---
    $query_args = [
        'post_type' => 'post', 'post_status' => 'future', 'posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC',
    ];
    $query = new WP_Query($query_args);
    $count = $query->found_posts;

    if ($count <= 0) return;

    $last_post_date_formatted = $query->have_posts() ? date_i18n('j F Y', strtotime($query->posts[0]->post_date)) : '';

    // --- Paramètres de la jauge (inchangé) ---
    $goal = 100; $color_progression = '#2B6CB0'; $color_fond = '#E2E8F0';
    $progress = min($count / $goal, 1);
    $radius = 16; $stroke_width = 6; $viewbox_size = 40; $center = $viewbox_size / 2;
    $circumference = 2 * M_PI * $radius;
    $offset = $circumference * (1 - $progress);

    // --- NOUVEAU: CSS amélioré pour la modale ---
    echo <<<CSS
    <style>
        #clm-patreon-widget-v4 { position: fixed; bottom: 20px; right: 200px; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); display: flex; align-items: center; padding: 5px 15px 5px 8px; z-index: 9999; transition: transform 0.3s ease; cursor: pointer; }
        #clm-patreon-widget-v4:hover { transform: translateY(-3px); }
        
        .clm-modal-overlay-v4 { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); z-index: 10000; display: none; align-items: center; justify-content: center; animation: clm-fadeIn-v4 0.3s ease-out; }
        
        .clm-modal-content-v4 { background: #fff; border-radius: 16px; padding: 35px 45px; width: 90%; max-width: 480px; text-align: center; position: relative; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transform: scale(0.95); animation: clm-scaleIn-v4 0.3s ease-out forwards; }
        .clm-modal-content-v4 h2 { margin-top: 0; font-size: 24px; font-weight: 700; color: #1A202C; }
        .clm-modal-content-v4 p { margin-bottom: 30px; color: #4A5568; font-size: 17px; line-height: 1.6; }
        .clm-modal-close-v4 { position: absolute; top: 15px; right: 20px; font-size: 30px; color: #A0AEC0; cursor: pointer; line-height: 1; transition: color 0.2s; }
        .clm-modal-close-v4:hover { color: #4A5568; }
        
                .clm-modal-buttons-v4 { display: flex; justify-content: center; gap: 15px; }
        .clm-modal-buttons-v4 button { border: none; border-radius: 10px; padding: 14px 24px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        #clm-poll-yes-v4 { background-color: #3182CE; color: white; }
        #clm-poll-yes-v4:hover { background-color: #2B6CB0; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(49, 130, 206, 0.3); }
        #clm-poll-no-v4 { background-color: #E2E8F0; color: #2D3748; }
        #clm-poll-no-v4:hover { background-color: #CBD5E0; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }

        /* Rappel de la question dans les résultats */
        #clm-stats-question-v4 { font-size: 16px; color: #718096; margin-bottom: 20px; padding: 15px; background-color: #F7FAFC; border-radius: 8px; font-style: italic; }

        .clm-stats-graph-v4 { margin-top: 20px; }
        .clm-stats-bar-v4 { height: 30px; line-height: 30px; color: white; font-weight: bold; text-align: left; padding-left: 12px; border-radius: 6px; white-space: nowrap; overflow: hidden; margin-bottom: 10px; transition: width 0.6s cubic-bezier(0.25, 1, 0.5, 1); }
        .clm-stats-bar-v4.yes { background-color: #38A169; }
        .clm-stats-bar-v4.no { background-color: #E53E3E; }
        .clm-stats-label-v4 { text-align: right; font-size: 14px; color: #718096; margin-top: -8px; margin-bottom: 18px; }
        
        @keyframes clm-fadeIn-v4 { from { opacity: 0; } to { opacity: 1; } }
        @keyframes clm-scaleIn-v4 { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
    </style>
CSS;

    // --- HTML du Widget et de la Modale améliorée ---
    echo <<<HTML
    <div id="clm-patreon-widget-v4" role="button" tabindex="0" aria-label="Sondage Patreon">
        <div style="position: relative; width: {$viewbox_size}px; height: {$viewbox_size}px;"><svg style="width: 100%; height: 100%; transform: rotate(-90deg);" viewBox="0 0 {$viewbox_size} {$viewbox_size}"><circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_fond};" cx="{$center}" cy="{$center}" r="{$radius}"></circle><circle style="fill: none; stroke-width: {$stroke_width}; stroke: {$color_progression}; stroke-linecap: round; stroke-dasharray: {$circumference}; stroke-dashoffset: {$offset}; transition: stroke-dashoffset 1.5s ease-out;" cx="{$center}" cy="{$center}" r="{$radius}"></circle></svg><div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 14px; font-weight: 700; color: #2D3748;">{$count}</div></div>
        <div style="display: flex; flex-direction: column; margin-left: 12px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;"><span style="font-size: 15px; font-weight: 600; color: #1A202C;">Articles programmés</span><span style="font-size: 12px; color: #718096; margin-top: 2px;">Jusqu'au {$last_post_date_formatted}</span></div>
    </div>

    <div id="clm-poll-modal-overlay-v4" class="clm-modal-overlay-v4">
        <div class="clm-modal-content-v4">
            <span id="clm-modal-close-v4" class="clm-modal-close-v4">&times;</span>
            
            <div id="clm-poll-view-v4" style="display: none;">
                <h2>Sondage Patreon</h2>
                <p id="clm-poll-question-text-v4">êtes-vous prêt(e) à me soutenir sur Patreon pour lire mes articles en avance ?</p>
                <div class="clm-modal-buttons-v4">
                    <button id="clm-poll-yes-v4">Oui, je suis intéressé(e)</button>
                    <button id="clm-poll-no-v4">Non, je ne suis pas intéressé(e)</button>
                </div>
            </div>

            <div id="clm-stats-view-v4" style="display: none;">
                <h2>Résultats du sondage</h2>
                <p id="clm-stats-question-v4"></p> <!-- NOUVEAU: Emplacement pour le rappel de la question -->
                <div class="clm-stats-graph-v4">
                    <div id="clm-stats-bar-yes-v4" class="clm-stats-bar-v4 yes" style="width: 0%;"></div>
                    <div id="clm-stats-label-yes-v4" class="clm-stats-label-v4">0%</div>
                    <div id="clm-stats-bar-no-v4" class="clm-stats-bar-v4 no" style="width: 0%;"></div>
                    <div id="clm-stats-label-no-v4" class="clm-stats-label-v4">0%</div>
                </div>
                <p id="clm-stats-total-v4" style="font-size: 14px; color: #718096; margin-top: 10px; margin-bottom: 0;">Total de 0 votes.</p>
            </div>
        </div>
    </div>
HTML;

    // --- JavaScript mis à jour pour la nouvelle modale et le rappel de la question ---
    $vote_nonce = wp_create_nonce('clm-patreon-poll-nonce-v4');
    $results_nonce = wp_create_nonce('clm-patreon-results-nonce-v4');
    $ajax_url = admin_url('admin-ajax.php');

    $javascript_code = <<<JS
    jQuery(document).ready(function($) {
        const widget = $('#clm-patreon-widget-v4');
        const overlay = $('#clm-poll-modal-overlay-v4');
        const pollView = $('#clm-poll-view-v4');
        const statsView = $('#clm-stats-view-v4');
        const closeModalBtn = $('#clm-modal-close-v4');
        
        const openModal = () => { overlay.css('display', 'flex').hide().fadeIn(300); };
        const closeModal = () => { overlay.fadeOut(300); };

        widget.on('click', function(e) {
            e.preventDefault();
            openModal();

            if (localStorage.getItem('clmPatreonPollAnswered-v4')) {
                statsView.show();
                pollView.hide();
                
                // NOUVEAU: Rappeler la question dans la vue des résultats
                const questionText = $('#clm-poll-question-text-v4').text();
                $('#clm-stats-question-v4').text(questionText);

                $('#clm-stats-bar-yes-v4, #clm-stats-bar-no-v4').css('width', '0%');

                $.ajax({
                    url: '{$ajax_url}',
                    type: 'POST',
                    data: { action: 'clm_get_patreon_poll_results_v4', _ajax_nonce: '{$results_nonce}' },
                    success: function(response) {
                        if(response.success) {
                            const yes = response.data.yes || 0;
                            const no = response.data.no || 0;
                            const total = yes + no;
                            const yes_percent = total > 0 ? Math.round((yes / total) * 100) : 0;
                            const no_percent = total > 0 ? (100 - yes_percent) : 0;

                            $('#clm-stats-bar-yes-v4').css('width', yes_percent + '%').text(yes_percent > 15 ? 'Oui (' + yes_percent + ' %)' : '');
                            $('#clm-stats-label-yes-v4').text(yes + ' votes');
                            $('#clm-stats-bar-no-v4').css('width', no_percent + '%').text(no_percent > 15 ? 'Non (' + no_percent + ' %)' : '');
                            $('#clm-stats-label-no-v4').text(no + ' votes');
                            $('#clm-stats-total-v4').text('Total de ' + total + ' votes.');
                        }
                    }
                });
            } else {
                pollView.show();
                statsView.hide();
            }
        });

        $('#clm-poll-yes-v4, #clm-poll-no-v4').on('click', function() {
            const vote = $(this).attr('id') === 'clm-poll-yes-v4' ? 'yes' : 'no';
            
            $.ajax({
                url: '{$ajax_url}',
                type: 'POST',
                data: { action: 'clm_handle_patreon_poll_v4', _ajax_nonce: '{$vote_nonce}', vote: vote },
                success: function(response) {
                    if(response.success) {
                        localStorage.setItem('clmPatreonPollAnswered-v4', 'true');
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
add_action('wp_ajax_clm_handle_patreon_poll_v4', 'clm_handle_patreon_poll_v4_callback');
add_action('wp_ajax_nopriv_clm_handle_patreon_poll_v4', 'clm_handle_patreon_poll_v4_callback');

function clm_handle_patreon_poll_v4_callback() {
    check_ajax_referer('clm-patreon-poll-nonce-v4');
    if (!isset($_POST['vote']) || !in_array($_POST['vote'], ['yes', 'no'])) {
        wp_send_json_error('Vote invalide.', 400);
    }
    $vote = sanitize_text_field($_POST['vote']);
    $results = get_option('clm_patreon_poll_results_v4', ['yes' => 0, 'no' => 0]);
    $results[$vote]++;
    update_option('clm_patreon_poll_results_v4', $results);
    wp_send_json_success('Vote enregistré.');
}

// == PARTIE 3: GÈRE LA RÉCUPÉRATION DES STATS ==
add_action('wp_ajax_clm_get_patreon_poll_results_v4', 'clm_get_patreon_poll_results_v4_callback');
add_action('wp_ajax_nopriv_clm_get_patreon_poll_results_v4', 'clm_get_patreon_poll_results_v4_callback');

function clm_get_patreon_poll_results_v4_callback() {
    check_ajax_referer('clm-patreon-results-nonce-v4');
    $results = get_option('clm_patreon_poll_results_v4', ['yes' => 0, 'no' => 0]);
    wp_send_json_success($results);
}

?>
