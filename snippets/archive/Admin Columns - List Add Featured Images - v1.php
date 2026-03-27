/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: A TRIER
 * Source path: A TRIER/WP_ADMIN Ajout de la featured image dans la liste des posts/ADMIN - Ajout featured images in posts list.php
 * Display name: ADMIN - Ajout featured images in posts list
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (16c462daa399…, 3 membres)
 * Canonical exact group ID: 76
 * Version family: DUP ADMIN - List Add featured images (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_ADMIN Ajout de la featured image dans la liste des posts/ADMIN - Ajout featured images in posts list.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: search-ui, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: manage_posts_columns, manage_posts_custom_column, admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 42 / 1333
 * Hash code normalise (sha256): 16c462daa39921e3f29ef02a4dc7160704e623afd4f07b714fb28e68810936b3
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: admin-list-add-featured-images__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-list-add-featured-images__v001.php
 * Resume fonctionnalites: customisation interface admin, interface de recherche, UI frontend (CSS/HTML), 3 hook(s) WP
 * Features detectees: admin-columns, search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: manage_posts_columns, manage_posts_custom_column, admin_head
 * Fonctions clefs: aucun
 * APIs WP detectees: add_filter, add_action, the_post_thumbnail
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 65 / 2394
 * Empreinte code (sha256): 84bdb41fea8bb8ca12e056fa705d7ff3cc791bfbf2273aa2b0855235b794dd82
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-list-add-featured-images__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-list-add-featured-images__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_columns_list
 * Clusters secondaires: media_images
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_columns_list=24, media_images=12, search_ui=10, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-columns, manage_posts_columns, custom_column, featured images in posts list
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
