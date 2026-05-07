<?php
/* CLM-CREATED-AT: 2026-04-21 */
/**
 * Plugin Name: Media Library - Usage Audit
 * Description: Colonne "Used In" + index d'usage (featured/content/orphan) avec analyse manuelle par batch.
 * Version: 7.1.0
 *
 * CHANGELOG
 * 7.1.0 - Ajout d'un lien permanent "Analyze Usage" dans la barre des vues de la mediatheque.
 * 7.0.0 - Acceleration filtre (meta_query), liens de filtres visibles, analyse en cache.
 * 6.0.0 - Version stable: analyse par batch manuel, affichage rapide depuis cache, filtre orphan/used/featured/content.
 * 5.0.0 - Suppression forcee des colonnes MLA en JS + force affichage colonne Used In.
 * 4.0.0 - Compatibilite PHP 7.4+, priorite maximale des colonnes, fallback CSS pour masquer colonnes MLA.
 * 3.0.0 - Unification du nom de snippet, suppression robuste des colonnes MLA, ajout filtre Orphan/Used/Featured/Content.
 * 2.0.0 - Ajout audit d'usage media (featured + content + orphan) avec colonnes dediees.
 * 1.0.0 - Premiere tentative basee MLA (taxonomies + meta), retiree du canonical.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'clm_media_usage_v7_boot' ) ) {

    function clm_media_usage_v7_boot() {
        add_filter( 'manage_upload_columns', 'clm_media_usage_v7_columns', PHP_INT_MAX );
        add_action( 'manage_media_custom_column', 'clm_media_usage_v7_render_column', 10, 2 );

        add_action( 'restrict_manage_posts', 'clm_media_usage_v7_filter_dropdown' );
        add_filter( 'views_upload', 'clm_media_usage_v7_views_links' );
        add_action( 'pre_get_posts', 'clm_media_usage_v7_apply_filter' );

        add_action( 'admin_notices', 'clm_media_usage_v7_notices' );
        add_action( 'admin_init', 'clm_media_usage_v7_handle_rebuild' );

        add_action( 'save_post', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'delete_post', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'add_attachment', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'delete_attachment', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'updated_postmeta', 'clm_media_usage_v7_maybe_mark_stale_meta', 10, 4 );
        add_action( 'added_post_meta', 'clm_media_usage_v7_maybe_mark_stale_meta', 10, 4 );
        add_action( 'deleted_post_meta', 'clm_media_usage_v7_maybe_mark_stale_meta', 10, 4 );
    }
    add_action( 'init', 'clm_media_usage_v7_boot' );

    function clm_media_usage_v7_columns( $columns ) {
        foreach ( $columns as $key => $label ) {
            $label_text = wp_strip_all_tags( (string) $label );
            $is_mla_key = in_array(
                (string) $key,
                array( 'media_category', 'media_tag', 'mla_meta', 'taxonomy-media_category', 'taxonomy-media_tag' ),
                true
            );
            $is_mla_label = false !== stripos( $label_text, 'Media Categories' )
                || false !== stripos( $label_text, 'Media Tags' )
                || false !== stripos( $label_text, 'MLA Meta' );

            if ( $is_mla_key || $is_mla_label ) {
                unset( $columns[ $key ] );
            }
        }

        $columns['clm_media_used_in'] = 'Used In';
        return $columns;
    }

    function clm_media_usage_v7_render_column( $column_name, $attachment_id ) {
        if ( 'clm_media_used_in' !== $column_name ) {
            return;
        }

        $data = get_post_meta( (int) $attachment_id, '_clm_media_usage_v7', true );
        if ( ! is_array( $data ) || empty( $data['analyzed_at'] ) ) {
            echo '<small>Not analyzed</small>';
            return;
        }

        $featured = ! empty( $data['featured_in'] ) && is_array( $data['featured_in'] ) ? $data['featured_in'] : array();
        $content  = ! empty( $data['content_in'] ) && is_array( $data['content_in'] ) ? $data['content_in'] : array();

        $lines = array();
        foreach ( $featured as $post_id ) {
            $post_id = (int) $post_id;
            $link = get_edit_post_link( $post_id );
            if ( $link ) {
                $title = get_the_title( $post_id );
                $lines[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $title ? $title : (string) $post_id ) . '</a> <small>(Featured)</small>';
            }
            if ( count( $lines ) >= 6 ) {
                break;
            }
        }

        if ( count( $lines ) < 6 ) {
            foreach ( $content as $post_id ) {
                $post_id = (int) $post_id;
                if ( in_array( $post_id, array_map( 'intval', $featured ), true ) ) {
                    continue;
                }
                $link = get_edit_post_link( $post_id );
                if ( $link ) {
                    $title = get_the_title( $post_id );
                    $lines[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $title ? $title : (string) $post_id ) . '</a> <small>(Content)</small>';
                }
                if ( count( $lines ) >= 6 ) {
                    break;
                }
            }
        }

        if ( empty( $lines ) ) {
            echo '<strong style="color:#b32d2e;">Orphan</strong>';
            return;
        }

        echo implode( '<br>', $lines );
    }

    function clm_media_usage_v7_filter_dropdown() {
        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return;
        }

        $current = isset( $_GET['clm_media_usage_filter'] ) ? sanitize_key( wp_unslash( $_GET['clm_media_usage_filter'] ) ) : '';
        ?>
        <select name="clm_media_usage_filter">
            <option value="" <?php selected( $current, '' ); ?>>All media</option>
            <option value="orphan" <?php selected( $current, 'orphan' ); ?>>Orphan only</option>
            <option value="used" <?php selected( $current, 'used' ); ?>>Used only</option>
            <option value="featured" <?php selected( $current, 'featured' ); ?>>Featured only</option>
            <option value="content" <?php selected( $current, 'content' ); ?>>Content only</option>
            <option value="not_analyzed" <?php selected( $current, 'not_analyzed' ); ?>>Not analyzed</option>
        </select>
        <?php
    }

    function clm_media_usage_v7_views_links( $views ) {
        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return $views;
        }

        $current = isset( $_GET['clm_media_usage_filter'] ) ? sanitize_key( wp_unslash( $_GET['clm_media_usage_filter'] ) ) : '';
        $links = array(
            'orphan'       => 'Orphan only',
            'used'         => 'Used only',
            'featured'     => 'Featured only',
            'content'      => 'Content only',
            'not_analyzed' => 'Not analyzed',
        );

        foreach ( $links as $value => $label ) {
            $url = add_query_arg( 'clm_media_usage_filter', $value, admin_url( 'upload.php' ) );
            $css = ( $current === $value ) ? ' class="current"' : '';
            $views[ 'clm_usage_' . $value ] = '<a' . $css . ' href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
        }

        $analyze_url = wp_nonce_url(
            add_query_arg(
                array(
                    'clm_media_usage_rebuild' => '1',
                    'offset'                 => '0',
                ),
                admin_url( 'upload.php' )
            ),
            'clm_media_usage_rebuild'
        );
        $views['clm_usage_analyze'] = '<a href="' . esc_url( $analyze_url ) . '"><strong>Analyze Usage</strong></a>';

        return $views;
    }

    function clm_media_usage_v7_apply_filter( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return;
        }

        $filter = isset( $_GET['clm_media_usage_filter'] ) ? sanitize_key( wp_unslash( $_GET['clm_media_usage_filter'] ) ) : '';
        if ( '' === $filter ) {
            return;
        }

        if ( 'not_analyzed' === $filter ) {
            $query->set(
                'meta_query',
                array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_clm_media_usage_v7_analyzed',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'   => '_clm_media_usage_v7_analyzed',
                        'value' => '0',
                    ),
                )
            );
            return;
        }

        if ( 'orphan' === $filter || 'used' === $filter ) {
            $query->set(
                'meta_query',
                array(
                    array(
                        'key'   => '_clm_media_usage_v7_state',
                        'value' => $filter,
                    ),
                )
            );
            return;
        }

        if ( 'featured' === $filter ) {
            $query->set(
                'meta_query',
                array(
                    array(
                        'key'   => '_clm_media_usage_v7_has_featured',
                        'value' => '1',
                    ),
                )
            );
            return;
        }

        if ( 'content' === $filter ) {
            $query->set(
                'meta_query',
                array(
                    array(
                        'key'   => '_clm_media_usage_v7_has_content',
                        'value' => '1',
                    ),
                )
            );
            return;
        }
    }

    function clm_media_usage_v7_notices() {
        if ( ! current_user_can( 'upload_files' ) ) {
            return;
        }

        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return;
        }

        $url = wp_nonce_url( add_query_arg( array( 'clm_media_usage_rebuild' => '1', 'offset' => '0' ), admin_url( 'upload.php' ) ), 'clm_media_usage_rebuild' );
        echo '<div class="notice notice-info"><p><strong>Media Usage Audit</strong> - <a class="button button-secondary" href="' . esc_url( $url ) . '">Analyze Media Usage</a></p></div>';

        if ( get_option( 'clm_media_usage_v7_stale' ) ) {
            echo '<div class="notice notice-warning"><p>Index is stale. Re-run <strong>Analyze Media Usage</strong> for exact results.</p></div>';
        }

        if ( isset( $_GET['clm_media_usage_done'] ) ) {
            $count = (int) $_GET['clm_media_usage_done'];
            echo '<div class="notice notice-success is-dismissible"><p>Analysis complete. Processed ' . esc_html( (string) $count ) . ' attachment(s).</p></div>';
        }
    }

    function clm_media_usage_v7_handle_rebuild() {
        if ( ! is_admin() || ! current_user_can( 'upload_files' ) ) {
            return;
        }

        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return;
        }

        if ( empty( $_GET['clm_media_usage_rebuild'] ) ) {
            return;
        }

        check_admin_referer( 'clm_media_usage_rebuild' );

        $offset = isset( $_GET['offset'] ) ? max( 0, (int) $_GET['offset'] ) : 0;
        $limit  = 35;

        $ids = get_posts(
            array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => $limit,
                'offset'         => $offset,
                'fields'         => 'ids',
                'orderby'        => 'ID',
                'order'          => 'ASC',
                'no_found_rows'  => true,
            )
        );

        foreach ( (array) $ids as $attachment_id ) {
            clm_media_usage_v7_analyze_one( (int) $attachment_id );
        }

        if ( count( $ids ) < $limit ) {
            $processed = $offset + count( $ids );
            update_option( 'clm_media_usage_v7_stale', 0, false );
            wp_safe_redirect( add_query_arg( array( 'clm_media_usage_done' => $processed ), admin_url( 'upload.php' ) ) );
            exit;
        }

        $next = $offset + $limit;
        $url = wp_nonce_url( add_query_arg( array( 'clm_media_usage_rebuild' => '1', 'offset' => $next ), admin_url( 'upload.php' ) ), 'clm_media_usage_rebuild' );
        wp_safe_redirect( $url );
        exit;
    }

    function clm_media_usage_v7_analyze_one( $attachment_id ) {
        $featured = clm_media_usage_v7_featured_posts( $attachment_id );
        $content  = clm_media_usage_v7_content_posts( $attachment_id );

        $has_featured = ! empty( $featured );
        $has_content  = ! empty( $content );
        $state        = ( $has_featured || $has_content ) ? 'used' : 'orphan';

        $data = array(
            'featured_in' => array_values( array_unique( array_map( 'intval', $featured ) ) ),
            'content_in'  => array_values( array_unique( array_map( 'intval', $content ) ) ),
            'analyzed_at' => time(),
        );

        update_post_meta( $attachment_id, '_clm_media_usage_v7', $data );
        update_post_meta( $attachment_id, '_clm_media_usage_v7_state', $state );
        update_post_meta( $attachment_id, '_clm_media_usage_v7_has_featured', $has_featured ? '1' : '0' );
        update_post_meta( $attachment_id, '_clm_media_usage_v7_has_content', $has_content ? '1' : '0' );
        update_post_meta( $attachment_id, '_clm_media_usage_v7_analyzed', '1' );
    }

    function clm_media_usage_v7_featured_posts( $attachment_id ) {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT p.ID
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE pm.meta_key = '_thumbnail_id'
               AND pm.meta_value = %d
               AND p.post_type NOT IN ('attachment', 'revision', 'nav_menu_item')
               AND p.post_status NOT IN ('auto-draft', 'trash', 'inherit')
             ORDER BY p.post_date DESC
             LIMIT 30",
            $attachment_id
        );

        return array_map( 'intval', (array) $wpdb->get_col( $sql ) );
    }

    function clm_media_usage_v7_content_posts( $attachment_id ) {
        global $wpdb;

        $url = wp_get_attachment_url( $attachment_id );
        if ( ! $url ) {
            return array();
        }

        $path = (string) wp_parse_url( $url, PHP_URL_PATH );
        $id_token = 'wp-image-' . $attachment_id;

        $sql = "SELECT ID, post_content
                FROM {$wpdb->posts}
                WHERE post_type NOT IN ('attachment', 'revision', 'nav_menu_item')
                  AND post_status NOT IN ('auto-draft', 'trash', 'inherit')
                  AND (post_content LIKE %s OR post_content LIKE %s";

        $params = array(
            '%' . $wpdb->esc_like( $id_token ) . '%',
            '%' . $wpdb->esc_like( $url ) . '%',
        );

        if ( '' !== $path ) {
            $sql .= ' OR post_content LIKE %s';
            $params[] = '%' . $wpdb->esc_like( $path ) . '%';
        }

        $sql .= ') ORDER BY post_date DESC LIMIT 120';

        $prepared = call_user_func_array( array( $wpdb, 'prepare' ), array_merge( array( $sql ), $params ) );
        $rows = (array) $wpdb->get_results( $prepared );

        $ids = array();
        foreach ( $rows as $row ) {
            $content = (string) $row->post_content;
            $has_id   = false !== strpos( $content, $id_token );
            $has_url  = false !== strpos( $content, $url );
            $has_path = '' !== $path ? ( false !== strpos( $content, $path ) ) : false;

            if ( $has_id || $has_url || $has_path ) {
                $ids[] = (int) $row->ID;
            }
        }

        return array_values( array_unique( $ids ) );
    }

    function clm_media_usage_v7_mark_stale() {
        update_option( 'clm_media_usage_v7_stale', 1, false );
    }

    function clm_media_usage_v7_maybe_mark_stale_meta( $meta_id, $object_id, $meta_key ) {
        if ( '_thumbnail_id' === $meta_key ) {
            update_option( 'clm_media_usage_v7_stale', 1, false );
        }
    }
}
