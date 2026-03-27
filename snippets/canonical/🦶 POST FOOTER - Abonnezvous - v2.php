/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/090__id-150__post-abonnezvous.php
 * Display name: POST - AbonnezVous
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 150
 * Online modified: 2026-01-19 09:54:59
 * Online revision: 1
 * Exact duplicate group: oui (83a61eb9dac5…, 2 membres)
 * Canonical exact group ID: 113
 * Version family: DUP POST - AbonnezVous (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/090__id-150__post-abonnezvous.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: jetpack, head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: clm_hide_subscribe_button_on_scroll_styles, clm_hide_subscribe_button_on_scroll_script, handleScroll
 * Lignes / octets (brut): 48 / 1533
 * Hash code normalise (sha256): 83a61eb9dac5f5c9da151f3523858b0f720ce95e3fe6e4b340a471f3d4cdd7d9
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-abonnezvous__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-abonnezvous__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: clm_hide_subscribe_button_on_scroll_styles, clm_hide_subscribe_button_on_scroll_script, handleScroll
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 62 / 2309
 * Empreinte code (sha256): bd56d8bda09af83b26998399f71cbd14b5bee264b1db4c0bb6760a8c169aef2f
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-abonnezvous__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-abonnezvous__v2__src-wp_snippets_online_current.php
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

// Masque le bouton Jetpack "Abonnez-vous" au scroll
function clm_hide_subscribe_button_on_scroll_styles() {
    ?>
    <style>
        .clm-subscribe-hidden {
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
    </style>
    <?php
}
add_action('wp_head', 'clm_hide_subscribe_button_on_scroll_styles');

function clm_hide_subscribe_button_on_scroll_script() {
    ?>
    <script>
        (function() {
            function handleScroll() {
                var btn = document.querySelector('button[name="jetpack_subscriptions_widget"]');
                if (!btn) return;
                if (window.scrollY > 10) {
                    btn.classList.add('clm-subscribe-hidden');
                } else {
                    btn.classList.remove('clm-subscribe-hidden');
                }
            }
            handleScroll();
            window.addEventListener('scroll', handleScroll, { passive: true });
        })();
    </script>
    <?php
}
add_action('wp_footer', 'clm_hide_subscribe_button_on_scroll_script');
