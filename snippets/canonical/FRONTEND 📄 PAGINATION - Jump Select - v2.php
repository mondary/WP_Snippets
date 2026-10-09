<?php
/*
 * Display name: FRONTEND 📄 PAGINATION - Jump Select - v2
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Title: Pagination « aller a la page » (v2)
 * Description: v2 - le jumper remplace les points de suspension de la
 *              pagination Kadence : un pill « ... » entre les premiers et
 *              les derniers numeros, exactement la ou le trou se trouve.
 *              Au clic, un popover compact propose :
 *              - une saisie « Page __ / N » validee par Entree ou le
 *                bouton « Aller » (clamp automatique 1..N)
 *              - des raccourcis -50 / -10 / +10 / +50 pour explorer
 *              Choix UX : ni drop list pure (scroller 298 items dans une
 *              dropdown native est penible sur desktop), ni saisie pure
 *              (clavier mobile penible, aucune exploration) : l'hybride
 *              sert la cible connue ET l'exploration en un clic.
 *              - pill <button> classe .page-numbers : style Kadence herite
 *              - popover position:fixed (jamais clippe par un overflow),
 *                fermable par ESC / clic exterieur, focus dans l'input
 *              - URL reconstruite depuis un lien /page/N/ existant,
 *                page 1 = base sans segment /page/
 *              - JS vanilla sans jQuery, HTML statique inchange :
 *                compatible cache LiteSpeed
 * Hooks WP: wp_footer
 * Fonctions clefs: clm_jump_select_footer_v2 (suffixee : la v1 definissait
 *                deja ce nom, l'activation de la v2 en parallele declenchait
 *                un « Cannot redeclare function » fatal via le test
 *                d'execution du plugin a l'activation)
 */

add_action('wp_footer', 'clm_jump_select_footer_v2', 99);
function clm_jump_select_footer_v2() {
    if (is_admin() || is_feed()) return;
    ?>
<style>
button.page-numbers.clm-p2-btn{cursor:pointer;background:transparent;font:inherit;color:inherit}
button.page-numbers.clm-p2-btn:focus-visible{outline:2px solid currentColor;outline-offset:2px}
.pagination button.clm-p2-btn:hover{background:var(--global-palette-btn-bg,#222);color:var(--global-palette-btn,#fff)}
.clm-p2-veil{position:fixed;inset:0;z-index:99998}
.clm-p2-pop{position:fixed;z-index:99999;width:238px;box-sizing:border-box;padding:12px;background:var(--global-palette9,#fff);border:1px solid var(--global-palette6,#e0e0e0);border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.14);font-size:14px;line-height:1.4}
.clm-p2-pop[hidden]{display:none}
.clm-p2-title{display:block;font-size:11px;letter-spacing:.05em;text-transform:uppercase;opacity:.55;margin-bottom:8px}
.clm-p2-row{display:flex;gap:6px;align-items:center}
.clm-p2-input{width:64px;padding:.35em .5em;border:1px solid var(--global-palette6,#ccc);border-radius:6px;font:inherit;text-align:center;background:transparent;color:inherit}
.clm-p2-input:focus{outline:2px solid currentColor;outline-offset:1px}
.clm-p2-total{opacity:.6;white-space:nowrap;font-size:13px}
.clm-p2-go{margin-left:auto;padding:.4em .9em;border:0;border-radius:6px;background:var(--global-palette-btn-bg,#222);color:var(--global-palette-btn,#fff);font:inherit;cursor:pointer}
.clm-p2-go:hover{opacity:.88}
.clm-p2-go:focus-visible{outline:2px solid currentColor;outline-offset:2px}
.clm-p2-quick{display:flex;gap:6px;margin-top:10px}
.clm-p2-quick button{flex:1;padding:.32em 0;border:1px solid var(--global-palette6,#ccc);border-radius:6px;background:transparent;color:inherit;font:inherit;font-size:13px;cursor:pointer}
.clm-p2-quick button:hover{background:rgba(0,0,0,.05)}
.clm-p2-quick button:focus-visible{outline:2px solid currentColor;outline-offset:1px}
@media (max-width:480px){.clm-p2-pop{width:216px}}
</style>
<script>
(function () {
    if (window.__clmJump2) return;
    window.__clmJump2 = true;

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
        if (!nav || nav.querySelector('.clm-p2-btn')) return;
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

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'page-numbers clm-p2-btn';
        btn.textContent = '\u22EF';
        btn.title = 'Aller \u00E0 la page \u00B7 Pagination v2';
        btn.setAttribute('aria-haspopup', 'dialog');
        btn.setAttribute('aria-expanded', 'false');

        var veil = document.createElement('div');
        veil.className = 'clm-p2-veil';
        veil.hidden = true;

        var pop = document.createElement('div');
        pop.className = 'clm-p2-pop';
        pop.setAttribute('role', 'dialog');
        pop.setAttribute('aria-label', 'Aller \u00E0 la page');
        pop.hidden = true;

        var title = document.createElement('span');
        title.className = 'clm-p2-title';
        title.textContent = 'Aller \u00E0 la page';

        var row = document.createElement('div');
        row.className = 'clm-p2-row';
        var input = document.createElement('input');
        input.type = 'text';
        input.inputMode = 'numeric';
        input.pattern = '[0-9]*';
        input.className = 'clm-p2-input';
        input.placeholder = String(current);
        input.setAttribute('aria-label', 'Num\u00E9ro de page');
        var total = document.createElement('span');
        total.className = 'clm-p2-total';
        total.textContent = '/ ' + max;
        var goBtn = document.createElement('button');
        goBtn.type = 'button';
        goBtn.className = 'clm-p2-go';
        goBtn.textContent = 'Aller';
        row.appendChild(input);
        row.appendChild(total);
        row.appendChild(goBtn);

        var quick = document.createElement('div');
        quick.className = 'clm-p2-quick';
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

        function place() {
            pop.style.visibility = 'hidden';
            pop.hidden = false;
            var r = btn.getBoundingClientRect();
            var pw = pop.offsetWidth, ph = pop.offsetHeight;
            var left = Math.max(8, Math.min(r.left + r.width / 2 - pw / 2, window.innerWidth - pw - 8));
            var top = r.bottom + 8;
            if (top + ph > window.innerHeight - 8) top = r.top - ph - 8;
            pop.style.left = left + 'px';
            pop.style.top = Math.max(8, top) + 'px';
            pop.style.visibility = '';
        }

        function open() {
            veil.hidden = false;
            place();
            btn.setAttribute('aria-expanded', 'true');
            window.setTimeout(function () { input.focus(); input.select(); }, 0);
        }

        function close(refocus) {
            pop.hidden = true;
            veil.hidden = true;
            btn.setAttribute('aria-expanded', 'false');
            if (refocus) btn.focus();
        }

        function go(n) {
            n = parseInt(n, 10);
            if (isNaN(n)) { close(false); return; }
            n = Math.max(1, Math.min(n, max));
            if (n === current) { close(false); return; }
            window.location.href = urlFor(tpl, n);
        }

        btn.addEventListener('click', function () {
            if (pop.hidden) open(); else close(true);
        });
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
            dots[dots.length - 1].replaceWith(btn);
        } else {
            nav.insertBefore(btn, nav.querySelector('.next') || null);
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
