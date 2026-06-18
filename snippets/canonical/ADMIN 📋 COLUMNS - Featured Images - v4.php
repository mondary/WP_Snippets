<?php
/*
 * Display name: ADMIN 📋 COLUMNS - Featured Images - v4
 * Scope: global
 *
 * v4:
 * - Colonne featured image dans la liste des articles.
 * - Bouton "Supprimer image" directement sous l'image.
 * - Supprime le media associé (attachment + fichier), sans ouvrir l'article.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function clm_admin_featured_image_column_add( $columns ) {
    $move_before     = 'title';
    $column_keys     = array_keys( $columns );
    $move_before_key = array_search( $move_before, $column_keys, true );

    if ( false === $move_before_key ) {
        $columns['featured_image'] = __( 'Featured Image' );
        return $columns;
    }

    $first_columns = array_slice( $columns, 0, $move_before_key, true );
    $last_columns  = array_slice( $columns, $move_before_key, null, true );

    return array_merge(
        $first_columns,
        array(
            'featured_image' => __( 'Featured Image' ),
        ),
        $last_columns
    );
}
add_filter( 'manage_posts_columns', 'clm_admin_featured_image_column_add' );

function clm_admin_featured_image_column_content( $column, $post_id ) {
    if ( 'featured_image' !== $column ) {
        return;
    }

    echo '<div class="clm-featured-col">';

    if ( has_post_thumbnail( $post_id ) ) {
        echo get_the_post_thumbnail( $post_id, array( 160, 120 ) );

        $delete_media_url = wp_nonce_url(
            add_query_arg(
                array(
                    'action'   => 'clm_delete_featured_media',
                    'post_id'  => (int) $post_id,
                    'redirect' => rawurlencode( wp_unslash( $_SERVER['REQUEST_URI'] ?? 'edit.php' ) ),
                ),
                admin_url( 'admin.php' )
            ),
            'clm_delete_featured_media_' . $post_id
        );

        echo '<p><a class="button button-small clm-delete-featured" href="' . esc_url( $delete_media_url ) . '"';
        echo ' onclick="return confirm(\'Supprimer définitivement ce média et le retirer de l\\\'article ?\');">Supprimer image</a></p>';
    } else {
        echo '<span class="clm-no-image">No image</span>';
    }

    echo '</div>';
}
add_action( 'manage_posts_custom_column', 'clm_admin_featured_image_column_content', 10, 2 );

function clm_admin_featured_image_column_css() {
    echo '<style>
        .column-featured_image {
            width: 180px !important;
            text-align: center;
        }
        .column-featured_image .clm-featured-col img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            display: block;
            margin: 0 auto 6px;
        }
        .column-featured_image .clm-delete-featured {
            border-color: #b32d2e;
            color: #b32d2e;
        }
        .column-featured_image .clm-delete-featured:hover {
            border-color: #8a2424;
            color: #8a2424;
        }
        .column-featured_image .clm-no-image {
            color: #777;
            font-size: 12px;
        }
    </style>';
}
add_action( 'admin_head', 'clm_admin_featured_image_column_css' );

function clm_delete_featured_media_action() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'Accès refusé.' ) );
    }

    $post_id = isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;

    if ( $post_id < 1 || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_die( __( 'Article invalide ou accès refusé.' ) );
    }

    check_admin_referer( 'clm_delete_featured_media_' . $post_id );

    $attachment_id = (int) get_post_thumbnail_id( $post_id );
    $deleted       = false;

    if ( $attachment_id > 0 && current_user_can( 'delete_post', $attachment_id ) ) {
        $deleted = (bool) wp_delete_attachment( $attachment_id, true );
    }

    $redirect_raw = isset( $_GET['redirect'] ) ? rawurldecode( wp_unslash( $_GET['redirect'] ) ) : 'edit.php';
    $redirect     = wp_validate_redirect( $redirect_raw, admin_url( 'edit.php' ) );

    $redirect = add_query_arg(
        array(
            'clm_featured_media_deleted' => $deleted ? '1' : '0',
        ),
        $redirect
    );

    wp_safe_redirect( $redirect );
    exit;
}
add_action( 'admin_action_clm_delete_featured_media', 'clm_delete_featured_media_action' );

function clm_delete_featured_media_admin_notice() {
    if ( ! isset( $_GET['clm_featured_media_deleted'] ) ) {
        return;
    }

    $ok = '1' === (string) $_GET['clm_featured_media_deleted'];
    $class = $ok ? 'notice notice-success is-dismissible' : 'notice notice-warning is-dismissible';
    $msg = $ok ? 'Média supprimé et retiré de la mise en avant.' : 'Suppression impossible (droits ou image absente).';

    echo '<div class="' . esc_attr( $class ) . '"><p>' . esc_html( $msg ) . '</p></div>';
}
add_action( 'admin_notices', 'clm_delete_featured_media_admin_notice' );
