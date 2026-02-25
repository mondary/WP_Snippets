/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - search auto.php
 * Display name: WP_POST - search auto
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (6e7b785dc157…, 2 membres)
 * Canonical exact group ID: 103
 * Version family: DUP POST - Search auto 🟢 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - search auto.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: search-ui, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 45 / 1845
 * Hash code normalise (sha256): 6e7b785dc157657f6a6d5e1ee5443b40a6617e2f2f9efcfda69bd122a460474f
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-search-auto__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-auto__v001.php
 * Resume fonctionnalites: interface de recherche, UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Selecteurs / IDs: #search-drawer .search-field
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 68 / 2679
 * Empreinte code (sha256): 6005a1578a3c757525773adced757281321ad70e15017615966db6993ec9b3b4
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-search-auto__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-auto__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: search_ui
 * Clusters secondaires: post_footer_ui
 * Domaine: post-front
 * Confiance: high
 * Scores (top): search_ui=10, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: search-ui, search
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let searchAlreadyOpened = false;

        document.addEventListener('keydown', function (e) {
            // Ignore si une touche spéciale est pressée (Ctrl, Alt, Meta)
            if (e.ctrlKey || e.metaKey || e.altKey) return;

            // Ignore si on tape dans un champ (input, textarea, select, contentEditable)
            const tag = document.activeElement.tagName.toLowerCase();
            const isTypingInField = ['input', 'textarea', 'select'].includes(tag) || document.activeElement.isContentEditable;
            if (isTypingInField) return;

            // Ignore si ce n'est pas une lettre, chiffre ou symbole "classique"
            if (e.key.length !== 1) return;

            // Empêche de réouvrir si déjà ouvert
            if (searchAlreadyOpened) return;

            // Ouvre la recherche
            const searchButton = document.querySelector('button.search-toggle-open');
            if (searchButton) {
                searchAlreadyOpened = true;
                searchButton.click();

                setTimeout(() => {
                    const input = document.querySelector('#search-drawer .search-field');
                    if (input) {
                        input.focus();
                        input.value = e.key; // préremplir avec la première lettre tapée
                        // Simule un événement input pour certains thèmes
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }, 300);

                // Remet à zéro après quelques secondes
                setTimeout(() => { searchAlreadyOpened = false; }, 2000);
            }
        });
    });
    </script>
    <?php
});
