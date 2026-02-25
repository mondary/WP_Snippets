/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/043__id-98__admin-list-column-size-posts.php
 * Display name: ADMIN - List column size (POSTS)
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 98
 * Online modified: 2025-03-31 09:15:38
 * Online revision: 16
 * Exact duplicate group: non
 * Version family: ADMIN - List column size (POSTS) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/043__id-98__admin-list-column-size-posts.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, manage_posts_columns, manage_posts_custom_column
 * Fonctions clefs: custom_admin_column_width, add_custom_columns, custom_column_content
 * Lignes / octets (brut): 54 / 1862
 * Hash code normalise (sha256): d83e1eff6ce32d2d7e7ad8acd2ed89ed3cacc008178868005693dc6798bdbd4c
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-list-column-size-posts__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-list-column-size-posts__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, UI frontend (CSS/HTML), automatisation date/programmation, 3 hook(s) WP, 3 fonction(s) clef
 * Features detectees: admin-columns, scheduler-date, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_head, manage_posts_columns, manage_posts_custom_column
 * Fonctions clefs: custom_admin_column_width, add_custom_columns, custom_column_content
 * APIs WP detectees: add_action, add_custom_columns, add_filter, get_post_meta
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 66 / 2481
 * Empreinte code (sha256): 54a520e301871859033e87088b0eaf63ba37a05e4e2b166abdce6a0a69134db1
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-list-column-size-posts__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-list-column-size-posts__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_columns_list
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_columns_list=18, scheduler_posts=8, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-columns, manage_posts_columns, custom_column
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function custom_admin_column_width() {
    echo '<style>
        /* Ajuste la taille des colonnes dans la liste des posts admin */
        .wp-list-table .column-title {
            width: 50% !important;
        }
        .wp-list-table .column-author {
            width: 1% !important;
        }
        .wp-list-table .column-date {
            width: 10% !important;
        }
        .wp-list-table .column-comments {
            width: 1% !important;
        }
        .wp-list-table .column-categories {
            width: 15% !important;
        }
        .wp-list-table .column-iawp_total_views {
            width: 6% !important;
        }
    </style>';
}
add_action('admin_head', 'custom_admin_column_width');

function add_custom_columns($columns) {
    // Ajout de la colonne "Vues" dans la liste des posts
    $columns['iawp_total_views'] = 'Vues';
    return $columns;
}
add_filter('manage_posts_columns', 'add_custom_columns');

function custom_column_content($column_name, $post_id) {
    if ($column_name == 'iawp_total_views') {
        // Récupérer le nombre de vues via le champ personnalisé
        $views = get_post_meta($post_id, 'iawp_total_views', true);
        echo $views ? $views : '0'; // Affiche le nombre de vues, ou 0 si aucune donnée
    }
}
add_action('manage_posts_custom_column', 'custom_column_content', 10, 2);
