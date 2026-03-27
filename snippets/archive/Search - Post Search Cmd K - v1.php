/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - search cmd+k.php
 * Display name: WP_POST - search cmd+k
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (faffa75a7b50…, 2 membres)
 * Canonical exact group ID: 137
 * Version family: DUP POST - Search cmd+k 🟢 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - search cmd+k.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: search-ui, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 36 / 1338
 * Hash code normalise (sha256): faffa75a7b50285b19c531058dd8f24f4f49d223ace464c00f9d9fd8105427ae
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-search-cmd-k__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-cmd-k__v001.php
 * Resume fonctionnalites: interface de recherche, UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Selecteurs / IDs: #search-drawer .search-field
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 59 / 2176
 * Empreinte code (sha256): faa860add153611b696c92fe8947bdb7c9433c776e248ae89822350620855e5d
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-search-cmd-k__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-cmd-k__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: search_ui
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): search_ui=15, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: search-ui, search, cmd+k
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('keydown', function (e) {
            const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
            const isKPressed = e.key === 'k' || e.key === 'K';
            const isCmdK = isMac && e.metaKey && isKPressed;
            const isCtrlK = !isMac && e.ctrlKey && isKPressed;

            if (isCmdK || isCtrlK) {
                e.preventDefault();

                // Bouton qui ouvre le tiroir de recherche
                const searchButton = document.querySelector('button.search-toggle-open');

                if (searchButton) {
                    searchButton.click();

                    // Focus automatique après un petit délai pour laisser le tiroir s’ouvrir
                    setTimeout(() => {
                        const input = document.querySelector('#search-drawer .search-field');
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    }, 300);
                } else {
                    console.warn("Bouton de recherche introuvable.");
                }
            }
        });
    });
    </script>
    <?php
});
