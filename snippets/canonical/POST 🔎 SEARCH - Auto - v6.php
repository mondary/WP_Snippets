<?php
/*
 * Display name: POST 🔎 SEARCH - Auto - v6
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Title: Recherche du site par frappe directe (v6)
 * Description: v6 - frappe d'une lettre/chiffre/symbole hors d'un champ
 *              ouvre la recherche Kadence et place le texte saisi dans le
 *              champ. Initialisation compatible avec LiteSpeed Delay JS :
 *              si DOMContentLoaded est deja passe, le listener est pose
 *              immediatement. Garde-fous pour les champs deja actifs et
 *              le contenu editable ; capture les frappes pendant
 *              l'animation d'ouverture afin de ne perdre aucune lettre.
 *              Code reconstruit proprement : v4 local/online contenait
 *              des blocs de metadata inseres dans le PHP et une seconde
 *              balise <?php, ce qui empechait son execution fiable.
 * Hooks WP: wp_footer
 * Fonctions clefs: aucune (callbacks anonymes)
 */

add_action('wp_footer', function () {
    if (is_admin() || is_feed()) return;
    ?>
<script>
(function () {
    if (window.__clmAutoSearchV6) return;
    window.__clmAutoSearchV6 = true;

    function init() {
        var opening = false;
        var typed = [];
        var captureTimer = null;
        var openTimer = null;

        function clearCapture() {
            window.clearTimeout(captureTimer);
            window.clearTimeout(openTimer);
            opening = false;
            typed = [];
        }

        function activeIsEditable() {
            var active = document.activeElement;
            if (!active) return false;
            var tag = (active.tagName || '').toLowerCase();
            return ['input', 'textarea', 'select'].indexOf(tag) !== -1 ||
                active.isContentEditable === true;
        }

        document.addEventListener('keydown', function (event) {
            if (event.ctrlKey || event.metaKey || event.altKey || event.key.length !== 1) return;

            if (opening) {
                var pendingInput = document.querySelector('#search-drawer .search-field');
                if (pendingInput && document.activeElement === pendingInput) {
                    pendingInput.value = typed.join('') + pendingInput.value;
                    pendingInput.setSelectionRange(pendingInput.value.length, pendingInput.value.length);
                    pendingInput.dispatchEvent(new Event('input', { bubbles: true }));
                    window.clearTimeout(captureTimer);
                    window.clearTimeout(openTimer);
                    opening = false;
                    typed = [];
                    return;
                }
                event.preventDefault();
                typed.push(event.key);
                window.clearTimeout(captureTimer);
                captureTimer = window.setTimeout(clearCapture, 2000);
                return;
            }

            var input = document.querySelector('#search-drawer .search-field');
            if (input && document.activeElement === input) return;
            if (activeIsEditable()) return;

            var toggle = document.querySelector('button.search-toggle-open');
            if (!toggle || !input) return;

            event.preventDefault();
            opening = true;
            typed = [event.key];
            toggle.click();

            function deliverWhenReady(attempt) {
                if (!opening) return;
                var field = document.querySelector('#search-drawer .search-field');
                if (field && document.body.classList.contains('showing-popup-drawer-from-full')) {
                    field.focus();
                    field.value = typed.join('');
                    field.setSelectionRange(field.value.length, field.value.length);
                    field.dispatchEvent(new Event('input', { bubbles: true }));
                    clearCapture();
                    return;
                }
                if (attempt < 20) {
                    openTimer = window.setTimeout(function () {
                        deliverWhenReady(attempt + 1);
                    }, 50);
                } else {
                    clearCapture();
                }
            }

            deliverWhenReady(0);
            captureTimer = window.setTimeout(clearCapture, 2000);
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>
    <?php
}, 99);
