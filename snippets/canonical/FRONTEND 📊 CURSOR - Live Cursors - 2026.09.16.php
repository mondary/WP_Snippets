<?php
/*
 * Display name: FRONTEND 📊 CURSOR - Live Cursors - 2026.09.16
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * 2026.09.16 : le badge "N en ligne · X vues" (haut droite) et son dropdown
 * sont supprimes, remplaces par l'entree Statistiques du FAB Hub Flottant v1.
 * Ce script met desormais a jour le compteur #fab-live-count du hub
 * (window.__clmLiveN). Curseurs live + confettis inchanges.
 */

/* ── AJAX ENDPOINTS ───────────────────────────────────────────────── */
add_action('wp_ajax_clm_cursor_update', 'clm_cursor_update');
add_action('wp_ajax_nopriv_clm_cursor_update', 'clm_cursor_update');
function clm_cursor_update() {
    $dir = wp_upload_dir()['basedir'] . '/site-stats';
    if (!is_dir($dir)) wp_mkdir_p($dir);
    $file = $dir . '/cursors.json';
    $id   = sanitize_text_field($_POST['id'] ?? '');
    $x    = floatval($_POST['x'] ?? 0);
    $y    = floatval($_POST['y'] ?? 0);
    /* Vrai nom si possible : compte WP > cookie commentaire > "Anonyme N" unique.
     * ponytail: compteur en option, 2 premiers heartbeats simultanés peuvent partager un numéro (cosmétique). */
    if (is_user_logged_in()) {
        $name = wp_get_current_user()->display_name;
    } else {
        $name = sanitize_text_field($_POST['name'] ?? '');
        if (!preg_match('/^Anonyme \d{1,4}$/', $name)) {
            $commenter = wp_get_current_commenter();
            $name = sanitize_text_field(trim((string) ($commenter['comment_author'] ?? '')));
            if ($name === '') {
                $n = (int) get_option('clm_anon_n', 0) + 1;
                update_option('clm_anon_n', $n, false);
                $name = 'Anonyme ' . $n;
            }
        }
    }
    $clr  = sanitize_hex_color($_POST['color'] ?? '#666');
    $click = intval($_POST['click'] ?? 0);
    $page = sanitize_text_field($_POST['page'] ?? '/');
    if (!$id) { header('Content-Type: application/json'); echo '{"ok":0}'; exit; }
    $cursors = [];
    if (file_exists($file)) {
        $raw = file_get_contents($file);
        $cursors = $raw ? json_decode($raw, true) : [];
        if (!is_array($cursors)) $cursors = [];
    }
    $now = time();
    $cursors = array_filter($cursors, function($c) use ($now) { return ($now - $c['t']) < 10; });
    $entry = ['x' => $x, 'y' => $y, 'n' => $name, 'c' => $clr, 't' => $now, 'p' => $page];
    if ($click) $entry['click'] = 1;
    $cursors[$id] = $entry;
    $f = fopen($file, 'c');
    if ($f) { flock($f, LOCK_EX); ftruncate($f, 0); fwrite($f, json_encode($cursors)); flock($f, LOCK_UN); fclose($f); }
    $out = [];
    foreach ($cursors as $cid => $c) {
        if ($cid !== $id) {
            $o = ['id' => $cid, 'x' => $c['x'], 'y' => $c['y'], 'n' => $c['n'], 'c' => $c['c'], 'p' => ($c['p'] ?? '/')];
            if (!empty($c['click'])) {
                $o['click'] = 1;
                unset($cursors[$cid]['click']);
            }
            $out[] = $o;
        }
    }
    $f = fopen($file, 'c');
    if ($f) { flock($f, LOCK_EX); ftruncate($f, 0); fwrite($f, json_encode($cursors)); flock($f, LOCK_UN); fclose($f); }
    header('Content-Type: application/json');
    echo json_encode(['ok' => 1, 'me' => $name, 'cursors' => $out]);
    exit;
}

add_action('wp_ajax_clm_cursor_leave', 'clm_cursor_leave');
add_action('wp_ajax_nopriv_clm_cursor_leave', 'clm_cursor_leave');
function clm_cursor_leave() {
    $file = wp_upload_dir()['basedir'] . '/site-stats/cursors.json';
    $id = sanitize_text_field($_POST['id'] ?? '');
    if ($id && file_exists($file)) {
        $raw = file_get_contents($file);
        $cursors = $raw ? json_decode($raw, true) : [];
        if (!is_array($cursors)) $cursors = [];
        unset($cursors[$id]);
        $f = fopen($file, 'c');
        if ($f) { flock($f, LOCK_EX); ftruncate($f, 0); fwrite($f, json_encode($cursors)); flock($f, LOCK_UN); fclose($f); }
    }
    header('Content-Type: application/json');
    echo '{"ok":1}';
    exit;
}

/* ── INJECT JS ON ALL FRONTEND PAGES ──────────────────────────────── */
add_action('wp_footer', 'clm_cursor_footer_js');
function clm_cursor_footer_js() {
    if (is_admin()) return;
    $ajax_url = admin_url('admin-ajax.php');
    $wp_name = is_user_logged_in() ? wp_get_current_user()->display_name : '';
    if (!$wp_name) { $c = wp_get_current_commenter(); $wp_name = sanitize_text_field(trim((string) ($c['comment_author'] ?? ''))); }
    ?>
    <style>
    #clm-fx{position:fixed;inset:0;z-index:9998;pointer-events:none}
    .clm-cursor{position:absolute;top:0;left:0;z-index:9999;pointer-events:none;will-change:transform;opacity:0;transition:opacity .3s;display:none}
    .clm-cursor.show{opacity:1;display:block}
    .clm-cursor svg{display:block;width:16px;height:16px;filter:drop-shadow(0 1px 1px #0003)}
    .clm-cursor b{position:absolute;left:12px;top:16px;padding:.125rem .375rem;border-radius:.25rem .3rem .3rem .3rem;font-size:10px;font-weight:500;line-height:1.4;color:#fff;white-space:nowrap}
    .clm-cursor em{position:absolute;left:12px;top:32px;padding:.1rem .35rem;border-radius:.25rem .3rem .3rem .3rem;
      font-size:9px;font-style:normal;font-weight:400;line-height:1.3;color:#fff;white-space:nowrap;opacity:.7}
    </style>
    <canvas id="clm-fx"></canvas>
    <script>
    (function(){
      if(window.__clmCursorLoaded)return;window.__clmCursorLoaded=true;
      var AJAX='<?php echo esc_url($ajax_url); ?>';
      var ARROW='<svg viewBox="0 0 24 24" fill="none"><g transform="rotate(-20 12 12)"><path d="M5.5 3.21V20.8c0 .45.54.67.85.35l4.86-4.86a.5.5 0 0 1 .35-.15h6.87a.5.5 0 0 0 .35-.85L6.35 2.85a.5.5 0 0 0-.85.35Z" fill="FILL" stroke="none"></path></g></svg>';
      var COLORS=['#2563eb','#c05202','#08863a','#884ef9','#e80425','#e06c1f','#c71585','#20b2aa'];
      var WP_NAME='<?php echo esc_js($wp_name); ?>';
      var stored=localStorage.getItem('clm_cursor_id');
      var storedName=localStorage.getItem('clm_cursor_name');
      if(storedName && storedName.indexOf('Anonyme ')!==0)storedName=''; /* purge anciens faux prénoms */
      var me={
        id: stored || ('c_'+Math.random().toString(36).slice(2,8)),
        name: WP_NAME || storedName || '',
        color: localStorage.getItem('clm_cursor_color') || COLORS[Math.floor(Math.random()*COLORS.length)]
      };
      localStorage.setItem('clm_cursor_id',me.id);
      if(WP_NAME)localStorage.setItem('clm_cursor_name',WP_NAME);
      localStorage.setItem('clm_cursor_color',me.color);
       var others={},elements={},lastSend=0,lastX=0,lastY=0,hasMoved=false;

      /* fireworks */
      var cv=document.getElementById('clm-fx'),ctx=cv.getContext('2d');
      function resize(){cv.width=innerWidth;cv.height=innerHeight}
      resize();addEventListener('resize',resize);
      var parts=[];
      function burst(x,y,c){for(var i=0;i<18;i++){var a=Math.random()*Math.PI*2,sp=1.5+Math.random()*3.5;
        parts.push({x:x,y:y,vx:Math.cos(a)*sp,vy:Math.sin(a)*sp-1.2,g:.12,life:1,decay:.018+Math.random()*.02,r:1.2+Math.random()*1.6,c:c||COLORS[Math.floor(Math.random()*COLORS.length)]})}}

      function docH(){return Math.max(document.body.scrollHeight,document.documentElement.scrollHeight)}
      function applyCursors(d){
        if(d&&d.me&&!WP_NAME){me.name=d.me;localStorage.setItem('clm_cursor_name',me.name);}
        if(d&&d.cursors){
          others={};
          d.cursors.forEach(function(c){
            if(c.click)burst(c.x*innerWidth,c.y*docH()-scrollY,c.c);
            others[c.id]=c;
          });
          renderCursors();updateCount(Object.keys(others).length+1);
        }
      }
       function sendPos(x,y,clicked){
         lastX=x;lastY=y;
         var now=Date.now();if(!clicked&&now-lastSend<800)return;lastSend=now;
        var ax=x/innerWidth, ay=(y+scrollY)/docH();
        var fd=new FormData();fd.append('action','clm_cursor_update');fd.append('id',me.id);
        fd.append('x',ax.toFixed(4));fd.append('y',ay.toFixed(4));
        fd.append('name',me.name);fd.append('color',me.color);fd.append('page',location.pathname);
        if(clicked)fd.append('click','1');
        fetch(AJAX,{method:'POST',body:fd}).then(function(r){return r.json()}).then(applyCursors).catch(function(){});
      }

      function renderCursors(){
        var dh=docH(), curPath=location.pathname;
        Object.keys(elements).forEach(function(id){if(!others[id]){elements[id].remove();delete elements[id]}});
        Object.keys(others).forEach(function(id){
          var c=others[id];
          if(!elements[id]){
            var el=document.createElement('div');el.className='clm-cursor';
            el.innerHTML=ARROW.replace('FILL',c.c)+'<b style="background:'+c.c+'">'+c.n+'</b>'+(c.p?'<em>'+c.p+'</em>':'');
            document.body.appendChild(el);elements[id]=el;
          }
          var el=elements[id];
          if(c.p===curPath){
            el.style.transform='translate('+(c.x*innerWidth).toFixed(1)+'px,'+(c.y*dh).toFixed(1)+'px)';
            if(!el.classList.contains('show'))requestAnimationFrame(function(){el.classList.add('show')});
          }else{
            el.classList.remove('show');
          }
        });
      }

      var countEl=document.getElementById('fab-live-count');
      function updateCount(n){
        n=Math.max(1,n);window.__clmLiveN=n;
        if(countEl)countEl.textContent=n;
      }

       function poll(){
         if(!hasMoved)return;
         var fd=new FormData();fd.append('action','clm_cursor_update');fd.append('id',me.id);
         fd.append('x',(lastX/innerWidth).toFixed(4));
         fd.append('y',((lastY+scrollY)/docH()).toFixed(4));
        fd.append('name',me.name);fd.append('color',me.color);fd.append('page',location.pathname);
        fetch(AJAX,{method:'POST',body:fd}).then(function(r){return r.json()}).then(applyCursors).catch(function(){});
      }

       document.addEventListener('mousemove',function(e){
         hasMoved=true;sendPos(e.clientX,e.clientY);
       });
       document.addEventListener('click',function(e){
         hasMoved=true;
         burst(e.clientX,e.clientY);
        sendPos(e.clientX,e.clientY,true);
      });

      /* particles loop */
      function loop(){
        if(parts.length){
          ctx.clearRect(0,0,cv.width,cv.height);
          parts=parts.filter(function(p){return p.life>0});
          parts.forEach(function(p){
            p.x+=p.vx;p.y+=p.vy;p.vy+=p.g;p.vx*=.985;p.life-=p.decay;
            ctx.globalAlpha=Math.max(0,p.life);ctx.fillStyle=p.c;
            ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,7);ctx.fill();
          });
          ctx.globalAlpha=1;
          if(!parts.length)ctx.clearRect(0,0,cv.width,cv.height);
        }
        requestAnimationFrame(loop);
      }
      requestAnimationFrame(loop);

      window.addEventListener('beforeunload',function(){
        var fd=new FormData();fd.append('action','clm_cursor_leave');fd.append('id',me.id);
        navigator.sendBeacon(AJAX,fd);
      });

      setInterval(poll,1500);
      poll();
    })();
    </script>
    <?php
}
