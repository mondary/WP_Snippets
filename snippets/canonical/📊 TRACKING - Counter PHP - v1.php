/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/012__id-19__tracker-counter-php.php
 * Display name: TRACKER - Counter (php)
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 19
 * Online modified: 2025-03-31 08:07:30
 * Online revision: 8
 * Exact duplicate group: non
 * Version family: TRACKER - Counter (php) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/012__id-19__tracker-counter-php.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_counter_script
 * Lignes / octets (brut): 24 / 717
 * Hash code normalise (sha256): 96d90c3c58c2420ed046415a662fb3cdcc1646a32f041e0067ae673d521dd0d6
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__tracker-counter-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-counter-php__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: tracking-analytics, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_counter_script
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 36 / 1260
 * Empreinte code (sha256): b43e313e9dc655d2de87af76e6e592b3bee759da47ae71df9e49e24cb383db7d
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__tracker-counter-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-counter-php__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: aucun
 * Domaine: tracking
 * Confiance: high
 * Scores (top): tracking_analytics=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: tracker, analytics, counter
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function inject_counter_script() {
    ?>
    <!-- COUNTER -->
    <script src="https://cdn.counter.dev/script.js" 
        data-id="e6dc104c-1496-4b45-a0e4-180540c9bf66" 
        data-utcoffset="1">
    </script>
    <?php
}
add_action('wp_head', 'inject_counter_script');
