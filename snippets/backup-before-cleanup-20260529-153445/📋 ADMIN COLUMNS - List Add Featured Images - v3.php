/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/admin/003__id-7__admin-list-add-featured-images.php
 * Display name: ADMIN - List Add featured images
 * Scope: admin
 * Online snippet: oui
 * Online active: oui
 * Online ID: 7
 * Online modified: 2025-03-07 14:43:49
 * Online revision: 5
 * Exact duplicate group: oui (16c462daa399…, 3 membres)
 * Canonical exact group ID: 76
 * Version family: DUP ADMIN - List Add featured images (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets_Online_Current/active/admin/003__id-7__admin-list-add-featured-images.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: search-ui, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: manage_posts_columns, manage_posts_custom_column, admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 56 / 1875
 * Hash code normalise (sha256): 16c462daa39921e3f29ef02a4dc7160704e623afd4f07b714fb28e68810936b3
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__admin__admin-list-add-featured-images__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-list-add-featured-images__v3__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, interface de recherche, UI frontend (CSS/HTML), 3 hook(s) WP
 * Features detectees: admin-columns, search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: manage_posts_columns, manage_posts_custom_column, admin_head
 * Fonctions clefs: aucun
 * APIs WP detectees: add_filter, add_action, the_post_thumbnail
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 69 / 2477
 * Empreinte code (sha256): 9cd9feb843dcda11af2a2359fc35096fc3c7a7a192d03ddbc52f50bbe27dfab0
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__admin__admin-list-add-featured-images__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-list-add-featured-images__v3__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_columns_list
 * Clusters secondaires: media_images, search_ui
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_columns_list=18, media_images=12, search_ui=10, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-columns, manage_posts_columns, custom_column
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Ajouter une colonne 'featured_image' et ajuster sa largeur
add_filter( 'manage_posts_columns', function ( $columns ) {
    $move_before = 'title'; 
    $move_before_key = array_search( $move_before, array_keys( $columns ), true );

    $first_columns = array_slice( $columns, 0, $move_before_key );
    $last_columns  = array_slice( $columns, $move_before_key );

    return array_merge(
        $first_columns,
        array(
            'featured_image' => __( 'Featured Image' ),
        ),
        $last_columns
    );
} );

add_action( 'manage_posts_custom_column', function ( $column ) {
    if ( 'featured_image' === $column ) {
        echo '<div style="max-width: 150px; overflow: hidden; text-align: center;">';
        if ( has_post_thumbnail() ) {
            the_post_thumbnail( array( 200, 150 ) ); // Adapter la taille ici si besoin
        } else {
            echo __( 'No image' );
        }
        echo '</div>';
    }
} );

// Ajuster la largeur de la colonne via CSS dans l'admin
add_action( 'admin_head', function () {
    echo '<style>
        .column-featured_image {
            width: 160px !important; /* Ajuste cette valeur si besoin */
            text-align: center;
        }
        .column-featured_image img {
            max-width: 100%;
            height: auto;
        }
    </style>';
} );
