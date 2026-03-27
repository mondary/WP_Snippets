/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - Already existing tags.php
 * Display name: WP_POST - Already existing tags
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (2e85d6a44859…, 2 membres)
 * Canonical exact group ID: 22
 * Version family: DUP WP_POST - Already existing tags (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_POST - Already existing tags.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_after_insert_post, wp_insert_post
 * Fonctions clefs: aet_included_categories, aet_halt, aet_tagging
 * Lignes / octets (brut): 76 / 3233
 * Hash code normalise (sha256): 2e85d6a448592318ae4ee5a65b8018d2af1a1fbdb20f389cf23e6ae65c27ff74
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-already-existing-tags__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-already-existing-tags__v002.php
 * Resume fonctionnalites: 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_after_insert_post, wp_insert_post
 * Fonctions clefs: aet_included_categories, aet_halt, aet_tagging
 * APIs WP detectees: get_option, get_post, get_the_terms, wp_list_pluck, wp_strip_all_tags, get_post_field, get_terms, wp_delete_object_term_relationships, wp_set_post_terms, add_action
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 99 / 4166
 * Empreinte code (sha256): 4f2a7361d2a96085d54727e2935a1adf026c33d9299be88388fd1934ab293819
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-already-existing-tags__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-already-existing-tags__v002.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: taxonomy_tags
 * Clusters secondaires: post_footer_ui
 * Domaine: post-front
 * Confiance: high
 * Scores (top): taxonomy_tags=10, post_footer_ui=5
 * Raisons principales: tags, already existing tags
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

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
