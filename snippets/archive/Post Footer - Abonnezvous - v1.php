/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - bouton abonnezvous.php
 * Display name: WP_POST - bouton abonnezvous
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (83a61eb9dac5…, 2 membres)
 * Canonical exact group ID: 113
 * Version family: DUP POST - AbonnezVous (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - bouton abonnezvous.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: jetpack, head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: clm_hide_subscribe_button_on_scroll_styles, clm_hide_subscribe_button_on_scroll_script, handleScroll
 * Lignes / octets (brut): 35 / 1142
 * Hash code normalise (sha256): 83a61eb9dac5f5c9da151f3523858b0f720ce95e3fe6e4b340a471f3d4cdd7d9
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-abonnezvous__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-abonnezvous__v001.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: clm_hide_subscribe_button_on_scroll_styles, clm_hide_subscribe_button_on_scroll_script, handleScroll
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 58 / 2110
 * Empreinte code (sha256): 27e4da074aeaad20b66f8fdc625cd8935668f4335b642f2d5d6fe8ccbdb98cdb
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-abonnezvous__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-abonnezvous__v001.php
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
