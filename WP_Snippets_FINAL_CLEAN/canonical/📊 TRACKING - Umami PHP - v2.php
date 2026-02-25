/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/024__id-42__tracker-umami-php.php
 * Display name: TRACKER - Umami (php) 🟢
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 42
 * Online modified: 2025-03-31 08:13:12
 * Online revision: 6
 * Exact duplicate group: non
 * Version family: TRACKER - Umami (php) 🟢 (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/024__id-42__tracker-umami-php.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: umami, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_umami_script
 * Lignes / octets (brut): 21 / 704
 * Hash code normalise (sha256): 0f4908056b0e5b66a81c27195af6f240e175591b8979c5ceeb508297932b475c
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__tracker-umami-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-umami-php__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: tracking-analytics, css-ui, footer-head-injection
 * Dependances probables: Service analytics externe
 * Hooks WP: wp_head
 * Fonctions clefs: inject_umami_script
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 33 / 1251
 * Empreinte code (sha256): 09fb72967de641569481663562ff0655aa214a3b8e2924b0fbeb34cdde2e5051
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__tracker-umami-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-umami-php__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: aucun
 * Domaine: tracking
 * Confiance: high
 * Scores (top): tracking_analytics=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: tracker, analytics, umami
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function inject_umami_script() {
    // Script Umami pour l'analytics
    echo '
    <script defer src="https://cloud.umami.is/script.js" data-website-id="18410156-63da-42cf-b3bb-474c0d61f208"></script>
    ';
}
add_action("wp_head", "inject_umami_script");
