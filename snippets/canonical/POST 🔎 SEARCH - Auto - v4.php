<?php
/*
 * POST 🔎 SEARCH - Auto - v4
 * Recherche clavier v4 — remplacée par la v6 (#437).
 */

/* STYLES CUSTOM (fusionnés depuis Search Custom v2) */
add_action('wp_head', function () {
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
    /* Champ de recherche géant, aligné à gauche */
    #search-drawer .search-field {
        all: unset !important;
        font-size: 15vw !important; /* Taille très grande */
        color: #ffffff !important;
        width: 100% !important;
        max-width: none !important;
    }
    </style>
    <?php
});

/* SCRIPT AUTO (v4 - capture et restitution des touches) */
add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let isOpening = false;
        let capturedKeys = [];
        let captureTimer = null;

        document.addEventListener('keydown', function (e) {
            // Ignore si une touche spéciale est pressée (Ctrl, Alt, Meta)
            if (e.ctrlKey || e.metaKey || e.altKey) return;

            // Vérifie si on tape DANS le champ de recherche déjà ouvert
            const searchInput = document.querySelector('#search-drawer .search-field');
            if (searchInput && document.activeElement === searchInput) {
                return; // Laisse la frappe normale continuer dans le champ
            }

            // Ignore si on tape dans un autre champ (input, textarea, select, contentEditable)
            const tag = document.activeElement.tagName.toLowerCase();
            const isTypingInField = ['input', 'textarea', 'select'].includes(tag) || document.activeElement.isContentEditable;
            if (isTypingInField) return;

            // Ignore si ce n'est pas une lettre, chiffre ou symbole "classique"
            if (e.key.length !== 1) return;

            // Si on est en train d'ouvrir la recherche, capture les touches
            if (isOpening) {
                e.preventDefault(); // Empêche le comportement par défaut
                capturedKeys.push(e.key);

                // Reset le timer de capture
                if (captureTimer) clearTimeout(captureTimer);
                captureTimer = setTimeout(() => {
                    capturedKeys = [];
                    isOpening = false;
                }, 2000);
                return;
            }

            // Ouvre la recherche et commence à capturer
            const searchButton = document.querySelector('button.search-toggle-open');
            if (searchButton) {
                isOpening = true;
                capturedKeys = [e.key]; // Capture la première lettre
                searchButton.click();

                setTimeout(() => {
                    const input = document.querySelector('#search-drawer .search-field');
                    if (input) {
                        // Restitue toutes les lettres capturées
                        input.value = capturedKeys.join('');
                        input.focus();
                        input.dispatchEvent(new Event('input', { bubbles: true }));

                        // Reset après ouverture réussie
                        isOpening = false;
                        capturedKeys = [];
                    } else {
                        // Fallback : si le champ n'existe pas
                        isOpening = false;
                        capturedKeys = [];
                    }
                }, 300);

                // Timeout de sécurité : reset si jamais le focus échoue
                setTimeout(() => {
                    isOpening = false;
                    capturedKeys = [];
                }, 1000);
            }
        });
    });
    </script>
    <?php
});
