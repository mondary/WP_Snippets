/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/023__id-41__tracker-google-reader-revenue-manager-php.php
 * Display name: TRACKER - Google Reader Revenue manager (php)
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 41
 * Online modified: 2025-03-31 08:07:45
 * Online revision: 9
 * Exact duplicate group: non
 * Version family: TRACKER - Google Reader Revenue manager (php) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/023__id-41__tracker-google-reader-revenue-manager-php.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_swg_basic_script
 * Lignes / octets (brut): 31 / 1055
 * Hash code normalise (sha256): 12d782fba75e3c4f4dff7f5ae493f4816fc3adaca6e62fc03e2019429ea90d34
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__tracker-google-reader-revenue-manager-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__tracker-google-reader-revenue-manager-php__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: tracking-analytics, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_swg_basic_script
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 43 / 1648
 * Empreinte code (sha256): 66126da80860335666bbdbacafe7f85530f97c50b40b1964c552f3ccc3378d22
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__tracker-google-reader-revenue-manager-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__tracker-google-reader-revenue-manager-php__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: aucun
 * Domaine: tracking
 * Confiance: high
 * Scores (top): tracking_analytics=12, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: tracker, analytics
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function inject_swg_basic_script() {
    // Script JS pour l'accès libre
    echo '
    <script async type="application/javascript" src="https://news.google.com/swg/js/v1/swg-basic.js"></script>
    <script>
      (self.SWG_BASIC = self.SWG_BASIC || []).push(basicSubscriptions => {
        basicSubscriptions.init({
          type: "NewsArticle",
          isPartOfType: ["Product"],
          isPartOfProductId: "CAow_ryzDA:openaccess",
          clientOptions: { theme: "light", lang: "fr" },
        });
      });
    </script>
    ';
}
add_action("wp_head", "inject_swg_basic_script");
