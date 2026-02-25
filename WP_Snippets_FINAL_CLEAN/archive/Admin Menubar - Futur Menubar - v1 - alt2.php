/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/100__id-161__admin-menubar-futur-notif.php
 * Display name: ADMIN - MENUBAR FUTUR (+notif)
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 161
 * Online modified: 2026-02-23 18:28:54
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: ADMIN - MENUBAR FUTUR (+notif) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/100__id-161__admin-menubar-futur-notif.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu, admin_init, template_redirect, show_admin_bar, pre_get_posts, wp_body_open, wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 247 / 7817
 * Hash code normalise (sha256): 7c5b3405e7fd550084a4e468db001e23e7848687d1825ab6fd531e9dbafcf9c3
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: admin-menubar-futur-notif__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-menubar-futur-notif__v001.php
 * Resume fonctionnalites: customisation interface admin, UI frontend (CSS/HTML), automatisation date/programmation, 7 hook(s) WP
 * Features detectees: admin-menubar, admin-ui, scheduler-date, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu, admin_init, template_redirect, show_admin_bar, pre_get_posts, wp_body_open, wp_footer
 * Fonctions clefs: aucun
 * Selecteurs / IDs: .fs-future-close
 * APIs WP detectees: add_action, add_submenu_page, add_query_arg, home_url, is_admin, wp_die, wp_safe_redirect, is_user_logged_in, add_filter, is_main_query, is_home, is_front_page, wp_timezone
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 260 / 8486
 * Empreinte code (sha256): 8397a29426895a398f26e2507baa6463c89546b302d1befec194d337562ab5e6
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-menubar-futur-notif__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-menubar-futur-notif__v001.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: scheduler_posts
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=12, scheduler_posts=8, post_footer_ui=5, admin_ui_settings=4, frontend_ui_widget=4
 * Raisons principales: admin-menubar, menubar
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

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
                    <span>Les posts planifiés sont visibles. (Checkpoint : <?php echo $stamp; ?>)</span>
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
            padding: 10px 12px;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0,0,0,.08);
        }
        #fs-future-banner .fs-future-banner__inner{
            max-width: 1200px;
            margin: 0 auto;
            display:flex;
            gap: 12px;
            align-items:center;
            justify-content: space-between;
            font: 13px/1.25 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        }
        #fs-future-banner .fs-future-banner__left{
            display:flex;
            gap: 10px;
            align-items:center;
            min-width: 0;
        }
        #fs-future-banner .fs-future-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(0,0,0,.06);
            font-weight: 800;
            letter-spacing: .02em;
            white-space: nowrap;
        }
        #fs-future-banner .fs-future-text{
            display:flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }
        #fs-future-banner .fs-future-text strong{
            font-size: 13px;
        }
        #fs-future-banner .fs-future-text span{
            opacity: .78;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 72vw;
        }
        #fs-future-banner .fs-future-banner__right{
            display:flex;
            gap: 10px;
            align-items:center;
            white-space: nowrap;
        }
        #fs-future-banner .fs-future-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height: 30px;
            padding: 0 10px;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,.14);
            background: rgba(0,0,0,.04);
            color: inherit;
            text-decoration:none;
        }
        #fs-future-banner .fs-future-close{
            appearance:none;
            border:0;
            background:transparent;
            font-size: 22px;
            line-height: 1;
            padding: 0 6px;
            cursor:pointer;
            opacity: .55;
        }
        #fs-future-banner .fs-future-close:hover{ opacity: .9; }

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