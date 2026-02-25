/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/021__id-39__post-gtranslate-white.php
 * Display name: POST - Gtranslate white
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 39
 * Online modified: 2026-01-19 09:42:55
 * Online revision: 10
 * Exact duplicate group: oui (08dc98add042…, 2 membres)
 * Canonical exact group ID: 88
 * Version family: DUP POST - Gtranslate white (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/021__id-39__post-gtranslate-white.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: gtranslate, head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: custom_styles_for_elements, gt_hide_on_scroll_script, handleScroll
 * Lignes / octets (brut): 53 / 1713
 * Hash code normalise (sha256): 08dc98add042b46e473d4d2258175dc2abcf3161b305358c4fe7811720efc7b0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-gtranslate-white__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-gtranslate-white__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: custom_styles_for_elements, gt_hide_on_scroll_script, handleScroll
 * Selecteurs / IDs: .gt_switcher_wrapper
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 66 / 2368
 * Empreinte code (sha256): cdaa4820fe60e8cbd3cc562a49c9bec79d9a2dc5eef58ce5b44b6e5047d6af1b
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-gtranslate-white__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-gtranslate-white__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
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
