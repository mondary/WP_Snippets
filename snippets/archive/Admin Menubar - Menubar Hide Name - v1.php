
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_ADMIN - MenuBar Hide name.php
 * Display name: WP_ADMIN - MenuBar Hide name
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - MenuBar Hide name (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - MenuBar Hide name.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 26 / 724
 * Hash code normalise (sha256): a9176cdef07406d3d7b175553edf0afabbffd5ba043098e575e0eceb8769dc61
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-menubar-hide-name__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-menubar-hide-name__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=12
 * Raisons principales: admin-menubar, menubar
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

archives
 * Source path: archives/WP_ADMIN - MenuBar Hide name.php
 * Display name: WP_ADMIN - MenuBar Hide name
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - MenuBar Hide name (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - MenuBar Hide name.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 26 / 724
 * Hash code normalise (sha256): a9176cdef07406d3d7b175553edf0afabbffd5ba043098e575e0eceb8769dc61
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Hide the site name in WordPress admin bar
 * while keeping the WordPress icon visible
 */

add_action('admin_head', function() {
    echo '<style>
        /* Hide site name text but keep the icon */
        #wpadminbar #wp-admin-bar-site-name .ab-item:first-child {
            font-size: 0;
            padding-right: 0 !important;
        }
        
        /* Keep the dashicon visible */
        #wpadminbar #wp-admin-bar-site-name > .ab-item:before {
            font-size: 20px;
            width: 20px;
        }
        
        /* Hide the site name in the hover menu */
        #wpadminbar .quicklinks li#wp-admin-bar-site-name.hover > .ab-item {
            font-size: 0;
        }
    </style>';
});