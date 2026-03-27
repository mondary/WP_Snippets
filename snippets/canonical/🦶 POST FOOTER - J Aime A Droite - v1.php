/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/026__id-46__post-j-aime-a-droite.php
 * Display name: POST - J'aime à droite
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 46
 * Online modified: 2025-01-27 08:47:55
 * Online revision: 3
 * Exact duplicate group: non
 * Version family: POST - J'aime à droite (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/026__id-46__post-j-aime-a-droite.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: jetpack, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 32 / 1131
 * Hash code normalise (sha256): 5c692cef99ef93defbd6af433a899134c23ff730c22c68cc9ee77e30ae026ba9
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-j-aime-a-droite__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-j-aime-a-droite__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: aucun
 * Selecteurs / IDs: .jetpack-likes-widget-loaded, .sd-sharing-enabled
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 45 / 1722
 * Empreinte code (sha256): 0dc8d9afb0d18bee0f0ad62bc611074d50ee43d36f537ca88bd5ba0f7f91aad5
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-j-aime-a-droite__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-j-aime-a-droite__v1__src-wp_snippets_online_current.php
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

add_action('wp_footer', function() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sélection des éléments
            const likesWidget = document.querySelector('.jetpack-likes-widget-loaded');
            const sharingElement = document.querySelector('.sd-sharing-enabled');

            if (likesWidget && sharingElement) {
                // Déplacer le widget des likes après l'élément de partage
                sharingElement.parentNode.insertBefore(likesWidget, sharingElement.nextSibling);

                // Ajouter une classe pour le styliser si besoin
                likesWidget.style.marginLeft = '10px';
            }
        });
    </script>
    <?php
});
