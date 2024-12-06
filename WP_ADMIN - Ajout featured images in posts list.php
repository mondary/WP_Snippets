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
