/**
 * Futur site
 * Apercu admin des posts publies + planifies via ?future_site=1
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('FS_FUTURE_SITE_QUERY_VAR')) {
    define('FS_FUTURE_SITE_QUERY_VAR', 'future_site');
}

if (!defined('FS_FUTURE_SITE_DEFAULT_CAP')) {
    define('FS_FUTURE_SITE_DEFAULT_CAP', 'fs_view_future_site');
}

if (!defined('FS_FUTURE_SITE_PREMIUM_ROLE')) {
    define('FS_FUTURE_SITE_PREMIUM_ROLE', 'premium');
}

if (!function_exists('fs_future_site_register_access_role')) {
    function fs_future_site_register_access_role() {
        $role = get_role(FS_FUTURE_SITE_PREMIUM_ROLE);

        if (!$role) {
            add_role(
                FS_FUTURE_SITE_PREMIUM_ROLE,
                'Premium',
                array(
                    'read' => true,
                    FS_FUTURE_SITE_DEFAULT_CAP => true,
                )
            );
            $role = get_role(FS_FUTURE_SITE_PREMIUM_ROLE);
        }

        if ($role && !$role->has_cap(FS_FUTURE_SITE_DEFAULT_CAP)) {
            $role->add_cap(FS_FUTURE_SITE_DEFAULT_CAP);
        }

        $admin_role = get_role('administrator');
        if ($admin_role && !$admin_role->has_cap(FS_FUTURE_SITE_DEFAULT_CAP)) {
            $admin_role->add_cap(FS_FUTURE_SITE_DEFAULT_CAP);
        }
    }
}

if (!function_exists('fs_future_site_preview_requested')) {
    function fs_future_site_preview_requested() {
        return isset($_GET[FS_FUTURE_SITE_QUERY_VAR]) && sanitize_text_field(wp_unslash($_GET[FS_FUTURE_SITE_QUERY_VAR])) === '1';
    }
}

if (!function_exists('fs_future_site_required_capability')) {
    function fs_future_site_required_capability() {
        return (string) apply_filters('fs_future_site_required_capability', FS_FUTURE_SITE_DEFAULT_CAP);
    }
}

if (!function_exists('fs_future_site_user_can_access')) {
    function fs_future_site_user_can_access($user_id = 0) {
        $user_id = $user_id ? (int) $user_id : (int) get_current_user_id();
        $required_cap = fs_future_site_required_capability();
        $can_access = $user_id > 0 && user_can($user_id, $required_cap);

        return (bool) apply_filters('fs_future_site_user_can_access', $can_access, $user_id, $required_cap);
    }
}

if (!function_exists('fs_future_site_preview_url')) {
    function fs_future_site_preview_url() {
        return add_query_arg(array(FS_FUTURE_SITE_QUERY_VAR => '1'), home_url('/'));
    }
}

if (!function_exists('fs_future_site_public_redirect_url')) {
    function fs_future_site_public_redirect_url() {
        return add_query_arg(array('future_premium' => '1'), home_url('/'));
    }
}

if (!function_exists('fs_future_site_premium_summary')) {
    function fs_future_site_premium_summary() {
        $future_posts = get_posts(array(
            'post_type'              => 'post',
            'post_status'            => 'future',
            'posts_per_page'         => -1,
            'orderby'                => 'date',
            'order'                  => 'ASC',
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'suppress_filters'       => false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ));

        $count = is_array($future_posts) ? count($future_posts) : 0;
        $days  = 0;
        $label = '';

        if ($count > 0) {
            $last_future_id = end($future_posts);
            $last_future_ts = get_post_time('U', false, $last_future_id);
            $now_ts = current_time('timestamp');

            if ($last_future_ts && $last_future_ts > $now_ts) {
                $days = (int) ceil(($last_future_ts - $now_ts) / DAY_IN_SECONDS);
                $label = sprintf(
                    _n(
                        '%d article en avance',
                        '%d articles en avance',
                        $count
                    ),
                    $count
                );

                if ($days > 0) {
                    $label .= ' sur ' . sprintf(
                        _n('%d jour', '%d jours', $days),
                        $days
                    );
                }
            } else {
                $label = sprintf(
                    _n(
                        '%d article planifie visible',
                        '%d articles planifies visibles',
                        $count
                    ),
                    $count
                );
            }
        }

        return array(
            'count' => $count,
            'days'  => $days,
            'label' => $label,
        );
    }
}

if (!function_exists('fs_future_site_is_post_query')) {
    function fs_future_site_is_post_query($query) {
        if (!$query instanceof WP_Query) {
            return false;
        }

        $post_type = $query->get('post_type');

        if (empty($post_type)) {
            return $query->is_home() || $query->is_archive() || $query->is_search() || $query->is_singular('post');
        }

        if (is_string($post_type)) {
            return $post_type === 'post';
        }

        if (is_array($post_type)) {
            return in_array('post', $post_type, true);
        }

        return false;
    }
}

if (!function_exists('fs_future_site_should_expand_post_status')) {
    function fs_future_site_should_expand_post_status($post_status) {
        if (empty($post_status)) {
            return true;
        }

        if (!is_array($post_status)) {
            $post_status = array($post_status);
        }

        $post_status = array_values(array_filter(array_map('strval', $post_status)));

        if ($post_status === array()) {
            return true;
        }

        if (in_array('future', $post_status, true) && !in_array('publish', $post_status, true)) {
            return false;
        }

        return in_array('publish', $post_status, true) || in_array('any', $post_status, true);
    }
}

add_action('init', 'fs_future_site_register_access_role', 5);

add_action('admin_menu', function () {
    if (!fs_future_site_user_can_access()) {
        return;
    }

    add_submenu_page(
        'edit.php',
        'Futur site',
        'Futur site',
        fs_future_site_required_capability(),
        'fs-futur-site',
        function () {
            $url = fs_future_site_preview_url();

            echo '<div class="wrap">';
            echo '<h1>Futur site</h1>';
            echo '<p>Redirection vers l’aperçu du site…</p>';
            echo '<p><a class="button button-primary" href="' . esc_url($url) . '">Ouvrir l’aperçu</a></p>';
            echo '</div>';
            echo '<script>setTimeout(function(){ window.location.href = ' . wp_json_encode($url) . '; }, 200);</script>';
        }
    );
}, 99);

add_action('admin_init', function () {
    if (!is_admin()) {
        return;
    }

    if (!isset($_GET['page']) || sanitize_key(wp_unslash($_GET['page'])) !== 'fs-futur-site') {
        return;
    }

    if (!fs_future_site_user_can_access()) {
        wp_die('Desole, vous n’avez pas l’autorisation d’acceder a cette page.');
    }

    wp_safe_redirect(fs_future_site_preview_url());
    exit;
});

add_action('template_redirect', function () {
    if (!fs_future_site_preview_requested()) {
        return;
    }

    if (!is_user_logged_in() || !fs_future_site_user_can_access()) {
        if (!is_user_logged_in()) {
            wp_safe_redirect(fs_future_site_public_redirect_url());
            exit;
        }

        global $wp_query;
        if ($wp_query instanceof WP_Query) {
            $wp_query->set_404();
        }

        status_header(404);
        nocache_headers();
        exit;
    }
}, 0);

add_filter('show_admin_bar', function ($show) {
    if (fs_future_site_preview_requested()) {
        return false;
    }

    return $show;
}, 20);

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query instanceof WP_Query) {
        return;
    }

    if (!fs_future_site_preview_requested() || !fs_future_site_user_can_access()) {
        return;
    }

    if (!fs_future_site_is_post_query($query)) {
        return;
    }

    $post_status = $query->get('post_status');
    if (!fs_future_site_should_expand_post_status($post_status)) {
        return;
    }

    if (empty($post_status)) {
        $post_status = array('publish');
    } elseif (!is_array($post_status)) {
        $post_status = array($post_status);
    }

    $post_status[] = 'future';
    $post_status[] = 'publish';

    $query->set('post_status', array_values(array_unique($post_status)));
    $query->set('ignore_sticky_posts', true);

    if ($query->is_main_query() && ($query->is_home() || $query->is_front_page() || $query->is_archive() || $query->is_search())) {
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }
}, 99);

add_filter('posts_results', function ($posts, $query) {
    if (is_admin() || !$query instanceof WP_Query || !$query->is_main_query()) {
        return $posts;
    }

    if (!fs_future_site_preview_requested() || !fs_future_site_user_can_access()) {
        return $posts;
    }

    if (!is_array($posts) || count($posts) < 2) {
        return $posts;
    }

    usort($posts, function ($a, $b) {
        $a_time = isset($a->post_date_gmt) ? strtotime($a->post_date_gmt . ' GMT') : 0;
        $b_time = isset($b->post_date_gmt) ? strtotime($b->post_date_gmt . ' GMT') : 0;
        return $b_time <=> $a_time;
    });

    return $posts;
}, 20, 2);

add_filter('query_loop_block_query_vars', function ($query_vars, $block) {
    if (!fs_future_site_preview_requested() || !fs_future_site_user_can_access()) {
        return $query_vars;
    }

    $post_type = isset($query_vars['post_type']) ? $query_vars['post_type'] : 'post';
    $is_post_query = false;

    if (is_string($post_type)) {
        $is_post_query = ($post_type === 'post');
    } elseif (is_array($post_type)) {
        $is_post_query = in_array('post', $post_type, true);
    }

    if (!$is_post_query) {
        return $query_vars;
    }

    $post_status = isset($query_vars['post_status']) ? $query_vars['post_status'] : array('publish');
    if (!fs_future_site_should_expand_post_status($post_status)) {
        return $query_vars;
    }

    if (!is_array($post_status)) {
        $post_status = array($post_status);
    }

    $post_status[] = 'publish';
    $post_status[] = 'future';
    $query_vars['post_status'] = array_values(array_unique($post_status));

    return $query_vars;
}, 20, 2);

add_action('admin_bar_menu', function ($admin_bar) {
    if (!is_user_logged_in() || !fs_future_site_user_can_access()) {
        return;
    }

    $preview_url = fs_future_site_preview_url();
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

$fs_render_banner = function () {
    if (!fs_future_site_preview_requested() || !fs_future_site_user_can_access()) {
        return;
    }

    $exit_url = remove_query_arg(FS_FUTURE_SITE_QUERY_VAR);
    $premium_summary = fs_future_site_premium_summary();
    ?>
    <div id="fs-future-banner" role="status" aria-live="polite">
        <div class="fs-future-banner__inner">
            <div class="fs-future-banner__left">
                <div class="fs-future-badge">PREMIUM</div>
                <div class="fs-future-text">
                    <strong>✨ Vue abonné premium.</strong>
                    <span>
                        <?php
                        if (!empty($premium_summary['label'])) {
                            echo esc_html('Tu vois actuellement ' . $premium_summary['label'] . '.');
                        } else {
                            echo esc_html('Tu vois ici les articles planifies en avant-premiere.');
                        }
                        ?>
                    </span>
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
            position:sticky;
            top:0;
            z-index:999999;
            padding:10px 12px;
            background:rgba(255,255,255,.94);
            backdrop-filter:blur(8px);
            border-bottom:1px solid rgba(0,0,0,.08);
        }
        #fs-future-banner .fs-future-banner__inner{
            max-width:1200px;
            margin:0 auto;
            display:flex;
            gap:12px;
            align-items:center;
            justify-content:space-between;
            font:13px/1.25 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        }
        #fs-future-banner .fs-future-banner__left{
            display:flex;
            gap:10px;
            align-items:center;
            min-width:0;
        }
        #fs-future-banner .fs-future-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:6px 10px;
            border-radius:999px;
            background:#111;
            color:#fff;
            font-weight:800;
            letter-spacing:.04em;
            white-space:nowrap;
        }
        #fs-future-banner .fs-future-text{
            display:flex;
            flex-direction:column;
            gap:2px;
            min-width:0;
        }
        #fs-future-banner .fs-future-text strong{
            font-size:13px;
        }
        #fs-future-banner .fs-future-text span{
            opacity:.78;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            max-width:72vw;
        }
        #fs-future-banner .fs-future-banner__right{
            display:flex;
            gap:10px;
            align-items:center;
            white-space:nowrap;
        }
        #fs-future-banner .fs-future-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            height:30px;
            padding:0 10px;
            border-radius:10px;
            border:1px solid rgba(0,0,0,.14);
            background:rgba(0,0,0,.04);
            color:inherit;
            text-decoration:none;
        }
        #fs-future-banner .fs-future-close{
            appearance:none;
            border:0;
            background:transparent;
            font-size:22px;
            line-height:1;
            padding:0 6px;
            cursor:pointer;
            opacity:.55;
        }
        #fs-future-banner .fs-future-close:hover{
            opacity:.9;
        }
        @media (max-width: 782px){
            #fs-future-banner .fs-future-banner__inner{
                flex-direction:column;
                align-items:flex-start;
            }
            #fs-future-banner .fs-future-banner__right{
                width:100%;
                justify-content:space-between;
            }
            #fs-future-banner .fs-future-text span{
                max-width:100%;
                white-space:normal;
            }
        }
        @media (prefers-reduced-motion:no-preference){
            #fs-future-banner{
                animation:fsBannerIn .18s ease-out;
            }
            @keyframes fsBannerIn{
                from{transform:translateY(-8px);opacity:0;}
                to{transform:translateY(0);opacity:1;}
            }
        }
    </style>

    <script>
        (function(){
            var banner = document.getElementById('fs-future-banner');
            if (!banner) {
                return;
            }

            var button = banner.querySelector('.fs-future-close');
            if (!button) {
                return;
            }

            button.addEventListener('click', function () {
                banner.style.display = 'none';
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

$fs_render_public_notice = function () {
    if (!isset($_GET['future_premium']) || sanitize_text_field(wp_unslash($_GET['future_premium'])) !== '1') {
        return;
    }
    ?>
    <div id="fs-future-public-notice" role="status" aria-live="polite">
        <div class="fs-future-public-notice__inner">
            <div class="fs-future-public-notice__text">
                <strong>Acces premium</strong>
                <span>Les articles planifies en avant-premiere sont reserves aux abonnes premium.</span>
            </div>
            <a class="fs-future-public-notice__btn" href="<?php echo esc_url(home_url('/')); ?>">Decouvrir l’abonnement</a>
        </div>
    </div>
    <style>
        #fs-future-public-notice{
            position:sticky;
            top:0;
            z-index:999998;
            padding:12px 16px;
            background:#111827;
            color:#fff;
        }
        #fs-future-public-notice .fs-future-public-notice__inner{
            max-width:1200px;
            margin:0 auto;
            display:flex;
            gap:14px;
            align-items:center;
            justify-content:space-between;
            font:13px/1.35 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        }
        #fs-future-public-notice .fs-future-public-notice__text{
            display:flex;
            flex-direction:column;
            gap:2px;
        }
        #fs-future-public-notice .fs-future-public-notice__btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:34px;
            padding:0 12px;
            border-radius:999px;
            background:#fff;
            color:#111827;
            text-decoration:none;
            font-weight:700;
            white-space:nowrap;
        }
        @media (max-width: 782px){
            #fs-future-public-notice .fs-future-public-notice__inner{
                flex-direction:column;
                align-items:flex-start;
            }
        }
    </style>
    <?php
};

if (function_exists('wp_body_open')) {
    add_action('wp_body_open', $fs_render_public_notice, 2);
} else {
    add_action('wp_footer', $fs_render_public_notice, 2);
}
