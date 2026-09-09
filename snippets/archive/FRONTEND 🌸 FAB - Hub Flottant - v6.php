<?php
/*
 * Display name: FRONTEND 🌸 FAB - Hub Flottant - v6
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Hub Flottant v6 : FAB lateral en STACK. Un bouton flottant (droite par
 * defaut) qui, au survol (ou tap / focus clavier), deploie une colonne
 * d'items a largeur unique (aspect v4 conserve) :
 *
 *   1. Google News          (lien externe, URL reprise de Google News Button v3)
 *   2. Articles programmés  (ligne d'info inline : mini-jauge + nombre + date,
 *                            plus de badge flottant ; lignee Scheduled Posts Popup v14)
 *   3. Statistiques         (lien /statistiques/ = page du snippet Site Stats Page,
 *                            + vues annee + compteur live #fab-live-count)
 *   4. Ko-fi                (lien externe, handle F1F31908HD repris de Social Ego v2)
 *   5. Newsletter           (lien /newsletter/, la page dediee du site)
 *   6. Traduction           (switcher .gt_switcher_wrapper adopte dans la stack,
 *                            fond blanc du contrat "GTranslate White v2")
 *
 * Une poignee chevron bascule toute la stack d'un cote a l'autre
 * (droite <-> gauche, memorise en localStorage). Pas de drag & drop.
 *
 * DELEGATION : ce snippet ne reinvente plus rien — chaque element reste porte
 * par son script dedie, le hub se contente de l'agreger :
 *   - POST ▶️ PLAYER - News Diaporama v3 : RESTE ACTIF (bouton play bleu
 *     #np-fab bas centre + overlay plein ecran + endpoint news-player/v1/posts)
 *   - POST 🦶 FOOTER - Scroll To Top v2 : RESTE ACTIF (#kt-scroll-up Kadence
 *     repositionne par lui ; PLUS de masquage dans ce snippet)
 *   - FRONTEND 📊 CURSOR - Live Cursors 2026.09.16 : alimente #fab-live-count
 *   - FRONTEND 📊 STATS - Site Stats Page : la page /statistiques/
 *   - POST 🦶 FOOTER - Social Ego v2 : widget Ko-fi du footer
 *
 * Retires en v6 : le panneau flottant "articles programmés" (infos passees
 * inline dans la stack) et le voile #mdhub-backdrop qui n'avait plus de role.
 * Deja retires en v5 : Flux RSS, Retour en haut, et tout le diaporama (deja
 * rendu integralement par News Diaporama v3 — ni endpoint REST, ni overlay).
 *
 * ACTIVATION : activer ce snippet PUIS desactiver uniquement :
 *   - POST 📌 FLOATING - Google News Button v3 (son bouton flottant bas
 *     gauche est remplace par l'item "Suivre sur Google News")
 *   - l'eventuel bouton Jetpack flottant (rendu par le theme/Jetpack)
 *
 * Hooks WP: wp_enqueue_scripts, wp_head, wp_footer
 * Fonctions clefs: mdhub_enqueue_icons, mdhub_print_styles, mdhub_render
 */


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
        /* ================= STACK FAB ================= */
        #mdhub {
            position: fixed;
            right: 14px;
            bottom: max(14px, calc(env(safe-area-inset-bottom) + 10px));
            z-index: 99998;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            transition: right 0.35s ease, left 0.35s ease;
        }
        #mdhub.mdhub-left { right: auto; left: 14px; }

        /* ---- Colonne d'items deployee au survol (hors flux : pas de zone fantome) ---- */
        #mdhub-petals {
            position: absolute;
            bottom: calc(100% + 8px);
            right: 0;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            width: max-content;
            max-width: calc(100vw - 88px);
            padding: 5px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.18);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.3s ease, visibility 0s 0.35s;
        }
        #mdhub.mdhub-left #mdhub-petals { right: auto; left: 0; }
        #mdhub.is-open #mdhub-petals {
            opacity: 1;
            visibility: visible;
            transform: none;
            pointer-events: auto;
            transition-delay: 0s;
        }

        /* ---- FAB + poignee chevron (bascule gauche/droite) ---- */
        #mdhub-dock {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        #mdhub.mdhub-left #mdhub-dock { flex-direction: row-reverse; }
        #mdhub-main {
            width: 52px;
            height: 52px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.18);
            color: #1f2937;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }
        #mdhub-main:hover, #mdhub-main:focus-visible { background: #f1f5f9; outline: none; }
        #mdhub-flip {
            width: 26px;
            height: 26px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.14);
            color: #475569;
            font-size: 11px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }
        #mdhub-flip:hover, #mdhub-flip:focus-visible { background: #f1f5f9; outline: none; }
        #mdhub-flip i { transition: transform 0.3s ease; }
        #mdhub.mdhub-left #mdhub-flip i { transform: rotate(180deg); }

        /* ---- Items : aspect v4 conserve, largeur unique ---- */
        .mdhub-item {
            width: 100%;
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

        /* ---- Switcher GTranslate integre a la stack ---- */
        .gt_switcher_wrapper.mdhub-gt {
            position: static !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            left: auto !important;
            width: 100% !important;
            margin: 0 !important;
            box-sizing: border-box;
            background-color: #ffffff !important; /* contrat GTranslate White v2 */
        }

        /* ---- Ligne info "Articles programmés" : jauge inline, plus de panneau ---- */
        .mdhub-item.mdhub-info { min-height: 44px; cursor: default; }
        .mdhub-item.mdhub-info:hover, .mdhub-item.mdhub-info:focus-visible {
            background: transparent;
            color: #1f2937;
        }
        .mdhub-gauge-mini {
            position: relative;
            width: 30px;
            height: 30px;
            flex: 0 0 30px;
            color: var(--c, #3182CE);
        }
        .mdhub-gauge-mini svg { width: 100%; height: 100%; transform: rotate(-90deg); display: block; }
        .mdhub-gauge-mini b {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 11px;
            font-weight: 800;
            color: var(--c, #3182CE);
            font-variant-numeric: tabular-nums;
        }
        .mdhub-label .mdhub-sched { color: var(--c, #3182CE); }

        /* Retour en haut : laisse le bouton #kt-scroll-up au script dedie
           POST 🦶 FOOTER - Scroll To Top v2 (aucun masquage ici). */

        /* ---- Mobile ---- */
        @media screen and (max-width: 600px) {
            #mdhub { right: 10px; bottom: max(10px, calc(env(safe-area-inset-bottom) + 8px)); }
            #mdhub.mdhub-left { right: auto; left: 10px; }
            #mdhub-petals { max-width: calc(100vw - 84px); border-radius: 10px; }
            .mdhub-item { min-height: 42px; padding: 7px 9px; font-size: 12px; }
            .mdhub-item .mdhub-label { overflow: hidden; text-overflow: ellipsis; }
            #mdhub-main { width: 48px; height: 48px; }
        }

        @media (prefers-reduced-motion: reduce) {
            #mdhub, #mdhub-petals, #mdhub-main, #mdhub-flip, #mdhub-flip i,
            .mdhub-item { transition: none; }
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
    $news_url   = home_url('/newsletter/'); // page dediee Newsletter du site
    $stats_url  = home_url('/statistiques/');
    $kofi_url   = 'https://ko-fi.com/F1F31908HD';

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
            'info'  => true,
            'c'     => '#3182CE',
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
            'label' => 'Newsletter',
            'href'  => $news_url,
            'blank' => false,
            'c'     => '#d94f8a',
            'icon'  => 'fa-solid fa-envelope',
        ),
    );
    ?>

    <nav id="mdhub" aria-label="Menu du site">
        <div id="mdhub-petals">
            <?php foreach ($items as $i => $it) :
                if (!empty($it['info'])) : ?>
            <div class="mdhub-item mdhub-info" style="--i:<?php echo $i; ?>;--c:<?php echo $it['c']; ?>">
                <span class="mdhub-gauge-mini" aria-hidden="true">
                    <svg viewBox="0 0 40 40">
                        <circle fill="none" stroke="#E2E8F0" stroke-width="6" cx="20" cy="20" r="16"></circle>
                        <circle fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="<?php echo esc_attr($gauge_circ); ?>"
                                stroke-dashoffset="<?php echo esc_attr($gauge_offset); ?>"
                                cx="20" cy="20" r="16"></circle>
                    </svg>
                    <b><?php echo number_format_i18n($sched_count); ?></b>
                </span>
                <span class="mdhub-label">Articles programmés · <?php echo $sched_date ? '<span class="mdhub-sched">en réserve · jusqu&rsquo;au ' . esc_html($sched_date) . '</span>' : 'aucun en attente'; ?></span>
                <span class="screen-reader-short">Articles programmés : <?php echo $sched_count > 0 ? number_format_i18n($sched_count) . ' en réserve, jusqu&rsquo;au ' . esc_html($sched_date) : 'aucun en attente'; ?></span>
            </div>
            <?php else :
                $tag = isset($it['href']) ? 'a' : 'button';
                $url = isset($it['href']) ? sprintf(' href="%s"', esc_url($it['href'])) : '';
                $blk = (!empty($it['blank'])) ? ' target="_blank" rel="noopener noreferrer"' : '';
            ?>
            <<?php echo $tag; ?> class="mdhub-item" style="--i:<?php echo $i; ?>;--c:<?php echo $it['c']; ?>"<?php echo $url . $blk; ?>>
                <span class="mdhub-icon" aria-hidden="true"><i class="<?php echo esc_attr($it['icon']); ?>"></i></span>
                <span class="mdhub-label"><?php echo $it['label']; ?></span>
                <span class="screen-reader-short"><?php echo wp_strip_all_tags($it['label']); ?></span>
            </<?php echo $tag; ?>>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div id="mdhub-dock">
            <button id="mdhub-flip" type="button" aria-label="Déplacer la stack vers la gauche">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>
            <button id="mdhub-main" type="button" aria-expanded="false" aria-controls="mdhub-petals" aria-label="Ouvrir le menu du site">
                <i class="fa-solid fa-bars mdhub-main-icon" aria-hidden="true"></i>
            </button>
        </div>
    </nav>

    <style>
        .screen-reader-short{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
    </style>

    <script>
    (function () {
        /* ============ STACK FAB ============ */
        var hub        = document.getElementById('mdhub');
        var mainBtn    = document.getElementById('mdhub-main');
        var flipBtn    = document.getElementById('mdhub-flip');
        var petals     = document.getElementById('mdhub-petals');
        var closeTimer = null;

        function stackOpen() { return hub.classList.contains('is-open'); }
        function setStack(open) {
            hub.classList.toggle('is-open', open);
            mainBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        /* Survol (desktop) : ouvre immediatement, referme apres un court delai */
        hub.addEventListener('mouseenter', function () {
            clearTimeout(closeTimer);
            setStack(true);
        });
        hub.addEventListener('mouseleave', function () {
            clearTimeout(closeTimer);
            closeTimer = setTimeout(function () { setStack(false); }, 350);
        });
        /* Tactile / clavier */
        mainBtn.addEventListener('click', function () { setStack(!stackOpen()); });
        hub.addEventListener('focusin', function () {
            clearTimeout(closeTimer);
            setStack(true);
        });

        /* Clic a l'exterieur ou Echap : on referme */
        document.addEventListener('click', function (e) {
            if (stackOpen() && !hub.contains(e.target)) setStack(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setStack(false);
        });
        window.addEventListener('scroll', function () {
            if (stackOpen()) setStack(false);
        }, { passive: true });

        /* ---- Bascule gauche/droite (chevron, memorisee en localStorage) ---- */
        function setSide(left, save) {
            document.body.classList.toggle('mdhub-side-left', left);
            hub.classList.toggle('mdhub-left', left);
            flipBtn.setAttribute('aria-label', left ? 'Déplacer la stack vers la droite' : 'Déplacer la stack vers la gauche');
            if (save) {
                try { localStorage.setItem('mdhubSide', left ? 'left' : 'right'); } catch (err) {}
            }
        }
        var savedSide = 'right';
        try { savedSide = localStorage.getItem('mdhubSide') || 'right'; } catch (err) {}
        setSide(savedSide === 'left', false);
        flipBtn.addEventListener('click', function () {
            /* la stack quitte le pointeur pendant la bascule : on la referme volontairement */
            setStack(false);
            setSide(!document.body.classList.contains('mdhub-side-left'), true);
        });

        /* ---- Items cliquables de la stack (les liens referment, la nav suit) ---- */
        Array.prototype.forEach.call(hub.querySelectorAll('.mdhub-item'), function (it) {
            if (it.tagName !== 'A' && it.tagName !== 'BUTTON') return; /* lignes d'info */
            it.addEventListener('click', function () {
                setStack(false);
            });
        });

        /* ---- GTranslate : adopte le switcher dans la stack des qu'il existe ---- */
        (function () {
            var tries = 0;
            (function adopt() {
                var gt = document.querySelector('.gt_switcher_wrapper');
                if (gt) {
                    if (!petals.contains(gt)) petals.appendChild(gt);
                    gt.classList.add('mdhub-gt');
                    return;
                }
                if (tries++ < 20) setTimeout(adopt, 500);
            })();
        })();

    })();
    </script>
    <?php
}
