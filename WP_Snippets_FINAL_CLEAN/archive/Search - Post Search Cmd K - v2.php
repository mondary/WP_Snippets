/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/047__id-105__post-search-cmd-k.php
 * Display name: POST - Search cmd+k 🟢
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 105
 * Online modified: 2025-05-05 09:02:34
 * Online revision: 6
 * Exact duplicate group: oui (faffa75a7b50…, 2 membres)
 * Canonical exact group ID: 137
 * Version family: DUP POST - Search cmd+k 🟢 (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/047__id-105__post-search-cmd-k.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: search-ui, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 50 / 1830
 * Hash code normalise (sha256): faffa75a7b50285b19c531058dd8f24f4f49d223ace464c00f9d9fd8105427ae
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-search-cmd-k__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-cmd-k__v002.php
 * Resume fonctionnalites: interface de recherche, UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Selecteurs / IDs: #search-drawer .search-field
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 63 / 2380
 * Empreinte code (sha256): 8755e3ae716559379b6e08a2681b9e0025e44b1acac3e933f3e576b935ea773d
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-search-cmd-k__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-cmd-k__v002.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
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
