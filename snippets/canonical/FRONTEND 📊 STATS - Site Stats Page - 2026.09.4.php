<?php
/*
 * Display name: FRONTEND 📊 STATS - Site Stats Page - 2026.09.4
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
    if (!get_option('clm_stats_flushed_v2')) {
        global $wpdb;
        $table = $wpdb->prefix . CLM_STATS_TABLE;
        $charset = $wpdb->get_charset_collate();
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            view_date date NOT NULL,
            views int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY view_date (view_date)
        ) {$charset}");
        flush_rewrite_rules();
        update_option('clm_stats_flushed_v2', 1);
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
        $lines = array_filter(array_map('trim', explode("\n", $csv)));
        $imported = 0; $errors = [];
        foreach ($lines as $i => $line) {
            $parts = array_map('trim', explode(',', $line));
            if (count($parts) < 2 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $parts[0])) {
                $errors[] = "Ligne " . ($i+1) . " : format invalide (attendu: YYYY-MM-DD,nombre)";
                continue;
            }
            $date = $parts[0];
            $views = intval($parts[1]);
            if ($views < 0) $views = 0;
            $wpdb->query($wpdb->prepare(
                "INSERT INTO {$table} (view_date, views) VALUES (%s, %d)
                 ON DUPLICATE KEY UPDATE views = VALUES(views)", $date, $views
            ));
            $imported++;
        }
        $msg = $imported . " ligne(s) importée(s).";
        if ($errors) $msg .= " Erreurs : " . implode(', ', array_slice($errors, 0, 5));
        delete_option('clm_stats_flushed_v2');
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
        <p>Format : une ligne par jour, <code>YYYY-MM-DD,nombre</code>. Les lignes existantes sont écrasées.</p>
        <p>Exemple pour exporter depuis Google Analytics : allez dans <em>Audience → Vue d'ensemble</em>, sélectionnez la période, puis <em>Exporter → CSV</em>. Colonne Date + Sessions/Utilisateurs.</p>
        <form method="post">
            <?php wp_nonce_field('clm_stats_import'); ?>
            <textarea name="csv_data" rows="15" class="large-text code" placeholder="2026-01-01,152&#10;2026-01-02,234&#10;2026-01-03,189&#10;..."></textarea>
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
    if (!is_singular() && !get_query_var('clm_stats_page')) return;
    if (get_query_var('clm_stats_page')) return;
    global $wpdb;
    $today = current_time('Y-m-d');
    $wpdb->query($wpdb->prepare(
        "INSERT INTO {$wpdb->prefix}" . CLM_STATS_TABLE . " (view_date,views) VALUES (%s,1)
         ON DUPLICATE KEY UPDATE views=views+1", $today
    ));
    $pid = get_the_ID();
    if ($pid) {
        $v = (int) get_post_meta($pid, '_clm_views', true);
        update_post_meta($pid, '_clm_views', $v + 1);
    }
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
    $week_ago = gmdate('Y-m-d', strtotime($today_str . ' -6 days'));
    $week_views = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(views) FROM {$table} WHERE view_date>=%s", $week_ago));
    $year_views = (int) $wpdb->get_var($wpdb->prepare("SELECT SUM(views) FROM {$table} WHERE YEAR(view_date)=%d", $year));
    $total_views = (int) $wpdb->get_var("SELECT SUM(views) FROM {$table}");
    $total_posts = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish'");
    $daily_rows = $wpdb->get_results($wpdb->prepare("SELECT view_date, views FROM {$table} WHERE YEAR(view_date)=%d ORDER BY view_date", $year), ARRAY_A);
    $daily_map = [];
    foreach ($daily_rows as $d) $daily_map[$d['view_date']] = (int) $d['views'];
    $is_current = ($year == intval(current_time('Y')));
    $leap = ($year % 4 == 0 && ($year % 100 != 0 || $year % 400 == 0));
    $max_day = $is_current ? (int) gmdate('z', current_time('U')) : ($leap ? 365 : 364);
    $cum = 0; $cum_data = [];
    for ($i = 0; $i <= $max_day; $i++) {
        $d = gmdate('Y-m-d', gmmktime(0, 0, 0, 1, 1 + $i, $year));
        $cum += $daily_map[$d] ?? 0;
        $cum_data[] = ['d' => $d, 'v' => $cum];
    }
    $daily_list = [];
    for ($i = $max_day; $i >= 0; $i--) {
        $d = gmdate('Y-m-d', gmmktime(0, 0, 0, 1, 1 + $i, $year));
        if (isset($daily_map[$d])) $daily_list[] = ['d' => $d, 'v' => $daily_map[$d]];
    }
    $dd_chart = [];
    for ($i = 0; $i <= $max_day; $i++) {
        $d = gmdate('Y-m-d', gmmktime(0, 0, 0, 1, 1 + $i, $year));
        $dd_chart[] = ['d' => $d, 'v' => $daily_map[$d] ?? 0];
    }
    return [
        'year' => $year, 'min_year' => $min_year,
        'today_views' => $today_views, 'week_views' => $week_views,
        'year_views' => $year_views, 'total_views' => $total_views,
        'total_posts' => $total_posts, 'cum_data' => $cum_data,
        'daily_list' => $daily_list, 'dd_chart' => $dd_chart,
    ];
}

/* ── RENDER STATS HTML ────────────────────────────────────────────── */
function clm_stats_render_html($data) {
    extract($data);
    $months_fr = ['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'];
    $year_nav = '';
    for ($y = $min_year; $y <= intval(current_time('Y')); $y++) {
        $href = esc_url(home_url('/' . CLM_STATS_SLUG . '/' . ($y == intval(current_time('Y')) ? '' : '?stats_year=' . $y)));
        $cls = ($y == $year) ? ' on' : '';
        $year_nav .= "<a class=\"ss-year-btn{$cls}\" href=\"{$href}\">{$y}</a>";
    }
    $table_rows = '';
    foreach ($daily_list as $d) {
        $dt = new DateTime($d['d']);
        $lbl = $dt->format('j') . ' ' . $months_fr[$dt->format('n') - 1] . ' ' . $dt->format('Y');
        $table_rows .= '<tr><td>' . $lbl . '</td><td>' . number_format_i18n($d['v']) . '</td></tr>';
    }
    if (!$table_rows) $table_rows = '<tr><td colspan="2" style="text-align:center;color:var(--muted);padding:2rem 0">Aucune donnée pour cette année</td></tr>';
    ?>
    <style>
    .ss-container{--bg:#fafafa;--fg:#171717;--muted:#525252;--subtle:#666;--faint:#6e6e6e;--border:#1717171a;--border2:#1717172e;background:var(--bg);color:var(--fg);font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;font-size:.875rem;line-height:1.5;-webkit-font-smoothing:antialiased}
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
    .ss-stat span{display:block;margin-bottom:.375rem;font-size:.625rem;letter-spacing:.15em;text-transform:uppercase;color:var(--subtle);white-space:nowrap}
    @media(max-width:40rem){.ss-row{gap:1.5rem 0}.ss-stat{padding:0 1.25rem}.ss-stat:nth-child(3){border-left:0}}
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
    .ss-note{margin:1.25rem auto 0;max-width:42rem;text-align:center;font-size:.75rem;color:var(--faint)}
    .ss-note b{color:var(--fg);font-weight:600}
    .ss-tip{position:absolute;z-index:5;transform:translate(-50%,-100%) rotate(-.6deg);background:#fff;border:1.5px solid var(--fg);border-radius:10px 13px 11px 14px;padding:3px 9px;font-size:11px;white-space:nowrap;box-shadow:0 2px 4px #00000014;pointer-events:none}
    .ss-tip[hidden]{display:none}
    .ss-section{margin-top:4rem;max-width:42rem;margin-inline:auto;padding-inline:1.5rem;padding-bottom:4rem}
    .ss-section-title{font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
    .ss-table{width:100%;margin-top:1rem;border-collapse:collapse}
    .ss-table th{font-weight:400;font-size:.75rem;color:var(--muted);text-align:left;padding:.5rem 0;border-bottom:1px solid var(--border2)}
    .ss-table th:last-child,.ss-table td:last-child{text-align:right;font-variant-numeric:tabular-nums}
    .ss-table td{padding:.5rem 0;border-bottom:1px solid var(--border);font-size:.8125rem}
    .ss-table tr:last-child td{border-bottom:0}
    </style>
    <div class="ss-container">
    <section class="ss-hero">
      <p class="ss-kicker">Stats</p>
      <h1 class="ss-hero-num" id="ss-total"><?php echo number_format_i18n($total_views) ?: '0'; ?></h1>
      <p class="ss-hero-sub">vues sur le site depuis <?php echo $min_year; ?></p>
    </section>
    <div class="ss-row">
      <div class="ss-stat"><span>Vues aujourd'hui</span><b><?php echo number_format_i18n($today_views); ?></b></div>
      <div class="ss-stat"><span>Vues · 7j</span><b><?php echo number_format_i18n($week_views); ?></b></div>
      <div class="ss-stat"><span>Vues · <?php echo $year; ?></span><b><?php echo number_format_i18n($year_views); ?></b></div>
      <div class="ss-stat"><span>Articles publiés</span><b><?php echo number_format_i18n($total_posts); ?></b></div>
    </div>
    <div style="max-width:58rem;margin-inline:auto;padding-inline:1.5rem">
    <nav class="ss-year-nav"><?php echo $year_nav; ?></nav>
    <div class="ss-tabs" role="tablist">
      <button class="ss-tab on" data-tab="daily" role="tab">Jour · <?php echo $year; ?></button>
      <button class="ss-tab" data-tab="cumul" role="tab">Cumul · <?php echo $year; ?></button>
    </div>
    <div class="ss-chart">
      <div class="ss-chart-views on" id="ss-view-daily"></div>
      <div class="ss-chart-views" id="ss-view-cumul"></div>
      <p class="ss-note"><b><?php echo number_format_i18n($year_views); ?> vues</b> en <?php echo $year; ?>.</p>
      <div class="ss-tip" id="ss-tip" hidden></div>
    </div>
    <section class="ss-section">
      <h2 class="ss-section-title">Vues par jour · <?php echo $year; ?></h2>
      <table class="ss-table">
        <thead><tr><th>Date</th><th>Vues</th></tr></thead>
        <tbody><?php echo $table_rows; ?></tbody>
      </table>
    </section>
    </div>
    </div>
    <script>
    (function(){
      var fmt=function(n){return n.toLocaleString('fr-FR')};
      var total=<?php echo max(1, $total_views); ?>;
      var cumData=<?php echo json_encode($cum_data); ?>;
      var ddData=<?php echo json_encode($dd_chart); ?>;
      var YEAR=<?php echo $year; ?>;

      /* count-up hero */
      (function(){
        var el=document.getElementById('ss-total'),t=total,s=Math.max(0,t-96),t0=null;
        function step(ts){if(!t0)t0=ts;var k=Math.min(1,(ts-t0)/700);
          el.textContent=fmt(Math.round(s+(t-s)*(1-Math.pow(1-k,3))));
          if(k<1)requestAnimationFrame(step)}
        requestAnimationFrame(step);
      })();

      /* tabs */
      document.querySelectorAll('.ss-tab').forEach(function(t){
        t.addEventListener('click',function(){
          document.querySelectorAll('.ss-tab').forEach(function(x){x.classList.toggle('on',x===t)});
          document.querySelectorAll('.ss-chart-views').forEach(function(v){v.classList.toggle('on',v.id==='ss-view-'+t.dataset.tab)});
        });
      });

      /* hand-drawn SVG */
      var seed=42;function rng(){seed=(seed*1103515245+12345)%2147483648;return seed/2147483648}
      function wob(x,y){return(x+(rng()-.5)*2.4).toFixed(2)+' '+(y+(rng()-.5)*2.4).toFixed(2)}
      function wobbly(pts){var d='',i;for(i=0;i<pts.length-1;i++){for(var t=0;t<6;t++){var k=t/6,x=pts[i][0]+(pts[i+1][0]-pts[i][0])*k,y=pts[i][1]+(pts[i+1][1]-pts[i][1])*k;d+=(d?' L ':'M ')+wob(x,y)}}return d}
       var X0=60,Y0=40,W=780,H=230;
       function px(i,n){return n<=1?X0+W/2:X0+W*i/(n-1)}
       function py(v,mx){return mx===0?Y0+H:Y0+H*(1-v/mx)}
       function dotpat(){return'<defs><pattern id="ss-dots" width="16" height="16" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r=".75" fill="var(--border)"></circle></pattern></defs><rect x="'+X0+'" y="'+Y0+'" width="'+W+'" height="'+H+'" fill="url(#ss-dots)" opacity=".8"></rect>'}
       function axes(){return'<path class="ss-axis" d="'+wobbly([[X0,Y0+H],[X0+W,Y0+H]])+'"></path><path class="ss-axis" d="'+wobbly([[X0,Y0],[X0,Y0+H]])+'"></path>'}
       var months=['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

       function drawChart(el,data,mx){
         if(!el||!data.length){if(el)el.innerHTML='<svg viewBox="0 0 900 350"><text x="450" y="175" text-anchor="middle" fill="var(--muted)" font-size="14">Aucune donnée</text></svg>';return}
         var N=data.length;mx=mx||1;
         var pts=data.map(function(d,i){return[px(i,N),py(d.v,mx)]});
         var h=dotpat()+axes();
         [0,.25,.5,.75,1].forEach(function(f){
           var v=Math.round(mx*f),yy=py(v,mx).toFixed(1);
           h+='<path class="ss-axis" d="M '+(X0-6)+' '+yy+' L '+(X0-1)+' '+yy+'" stroke-opacity=".5"></path>';
           h+='<text x="'+(X0-12)+'" y="'+(parseFloat(yy)+3)+'" text-anchor="end" font-size="10" fill="var(--muted)">'+(v>999?(v/1000).toFixed(0)+'k':v)+'</text>';
         });
         h+='<path class="ss-line" d="'+wobbly(pts)+'"></path>';
         pts.forEach(function(p){h+='<circle cx="'+p[0].toFixed(1)+'" cy="'+p[1].toFixed(1)+'" r="2.4" fill="var(--fg)"></circle>'});
         for(var m=0;m<12;m++){
           var dt=new Date(YEAR,m,1);var idx=Math.floor((dt-new Date(YEAR,0,1))/864e5);
           if(idx>=0&&idx<N){var xx=px(idx,N).toFixed(1);
             h+='<path class="ss-axis" d="M '+xx+' '+(Y0+H)+' L '+xx+' '+(Y0+H+8)+'"></path>';
             h+='<text x="'+xx+'" y="'+(Y0+H+22)+'" text-anchor="middle" font-size="11" fill="var(--muted)">'+months[m]+'</text>'}
         }
         el.innerHTML='<svg viewBox="0 0 900 350" aria-hidden="true">'+h+'</svg>';
         bindTip(el.querySelector('svg'),data);
       }

      var cumMx=cumData.length?cumData[cumData.length-1].v:1;
      drawChart(document.getElementById('ss-view-cumul'),cumData,cumMx||1);
      var ddMx=0;ddData.forEach(function(d){if(d.v>ddMx)ddMx=d.v});
      drawChart(document.getElementById('ss-view-daily'),ddData,ddMx||1);

      /* tooltips */
      function bindTip(svg,data){
        if(!svg)return;var wrap=document.querySelector('.ss-chart'),tip=document.getElementById('ss-tip');
        var pts=null;
        function ensure(){pts=[];svg.querySelectorAll('circle').forEach(function(c,i){var r=c.getBoundingClientRect();pts.push({x:r.left+r.width/2,y:r.top+r.height/2,i:i})})}
        svg.addEventListener('mouseenter',ensure);
        svg.addEventListener('mousemove',function(e){
          if(!pts)ensure();var best=null,bd=1e9;
          pts.forEach(function(p){var d=Math.abs(p.x-e.clientX);if(d<bd){bd=d;best=p}});
          if(best&&bd<40){var d=data[best.i];var dt=new Date(d.d+'T00:00:00');
            var lbl=dt.getDate()+' '+['jan','fév','mar','avr','mai','jun','jul','aoû','sep','oct','nov','déc'][dt.getMonth()]+' '+dt.getFullYear();
            tip.innerHTML=lbl+' · '+fmt(d.v)+' vues';
            var wr=wrap.getBoundingClientRect();tip.style.left=(best.x-wr.left)+'px';tip.style.top=(best.y-wr.top-10)+'px';tip.hidden=false;
          }else tip.hidden=true;
        });
        svg.addEventListener('mouseleave',function(){tip.hidden=true;pts=null});
      }
    })();
    </script>
    <?php
}
