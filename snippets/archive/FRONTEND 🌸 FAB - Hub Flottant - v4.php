<?php
/*
 * Display name: FRONTEND 🌸 FAB - Hub Flottant - v4
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Hub Flottant : UN SEUL bouton d'action flottant (bas droite) qui regroupe
 * en barre d'actions secondaire sticky (stats, live, langue et outils) :
 *
 *   1. Google News          (lien externe)          <- POST FLOATING Google News Button v3
 *   2. Flux RSS             (lien /feed/)           <- nouveau
 *   3. Newsletter           (ancre Jetpack)         <- POST FOOTER Abonnezvous v2
 *   4. Diaporama articles   (overlay plein ecran)   <- POST PLAYER News Diaporama v3
 *   5. Articles programmes  (panneau jauge+date)    <- SCHEDULER Scheduled Posts Popup v14
 *      (le sondage Patreon de la v14 est abandonne : remplace par l'entree Ko-fi)
 *   6. Statistiques         (lien /statistiques/ + vues annee + compteur live)
 *                                                     <- badge #clm-live de Live Cursors
 *   7. Ko-fi                (lien externe)          <- nouveau (handle de Social Ego v2)
 *   8. Retour en haut       (scroll smooth)         <- POST FOOTER Scroll To Top v2
 *
 * Absorbe aussi : positionnement + masquage au scroll de GTranslate
 * (ex Google News Button v3) et masquage du #kt-scroll-up Kadence.
 *
 * Le compteur "N en ligne" du menu est alimente par Live Cursors >= 2026.09.16
 * (qui met a jour #fab-live-count ; l'ancien badge en haut a droite disparait).
 *
 * ACTIVATION : activer ce snippet PUIS desactiver sur le site (sinon doublons
 * et collision fatale de fonctions news_player_*) :
 *   - POST PLAYER News Diaporama v3
 *   - POST FLOATING Google News Button v3
 *   - POST FOOTER Scroll To Top v2
 *   - l'eventuel bouton Jetpack flottant (rendu par le theme/Jetpack)
 *
 * Hooks WP: rest_api_init, wp_head, wp_footer
 * Fonctions clefs: mdhub_news_player_get_posts, mdhub_print_styles, mdhub_render
 */

// -----------------------------------------------------------------------------
// 1) Endpoint REST du diaporama (repris tel quel de News Diaporama v3)
// -----------------------------------------------------------------------------
add_action('rest_api_init', function () {
    register_rest_route('mdhub/v1', '/posts', array(
        'methods'             => 'GET',
        'callback'            => 'mdhub_news_player_get_posts',
        'permission_callback' => '__return_true',
    ));
});

function mdhub_news_player_get_posts(WP_REST_Request $request) {
    $args = array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 20,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
    );

    $query  = new WP_Query($args);
    $result = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            $featured = '';
            if (has_post_thumbnail($post_id)) {
                $featured = get_the_post_thumbnail_url($post_id, 'large');
            }

            $raw_content     = get_the_content();
            $content_images  = array();
            if (preg_match_all('/<img[^>]+src="([^">]+)"/i', $raw_content, $m)) {
                $content_images = array_values(array_unique($m[1]));
            }

            $filtered = apply_filters('the_content', $raw_content);
            $filtered = preg_replace('/<img[^>]*>/i', '', $filtered);
            $filtered = preg_replace('/<figure[^>]*>\s*<\/figure>/i', '', $filtered);
            $text     = wp_kses_post($filtered);

            $result[] = array(
                'id'       => $post_id,
                'title'    => get_the_title(),
                'excerpt'  => wp_strip_all_tags(get_the_excerpt()),
                'content'  => $text,
                'link'     => get_permalink($post_id),
                'date'     => get_the_date(),
                'author'   => get_the_author(),
                'featured' => $featured,
                'images'   => $content_images,
            );
        }
        wp_reset_postdata();
    }

    return rest_ensure_response($result);
}

// -----------------------------------------------------------------------------
// 2) Styles
// -----------------------------------------------------------------------------
add_action('wp_enqueue_scripts', 'mdhub_enqueue_icons');
function mdhub_enqueue_icons() {
    wp_enqueue_style('mdhub-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
}

add_action('wp_head', 'mdhub_print_styles');
function mdhub_print_styles() {
    if (is_admin()) return;
    ?>
    <style>
        /* ================= BARRE D'ACTIONS ================= */
        #mdhub {
            position: fixed;
            left: 50%;
            bottom: max(10px, env(safe-area-inset-bottom));
            width: min(1000px, calc(100vw - 24px));
            height: auto;
            transform: translateX(-50%);
            z-index: 99998;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        #mdhub-main {
            display: none;
        }

        /* ---- Barre toujours visible : pas de déploiement, pas de recouvrement ---- */
        #mdhub-petals {
            display: flex;
            align-items: stretch;
            gap: 2px;
            width: 100%;
            padding: 5px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.18);
            overflow-x: auto;
            scrollbar-width: none;
        }
        #mdhub-petals::-webkit-scrollbar { display: none; }
        .mdhub-item {
            flex: 1 0 auto;
            width: auto;
            min-height: 40px;
            margin: 0;
            padding: 7px 10px;
            gap: 7px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #1f2937;
            cursor: pointer;
            display: flex;
            align-items: center;
            text-align: left;
            text-decoration: none;
            font: inherit;
            font-size: 12px;
            font-weight: 650;
            line-height: 1.25;
            box-sizing: border-box;
            white-space: nowrap;
        }
        .mdhub-item:hover, .mdhub-item:focus-visible {
            background: #f1f5f9;
            color: var(--c, #1f2937);
            outline: none;
        }
        .mdhub-item .mdhub-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            flex: 0 0 18px;
            color: var(--c, #475569);
            font-size: 14px;
        }
        .mdhub-label {
            position: static;
            display: block;
            min-width: 0;
            color: inherit;
            white-space: nowrap;
        }
        .mdhub-label .mdhub-live {
            color: #16803c;
            font-variant-numeric: tabular-nums;
        }

        /* ---- Voile d'arriere-plan (clic exterieur + focus) ---- */
        #mdhub-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(10, 12, 16, 0.14);
            z-index: 99997;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #mdhub-backdrop.is-open { opacity: 1; pointer-events: auto; }

        /* ---- Panneau "articles programmés" ---- */
        #mdhub-panel {
            position: fixed;
            bottom: 96px;
            right: 22px;
            z-index: 99999;
            background: #fff;
            border-radius: 18px;
            padding: 16px 20px;
            box-shadow: 0 14px 44px rgba(0, 0, 0, 0.24), inset 0 0 0 1px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 14px;
            opacity: 0;
            transform: translateY(12px) scale(0.95);
            transform-origin: bottom right;
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        #mdhub-panel.is-open {
            opacity: 1;
            transform: none;
            pointer-events: auto;
        }
        #mdhub-panel .mdhub-gauge {
            position: relative;
            width: 54px;
            height: 54px;
            flex-shrink: 0;
        }
        #mdhub-panel .mdhub-gauge svg { width: 100%; height: 100%; transform: rotate(-90deg); display: block; }
        #mdhub-panel .mdhub-gauge b {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 15px;
            font-weight: 800;
            color: #1a202c;
            font-variant-numeric: tabular-nums;
        }
        #mdhub-panel .mdhub-panel-txt strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #1a202c;
        }
        #mdhub-panel .mdhub-panel-txt small {
            display: block;
            margin-top: 3px;
            font-size: 12px;
            color: #718096;
        }

        /* ---- Absorptions : GTranslate (ex Google News v3) + bouton Kadence ---- */
        #kt-scroll-up { display: none !important; }
        .gt_switcher_wrapper {
            bottom: 20px !important;
            left: 20px !important;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .gt_switcher_wrapper.gt-hidden {
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
        }

        /* ---- Mobile ---- */
        @media screen and (max-width: 600px) {
            #mdhub { bottom: max(6px, env(safe-area-inset-bottom)); width: calc(100vw - 12px); }
            #mdhub-petals { gap: 1px; padding: 4px; border-radius: 10px; }
            .mdhub-item { min-height: 42px; padding: 7px 9px; font-size: 12px; }
            #mdhub-panel { bottom: 84px; right: 16px; padding: 13px 16px; }
            .gt_switcher_wrapper { bottom: 62px !important; left: 8px !important; }
        }

        @media (prefers-reduced-motion: reduce) {
            #mdhub-main::after { animation: none; }
            #mdhub-main, .mdhub-item, .mdhub-label, #mdhub-panel { transition: none; }
        }

        /* ================= DIAPORAMA (repris de News Diaporama v3, sans FAB propre) ================= */
        #np-overlay {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 99999;
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease;
            overflow: hidden;
        }
        #np-overlay.is-open { display: block; opacity: 1; }

        #np-stage {
            position: absolute;
            inset: 0;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
        }
        #np-stage::-webkit-scrollbar { width: 8px; }
        #np-stage::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.25); border-radius: 4px; }
        #np-stage::-webkit-scrollbar-track { background: transparent; }

        #np-bg {
            position: sticky;
            top: 0;
            height: 60vh;
            background-color: #111;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: opacity 0.45s ease;
            z-index: 0;
        }
        #np-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0, 0, 0, 0.10) 0%,
                rgba(0, 0, 0, 0.25) 55%,
                rgba(10, 10, 10, 0.90) 95%,
                #0a0a0a 100%
            );
            pointer-events: none;
        }

        #np-bottom {
            position: relative;
            background: #0a0a0a;
            color: #fff;
            padding: 36px 8% 90px;
            margin-top: -28px;
            border-radius: 24px 24px 0 0;
            z-index: 1;
            min-height: 55vh;
        }
        #np-meta {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.6;
            margin-bottom: 12px;
        }
        #np-title {
            font-size: clamp(24px, 4vw, 42px);
            line-height: 1.18;
            margin: 0 0 22px;
            font-weight: 800;
            color: #4d7cff;
            text-shadow: 0 0 24px rgba(77, 124, 255, 0.45);
        }
        #np-text { font-size: 17px; line-height: 1.8; opacity: 0.95; }
        #np-text p { margin: 0 0 18px; }
        #np-text p:last-child { margin-bottom: 0; }
        #np-text h2, #np-text h3, #np-text h4 {
            color: #fff; margin: 32px 0 14px; line-height: 1.25; font-weight: 700;
        }
        #np-text h2 { font-size: 1.6em; }
        #np-text h3 { font-size: 1.3em; }
        #np-text h4 { font-size: 1.1em; }
        #np-text a { color: #4d7cff; text-decoration: underline; }
        #np-text a:hover { color: #7aa5ff; }
        #np-text ul, #np-text ol { margin: 0 0 18px; padding-left: 1.4em; }
        #np-text li { margin: 0 0 8px; }
        #np-text blockquote {
            margin: 0 0 18px;
            padding: 12px 18px;
            border-left: 3px solid #4d7cff;
            background: rgba(77, 124, 255, 0.08);
            font-style: italic;
            border-radius: 0 6px 6px 0;
        }
        #np-text strong { color: #fff; font-weight: 700; }
        #np-text img { display: none; }

        #np-thumbs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 16px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }
        #np-thumbs::-webkit-scrollbar { height: 6px; }
        #np-thumbs::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.3); border-radius: 3px; }
        #np-thumbs img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.15);
            transition: border-color 0.2s ease, transform 0.2s ease;
        }
        #np-thumbs img:hover { border-color: #fff; transform: translateY(-3px); }

        #np-readmore {
            display: inline-block;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 18px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 30px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        #np-readmore:hover { background: rgba(255, 255, 255, 0.18); border-color: #fff; }

        #np-close, #np-prev, #np-next {
            position: absolute;
            background: rgba(255, 255, 255, 0.10);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: background 0.2s ease, transform 0.2s ease;
            z-index: 2;
        }
        #np-close:hover, #np-prev:hover, #np-next:hover { background: rgba(255, 255, 255, 0.25); }
        #np-close { top: 24px; right: 24px; width: 44px; height: 44px; border-radius: 50%; }
        #np-prev, #np-next {
            top: 50%;
            transform: translateY(-50%);
            width: 52px;
            height: 52px;
            border-radius: 50%;
        }
        #np-prev { left: 24px; }
        #np-next { right: 24px; }
        #np-prev:hover { transform: translateY(-50%) scale(1.06); }
        #np-next:hover { transform: translateY(-50%) scale(1.06); }

        #np-lightbox {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.96);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 3;
            padding: 40px;
        }
        #np-lightbox.is-open { display: flex; }
        #np-lightbox img {
            max-width: 90vw;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        }
        #np-lightbox-close {
            position: absolute;
            top: 24px;
            right: 24px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            #np-prev { left: 8px; }
            #np-next { right: 8px; }
            #np-close { top: 14px; right: 14px; }
            #np-bottom { padding: 28px 6% 70px; border-radius: 20px 20px 0 0; }
            #np-thumbs img { width: 58px; height: 58px; }
            #np-bg { height: 50vh; }
            #np-text { font-size: 16px; }
        }
    </style>
    <?php
}

// -----------------------------------------------------------------------------
// 3) Markup + scripts
// -----------------------------------------------------------------------------
add_action('wp_footer', 'mdhub_render');
function mdhub_render() {
    if (is_admin()) return;

    // ---- Donnees : articles programmés (1 seule requete) ----
    $sched = new WP_Query(array(
        'post_type'   => 'post',
        'post_status' => 'future',
        'posts_per_page' => 1,
        'orderby'      => 'date',
        'order'        => 'DESC',
    ));
    $sched_count = (int) $sched->found_posts;
    $sched_date  = $sched->have_posts()
        ? date_i18n('j F Y', strtotime($sched->posts[0]->post_date))
        : '';
    wp_reset_postdata();

    // ---- Donnees : vues de l'annee (meme source que l'ancien badge #clm-live) ----
    global $wpdb;
    $year_views = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(views) FROM {$wpdb->prefix}site_views WHERE YEAR(view_date)=%d",
        intval(current_time('Y'))
    ));

    // ---- Jauge du panneau ----
    $gauge_r      = 16;
    $gauge_circ   = 2 * M_PI * $gauge_r;
    $gauge_pct    = ($sched_count > 0) ? (($sched_count % 100 === 0) ? 1 : ($sched_count % 100) / 100) : 0;
    $gauge_offset = $gauge_circ * (1 - $gauge_pct);

    $gnews_url  = 'https://www.google.com/preferences/source?q=https://mondary.design';
    $rss_url    = home_url('/feed/');
    $news_url   = home_url('/#subscribe-blog'); // ancre Jetpack : ajuster si le formulaire vit ailleurs
    $stats_url  = home_url('/statistiques/');
    $kofi_url   = 'https://ko-fi.com/F1F31908HD';
    $rest_url   = esc_url_raw(rest_url('mdhub/v1/posts'));

    // ---- Definitions du panneau (ordre de lecture) ----
    $items = array(
        array(
            'label' => 'Suivre sur Google News',
            'href'  => $gnews_url,
            'blank' => true,
            'c'     => '#4285F4',
            'icon'  => 'fa-brands fa-google',
        ),
        array(
            'label' => 'Flux RSS',
            'href'  => $rss_url,
            'blank' => true,
            'c'     => '#f28b30',
            'icon'  => 'fa-solid fa-rss',
        ),
        array(
            'label' => 'Newsletter',
            'href'  => $news_url,
            'blank' => false,
            'c'     => '#d94f8a',
            'icon'  => 'fa-solid fa-envelope',
        ),
        array(
            'label' => 'Lire en diaporama',
            'action'=> 'player',
            'c'     => '#4d7cff',
            'icon'  => 'fa-solid fa-images',
        ),
        array(
            'label' => 'Articles programmés',
            'action'=> 'sched',
            'c'     => '#3182CE',
            'icon'  => 'fa-solid fa-calendar-days',
        ),
        array(
            'label' => 'Statistiques · <span class="mdhub-live"><span id="fab-live-count">1</span> en ligne</span> · ' . number_format_i18n($year_views) . ' vues',
            'href'  => $stats_url,
            'blank' => false,
            'c'     => '#0f9d58',
            'icon'  => 'fa-solid fa-chart-column',
        ),
        array(
            'label' => 'Soutenir sur Ko-fi',
            'href'  => $kofi_url,
            'blank' => true,
            'c'     => '#FF5E5B',
            'icon'  => 'fa-solid fa-mug-hot',
        ),
        array(
            'label' => 'Retour en haut',
            'action'=> 'top',
            'c'     => '#2d3748',
            'icon'  => 'fa-solid fa-arrow-up',
        ),
    );
    ?>
    <div id="mdhub-backdrop" aria-hidden="true"></div>

    <nav id="mdhub" aria-label="Menu du site">
        <button id="mdhub-main" type="button" aria-expanded="false" aria-controls="mdhub-petals" aria-label="Ouvrir le menu du site">
            <i class="fa-solid fa-bars mdhub-main-icon" aria-hidden="true"></i>
        </button>
        <div id="mdhub-petals">
            <?php foreach ($items as $i => $it) :
                $tag = isset($it['href']) ? 'a' : 'button';
                $url = isset($it['href']) ? sprintf(' href="%s"', esc_url($it['href'])) : ' type="button" data-action="' . esc_attr($it['action']) . '"';
                $blk = (!empty($it['blank'])) ? ' target="_blank" rel="noopener noreferrer"' : '';
            ?>
            <<?php echo $tag; ?> class="mdhub-item" style="--i:<?php echo $i; ?>;--c:<?php echo $it['c']; ?>"<?php echo $url . $blk; ?>>
                <span class="mdhub-icon" aria-hidden="true"><i class="<?php echo esc_attr($it['icon']); ?>"></i></span>
                <span class="mdhub-label"><?php echo $it['label']; ?></span>
                <span class="screen-reader-short"><?php echo wp_strip_all_tags($it['label']); ?></span>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </nav>

    <div id="mdhub-panel" role="dialog" aria-label="Articles programmés">
        <div class="mdhub-gauge">
            <svg viewBox="0 0 40 40" aria-hidden="true">
                <circle fill="none" stroke="#E2E8F0" stroke-width="6" cx="20" cy="20" r="16"></circle>
                <circle fill="none" stroke="#3182CE" stroke-width="6" stroke-linecap="round"
                        stroke-dasharray="<?php echo esc_attr($gauge_circ); ?>"
                        stroke-dashoffset="<?php echo esc_attr($gauge_offset); ?>"
                        cx="20" cy="20" r="16"
                        style="transition: stroke-dashoffset 1.2s ease-out;"></circle>
            </svg>
            <b><?php echo number_format_i18n($sched_count); ?></b>
        </div>
        <div class="mdhub-panel-txt">
            <strong>Articles programmés</strong>
            <small><?php echo $sched_date ? 'en réserve · jusqu&rsquo;au ' . esc_html($sched_date) : 'aucun article en attente'; ?></small>
        </div>
    </div>

    <div id="np-overlay" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Diaporama d'articles">
        <button id="np-close" type="button" aria-label="Fermer">
            <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </button>
        <button id="np-prev" type="button" aria-label="Article precedent">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
                <path fill="currentColor" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </button>
        <button id="np-next" type="button" aria-label="Article suivant">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
                <path fill="currentColor" d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </button>
        <div id="np-stage">
            <div id="np-bg"></div>
            <div id="np-bottom">
                <div id="np-meta"></div>
                <h2 id="np-title"></h2>
                <div id="np-text"></div>
                <div id="np-thumbs"></div>
                <a id="np-readmore" href="#" target="_blank" rel="noopener noreferrer">Lire l'article complet &rarr;</a>
            </div>
        </div>
        <div id="np-lightbox" aria-hidden="true">
            <button id="np-lightbox-close" type="button" aria-label="Fermer l'image">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                    <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
            <img id="np-lightbox-img" src="" alt="" />
        </div>
    </div>

    <style>
        .screen-reader-short{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
    </style>

    <script>
    (function () {
        /* ============ HUB ============ */
        var hub      = document.getElementById('mdhub');
        var mainBtn  = document.getElementById('mdhub-main');
        var backdrop = document.getElementById('mdhub-backdrop');
        var panel    = document.getElementById('mdhub-panel');

        function syncBackdrop() {
            backdrop.classList.toggle('is-open',
                hub.classList.contains('is-open') || panel.classList.contains('is-open'));
        }
        function setMenu(open) {
            hub.classList.toggle('is-open', open);
            mainBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (!open) { panel.classList.remove('is-open'); }
            syncBackdrop();
        }
        function closeAll() {
            hub.classList.remove('is-open');
            mainBtn.setAttribute('aria-expanded', 'false');
            panel.classList.remove('is-open');
            panel.setAttribute('aria-hidden', 'true');
            syncBackdrop();
        }

        mainBtn.addEventListener('click', function () {
            setMenu(!hub.classList.contains('is-open'));
        });
        backdrop.addEventListener('click', closeAll);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll();
        });

        Array.prototype.forEach.call(hub.querySelectorAll('.mdhub-item'), function (it) {
            it.addEventListener('click', function (e) {
                var a = it.getAttribute('data-action');
                if (a === 'top') {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    closeAll();
                    return;
                }
                if (a === 'player') {
                    e.preventDefault();
                    closeAll();
                    if (window.mdPlayOpen) window.mdPlayOpen();
                    return;
                }
                if (a === 'sched') {
                    e.preventDefault();
                    var open = !panel.classList.contains('is-open');
                    panel.classList.toggle('is-open', open);
                    panel.setAttribute('aria-hidden', open ? 'false' : 'true');
                    hub.classList.remove('is-open');
                    mainBtn.setAttribute('aria-expanded', 'false');
                    syncBackdrop();
                    return;
                }
                /* liens : on ferme, la navigation suit */
                closeAll();
            });
        });

        /* Masque le switcher GTranslate au scroll (repris de Google News v3). */
        (function () {
            function handleScroll() {
                var el = document.querySelector('.gt_switcher_wrapper');
                if (!el) return;
                el.classList.toggle('gt-hidden', window.scrollY > 10);
            }
            handleScroll();
            window.addEventListener('scroll', handleScroll, { passive: true });
        })();

        /* ============ DIAPORAMA (repris de News Diaporama v3, declenchement via le hub) ============ */
        var REST_URL = <?php echo wp_json_encode($rest_url); ?>;

        var overlay      = document.getElementById('np-overlay');
        var stage        = document.getElementById('np-stage');
        var closeBtn     = document.getElementById('np-close');
        var prevBtn      = document.getElementById('np-prev');
        var nextBtn      = document.getElementById('np-next');
        var bg           = document.getElementById('np-bg');
        var metaEl       = document.getElementById('np-meta');
        var titleEl      = document.getElementById('np-title');
        var textEl       = document.getElementById('np-text');
        var thumbsEl     = document.getElementById('np-thumbs');
        var readmore     = document.getElementById('np-readmore');
        var lightbox     = document.getElementById('np-lightbox');
        var lightboxImg  = document.getElementById('np-lightbox-img');
        var lightboxClose= document.getElementById('np-lightbox-close');

        var posts  = [];
        var current = 0;
        var loaded  = false;

        function fetchPosts() {
            if (loaded) return Promise.resolve();
            return fetch(REST_URL, { credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(function (data) { posts = data || []; loaded = true; });
        }

        function render() {
            if (!posts.length) return;
            var p = posts[current];

            stage.scrollTop = 0;

            bg.style.opacity = '0';
            setTimeout(function () {
                bg.style.backgroundImage = p.featured ? 'url("' + p.featured + '")' : 'none';
                bg.style.opacity = '1';
            }, 250);

            metaEl.textContent  = (p.date || '') + (p.author ? '   ·   ' + p.author : '');
            titleEl.textContent = p.title || '';
            textEl.innerHTML = p.content || '';
            makeLinksClickable(textEl);
            readmore.setAttribute('href', p.link || '#');

            thumbsEl.innerHTML = '';
            if (p.featured) thumbsEl.appendChild(buildThumb(p.featured));
            (p.images || []).forEach(function (url) { thumbsEl.appendChild(buildThumb(url)); });
        }

        function buildThumb(url) {
            var img = document.createElement('img');
            img.src = url;
            img.alt = '';
            img.loading = 'lazy';
            img.addEventListener('click', function () { openLightbox(url); });
            return img;
        }

        function escHtml(s) {
            var d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        function makeLinksClickable(root) {
            Array.prototype.forEach.call(root.querySelectorAll('a'), function (a) {
                a.target = '_blank';
                a.setAttribute('rel', 'noopener noreferrer');
            });
            var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
            var nodes = [], n;
            while ((n = walker.nextNode())) {
                if (/https?:\/\//i.test(n.nodeValue) &&
                    !(n.parentNode && n.parentNode.closest('a'))) {
                    nodes.push(n);
                }
            }
            nodes.forEach(function (node) {
                var raw = node.nodeValue, html = '', last = 0;
                raw.replace(/https?:\/\/[^\s<>"']+/gi, function (url, idx) {
                    html += escHtml(raw.slice(last, idx))
                         + '<a href="' + escHtml(url) + '" target="_blank" rel="noopener noreferrer">'
                         + escHtml(url) + '</a>';
                    last = idx + url.length;
                    return url;
                });
                html += escHtml(raw.slice(last));
                if (html !== raw) {
                    var span = document.createElement('span');
                    span.innerHTML = html;
                    node.parentNode.replaceChild(span, node);
                }
            });
        }

        function openLightbox(url) {
            lightboxImg.src = url;
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
        }
        function closeLightbox() {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
        }

        function open() {
            fetchPosts().then(function () {
                if (!posts.length) { alert('Aucun article a afficher.'); return; }
                current = 0;
                render();
                overlay.classList.add('is-open');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }).catch(function () {
                alert('Impossible de charger les articles.');
            });
        }
        function close() {
            var p = posts[current];
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            closeLightbox();
            document.body.style.overflow = '';
            var target = (p && p.link) ? p.link.split('#')[0].replace(/\/+$/, '') : '';
            var here   = window.location.href.split('#')[0].replace(/\/+$/, '');
            if (target && target !== here) window.location.href = p.link;
        }
        function next() { if (posts.length) { current = (current + 1) % posts.length; render(); } }
        function prev() { if (posts.length) { current = (current - 1 + posts.length) % posts.length; render(); } }

        window.mdPlayOpen = open;

        closeBtn.addEventListener('click', close);
        nextBtn.addEventListener('click', next);
        prevBtn.addEventListener('click', prev);
        lightboxClose.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', function (e) {
            if (!overlay.classList.contains('is-open')) return;
            if (e.key === 'Escape') {
                if (lightbox.classList.contains('is-open')) closeLightbox();
                else close();
            } else if (e.key === 'ArrowRight' && !lightbox.classList.contains('is-open')) {
                next();
            } else if (e.key === 'ArrowLeft' && !lightbox.classList.contains('is-open')) {
                prev();
            }
        });

        var startX = 0, startY = 0;
        overlay.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });
        overlay.addEventListener('touchend', function (e) {
            var dx = e.changedTouches[0].clientX - startX;
            var dy = e.changedTouches[0].clientY - startY;
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
                if (dx < 0) next(); else prev();
            }
        }, { passive: true });
    })();
    </script>
    <?php
}
