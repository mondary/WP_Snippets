/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - Scroll to top white + move.php
 * Display name: WP_POST - Scroll to top white + move
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (bfa7c7566fbd…, 2 membres)
 * Canonical exact group ID: 90
 * Version family: DUP POST : Scroll to top white + move (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - Scroll to top white + move.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: gtranslate, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: custom_styles_and_js_for_elements
 * Lignes / octets (brut): 21 / 650
 * Hash code normalise (sha256): bfa7c7566fbdcbba44d539aa449cee4aa2f90b5bb2b60ec465bdd2c2a8882e57
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-scroll-to-top-white-move__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-scroll-to-top-white-move__v001.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: custom_styles_and_js_for_elements
 * Selecteurs / IDs: #kt-scroll-up
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 44 / 1562
 * Empreinte code (sha256): 39bbc1102a853f6ff1bfc5cbdac7a29f6ad6ab6e0fd39279192e5bfe259c041c
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-scroll-to-top-white-move__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-scroll-to-top-white-move__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
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

