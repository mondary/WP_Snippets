<?php
/*
 * Display name: FRONTEND 📊 STATS - Site Stats Page - 2026.09.10
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/* ── CONSTANTS ────────────────────────────────────────────────────── */
define('CLM_STATS_SLUG', 'statistiques');
define('CLM_STATS_TABLE', 'site_views');

/* ── REWRITE RULE + FLUSH ONCE ─────────────────────────────────────── */
add_action('init', 'clm_stats_rewrite');
function clm_stats_rewrite() {
    add_rewrite_rule('^' . CLM_STATS_SLUG . '/?$', 'index.php?clm_stats_page=1', 'top');
    add_rewrite_tag('%clm_stats_page%', '1');
    if (!get_option('clm_stats_flushed_v3')) {
        global $wpdb;
        $table = $wpdb->prefix . CLM_STATS_TABLE;
        $charset = $wpdb->get_charset_collate();
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            view_date date NOT NULL,
            views int(11) NOT NULL DEFAULT 0,
            visitors int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY view_date (view_date)
        ) {$charset}");
        $row = $wpdb->get_row("SELECT * FROM {$table} LIMIT 1", ARRAY_A);
        if ($row && !isset($row['visitors'])) {
            $wpdb->query("ALTER TABLE {$table} ADD COLUMN visitors int(11) NOT NULL DEFAULT 0 AFTER views");
            $wpdb->query("UPDATE {$table} SET visitors = ROUND(views * 0.8) WHERE visitors = 0");
        }
        flush_rewrite_rules();
        update_option('clm_stats_flushed_v3', 1);
    }
}

/* ── ADMIN MENU + IMPORT ──────────────────────────────────────────── */
add_action('admin_menu', 'clm_stats_admin_menu');
function clm_stats_admin_menu() {
    add_submenu_page('tools.php', 'Importer Stats', '📊 Importer Stats', 'manage_options', 'clm-stats-import', 'clm_stats_import_page');
}

function clm_stats_import_page() {
    global $wpdb;
    $table = $wpdb->prefix . CLM_STATS_TABLE;
    $msg = '';
    if (isset($_POST['clm_import_stats']) && check_admin_referer('clm_stats_import')) {
        $csv = sanitize_textarea_field($_POST['csv_data'] ?? '');
        $reset = !empty($_POST['clm_reset_stats']);
        $lines = array_filter(array_map('trim', explode("\n", $csv)));
        $imported = 0; $errors = [];
        if ($reset) {
            $wpdb->query("DELETE FROM {$table}");
            $msg .= "Table vidée. ";
        }
        foreach ($lines as $i => $line) {
            $line = preg_replace('/^#.*$/', '', $line);
            $line = trim($line);
            if ($line === '') continue;
            $parts = array_map('trim', explode(',', $line));
            if (count($parts) < 2 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $parts[0])) {
                $errors[] = "Ligne " . ($i+1) . " : format invalide";
                continue;
            }
            $date = $parts[0];
            $views = max(0, intval($parts[1]));
            $visitors = count($parts) >= 3 ? max(0, intval($parts[2])) : intval($views * 0.85);
            $wpdb->query($wpdb->prepare(
                "INSERT INTO {$table} (view_date, views, visitors) VALUES (%s, %d, %d)
                 ON DUPLICATE KEY UPDATE views = VALUES(views), visitors = VALUES(visitors)", $date, $views, $visitors
            ));
            $imported++;
        }
        $msg .= $imported . " ligne(s) importée(s).";
        if ($errors) $msg .= " Erreurs : " . implode(', ', array_slice($errors, 0, 5));
    }
    $total = (int) $wpdb->get_var("SELECT SUM(views) FROM {$table}");
    $days = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    ?>
    <div class="wrap">
        <h1>📊 Importer des statistiques</h1>
        <?php if ($msg): ?><div class="updated"><p><?php echo $msg; ?></p></div><?php endif; ?>
        <p>Total actuel : <b><?php echo number_format_i18n($total); ?></b> vues sur <b><?php echo $days; ?></b> jours.</p>
        <p>Pour voir les stats : <a href="<?php echo home_url('/' . CLM_STATS_SLUG . '/'); ?>" target="_blank"><?php echo home_url('/' . CLM_STATS_SLUG . '/'); ?></a></p>
        <h2>Importer un CSV</h2>
        <p>Format : une ligne par jour, <code>YYYY-MM-DD,vues[,visiteurs]</code>. Les lignes existantes sont écrasées.</p>
        <form method="post">
            <?php wp_nonce_field('clm_stats_import'); ?>
            <textarea name="csv_data" rows="15" class="large-text code" placeholder="2026-01-01,152&#10;2026-01-02,234,198&#10;..."></textarea>
            <p><label><input type="checkbox" name="clm_reset_stats" value="1"> Vider la table avant import (remplacement complet)</label></p>
            <p class="submit"><input type="submit" name="clm_import_stats" class="button-primary" value="Importer"></p>
        </form>
    </div>
    <?php
}

/* ── RENDER STANDALONE PAGE ────────────────────────────────────────── */
add_action('template_redirect', 'clm_stats_page');
function clm_stats_page() {
    if (!get_query_var('clm_stats_page')) return;
    nocache_headers();
    $year = isset($_GET['stats_year']) ? intval($_GET['stats_year']) : null;
    $data = clm_stats_get_data($year);
    get_header();
    clm_stats_render_html($data);
    get_footer();
    exit;
}

/* ── TRACKING ─────────────────────────────────────────────────────── */
add_action('template_redirect', 'clm_stats_track');
function clm_stats_track() {
    if (is_admin() || is_preview()) return;
    if (is_user_logged_in()) return;
    if (!is_singular('post')) return;
    if (clm_stats_is_bot()) return;

    global $wpdb;
    $today = current_time('Y-m-d');
    $table = $wpdb->prefix . CLM_STATS_TABLE;

    $is_new_visitor = false;
    $cookie_name = 'clm_v_' . str_replace('-', '', $today);
    if (!isset($_COOKIE[$cookie_name])) {
        $is_new_visitor = true;
        setcookie($cookie_name, '1', strtotime($today . ' 23:59:59'), '/');
    }

    $wpdb->query($wpdb->prepare(
        "INSERT INTO {$table} (view_date, views, visitors) VALUES (%s, 1, %d)
         ON DUPLICATE KEY UPDATE views = views + 1" . ($is_new_visitor ? ", visitors = visitors + 1" : ""),
        $today, $is_new_visitor ? 1 : 0
    ));

    $pid = get_the_ID();
    if ($pid) {
        $v = (int) get_post_meta($pid, '_clm_views', true);
        update_post_meta($pid, '_clm_views', $v + 1);
    }
}

/* ── BOT FILTER ───────────────────────────────────────────────────── */
function clm_stats_is_bot() {
    if (wp_doing_cron() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) return true;
    if (!empty($_SERVER['HTTP_X_PURPOSE']) || !empty($_SERVER['HTTP_X_MOZ'])) return true;

    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    if ($accept !== '' && strpos($accept, 'text/html') === false && strpos($accept, '*/*') === false) return true;

    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if ($ua === '') return true;

    static $bots = ['bot','crawl','spider','slurp','scraper','curl','wget','python','http-client','http_client',
        'java/','okhttp','go-http','php/','ruby','perl','libwww','headless','phantom','puppeteer','playwright',
        'lighthouse','pagespeed','gtmetrix','pingdom','uptime','monitor','ahrefs','semrush','mj12bot','dotbot',
        'rogerbot','screaming frog','sistrix','seznam','baidu','yandex','duckduck','facebookexternalhit',
        'twitterbot','linkedinbot','slackbot','discordbot','telegrambot','whatsapp','pinterest','applebot',
        'gptbot','chatgpt','claudebot','claude-','anthropic','perplexity','bytespider','ccbot','cohere',
        'ai2bot','omgili','meta-external','amazonbot','diffbot','timpibot','imagesift','petalbot','semanticscholar',
        'youbot','mojeek','qwant','exabot','ia_archiver','archive.org','heritrix','linkchecker','validator',
        'feedfetcher','rss','reader','mediapartners','adsbot','apis-google','inspectlet','datadog',
        'uptimerobot','freshping','statuscake','node-fetch','undici','fetch/','axios/','got/','superagent',
        'scrapy','httpx/','aiohttp','node','deno','bun'];
    $ua_l = strtolower($ua);
    foreach ($bots as $b) {
        if (strpos($ua_l, $b) !== false) return true;
    }

    $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    $enc = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
    if ($lang === '' && $enc === '' && $ua !== '') return true;

    return false;
}

/* ── STATS DATA ───────────────────────────────────────────────────── */
function clm_stats_get_data($year = null) {
    global $wpdb;
    $table = $wpdb->prefix . CLM_STATS_TABLE;
    if (!$year) $year = intval(current_time('Y'));
    $first = $wpdb->get_var("SELECT MIN(view_date) FROM {$table}");
    $min_year = $first ? intval(substr($first, 0, 4)) : intval(current_time('Y'));
    $today_str = current_time('Y-m-d');
    $today_views = (int) $wpdb->get_var($wpdb->prepare("SELECT views FROM {$table} WHERE view_date=%s", $today_str));
    $today_visitors = (int) $wpdb->get_var($wpdb->prepare("SELECT visitors FROM {$table} WHERE view_date=%s", $today_str));
    $week_ago = gmdate('Y-m-d', strtotime($today_str . ' -6 days'));
    $week_views = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(views) FROM {$table} WHERE view_date>=%s", $week_ago));
    $week_visitors = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM {$table} WHERE view_date>=%s", $week_ago));
    $year_views = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(views) FROM {$table} WHERE YEAR(view_date)=%d", $year));
    $year_visitors = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(visitors) FROM {$table} WHERE YEAR(view_date)=%d", $year));
    $total_views = (int) $wpdb->get_var("SELECT SUM(views) FROM {$table}");
    $total_visitors = (int) $wpdb->get_var("SELECT SUM(visitors) FROM {$table}");
    $total_posts = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish'");
    $daily_rows = $wpdb->get_results($wpdb->prepare("SELECT view_date, views, visitors FROM {$table} WHERE YEAR(view_date)=%d ORDER BY view_date", $year), ARRAY_A);
    $daily_map = [];
    foreach ($daily_rows as $d) $daily_map[$d['view_date']] = ['v' => (int) $d['views'], 'u' => (int) $d['visitors']];
    $is_current = ($year == intval(current_time('Y')));
    $leap = ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0));
    $max_day = $is_current ? (int) gmdate('z', current_time('U')) : ($leap ? 365 : 364);
    $cum = 0; $cum_u = 0; $cum_data = [];
    for ($i = 0; $i <= $max_day; $i++) {
        $d = gmdate('Y-m-d', gmmktime(0, 0, 0, 1, 1 + $i, $year));
        $val = $daily_map[$d] ?? ['v'=>0,'u'=>0];
        $cum += $val['v']; $cum_u += $val['u'];
        $cum_data[] = ['d' => $d, 'v' => $cum, 'u' => $cum_u];
    }
    $daily_list = [];
    for ($i = $max_day; $i >= 0; $i--) {
        $d = gmdate('Y-m-d', gmmktime(0, 0, 0, 1, 1 + $i, $year));
        if (isset($daily_map[$d])) $daily_list[] = ['d' => $d, 'v' => $daily_map[$d]['v'], 'u' => $daily_map[$d]['u']];
    }
    $dd_chart = [];
    for ($i = 0; $i <= $max_day; $i++) {
        $d = gmdate('Y-m-d', gmmktime(0, 0, 0, 1, 1 + $i, $year));
        $val = $daily_map[$d] ?? ['v'=>0,'u'=>0];
        $dd_chart[] = ['d' => $d, 'v' => $val['v'], 'u' => $val['u']];
    }
    return [
        'year' => $year, 'min_year' => $min_year,
        'today_views' => $today_views, 'today_visitors' => $today_visitors,
        'week_views' => $week_views, 'week_visitors' => $week_visitors,
        'year_views' => $year_views, 'year_visitors' => $year_visitors,
        'total_views' => $total_views, 'total_visitors' => $total_visitors,
        'total_posts' => $total_posts, 'cum_data' => $cum_data,
        'daily_list' => $daily_list, 'dd_chart' => $dd_chart,
    ];
}

/* ── RENDER STATS HTML ────────────────────────────────────────────── */
function clm_stats_render_html($data) {
    extract($data);
    /* Version du snippet curseur réellement actif sur le site */
    $cursor_ver = '?';
    global $wpdb;
    $cTable = $wpdb->prefix . 'snippets';
    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $cTable)) === $cTable) {
        $cn = $wpdb->get_var($wpdb->prepare(
            "SELECT name FROM {$cTable} WHERE active = 1 AND name LIKE %s ORDER BY id DESC LIMIT 1",
            $wpdb->esc_like('FRONTEND 📊 CURSOR - Live Cursors - ') . '%'
        ));
        if ($cn && preg_match('/(\d{4}\.\d{1,2}\.\d+)$/u', $cn, $m)) $cursor_ver = $m[1];
    }
    $months_fr = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    $months_short = ['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'];
    $year_nav = '';
    for ($y = $min_year; $y <= intval(current_time('Y')); $y++) {
        $href = esc_url(home_url('/' . CLM_STATS_SLUG . '/' . ($y == intval(current_time('Y')) ? '' : '?stats_year=' . $y)));
        $cls = ($y == $year) ? ' on' : '';
        $year_nav .= "<a class=\"ss-year-btn{$cls}\" href=\"{$href}\">{$y}</a>";
    }

    // Group daily_list by month and fill missing days
    $months_data = [];
    $daily_map = [];
    foreach ($daily_list as $d) {
        $daily_map[$d['d']] = $d;
    }

    // Build months with all days filled
    for ($m = 12; $m >= 1; $m--) {
        $mk = $year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $m, $year);
        $month_label = $months_fr[$m - 1] . ' ' . $year;
        $month_days = [];
        $total_v = 0; $total_u = 0;
        for ($d = 1; $d <= $days_in_month; $d++) {
            $date_str = $year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT) . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
            if (isset($daily_map[$date_str])) {
                $month_days[] = ['day' => $d, 'v' => $daily_map[$date_str]['v'], 'u' => $daily_map[$date_str]['u'], 'has_data' => true];
                $total_v += $daily_map[$date_str]['v'];
                $total_u += $daily_map[$date_str]['u'];
            } else {
                $month_days[] = ['day' => $d, 'v' => 0, 'u' => 0, 'has_data' => false];
            }
        }
        $has_any = $total_v > 0;
        $months_data[] = ['label' => $month_label, 'days' => $month_days, 'total_v' => $total_v, 'total_u' => $total_u, 'has_any' => $has_any];
    }

    $table_html = '';
    foreach ($months_data as $m) {
        if (!$m['has_any']) continue;
        $table_html .= '<div class="ss-month"><table class="ss-table"><thead><tr>';
        $table_html .= '<th>' . $m['label'] . '</th>';
        $table_html .= '<th>' . number_format_i18n($m['total_v']) . ' vues</th>';
        $table_html .= '<th>' . number_format_i18n($m['total_u']) . ' visiteurs</th>';
        $table_html .= '</tr></thead><tbody>';
        foreach ($m['days'] as $d) {
            if (!$d['has_data']) {
                $table_html .= '<tr class="ss-empty"><td>' . $d['day'] . '</td><td>—</td><td>—</td></tr>';
            } else {
                $table_html .= '<tr><td>' . $d['day'] . '</td><td>' . number_format_i18n($d['v']) . '</td><td>' . number_format_i18n($d['u']) . '</td></tr>';
            }
        }
        $table_html .= '</tbody></table></div>';
    }
    if (!$table_html) $table_html = '<div class="ss-month"><p style="text-align:center;color:var(--muted);padding:2rem 0">Aucune donnée pour cette année</p></div>';
    ?>
    <style>
    .ss-container{position:relative;--bg:#fafafa;--fg:#171717;--muted:#525252;--subtle:#666;--faint:#6e6e6e;--border:#1717171a;--border2:#1717172e;background:var(--bg);color:var(--fg);font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:.875rem;line-height:1.5;-webkit-font-smoothing:antialiased}
    .ss-container *{box-sizing:border-box}
    .ss-container a{color:inherit;text-decoration:none}
    .ss-container button{font:inherit;background:none;color:inherit;cursor:pointer}
    .ss-hero{text-align:center;padding:4rem 1.5rem 0}
    .ss-kicker{font-size:.75rem;letter-spacing:.25em;text-transform:uppercase;color:var(--muted)}
    .ss-hero-num{font-family:"Instrument Serif","Times New Roman",Georgia,serif;font-weight:400;font-size:clamp(4rem,12vw,7.5rem);line-height:1;margin:.5rem 0 .75rem;font-variant-numeric:tabular-nums}
    .ss-hero-sub{font-size:.875rem;color:var(--muted)}
    .ss-row{display:flex;justify-content:center;flex-wrap:wrap;gap:1.5rem 0;margin-top:3.5rem;padding-inline:1.5rem}
    .ss-stat{padding:0 2rem;text-align:center}
    .ss-stat+.ss-stat{border-left:1px solid var(--border)}
    .ss-stat b{display:block;font-family:"Instrument Serif","Times New Roman",Georgia,serif;font-weight:400;font-size:2rem;line-height:1.2;font-variant-numeric:tabular-nums}
    .ss-stat small{display:block;margin-top:.25rem;font-size:.625rem;color:var(--faint);font-variant-numeric:tabular-nums}
    .ss-stat span{display:block;margin-bottom:.375rem;font-size:.625rem;letter-spacing:.15em;text-transform:uppercase;color:var(--subtle);white-space:nowrap}
    @media(max-width:40rem){.ss-row{gap:1.5rem 0}.ss-stat{padding:0 1.25rem}.ss-stat:nth-child(4){border-left:0}}
    .ss-year-nav{display:flex;align-items:center;justify-content:center;gap:.5rem;margin-top:3rem;flex-wrap:wrap}
    .ss-year-btn{display:inline-flex;align-items:center;justify-content:center;min-width:2.75rem;height:2rem;padding:0 .625rem;font-size:.75rem;font-weight:600;border:1.5px solid var(--fg);border-radius:9999px;transition:all .15s}
    .ss-year-btn.on{background:var(--fg);color:var(--bg)}
    .ss-year-btn:hover:not(.on){background:rgba(23,23,23,.06)}
    .ss-tabs{display:flex;gap:1.25rem;margin-top:2rem}
    .ss-tab{padding-bottom:.375rem;font-size:.875rem;color:var(--muted);border-bottom:2px solid transparent}
    .ss-tab.on{color:var(--fg);border-bottom-color:var(--fg);font-weight:500}
    .ss-chart{margin-top:1.5rem;position:relative}
    .ss-chart-views{display:none}.ss-chart-views.on{display:block}
    .ss-chart svg{display:block;width:100%;height:auto}
    .ss-axis{fill:none;stroke:var(--fg);stroke-width:1.3;stroke-linecap:round}
    .ss-line{fill:none;stroke:var(--fg);stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
    .ss-note{margin:1.25rem auto 0;max-width:75rem;text-align:center;font-size:.75rem;color:var(--faint)}
    .ss-note b{color:var(--fg);font-weight:600}
    .ss-tip{position:absolute;z-index:5;transform:translate(-50%,-100%) rotate(-.6deg);background:#fff;border:1.5px solid var(--fg);border-radius:10px 13px 11px 14px;padding:3px 9px;font-size:11px;white-space:nowrap;box-shadow:0 2px 4px #00000014;pointer-events:none}
    .ss-tip[hidden]{display:none}
    .ss-section{margin-top:4rem;max-width:75rem;margin-inline:auto;padding-inline:1.5rem;padding-bottom:4rem}
    .ss-section-title{font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
    .ss-months{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1rem}
    @media(max-width:50rem){.ss-months{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:30rem){.ss-months{grid-template-columns:1fr}}
    .ss-month{border:1px solid var(--border2);border-radius:8px;overflow:hidden}
    .ss-month .ss-table{margin-top:0;width:100%;border-collapse:collapse}
    .ss-month .ss-table thead th{background:var(--fg);color:var(--bg);font-size:.625rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.5rem .625rem;text-align:left}
    .ss-month .ss-table thead th:last-child,.ss-month .ss-table thead th:nth-child(2){text-align:right}
    .ss-month .ss-table td{padding:.25rem .625rem;border-bottom:1px solid var(--border);font-size:.6875rem;line-height:1.4}
    .ss-month .ss-table td:first-child{color:var(--muted);width:2rem}
    .ss-month .ss-table td:last-child,.ss-month .ss-table td:nth-child(2){text-align:right;font-variant-numeric:tabular-nums}
    .ss-month .ss-table tr:last-child td{border-bottom:0}
    .ss-month .ss-table .ss-empty td{color:var(--border);font-size:.625rem}
    .ss-table{width:100%;border-collapse:collapse}
    .ss-version{position:absolute;top:1.25rem;right:1.5rem;font-size:.75rem;font-weight:600;color:var(--muted);letter-spacing:.06em}
    </style>
    <div class="ss-container">
    <section class="ss-hero">
      <p class="ss-kicker">Vues en <?php echo $year; ?></p>
      <h1 class="ss-hero-num" id="ss-total"><?php echo number_format_i18n($year_views) ?: '0'; ?></h1>
      <p class="ss-hero-sub"><?php echo number_format_i18n($year_visitors); ?> visiteurs · <?php echo number_format_i18n($total_views); ?> vues totales depuis <?php echo $min_year; ?></p>
    </section>
    <div class="ss-row">
      <div class="ss-stat"><span>En <?php echo $year; ?></span><b><?php echo number_format_i18n($year_views); ?> vues</b><small><?php echo number_format_i18n($year_visitors); ?> visiteurs</small></div>
      <div class="ss-stat"><span>Depuis <?php echo $min_year; ?></span><b><?php echo number_format_i18n($total_views); ?> vues</b><small><?php echo number_format_i18n($total_visitors); ?> visiteurs</small></div>
      <div class="ss-stat"><span>Aujourd'hui</span><b><?php echo number_format_i18n($today_views); ?> vues</b><small><?php echo number_format_i18n($today_visitors); ?> visiteurs</small></div>
    </div>
    <div style="max-width:75rem;margin-inline:auto;padding-inline:1.5rem">
    <nav class="ss-year-nav"><?php echo $year_nav; ?></nav>
    <div class="ss-tabs" role="tablist">
      <button class="ss-tab on" data-tab="daily" role="tab">Jour · <?php echo $year; ?></button>
      <button class="ss-tab" data-tab="cumul" role="tab">Cumul · <?php echo $year; ?></button>
    </div>
    <div class="ss-chart">
      <div class="ss-chart-views on" id="ss-view-daily"></div>
      <div class="ss-chart-views" id="ss-view-cumul"></div>
      <p class="ss-note"><b><?php echo number_format_i18n($year_views); ?> vues</b> · <b><?php echo number_format_i18n($year_visitors); ?> visiteurs</b> en <?php echo $year; ?>.</p>
      <div class="ss-tip" id="ss-tip" hidden></div>
    </div>
    <section class="ss-section">
      <h2 class="ss-section-title">Par jour · <?php echo $year; ?></h2>
      <div class="ss-months"><?php echo $table_html; ?></div>
    </section>
    <div class="ss-version" title="Site Stats Page · Live Cursors">Stats v2026.09.10 · Cursor v<?php echo esc_html($cursor_ver); ?></div>
    </div>
    </div>
    <script>
    (function(){
      var fmt=function(n){return n.toLocaleString('fr-FR')};
      var total=<?php echo max(1, $year_views); ?>;
      var cumData=<?php echo json_encode($cum_data); ?>;
      var ddData=<?php echo json_encode($dd_chart); ?>;
      var YEAR=<?php echo $year; ?>;

      (function(){
        var el=document.getElementById('ss-total'),t=total,s=Math.max(0,t-96),t0=null;
        function step(ts){if(!t0)t0=ts;var k=Math.min(1,(ts-t0)/700);
          el.textContent=fmt(Math.round(s+(t-s)*(1-Math.pow(1-k,3))));
          if(k<1)requestAnimationFrame(step)}
        requestAnimationFrame(step);
      })();

      document.querySelectorAll('.ss-tab').forEach(function(t){
        t.addEventListener('click',function(){
          document.querySelectorAll('.ss-tab').forEach(function(x){x.classList.toggle('on',x===t)});
          document.querySelectorAll('.ss-chart-views').forEach(function(v){v.classList.toggle('on',v.id==='ss-view-'+t.dataset.tab)});
        });
      });

      var seed=42;function rng(){seed=(seed*1103515245+12345)%2147483648;return seed/2147483648}
      function wob(x,y){return(x+(rng()-.5)*2).toFixed(2)+' '+(y+(rng()-.5)*2).toFixed(2)}
      function wobbly(pts){if(!pts.length)return"";var d='M '+wob(pts[0][0],pts[0][1]),i;for(i=1;i<pts.length;i++){d+=' L '+wob(pts[i][0],pts[i][1])}return d}
      var X0=70,Y0=50,W=760,H=220;
      function px(i,n){return n<=1?X0+W/2:X0+W*i/(n-1)}
      function py(v,mx){return mx<=0?Y0+H:Y0+H*(1-v/mx)}
      function dotpat(){return'<defs><pattern id="ss-dots" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r=".75" fill="var(--border)"></circle></pattern></defs><rect x="'+X0+'" y="'+Y0+'" width="'+W+'" height="'+H+'" fill="url(#ss-dots)" opacity=".8"></rect>'}
      function axes(){return'<path class="ss-axis" d="'+wobbly([[X0,Y0+H],[X0+W,Y0+H]])+'"></path><path class="ss-axis" d="'+wobbly([[X0,Y0],[X0,Y0+H]])+'"></path>'}
      var months=['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

      function drawChart(el,data,mx){
        if(!el||!data.length){if(el)el.innerHTML='<svg viewBox="0 0 900 380"><text x="450" y="190" text-anchor="middle" fill="var(--muted)" font-size="14">Aucune donnée</text></svg>';return}
        var N=data.length;mx=mx||1;
        var pts=data.map(function(d,i){return[px(i,N),py(d.v,mx)]});
        var ptsU=data.map(function(d,i){return[px(i,N),py(d.u,mx)]});
        var h=dotpat()+axes();
        [0,.25,.5,.75,1].forEach(function(f){
          var v=Math.round(mx*f),yy=py(v,mx).toFixed(1);
          h+='<path class="ss-axis" d="M '+(X0-6)+' '+yy+' L '+(X0-1)+' '+yy+'" stroke-opacity=".3"></path>';
          h+='<text x="'+(X0-12)+'" y="'+(parseFloat(yy)+4)+'" text-anchor="end" font-size="10" fill="var(--muted)">'+(v>999?(v/1000).toFixed(1)+'k':v)+'</text>';
        });
        h+='<path class="ss-line" d="'+wobbly(pts)+'" stroke-width="2"></path>';
        h+='<path class="ss-line" d="'+wobbly(ptsU)+'" stroke-width="1.5" stroke-dasharray="4 3" opacity=".5"></path>';
        
        var step=N>100?4:1;
        for(var i=0;i<N;i+=step){
            h+='<circle cx="'+pts[i][0].toFixed(1)+'" cy="'+pts[i][1].toFixed(1)+'" r="1.8" fill="var(--fg)"></circle>';
        }

        for(var m=0;m<12;m++){
          var dt=new Date(YEAR,m,1);var idx=Math.floor((dt-new Date(YEAR,0,1))/864e5);
          if(idx>=0&&idx<N){var xx=px(idx,N).toFixed(1);
            h+='<path class="ss-axis" d="M '+xx+' '+(Y0+H)+' L '+xx+' '+(Y0+H+8)+'"></path>';
            h+='<text x="'+xx+'" y="'+(Y0+H+24)+'" text-anchor="middle" font-size="11" fill="var(--muted)">'+months[m]+'</text>'}
        }
        el.innerHTML='<svg viewBox="0 0 900 380" aria-hidden="true" style="overflow:visible">'+h+'</svg>';
        bindTip(el.querySelector('svg'),data);
      }

      var cumMx=cumData.length?Math.max(cumData[cumData.length-1].v, 1):1;
      drawChart(document.getElementById('ss-view-cumul'),cumData,cumMx);
      var ddMx=0;ddData.forEach(function(d){if(d.v>ddMx)ddMx=d.v});
      drawChart(document.getElementById('ss-view-daily'),ddData,ddMx||1);

      function bindTip(svg,data){
        if(!svg)return;var wrap=document.querySelector('.ss-chart'),tip=document.getElementById('ss-tip');
        svg.addEventListener('mousemove',function(e){
          var r=svg.getBoundingClientRect(), x=(e.clientX-r.left)*(900/r.width);
          var idx=Math.round((x-X0)/W * (data.length-1));
          if(idx>=0&&idx<data.length){
            var d=data[idx], dt=new Date(d.d+'T00:00:00');
            var lbl=dt.getDate()+' '+['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'][dt.getMonth()]+' '+dt.getFullYear();
            tip.innerHTML=lbl+'<br><b>'+fmt(d.v)+'</b> vues · <b>'+fmt(d.u)+'</b> visiteurs';
            var wr=wrap.getBoundingClientRect();
            tip.style.left=((px(idx,data.length)/900)*wr.width)+'px';
            tip.style.top=((py(d.v,Math.max(...data.map(i=>i.v)))/380)*wr.height - 20)+'px';
            tip.hidden=false;
          }else tip.hidden=true;
        });
        svg.addEventListener('mouseleave',function(){tip.hidden=true});
      }
    })();
    </script>
    <?php
}