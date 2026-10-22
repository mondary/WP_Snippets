<?php
/*
 * POST 🔎 SEARCH - Auto - v7
 * Frappe directe sur le site : recherche Kadence plein écran avec champ géant.
 */

if (!defined('ABSPATH')) exit;

/*
 * Title: Recherche du site par frappe directe (v7)
 * Description: v7 - restaure le style plein écran de la v4 (champ geant
 *              15vw, texte blanc, aligne a gauche) perdu lors de la
 *              reconstruction des v5/v6. Conserve la capture des frappes
 *              de la v6 et corrige la course au demarrage : sur une page
 *              non cachee, LiteSpeed charge le JS du theme apres la
 *              premiere frappe ; v7 reessaie l'ouverture tant que le
 *              tiroir Kadence n'est pas pret, puis restitue toutes les
 *              lettres saisies. Le script est marque data-no-defer pour
 *              ne pas etre differe par LiteSpeed.
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: aucune (callbacks anonymes)
 */

/* STYLES CUSTOM (restaures depuis la v4 / Search Custom v2) */
add_action('wp_head', function () {
    if (is_admin() || is_feed()) return;
    ?>
<style>
/* Nettoyage des couches internes */
#search-drawer .drawer-inner-wrap,
#search-drawer .search-form,
#search-drawer .wp-block-search__inside-wrapper {
    all: unset !important;
    display: flex !important;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
}
/* Champ de recherche geant, aligne a gauche */
#search-drawer .search-field {
    all: unset !important;
    font-size: 15vw !important; /* Taille tres grande */
    color: #ffffff !important;
    width: 100% !important;
    max-width: none !important;
}
</style>
    <?php
});

/* SCRIPT AUTO (v7 - ouverture fiable + capture et restitution des touches) */
add_action('wp_footer', function () {
    if (is_admin() || is_feed()) return;
    ?>
<script data-no-defer="1" data-no-optimize="1">
(function () {
    if (window.__clmAutoSearchV7) return;
    window.__clmAutoSearchV7 = true;

    function init() {
        var opening = false;
        var typed = [];
        var retryTimer = null;
        var giveUpTimer = null;
        var attempts = 0;

        function getDrawer() {
            return document.querySelector('#search-drawer');
        }

        function getField() {
            return document.querySelector('#search-drawer .search-field');
        }

        function isOpen() {
            var drawer = getDrawer();
            return !!(drawer && drawer.classList.contains('show-drawer')) ||
                document.body.classList.contains('showing-popup-drawer-from-full');
        }

        function activeIsEditable() {
            var active = document.activeElement;
            if (!active) return false;
            var tag = (active.tagName || '').toLowerCase();
            return ['input', 'textarea', 'select'].indexOf(tag) !== -1 ||
                active.isContentEditable === true;
        }

        function reset() {
            window.clearTimeout(retryTimer);
            window.clearTimeout(giveUpTimer);
            retryTimer = null;
            opening = false;
            attempts = 0;
            typed = [];
        }

        function fill() {
            if (!opening || !isOpen()) return false;
            var input = getField();
            if (!input) return false;
            input.focus();
            input.value = typed.join('');
            input.setSelectionRange(input.value.length, input.value.length);
            input.dispatchEvent(new Event('input', { bubbles: true }));
            reset();
            return true;
        }

        function tryOpen() {
            retryTimer = null;
            if (!opening) return;
            var toggle = document.querySelector('button.search-toggle-open');
            if (!toggle) {
                reset();
                return;
            }

            if (isOpen()) {
                fill();
                return;
            }

            if (window.kadence && typeof window.kadence.toggleDrawer === 'function') {
                window.kadence.toggleDrawer(toggle);
            } else {
                toggle.click();
            }
            attempts++;

            if (isOpen()) {
                fill();
                return;
            }

            if (attempts < 80) {
                retryTimer = window.setTimeout(tryOpen, 50);
            } else {
                reset();
            }
        }

        document.addEventListener('keydown', function (event) {
            if (event.ctrlKey || event.metaKey || event.altKey || event.key.length !== 1) return;

            var input = getField();
            var fieldActive = !!(input && document.activeElement === input);

            if (opening) {
                event.preventDefault();
                typed.push(event.key);
                if (fieldActive && isOpen()) {
                    input.value = typed.join('') + input.value;
                    input.setSelectionRange(input.value.length, input.value.length);
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    reset();
                }
                return;
            }

            if (fieldActive) return;
            if (activeIsEditable()) return;

            var toggle = document.querySelector('button.search-toggle-open');
            if (!toggle || !input) return;

            event.preventDefault();
            opening = true;
            attempts = 0;
            typed = [event.key];
            giveUpTimer = window.setTimeout(reset, 5000);
            tryOpen();
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
});
