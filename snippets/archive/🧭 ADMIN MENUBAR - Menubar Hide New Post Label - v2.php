/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/032__id-60__admin-menubar-hide-new-post-label.php
 * Display name: ADMIN - MenuBar Hide new post label
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 60
 * Online modified: 2025-03-07 13:59:03
 * Online revision: 5
 * Exact duplicate group: oui (18fa25a00d09…, 2 membres)
 * Canonical exact group ID: 95
 * Version family: DUP ADMIN - MenuBar Hide new post label (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/032__id-60__admin-menubar-hide-new-post-label.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 32 / 1035
 * Hash code normalise (sha256): 18fa25a00d0909fb41d93ed08befa475c8bd75a6fe7c3ec0245b0267840034d2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-menubar-hide-new-post-label__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-menubar-hide-new-post-label__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: admin-menubar, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 45 / 1592
 * Empreinte code (sha256): 9afea0a4bbb7cbae204d1cc6e89e30c4cc802e735bb36a604bda3e13b0739fa5
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-menubar-hide-new-post-label__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-menubar-hide-new-post-label__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=12, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-menubar, menubar
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Hide the 'Create' text label next to the '+' icon in WordPress admin bar
 * while keeping the icon visible
 */

add_action('admin_head', function() {
    echo '<style>
        /* Hide text but keep the icon */
        #wp-admin-bar-new-content .ab-label {
            display: none !important;
        }
        
        /* Adjust spacing for better visual appearance */
        #wp-admin-bar-new-content .ab-icon {
            margin-right: 0 !important;
        }
    </style>';
});