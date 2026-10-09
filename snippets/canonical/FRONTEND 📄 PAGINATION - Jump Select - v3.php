<?php
/*
 * Display name: FRONTEND 📄 PAGINATION - Jump Select - v3
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Title: Pagination « aller a la page » (v3)
 * Description: v3 - CHAQUE groupe de points de suspension de la pagination
 *              Kadence devient un pill « ... » cliquable qui ouvre le
 *              popover de saut de page. En v2, seul le groupe de droite
 *              etait remplace : sur une page du milieu (ex. 1 ... 49 50 51
 *              52 53 ... 298), les points de gauche restaient un « ... »
 *              mort et impossible a distinguer du jumper - perturbant.
 *              Regle v3 : partout ou il y a des points, on peut cliquer.
 *              Le popover (partage, positionne sous le bouton clique) :
 *              - saisie « Page __ / N » validee par Entree ou « Aller »
 *                (clamp automatique 1..N)
 *              - raccourcis -50 / -10 / +10 / +50 pour explorer
 *              - fermable par ESC (retour focus au bouton), clic exterieur
 *              Pill <button> classe .page-numbers (style Kadence herite,
 *              hover reactif contrairement au span statique), popover
 *              position:fixed jamais clippe, URL reconstruite depuis un
 *              lien /page/N/ existant (page 1 = base sans /page/).
 *              JS vanilla sans jQuery, HTML statique inchange : compatible
 *              cache LiteSpeed.
 * Hooks WP: wp_footer
 * Fonctions clefs: clm_jump_select_footer_v3 (suffixee par version :
 *                le plugin execute le snippet a l'activation, deux
 *                versions declarant le meme nom de fonction = fatal
 *                « Cannot redeclare »)
 */

add_action('wp_footer', 'clm_jump_select_footer_v3', 99);
function clm_jump_select_footer_v3() {
    if (is_admin() || is_feed()) return;
    ?>
<style>
button.page-numbers.clm-p3-btn{cursor:pointer;background:transparent;font:inherit;color:inherit}
button.page-numbers.clm-p3-btn:focus-visible{outline:2px solid currentColor;outline-offset:2px}
.pagination button.clm-p3-btn:hover{background:var(--global-palette-btn-bg,#222);color:var(--global-palette-btn,#fff)}
.clm-p3-veil{position:fixed;inset:0;z-index:99998}
.clm-p3-pop{position:fixed;z-index:99999;width:238px;box-sizing:border-box;padding:12px;background:var(--global-palette9,#fff);border:1px solid var(--global-palette6,#e0e0e0);border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.14);font-size:14px;line-height:1.4}
.clm-p3-pop[hidden]{display:none}
.clm-p3-title{display:block;font-size:11px;letter-spacing:.05em;text-transform:uppercase;opacity:.55;margin-bottom:8px}
.clm-p3-row{display:flex;gap:6px;align-items:center}
.clm-p3-input{width:64px;padding:.35em .5em;border:1px solid var(--global-palette6,#ccc);border-radius:6px;font:inherit;text-align:center;background:transparent;color:inherit}
.clm-p3-input:focus{outline:2px solid currentColor;outline-offset:1px}
.clm-p3-total{opacity:.6;white-space:nowrap;font-size:13px}
.clm-p3-go{margin-left:auto;padding:.4em .9em;border:0;border-radius:6px;background:var(--global-palette-btn-bg,#222);color:var(--global-palette-btn,#fff);font:inherit;cursor:pointer}
.clm-p3-go:hover{opacity:.88}
.clm-p3-go:focus-visible{outline:2px solid currentColor;outline-offset:2px}
.clm-p3-quick{display:flex;gap:6px;margin-top:10px}
.clm-p3-quick button{flex:1;padding:.32em 0;border:1px solid var(--global-palette6,#ccc);border-radius:6px;background:transparent;color:inherit;font:inherit;font-size:13px;cursor:pointer}
.clm-p3-quick button:hover{background:rgba(0,0,0,.05)}
.clm-p3-quick button:focus-visible{outline:2px solid currentColor;outline-offset:1px}
@media (max-width:480px){.clm-p3-pop{width:216px}}
</style>
<script>
(function () {
    if (window.__clmJump3) return;
    window.__clmJump3 = true;

    function urlFor(tpl, n) {
        if (/\/page\/\d+/.test(tpl)) {
            return n === 1
                ? tpl.replace(/\/page\/\d+\/?($|\?|#)/, '$1') || '/'
                : tpl.replace(/\/page\/\d+/, '/page/' + n);
        }
        try {
            var u = new URL(tpl, window.location.href);
            u.searchParams.delete('paged');
            if (n > 1) u.searchParams.set('paged', n);
            return u.toString();
        } catch (e) {
            return n === 1 ? tpl.replace(/([?&])paged=\d+&?/, '$1') : tpl.replace(/([?&]paged=)\d+/, '$1' + n);
        }
    }

    function buildNav(nav) {
        if (!nav || nav.querySelector('.clm-p3-btn')) return;
        var links = nav.querySelectorAll('.page-numbers');
        if (!links.length) return;

        var current = 0, max = 0, tpl = '';
        links.forEach(function (el) {
            if (el.classList.contains('prev') || el.classList.contains('next')) return;
            var n = 0;
            var href = el.getAttribute('href') || '';
            var m = href.match(/\/page\/(\d+)/) || href.match(/[?&]paged=(\d+)/);
            if (m) {
                n = parseInt(m[1], 10);
            } else {
                var t = (el.textContent || '').trim();
                if (/^\d+$/.test(t)) n = parseInt(t, 10);
            }
            if (!n) return;
            if (n > max) max = n;
            if (el.classList.contains('current')) current = n;
            if (el.tagName === 'A' && href) tpl = href;
        });
        if (max < 2 || !tpl) return;
        if (!current) {
            var loc = window.location.pathname.match(/\/page\/(\d+)/);
            current = loc ? parseInt(loc[1], 10) : 1;
        }

        var veil = document.createElement('div');
        veil.className = 'clm-p3-veil';
        veil.hidden = true;

        var pop = document.createElement('div');
        pop.className = 'clm-p3-pop';
        pop.setAttribute('role', 'dialog');
        pop.setAttribute('aria-label', 'Aller \u00E0 la page');
        pop.hidden = true;

        var title = document.createElement('span');
        title.className = 'clm-p3-title';
        title.textContent = 'Aller \u00E0 la page';

        var row = document.createElement('div');
        row.className = 'clm-p3-row';
        var input = document.createElement('input');
        input.type = 'text';
        input.inputMode = 'numeric';
        input.pattern = '[0-9]*';
        input.className = 'clm-p3-input';
        input.placeholder = String(current);
        input.setAttribute('aria-label', 'Num\u00E9ro de page');
        var total = document.createElement('span');
        total.className = 'clm-p3-total';
        total.textContent = '/ ' + max;
        var goBtn = document.createElement('button');
        goBtn.type = 'button';
        goBtn.className = 'clm-p3-go';
        goBtn.textContent = 'Aller';
        row.appendChild(input);
        row.appendChild(total);
        row.appendChild(goBtn);

        var quick = document.createElement('div');
        quick.className = 'clm-p3-quick';
        [-50, -10, 10, 50].forEach(function (d) {
            var qb = document.createElement('button');
            qb.type = 'button';
            qb.textContent = (d > 0 ? '+' : '\u2212') + Math.abs(d);
            qb.title = (d > 0 ? 'Avancer' : 'Reculer') + ' de ' + Math.abs(d) + ' pages';
            qb.addEventListener('click', function () { go(current + d); });
            quick.appendChild(qb);
        });

        pop.appendChild(title);
        pop.appendChild(row);
        pop.appendChild(quick);

        var opener = null;

        function place() {
            pop.style.visibility = 'hidden';
            pop.hidden = false;
            var r = opener.getBoundingClientRect();
            var pw = pop.offsetWidth, ph = pop.offsetHeight;
            var left = Math.max(8, Math.min(r.left + r.width / 2 - pw / 2, window.innerWidth - pw - 8));
            var top = r.bottom + 8;
            if (top + ph > window.innerHeight - 8) top = r.top - ph - 8;
            pop.style.left = left + 'px';
            pop.style.top = Math.max(8, top) + 'px';
            pop.style.visibility = '';
        }

        function open(btn) {
            opener = btn;
            veil.hidden = false;
            place();
            btn.setAttribute('aria-expanded', 'true');
            window.setTimeout(function () { input.focus(); input.select(); }, 0);
        }

        function close(refocus) {
            pop.hidden = true;
            veil.hidden = true;
            if (opener) opener.setAttribute('aria-expanded', 'false');
            if (refocus && opener) opener.focus();
        }

        function go(n) {
            n = parseInt(n, 10);
            if (isNaN(n)) { close(false); return; }
            n = Math.max(1, Math.min(n, max));
            if (n === current) { close(false); return; }
            window.location.href = urlFor(tpl, n);
        }

        function makeBtn() {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'page-numbers clm-p3-btn';
            b.textContent = '\u22EF';
            b.title = 'Aller \u00E0 la page \u00B7 Pagination v3';
            b.setAttribute('aria-haspopup', 'dialog');
            b.setAttribute('aria-expanded', 'false');
            b.addEventListener('click', function () {
                if (pop.hidden) open(b); else close(true);
            });
            return b;
        }

        veil.addEventListener('click', function () { close(false); });
        goBtn.addEventListener('click', function () { go(input.value); });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); go(input.value); }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !pop.hidden) close(true);
        });

        var dots = nav.querySelectorAll('.page-numbers.dots');
        if (dots.length) {
            dots.forEach(function (d) { d.replaceWith(makeBtn()); });
        } else {
            nav.insertBefore(makeBtn(), nav.querySelector('.next') || null);
        }
        document.body.appendChild(veil);
        document.body.appendChild(pop);
    }

    function init() {
        document.querySelectorAll('nav.pagination .nav-links').forEach(buildNav);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
    <?php
}
