defined( 'ABSPATH' ) || die( 'Cannot access pages directly.' );

// Fonction pour récupérer les catégories incluses dans les options
function aet_included_categories() {
    return get_option( 'aet_included_categories' ) ? array_map(
        function( $cat_id ) {
            return (int) $cat_id;
        },
        get_option( 'aet_included_categories' )
    ) : array();
}

// Vérifie si les conditions pour l'examen de l'article sont remplies
function aet_halt() {
    $examine_post = get_option( 'aet_examine_post_title' ) || get_option( 'aet_examine_post_content' );
    return ! $examine_post || ( get_option( 'aet_filter_by_category' ) && empty( aet_included_categories() ) );
}

// Fonction pour ajouter automatiquement les tags existants
function aet_tagging( $the_post_id ) {
    $post = get_post( $the_post_id );

    if ( 'post' === $post->post_type ) {
        // Récupérer les catégories de l'article
        $post_categories = ( get_the_terms( $the_post_id, 'category' ) ) ? wp_list_pluck( get_the_terms( $the_post_id, 'category' ), 'term_id' ) : array();

        // Récupérer le titre et le contenu de l'article
        if ( get_option( 'aet_examine_post_title' ) ) {
            $the_post_title = wp_strip_all_tags( get_post_field( 'post_title', $the_post_id ) );
        }

        if ( get_option( 'aet_examine_post_content' ) ) {
            $the_post_content = wp_strip_all_tags( get_post_field( 'post_content', $the_post_id ) );
        }

        // Récupérer tous les tags existants
        $existing_tags = get_terms(
            array(
                'taxonomy' => 'post_tag',
                'hide_empty' => false,
            )
        );

        if ( $existing_tags && ( ! get_option( 'aet_filter_by_category' ) || array_intersect( $post_categories, aet_included_categories() ) ) ) {
            // Supprime les tags manuellement ajoutés si nécessaire
            if ( get_option( 'aet_block_manually_added_tags' ) ) {
                wp_delete_object_term_relationships( $the_post_id, 'post_tag' );
            }

            // Parcours chaque tag existant et vérifie s'il est mentionné dans le titre ou le contenu
            foreach ( $existing_tags as $newtag ) {
                $pattern = preg_quote( $newtag->name, '/' );
                $pattern = '/\b' . $pattern . '\b/ui';

                // Vérification dans le titre
                if ( get_option( 'aet_examine_post_title' ) && preg_match( $pattern, $the_post_title ) ) {
                    wp_set_post_terms( $the_post_id, $newtag->name, 'post_tag', true );
                }

                // Vérification dans le contenu
                if ( get_option( 'aet_examine_post_content' ) && preg_match( $pattern, $the_post_content ) ) {
                    wp_set_post_terms( $the_post_id, $newtag->name, 'post_tag', true );
                }
            }
        }
    }
}

// Ajout du hook pour exécuter le code lors de l'enregistrement ou modification d'un article
if ( get_option( 'aet_turn_on' ) && ! aet_halt() ) {
    if ( function_exists( 'wp_after_insert_post' ) ) {
        add_action( 'wp_after_insert_post', 'aet_tagging' );
    } else {
        add_action( 'wp_insert_post', 'aet_tagging' );
    }
}
