<?php
/*
 * Display name: FRONTEND 📄 PAGINATION - Jump Select - v1
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Title: Pagination « aller a la page » (v1)
 * Description: v1 - un menu deroulant « Page N » est ajoute dans la
 *              pagination Kadence (home, archives, recherche) : avec ~298
 *              pages, on peut enfin sauter a n'importe laquelle d'un seul
 *              geste au lieu des seuls liens 1 2 3 ... 298.
 *              - JS vanilla injecte en wp_footer, sans jQuery ni requete
 *                serveur supplementaire
 *              - le <select> porte la classe .page-numbers de Kadence :
 *                style pill du theme herite automatiquement (bord, rayon,
 *                survol)
 *              - URL cible reconstruite depuis un lien /page/N/ existant
 *                (jolis permaliens et ?paged=N en repli) ; la page 1
 *                pointe vers la base sans segment /page/N/
 *              - compatible cache LiteSpeed : le HTML reste identique, le
 *                select se construit cote client
 * Hooks WP: wp_footer
 * Fonctions clefs: clm_jump_select_footer
 */

add_action('wp_footer', 'clm_jump_select_footer', 99);
function clm_jump_select_footer() {
    if (is_admin() || is_feed()) return;
    ?>
<style>
.clm-jump-wrap{position:relative;display:inline-flex;align-items:center;margin-right:.5em}
.clm-jump-wrap::after{content:"";position:absolute;right:.62em;pointer-events:none;width:0;height:0;border-left:.3em solid transparent;border-right:.3em solid transparent;border-top:.42em solid currentColor;transform:translateY(-45%);opacity:.8}
select.page-numbers.clm-jump{appearance:none;-webkit-appearance:none;-moz-appearance:none;cursor:pointer;background:transparent;color:inherit;font:inherit;margin:0;padding:0 .35em;padding-right:2em;height:2.25em;min-width:6.5em;text-align:center;text-align-last:center;line-height:normal;vertical-align:top;border-radius:.25rem}
select.page-numbers.clm-jump:focus-visible{outline:2px solid currentColor;outline-offset:2px}
@media (max-width:480px){select.page-numbers.clm-jump{min-width:5.5em}}
</style>
<script>
(function () {
    if (window.__clmJumpSelect) return;
    window.__clmJumpSelect = true;

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
        if (!nav || nav.querySelector('.clm-jump')) return;
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

        var wrap = document.createElement('span');
        wrap.className = 'clm-jump-wrap';

        var sel = document.createElement('select');
        sel.className = 'page-numbers clm-jump';
        sel.setAttribute('aria-label', 'Aller à la page');
        sel.title = 'Aller à la page · Pagination v1';

        var frag = document.createDocumentFragment();
        for (var i = 1; i <= max; i++) {
            var opt = document.createElement('option');
            opt.value = String(i);
            opt.textContent = 'Page ' + i;
            if (i === current) opt.selected = true;
            frag.appendChild(opt);
        }
        sel.appendChild(frag);

        sel.addEventListener('change', function () {
            var n = parseInt(this.value, 10);
            if (!n || n === current) return;
            window.location.href = urlFor(tpl, n);
        });

        wrap.appendChild(sel);
        var next = nav.querySelector('.next');
        nav.insertBefore(wrap, next || null);
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
