/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/014__id-21__tracker-datapulse-php.php
 * Display name: TRACKER - Datapulse (php)
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 21
 * Online modified: 2025-03-31 08:12:44
 * Online revision: 5
 * Exact duplicate group: non
 * Version family: TRACKER - Datapulse (php) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/014__id-21__tracker-datapulse-php.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_datapulse_script
 * Lignes / octets (brut): 26 / 820
 * Hash code normalise (sha256): d1a726cb80173b66dd23cce9fd4053b7d215625fcef908fa0e63d62186448cb8
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__tracker-datapulse-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-datapulse-php__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: tracking-analytics, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_datapulse_script
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 38 / 1371
 * Empreinte code (sha256): b59563beef6aa8eb1afa25f26259330bd3b1e1c72360550c94f48fbce59fa521
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__tracker-datapulse-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-datapulse-php__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: aucun
 * Domaine: tracking
 * Confiance: high
 * Scores (top): tracking_analytics=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: tracker, analytics, datapulse
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function inject_datapulse_script() {
    ?>
    <!-- DataPulse -->
    <script defer type="text/javascript" 
        src="https://datapulse.app/datapulse.min.js" 
        id="datapulse" 
        data-endpoint="https://datapulse.app/api/v1/event" 
        data-workspace="clkgtxn234ue8e937pmbnnpay">
    </script>
    <?php
}
add_action('wp_head', 'inject_datapulse_script');
