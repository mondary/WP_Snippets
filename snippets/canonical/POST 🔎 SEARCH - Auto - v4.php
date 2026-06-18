<?php
/*
 * Display name: POST 🔎 SEARCH - Auto - v4
 * Scope: global
 */

<?php
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/048__id-106__post-search-auto.php
 * Display name: POST - Search auto 🟢
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 106
 * Online modified: 2025-05-05 09:02:59
 * Online revision: 4
 * Exact duplicate group: oui (6e7b785dc157…, 2 membres)
 * Canonical exact group ID: 103
 * Version family: DUP POST - Search auto 🟢 (1 variantes)
 * Version: v4
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/048__id-106__post-search-auto.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: search-ui, footer-injection, head-injection
 * Changelog v4: Fix bug lettres manquantes - capture et restitution des touches pendant l'ouverture
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 98 / 3415
 * Hash code normalise (sha256): 6e7b785dc157657f6a6d5e1ee5443b40a6617e2f2f9efcfda69bd122a460474f
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

 * Fichier: ACTIVE__global__post-search-auto__v4__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-search-auto__v4__src-wp_snippets_online_current.php
 * Resume fonctionnalites: interface de recherche, UI frontend (CSS/HTML), 2 hook(s) WP
 * Features detectees: search-ui, css-ui, footer-head-injection, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: aucun
 * Selecteurs / IDs: #search-drawer .search-field
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 111 / 4006
 * Empreinte code (sha256): e31d96bbde56efc71a66878915d54364c5a69fe1557472a614184445e366df2e
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

 * Fichier: ACTIVE__global__post-search-auto__v4__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-search-auto__v4__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: search_ui
 * Clusters secondaires: post_footer_ui
 * Domaine: post-front
 * Confiance: high
 * Scores (top): search_ui=10, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: search-ui, search
 * Classification generee le (UTC): 2026-02-24T16:05:10+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

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

