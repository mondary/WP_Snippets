/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - Granslate White.php
 * Display name: WP_POST - Granslate White
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (08dc98add042…, 2 membres)
 * Canonical exact group ID: 88
 * Version family: DUP POST - Gtranslate white (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - Granslate White.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: gtranslate, head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: custom_styles_for_elements, gt_hide_on_scroll_script, handleScroll
 * Lignes / octets (brut): 39 / 1216
 * Hash code normalise (sha256): 08dc98add042b46e473d4d2258175dc2abcf3161b305358c4fe7811720efc7b0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-gtranslate-white__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-gtranslate-white__v001.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: custom_styles_for_elements, gt_hide_on_scroll_script, handleScroll
 * Selecteurs / IDs: .gt_switcher_wrapper
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 62 / 2147
 * Empreinte code (sha256): b9dd8d0aa99d280a6881faa974ddaa66e713bd4f33ba7e7bab640f11200cd9e6
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-gtranslate-white__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-gtranslate-white__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: post_footer_ui
 * Clusters secondaires: frontend_ui_widget
 * Domaine: post-front
 * Confiance: low
 * Scores (top): post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: footer
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Ajout d'un script pour changer le background en blanc pour .gt_switcher_wrapper et #kt-scroll-up
function custom_styles_for_elements() {
    ?>
    <style>
        .gt_switcher_wrapper {
            background-color: #ffffff !important;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .gt_switcher_wrapper.gt-hidden {
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
        }
    </style>
    <?php
}
add_action('wp_head', 'custom_styles_for_elements');

// Masque le switcher GTranslate au scroll
function gt_hide_on_scroll_script() {
    ?>
    <script>
        (function() {
            function handleScroll() {
                var el = document.querySelector('.gt_switcher_wrapper');
                if (!el) return;
                if (window.scrollY > 10) {
                    el.classList.add('gt-hidden');
                } else {
                    el.classList.remove('gt-hidden');
                }
            }
            handleScroll();
            window.addEventListener('scroll', handleScroll, { passive: true });
        })();
    </script>
    <?php
}
add_action('wp_footer', 'gt_hide_on_scroll_script');
