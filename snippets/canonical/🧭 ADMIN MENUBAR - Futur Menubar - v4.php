<?php
/*
 * Display name: 🧭 ADMIN MENUBAR - Futur Menubar - v4
 * Scope: global
 */

/**
 * Futur site v4 — aperçu des articles programmés, réservé aux membres premium.
 *
 * Admin : Articles > Futur site (+ icône fusée dans la barre d'admin)
 * Front : /?future_site=1
 *
 * v4 (base v3, retour au canonique) :
 *   - Le garde-fou n'est plus admin-only : accès si admin (manage_options)
 *     OU si le compte porte un rôle premium ($FS_PREMIUM_ROLES ci-dessous,
 *     filtrable via le filtre fs_futur_premium_roles). AJUSTER le slug du
 *     rôle attribué aux abonnés après paiement.
 *   - Les non-abonnés qui ouvrent /?future_site=1 ne voient plus une 404
 *     mais un PAYWALL (CTA vers /abonnement/ + retour au présent).
 *   - Les abonnés voient la home avec les articles 'future' inclus et le
 *     bandeau « Bienvenue dans la version spoiler du site ».
 *
 * Entrée publique : la ligne « Articles programmés » du FAB Hub Flottant v10.
 * Hooks WP: admin_menu, admin_init, template_redirect, show_admin_bar, pre_get_posts, admin_bar_menu, wp_body_open, wp_footer
 */

/**
 * Configuration :
 * - manage_options : les admins passent toujours
 * - $FS_PREMIUM_ROLES : rôles WP attribués aux abonnés payants.
 *   AJUSTER si le slug diffère (ex. 'premium_member', 'abonne', 'customer').
 *   Extensible via add_filter('fs_futur_premium_roles', fn($r) => [...]);
 */
$FS_CAP = 'manage_options';

/**
 * L'utilisateur courant peut-il ouvrir le futur site ?
 */
function fs_futur_user_can_access() {
    if (!is_user_logged_in()) return false;
    if (current_user_can('manage_options')) return true;

    $roles = (array) apply_filters('fs_futur_premium_roles', array('premium'));
    $user  = wp_get_current_user();

    foreach ($roles as $role) {
        if (in_array($role, (array) $user->roles, true)) return true;
    }

    return false;
}

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
 * Front gate ?future_site=1 :
 * - abonné premium ou admin -> futur site (home avec articles 'future' + bandeau)
 * - tout le monde -> PAYWALL (CTA abonnement + retour au présent)
 */
add_action('template_redirect', function () {
    if (!isset($_GET['future_site']) || $_GET['future_site'] !== '1') return;

    if (fs_futur_user_can_access()) return; // accès autorisé, on continue vers la home « future »

    status_header(200);
    nocache_headers();

    $premium_url = esc_url(home_url('/abonnement/'));
    $exit_url    = esc_url(home_url('/'));

    ?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Le futur est réservé aux membres premium</title>
    <style>
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
             background:#0a0e1a;color:#e2e8f0;font:15px/1.5 -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;
             text-align:center;padding:24px;box-sizing:border-box}
        .fs-paywall{max-width:460px}
        .fs-paywall__badge{display:inline-flex;align-items:center;justify-content:center;
             padding:8px 14px;border-radius:999px;background:rgba(77,124,255,.15);
             color:#7aa5ff;font-weight:800;letter-spacing:.02em;font-size:13px}
        .fs-paywall h1{font-size:26px;line-height:1.2;margin:18px 0 10px;color:#fff}
        .fs-paywall p{margin:0 0 26px;opacity:.75}
        .fs-paywall__actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
        .fs-paywall__cta{display:inline-flex;align-items:center;height:44px;padding:0 22px;
             border-radius:12px;background:#4d7cff;color:#fff;text-decoration:none;font-weight:700;
             box-shadow:0 8px 24px rgba(77,124,255,.35)}
        .fs-paywall__cta:hover{background:#6b93ff}
        .fs-paywall__back{display:inline-flex;align-items:center;height:44px;padding:0 18px;
             border-radius:12px;border:1px solid rgba(226,232,240,.25);color:#e2e8f0;
             text-decoration:none;font-weight:600}
        .fs-paywall__back:hover{border-color:rgba(226,232,240,.5)}
    </style>
</head>
<body>
    <main class="fs-paywall">
        <span class="fs-paywall__badge">🚀 FUTUR SITE</span>
        <h1>Le futur vous attend…<br>mais il est réservé aux membres premium.</h1>
        <p>Les articles programmés sont visibles en avance par les abonnés. Devenez premium pour voyager dans le futur du site.</p>
        <div class="fs-paywall__actions">
            <a class="fs-paywall__cta" href="<?php echo $premium_url; ?>">Devenir premium</a>
            <a class="fs-paywall__back" href="<?php echo $exit_url; ?>">← Retour au présent</a>
        </div>
    </main>
</body>
</html>
    <?php
    exit;
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
