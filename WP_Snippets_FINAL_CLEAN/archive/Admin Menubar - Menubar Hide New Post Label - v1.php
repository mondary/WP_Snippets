
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - Hide New post label.php
 * Display name: WP_ADMIN - Hide New post label
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (18fa25a00d09…, 2 membres)
 * Canonical exact group ID: 95
 * Version family: DUP ADMIN - MenuBar Hide new post label (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide New post label.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 19 / 497
 * Hash code normalise (sha256): 18fa25a00d0909fb41d93ed08befa475c8bd75a6fe7c3ec0245b0267840034d2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-menubar-hide-new-post-label__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-menubar-hide-new-post-label__v001.php
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

l.php
 * Display name: WP_ADMIN - Hide New post label
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (18fa25a00d09…, 2 membres)
 * Canonical exact group ID: 95
 * Version family: DUP ADMIN - MenuBar Hide new post label (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Hide New post label.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 19 / 497
 * Hash code normalise (sha256): 18fa25a00d0909fb41d93ed08befa475c8bd75a6fe7c3ec0245b0267840034d2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

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