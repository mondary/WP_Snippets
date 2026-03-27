/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/096__id-156__tracker-rybbit-php.php
 * Display name: TRACKER - rybbit (php)🟢
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 156
 * Online modified: 2026-02-20 10:28:49
 * Online revision: 18
 * Exact duplicate group: non
 * Version family: TRACKER - rybbit (php)🟢 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/096__id-156__tracker-rybbit-php.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: inject_rybbit_script
 * Lignes / octets (brut): 19 / 683
 * Hash code normalise (sha256): c26f9e404e10f5be5e9796d9cf4eb637ecbf15b9603736896015093d90f80d4d
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__tracker-rybbit-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-rybbit-php__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: tracking-analytics, css-ui, footer-head-injection
 * Dependances probables: Service analytics externe
 * Hooks WP: wp_head
 * Fonctions clefs: inject_rybbit_script
 * APIs WP detectees: is_admin, add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 31 / 1228
 * Empreinte code (sha256): a307a6e531df9e7615b2d529231359b563de2ff542b4285aad6a9171546682e9
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__tracker-rybbit-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__tracker-rybbit-php__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: aucun
 * Domaine: tracking
 * Confiance: high
 * Scores (top): tracking_analytics=18, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: tracker, analytics, rybbit
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function inject_rybbit_script() {
      if (is_admin()) return;
      echo '<script src="https://app.rybbit.io/api/script.js" data-site-id="fd98345ff25f" defer></script>' . "\n";
  }
  add_action('wp_head', 'inject_rybbit_script', 99);