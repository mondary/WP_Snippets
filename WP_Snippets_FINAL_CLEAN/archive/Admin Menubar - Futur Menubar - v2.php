
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - FuturSite.php
 * Display name: WP_ADMIN - FuturSite
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - FuturSite (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_ADMIN - FuturSite.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: calendar, admin-bar, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu, admin_init, template_redirect, show_admin_bar, pre_get_posts, admin_bar_menu, wp_body_open, wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 369 / 12893
 * Hash code normalise (sha256): 0e8c4a1a35006372e59d5e274451c1ed8fbf3f1169ea305628f8d584de8fd77d
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-futursite__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-futursite__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: post_footer_ui
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_menubar=6, post_footer_ui=5
 * Raisons principales: admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

line-style, inline-script, html-markup
 * Lignes / octets: 391 / 13767
 * Empreinte code (sha256): 71e38f490c9ecd2308e8a9f6ccdbbac44cf338ff68aeb2972314912bd20b28b0
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - FuturSite.php
 * Display name: WP_ADMIN - FuturSite
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - FuturSite (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_ADMIN - FuturSite.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: calendar, admin-bar, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu, admin_init, template_redirect, show_admin_bar, pre_get_posts, admin_bar_menu, wp_body_open, wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 369 / 12893
 * Hash code normalise (sha256): 0e8c4a1a35006372e59d5e274451c1ed8fbf3f1169ea305628f8d584de8fd77d
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Futur site (ONE menu entry) — with a front-end banner (no admin bar)
 * Admin: Articles > Futur site
 * Front preview: /?future_site=1
 *
 * Banner:
 * - Only displayed when future_site=1 is active
 * - Funny message + "Quitter l’aperçu" button
 */

if (!defined('ABSPATH')) exit;

/**
 * Capability to use (adjust if needed):
 * - 'manage_options' (admin)
 * - 'edit_posts' (editor/author depending on setup)
 */
$FS_CAP = 'manage_options';

/**
 * Admin submenu: Articles > Futur site
 */
add_action('admin_menu', function () use ($FS_CAP) {
    if (!current_user_can($FS_CAP)) return;

    add_submenu_page(
        'edit.php',
        'Futur site',
        'Futur site',
        $FS_CAP,
        'fs-futur-site',
        function () {
            $url = add_query_arg(array('future_site' => '1'), home_url('/'));
            echo '<div class="wrap">';
            echo '<h1>Futur site</h1>';
            echo '<p>Redirection vers l’aperçu du site…</p>';
            echo '<p><a class="button button-primary" href="' . esc_url($url) . '">Ouvrir l’aperçu</a></p>';
            echo '</div>';
            echo '<script>setTimeout(function(){ window.location.href = ' . json_encode($url) . '; }, 200);</script>';
        }
    );
}, 99);

/**
 * Redirect early when the submenu page is opened
 */
add_action('admin_init', function () use ($FS_CAP) {
    if (!is_admin()) return;
    if (!isset($_GET['page']) || $_GET['page'] !== 'fs-futur-site') return;

    if (!current_user_can($FS_CAP)) {
        wp_die('Désolé, vous n’avez pas l’autorisation d’accéder à cette page.');
    }

    $url = add_query_arg(array('future_site' => '1'), home_url('/'));
    wp_safe_redirect($url);
    exit;
});

/**
 * Front gate: only allowed users can use ?future_site=1
 */
add_action('template_redirect', function () use ($FS_CAP) {
    if (!isset($_GET['future_site']) || $_GET['future_site'] !== '1') return;

    if (!is_user_logged_in() || !current_user_can($FS_CAP)) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        exit;
    }
}, 0);

/**
 * Hide admin bar on futur preview page
 */
add_filter('show_admin_bar', function ($show) {
    if (isset($_GET['future_site']) && $_GET['future_site'] === '1') return false;
    return $show;
}, 20);

/**
 * Include scheduled posts on homepage/front page main query
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if (!isset($_GET['future_site']) || $_GET['future_site'] !== '1') return;
    if (!($query->is_home() || $query->is_front_page())) return;

    $query->set('post_status', array('publish', 'future'));
    $query->set('orderby', 'date');
    $query->set('order', 'DESC');
}, 99);

/**
 * Admin bar shortcut (rocket icon) to the future preview
 */
add_action('admin_bar_menu', function ($admin_bar) use ($FS_CAP) {
    if (!is_user_logged_in() || !current_user_can($FS_CAP)) return;

    $preview_url = add_query_arg(array('future_site' => '1'), home_url('/'));
    $rocket_node = array(
        'id'    => 'fs-futur-site',
        'title' => '<span class="ab-icon" aria-hidden="true" style="display:flex;align-items:center;justify-content:center;width:20px;height:20px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width:16px;height:16px;display:block;fill:currentColor;"><path d="M192 384L88.5 384C63.6 384 48.3 356.9 61.1 335.5L114 247.3C122.7 232.8 138.3 224 155.2 224L250.2 224C326.3 95.1 439.8 88.6 515.7 99.7C528.5 101.6 538.5 111.6 540.3 124.3C551.4 200.2 544.9 313.7 416 389.8L416 484.8C416 501.7 407.2 517.3 392.7 526L304.5 578.9C283.2 591.7 256 576.3 256 551.5L256 448C256 412.7 227.3 384 192 384L191.9 384zM464 224C464 197.5 442.5 176 416 176C389.5 176 368 197.5 368 224C368 250.5 389.5 272 416 272C442.5 272 464 250.5 464 224z"/></svg></span>',
        'href'  => esc_url($preview_url),
        'meta'  => array(
            'title'  => 'Ouvrir l’aperçu Futur site',
            'target' => '_self',
        ),
    );

    $calendar_ids = array('calendar', 'scheduled-posts-calendar');
    $calendar_node = null;

    foreach ($calendar_ids as $calendar_id) {
        $node = $admin_bar->get_node($calendar_id);
        if ($node) {
            $calendar_node = $node;
            break;
        }
    }

    if ($calendar_node) {
        if (!empty($calendar_node->parent)) {
            $rocket_node['parent'] = $calendar_node->parent;
        }

        // Reinsert calendar first, then rocket, so both stay adjacent.
        $admin_bar->remove_node($calendar_node->id);
        $admin_bar->add_node(array(
            'id'     => $calendar_node->id,
            'parent' => isset($calendar_node->parent) ? $calendar_node->parent : false,
            'title'  => isset($calendar_node->title) ? $calendar_node->title : '',
            'href'   => isset($calendar_node->href) ? $calendar_node->href : false,
            'group'  => !empty($calendar_node->group),
            'meta'   => isset($calendar_node->meta) && is_array($calendar_node->meta) ? $calendar_node->meta : array(),
        ));
    }

    $admin_bar->add_node($rocket_node);
}, 999);

/**
 * FRONT BANNER (funny) when future preview is active
 * Injected in wp_body_open when available, otherwise in wp_footer as fallback.
 */
$fs_render_banner = function () {
    if (!isset($_GET['future_site']) || $_GET['future_site'] !== '1') return;

    $exit_url = home_url('/'); // remove query args
    $now = new DateTime('now', wp_timezone());
    $stamp = esc_html($now->format('d/m/Y H:i'));

    ?>
    <div id="fs-future-banner" role="status" aria-live="polite">
        <div class="fs-future-banner__inner">
            <div class="fs-future-banner__left">
                <div class="fs-future-badge">🚀 FUTUR</div>
                <div class="fs-future-text">
                    <strong>Bienvenue dans la version “spoiler” du site.</strong>
                    <div class="fs-future-meta">
                        <span class="fs-future-pill">Posts planifiés visibles</span>
                        <span class="fs-future-time">Checkpoint : <?php echo $stamp; ?></span>
                    </div>
                </div>
            </div>

            <div class="fs-future-banner__right">
                <a class="fs-future-btn" href="<?php echo esc_url($exit_url); ?>">Quitter le futur</a>
                <button class="fs-future-close" type="button" aria-label="Masquer le bandeau">×</button>
            </div>
        </div>
    </div>

    <style>
        #fs-future-banner{
            position: sticky;
            top: 0;
            z-index: 999999;
            padding: 10px 12px 12px;
            background:
                linear-gradient(90deg, rgba(255,122,0,.09), rgba(255,122,0,0) 34%),
                rgba(255,255,255,.94);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,.06);
            box-shadow: 0 6px 22px rgba(0,0,0,.06);
        }
        #fs-future-banner .fs-future-banner__inner{
            max-width: 1200px;
            margin: 0 auto;
            display:flex;
            gap: 14px;
            align-items:center;
            justify-content: space-between;
            font: 13px/1.25 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            padding: 2px 0;
        }
        #fs-future-banner .fs-future-banner__left{
            display:flex;
            gap: 12px;
            align-items:flex-start;
            min-width: 0;
        }
        #fs-future-banner .fs-future-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding: 7px 11px;
            border-radius: 999px;
            background: linear-gradient(180deg, #ff8a2b, #ff6a00);
            color: #fff;
            font-weight: 800;
            letter-spacing: .04em;
            font-size: 11px;
            box-shadow: 0 6px 14px rgba(255,106,0,.22);
            white-space: nowrap;
        }
        #fs-future-banner .fs-future-text{
            display:flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }
        #fs-future-banner .fs-future-text strong{
            font-size: 13px;
            color: #1d2327;
            font-weight: 700;
        }
        #fs-future-banner .fs-future-meta{
            display:flex;
            flex-wrap: wrap;
            align-items:center;
            gap: 6px;
            min-width: 0;
        }
        #fs-future-banner .fs-future-pill{
            display:inline-flex;
            align-items:center;
            gap: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(2, 128, 80, .10);
            border: 1px solid rgba(2, 128, 80, .14);
            color: #0f5132;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        #fs-future-banner .fs-future-time{
            color: rgba(29,35,39,.72);
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 52vw;
        }
        #fs-future-banner .fs-future-banner__right{
            display:flex;
            gap: 8px;
            align-items:center;
            white-space: nowrap;
        }
        #fs-future-banner .fs-future-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height: 34px;
            padding: 0 12px;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,.10);
            background: #1d2327;
            color: #fff;
            text-decoration:none;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            transition: transform .12s ease, box-shadow .12s ease, opacity .12s ease;
        }
        #fs-future-banner .fs-future-btn:hover{ color:#fff; opacity:.95; transform: translateY(-1px); box-shadow: 0 5px 14px rgba(0,0,0,.13); }
        #fs-future-banner .fs-future-close{
            appearance:none;
            border:1px solid rgba(0,0,0,.08);
            background: rgba(255,255,255,.75);
            width: 34px;
            height: 34px;
            border-radius: 10px;
            font-size: 20px;
            line-height: 1;
            padding: 0;
            cursor:pointer;
            opacity: .72;
        }
        #fs-future-banner .fs-future-close:hover{ opacity: .95; background:#fff; }

        @media (max-width: 782px){
            #fs-future-banner{
                padding: 8px 10px 10px;
            }
            #fs-future-banner .fs-future-banner__inner{
                align-items:flex-start;
                gap: 10px;
            }
            #fs-future-banner .fs-future-banner__left{
                gap: 8px;
            }
            #fs-future-banner .fs-future-badge{
                padding: 6px 8px;
                font-size: 10px;
            }
            #fs-future-banner .fs-future-text strong{
                font-size: 12px;
                line-height: 1.25;
            }
            #fs-future-banner .fs-future-meta{
                gap: 5px;
            }
            #fs-future-banner .fs-future-pill{
                font-size: 10px;
                padding: 2px 6px;
            }
            #fs-future-banner .fs-future-time{
                max-width: 58vw;
                font-size: 10px;
            }
            #fs-future-banner .fs-future-banner__right{
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }
            #fs-future-banner .fs-future-btn,
            #fs-future-banner .fs-future-close{
                height: 30px;
            }
            #fs-future-banner .fs-future-close{
                width: 30px;
                align-self: flex-end;
                font-size: 18px;
            }
        }

        @media (prefers-reduced-motion: no-preference){
            #fs-future-banner{ animation: fsBannerIn .18s ease-out; }
            @keyframes fsBannerIn{
                from{ transform: translateY(-8px); opacity: 0; }
                to{ transform: translateY(0); opacity: 1; }
            }
        }
    </style>

    <script>
        (function(){
            var b = document.getElementById('fs-future-banner');
            if (!b) return;
            var btn = b.querySelector('.fs-future-close');
            if (!btn) return;
            btn.addEventListener('click', function(){
                b.style.display = 'none';
            });
        })();
    </script>
    <?php
};

if (function_exists('wp_body_open')) {
    add_action('wp_body_open', $fs_render_banner, 1);
} else {
    add_action('wp_footer', $fs_render_banner, 1);
}
