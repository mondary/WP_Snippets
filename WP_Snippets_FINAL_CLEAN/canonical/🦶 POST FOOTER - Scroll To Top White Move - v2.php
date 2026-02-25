/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/025__id-43__post-scroll-to-top-white-move.php
 * Display name: POST : Scroll to top white + move
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 43
 * Online modified: 2025-01-22 13:34:42
 * Online revision: 15
 * Exact duplicate group: oui (bfa7c7566fbd…, 2 membres)
 * Canonical exact group ID: 90
 * Version family: DUP POST : Scroll to top white + move (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/025__id-43__post-scroll-to-top-white-move.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: gtranslate, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: custom_styles_and_js_for_elements
 * Lignes / octets (brut): 34 / 1055
 * Hash code normalise (sha256): bfa7c7566fbdcbba44d539aa449cee4aa2f90b5bb2b60ec465bdd2c2a8882e57
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-scroll-to-top-white-move__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-scroll-to-top-white-move__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: custom_styles_and_js_for_elements
 * Selecteurs / IDs: #kt-scroll-up
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 48 / 1776
 * Empreinte code (sha256): 3c3bdd411668a74986667961f03ccf15be59c0ee07482c40959c13281159f62f
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-scroll-to-top-white-move__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-scroll-to-top-white-move__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: post_footer_ui
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): post_footer_ui=10, frontend_ui_widget=4
 * Raisons principales: footer, scroll to top
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function custom_styles_and_js_for_elements() {
    ?>
    <style>
        .gt_switcher_wrapper,
        #kt-scroll-up {
            background-color: #ffffff !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollButton = document.querySelector('#kt-scroll-up');
            if (scrollButton) {
                scrollButton.style.position = 'fixed';
                scrollButton.style.bottom = '80px';
                scrollButton.style.right = '20px';
            }
        });
    </script>
    <?php
}
add_action('wp_head', 'custom_styles_and_js_for_elements');
