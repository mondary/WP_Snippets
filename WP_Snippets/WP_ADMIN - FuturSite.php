<?php
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

    $admin_bar->add_node(array(
        'id'    => 'fs-futur-site',
        'title' => '<span class="ab-icon" aria-hidden="true" style="display:flex;align-items:center;justify-content:center;width:20px;height:20px;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="width:16px;height:16px;display:block;fill:currentColor;"><path d="M192 384L88.5 384C63.6 384 48.3 356.9 61.1 335.5L114 247.3C122.7 232.8 138.3 224 155.2 224L250.2 224C326.3 95.1 439.8 88.6 515.7 99.7C528.5 101.6 538.5 111.6 540.3 124.3C551.4 200.2 544.9 313.7 416 389.8L416 484.8C416 501.7 407.2 517.3 392.7 526L304.5 578.9C283.2 591.7 256 576.3 256 551.5L256 448C256 412.7 227.3 384 192 384L191.9 384zM464 224C464 197.5 442.5 176 416 176C389.5 176 368 197.5 368 224C368 250.5 389.5 272 416 272C442.5 272 464 250.5 464 224z"/></svg></span>',
        'href'  => esc_url($preview_url),
        'meta'  => array(
            'title'  => 'Ouvrir l’aperçu Futur site',
            'target' => '_self',
        ),
    ));
}, 90);

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
