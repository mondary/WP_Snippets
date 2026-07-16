<?php
/*
 * Display name: MEDIA 🖼️ IMAGES - Orphans - v3
 * Scope: global
 */

/**
 * Plugin Name: Media Library - Usage Audit
 * Description: Colonne "Used In" + index d'usage (featured/content/orphan) avec analyse manuelle par batch. Inclut la taille de la médiathèque.
 * Version: 8.3.0
 *
 * CHANGELOG
 * 8.3.0 - Fusion taille médiathèque intégrée (plus de doublon), UI allégée (filtres dans dropdown uniquement),
 *         analyse du plus récent au plus ancien (DESC), liens colonne Used In fiabilisés (fallback permalink).
 * 8.2.0 - Barre d'avancement pendant l'analyse (progression lot par lot + auto-continue).
 * 8.1.0 - Liens rapides en notice (filtres + force rebuild) et token rebuild persistant (fix lien expire).
 * 8.0.0 - Filtre Orphan only rendu strict + fusion meta_query admin pour eviter les conflits.
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

    /* ------------------------------------------------------------------ */
    /*  MEDIA SIZE (fusionné, plus de doublon)                            */
    /* ------------------------------------------------------------------ */

    function clm_media_usage_v7_get_size() {
        $k    = 'clm_media_size_total_v7';
        $size = get_transient( $k );
        if ( false !== $size ) {
            return (int) $size;
        }
        $u    = wp_upload_dir();
        $base = isset( $u['basedir'] ) ? $u['basedir'] : '';
        if ( ! $base || ! is_dir( $base ) || ! is_readable( $base ) ) {
            return 0;
        }
        $size = 0;
        try {
            $it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $base, FilesystemIterator::SKIP_DOTS ) );
            foreach ( $it as $f ) {
                if ( $f->isFile() ) {
                    $size += $f->getSize();
                }
            }
        } catch ( Throwable $e ) {
            return 0;
        }
        set_transient( $k, $size, HOUR_IN_SECONDS );
        return (int) $size;
    }

    /* ------------------------------------------------------------------ */
    /*  BOOT                                                              */
    /* ------------------------------------------------------------------ */

    function clm_media_usage_v7_boot() {
        add_filter( 'manage_upload_columns', 'clm_media_usage_v7_columns', PHP_INT_MAX );
        add_action( 'manage_media_custom_column', 'clm_media_usage_v7_render_column', 10, 2 );

        add_action( 'restrict_manage_posts', 'clm_media_usage_v7_filter_dropdown' );
        add_filter( 'views_upload', 'clm_media_usage_v7_views_links' );
        add_action( 'pre_get_posts', 'clm_media_usage_v7_apply_filter' );

        add_action( 'admin_notices', 'clm_media_usage_v7_notices' );
        add_action( 'admin_init', 'clm_media_usage_v7_handle_rebuild' );
        add_action( 'admin_init', 'clm_media_usage_v7_handle_recalc_size' );

        add_action( 'save_post', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'delete_post', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'add_attachment', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'delete_attachment', 'clm_media_usage_v7_mark_stale', 20 );
        add_action( 'updated_postmeta', 'clm_media_usage_v7_maybe_mark_stale_meta', 10, 4 );
        add_action( 'added_post_meta', 'clm_media_usage_v7_maybe_mark_stale_meta', 10, 4 );
        add_action( 'deleted_post_meta', 'clm_media_usage_v7_maybe_mark_stale_meta', 10, 4 );
    }
    add_action( 'init', 'clm_media_usage_v7_boot' );

    /* ------------------------------------------------------------------ */
    /*  COLUMNS                                                           */
    /* ------------------------------------------------------------------ */

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

    /* ------------------------------------------------------------------ */
    /*  COLUMN RENDER — lien fiabilisé                                    */
    /* ------------------------------------------------------------------ */

    function clm_media_usage_v7_post_link( $post_id ) {
        $post_id  = (int) $post_id;
        $title    = get_the_title( $post_id );
        $label    = $title ? $title : '#' . $post_id;

        $link = get_edit_post_link( $post_id );
        if ( $link ) {
            return '<a href="' . esc_url( $link ) . '">' . esc_html( $label ) . '</a>';
        }

        $permalink = get_permalink( $post_id );
        if ( $permalink ) {
            return '<a href="' . esc_url( $permalink ) . '">' . esc_html( $label ) . '</a>';
        }

        return esc_html( $label );
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

        $featured     = ! empty( $data['featured_in'] ) && is_array( $data['featured_in'] ) ? $data['featured_in'] : array();
        $content      = ! empty( $data['content_in'] ) && is_array( $data['content_in'] ) ? $data['content_in'] : array();
        $featured_ids = array_map( 'intval', $featured );
        $lines        = array();

        foreach ( $featured as $post_id ) {
            $lines[] = clm_media_usage_v7_post_link( (int) $post_id ) . ' <small>(Featured)</small>';
            if ( count( $lines ) >= 6 ) {
                break;
            }
        }

        if ( count( $lines ) < 6 ) {
            foreach ( $content as $post_id ) {
                $post_id = (int) $post_id;
                if ( in_array( $post_id, $featured_ids, true ) ) {
                    continue;
                }
                $lines[] = clm_media_usage_v7_post_link( $post_id ) . ' <small>(Content)</small>';
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

    /* ------------------------------------------------------------------ */
    /*  FILTER DROPDOWN                                                   */
    /* ------------------------------------------------------------------ */

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

    /* ------------------------------------------------------------------ */
    /*  VIEWS LINKS — filtres + Analyze + Recalc taille                   */
    /* ------------------------------------------------------------------ */

    function clm_media_usage_v7_views_links( $views ) {
        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return $views;
        }

        $current = isset( $_GET['clm_media_usage_filter'] ) ? sanitize_key( wp_unslash( $_GET['clm_media_usage_filter'] ) ) : '';

        $filter_links = array(
            'orphan'       => 'Orphan only',
            'used'         => 'Used only',
            'featured'     => 'Featured only',
            'content'      => 'Content only',
            'not_analyzed' => 'Not analyzed',
        );

        foreach ( $filter_links as $value => $label ) {
            $url  = add_query_arg( 'clm_media_usage_filter', $value, admin_url( 'upload.php' ) );
            $css  = ( $current === $value ) ? ' class="current"' : '';
            $views[ 'clm_usage_' . $value ] = '<a' . $css . ' href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
        }

        $analyze_url = clm_media_usage_v7_rebuild_url( 0, false );
        $views['clm_usage_analyze'] = '<a href="' . esc_url( $analyze_url ) . '" style="font-weight:600;">Analyze Usage</a>';

        $token     = get_option( 'clm_media_usage_v7_rebuild_token', '' );
        $recalc_url = add_query_arg( array(
            'clm_media_size_recalc'  => '1',
            'clm_media_usage_token'  => $token,
        ), admin_url( 'upload.php' ) );
        $views['clm_media_size_recalc'] = '<a href="' . esc_url( $recalc_url ) . '">Recalculer la taille</a>';

        return $views;
    }

    /* ------------------------------------------------------------------ */
    /*  APPLY FILTER                                                      */
    /* ------------------------------------------------------------------ */

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

        $existing_meta = $query->get( 'meta_query' );
        if ( ! is_array( $existing_meta ) ) {
            $existing_meta = array();
        }

        $new_meta = array();

        if ( 'not_analyzed' === $filter ) {
            $new_meta[] = array(
                'relation' => 'OR',
                array( 'key' => '_clm_media_usage_v7_analyzed', 'compare' => 'NOT EXISTS' ),
                array( 'key' => '_clm_media_usage_v7_analyzed', 'value' => '0' ),
            );
        } elseif ( 'orphan' === $filter ) {
            $new_meta[] = array( 'key' => '_clm_media_usage_v7_analyzed', 'value' => '1' );
            $new_meta[] = array( 'key' => '_clm_media_usage_v7_has_featured', 'value' => '0' );
            $new_meta[] = array( 'key' => '_clm_media_usage_v7_has_content', 'value' => '0' );
        } elseif ( 'used' === $filter ) {
            $new_meta[] = array( 'key' => '_clm_media_usage_v7_state', 'value' => 'used' );
        } elseif ( 'featured' === $filter ) {
            $new_meta[] = array( 'key' => '_clm_media_usage_v7_has_featured', 'value' => '1' );
        } elseif ( 'content' === $filter ) {
            $new_meta[] = array( 'key' => '_clm_media_usage_v7_has_content', 'value' => '1' );
        } else {
            return;
        }

        if ( empty( $existing_meta ) ) {
            $query->set( 'meta_query', $new_meta );
            return;
        }

        $query->set( 'meta_query', array( 'relation' => 'AND', $existing_meta, $new_meta ) );
    }

    /* ------------------------------------------------------------------ */
    /*  NOTICES — taille + analyse en cours + statuts                     */
    /* ------------------------------------------------------------------ */

    function clm_media_usage_v7_notices() {
        if ( ! current_user_can( 'upload_files' ) ) {
            return;
        }

        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return;
        }

        $size = clm_media_usage_v7_get_size();
        if ( $size > 0 ) {
            echo '<div class="notice notice-info inline"><p style="margin:.5em 0;">'
                . '<strong>Taille totale de la médiathèque :</strong> '
                . esc_html( size_format( $size ) )
                . '</p></div>';
        }

        if ( isset( $_GET['clm_media_usage_progress'] ) ) {
            $processed = isset( $_GET['processed'] ) ? max( 0, (int) $_GET['processed'] ) : 0;
            $total     = isset( $_GET['total'] ) ? max( 1, (int) $_GET['total'] ) : 1;
            $percent   = min( 100, (int) floor( ( $processed / $total ) * 100 ) );
            $next_raw  = isset( $_GET['next'] ) ? (string) wp_unslash( $_GET['next'] ) : '';
            $next_url  = $next_raw ? esc_url_raw( rawurldecode( $next_raw ) ) : '';

            echo '<div class="notice notice-info"><p><strong>Analyse en cours...</strong> '
                . esc_html( (string) $processed ) . ' / ' . esc_html( (string) $total )
                . ' (' . esc_html( (string) $percent ) . '%)</p>'
                . '<p><progress max="100" value="' . esc_attr( (string) $percent ) . '" style="width:320px;max-width:100%;height:16px;"></progress></p>';

            if ( $next_url ) {
                echo '<p><a class="button button-primary" href="' . esc_url( $next_url ) . '">Continuer</a> '
                    . '<a class="button" href="' . esc_url( admin_url( 'upload.php' ) ) . '">Pause</a></p>'
                    . '<script>setTimeout(function(){window.location.href=' . wp_json_encode( $next_url ) . ';}, 350);</script>';
            }

            echo '</div>';
        }

        if ( get_option( 'clm_media_usage_v7_stale' ) ) {
            echo '<div class="notice notice-warning"><p>Index is stale. Re-run <strong>Analyze Media Usage</strong> for exact results.</p></div>';
        }

        if ( isset( $_GET['clm_media_usage_done'] ) ) {
            $count = (int) $_GET['clm_media_usage_done'];
            echo '<div class="notice notice-success is-dismissible"><p>Analysis complete. Processed ' . esc_html( (string) $count ) . ' attachment(s).</p></div>';
        }
    }

    /* ------------------------------------------------------------------ */
    /*  REBUILD HANDLER — DESC (plus récent en premier)                   */
    /* ------------------------------------------------------------------ */

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

        if ( ! clm_media_usage_v7_validate_rebuild_token() ) {
            wp_die( 'Le lien suivi est expiré. Veuillez réessayer.' );
        }

        if ( ! empty( $_GET['clm_media_usage_force_rebuild'] ) ) {
            clm_media_usage_v7_force_rebuild_reset();
        }

        $offset = isset( $_GET['offset'] ) ? max( 0, (int) $_GET['offset'] ) : 0;
        $limit  = 35;
        $force  = ! empty( $_GET['clm_media_usage_force_rebuild'] );

        if ( 0 === $offset ) {
            $total = wp_count_posts( 'attachment' );
            $total_inherit = ( $total && isset( $total->inherit ) ) ? (int) $total->inherit : 0;
            update_option( 'clm_media_usage_v7_total', max( 0, $total_inherit ), false );
        }

        $ids = get_posts(
            array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => $limit,
                'offset'         => $offset,
                'fields'         => 'ids',
                'orderby'        => 'ID',
                'order'          => 'DESC',
                'no_found_rows'  => true,
            )
        );

        foreach ( (array) $ids as $attachment_id ) {
            clm_media_usage_v7_analyze_one( (int) $attachment_id );
        }

        if ( count( $ids ) < $limit ) {
            $processed = $offset + count( $ids );
            update_option( 'clm_media_usage_v7_stale', 0, false );
            delete_option( 'clm_media_usage_v7_total' );
            wp_safe_redirect( add_query_arg( array( 'clm_media_usage_done' => $processed ), admin_url( 'upload.php' ) ) );
            exit;
        }

        $next     = $offset + $limit;
        $next_url = clm_media_usage_v7_rebuild_url( $next, $force );
        $total    = (int) get_option( 'clm_media_usage_v7_total', 0 );
        if ( $total <= 0 ) {
            $total = max( $next, $offset + count( $ids ) + 1 );
        }
        $processed = min( $total, $offset + count( $ids ) );

        $progress_url = add_query_arg(
            array(
                'clm_media_usage_progress' => '1',
                'processed'               => $processed,
                'total'                   => $total,
                'next'                    => rawurlencode( $next_url ),
            ),
            admin_url( 'upload.php' )
        );
        wp_safe_redirect( $progress_url );
        exit;
    }

    function clm_media_usage_v7_rebuild_url( $offset = 0, $force = false ) {
        $token = get_option( 'clm_media_usage_v7_rebuild_token', '' );
        if ( ! is_string( $token ) || '' === $token ) {
            $token = wp_generate_password( 20, false, false );
            update_option( 'clm_media_usage_v7_rebuild_token', $token, false );
        }

        $args = array(
            'clm_media_usage_rebuild' => '1',
            'offset'                  => max( 0, (int) $offset ),
            'clm_media_usage_token'   => $token,
        );
        if ( $force ) {
            $args['clm_media_usage_force_rebuild'] = '1';
        }

        return add_query_arg( $args, admin_url( 'upload.php' ) );
    }

    function clm_media_usage_v7_validate_rebuild_token() {
        $provided = isset( $_GET['clm_media_usage_token'] ) ? sanitize_text_field( wp_unslash( $_GET['clm_media_usage_token'] ) ) : '';
        $stored   = get_option( 'clm_media_usage_v7_rebuild_token', '' );

        return is_string( $stored ) && '' !== $stored && hash_equals( $stored, $provided );
    }

    function clm_media_usage_v7_force_rebuild_reset() {
        global $wpdb;

        $meta_keys = array(
            '_clm_media_usage_v7',
            '_clm_media_usage_v7_state',
            '_clm_media_usage_v7_has_featured',
            '_clm_media_usage_v7_has_content',
            '_clm_media_usage_v7_analyzed',
        );

        $in = "'" . implode( "','", array_map( 'esc_sql', $meta_keys ) ) . "'";
        $wpdb->query(
            "DELETE pm FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
             WHERE p.post_type = 'attachment'
               AND pm.meta_key IN ({$in})"
        );
        update_option( 'clm_media_usage_v7_stale', 1, false );
    }

    /* ------------------------------------------------------------------ */
    /*  ANALYZE ONE                                                       */
    /* ------------------------------------------------------------------ */

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

        $path     = (string) wp_parse_url( $url, PHP_URL_PATH );
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
            $sql     .= ' OR post_content LIKE %s';
            $params[] = '%' . $wpdb->esc_like( $path ) . '%';
        }

        $sql .= ') ORDER BY post_date DESC LIMIT 120';

        $prepared = call_user_func_array( array( $wpdb, 'prepare' ), array_merge( array( $sql ), $params ) );
        $rows     = (array) $wpdb->get_results( $prepared );

        $ids = array();
        foreach ( $rows as $row ) {
            $content  = (string) $row->post_content;
            $has_id   = false !== strpos( $content, $id_token );
            $has_url  = false !== strpos( $content, $url );
            $has_path = '' !== $path ? ( false !== strpos( $content, $path ) ) : false;

            if ( $has_id || $has_url || $has_path ) {
                $ids[] = (int) $row->ID;
            }
        }

        return array_values( array_unique( $ids ) );
    }

    /* ------------------------------------------------------------------ */
    /*  STALE                                                             */
    /* ------------------------------------------------------------------ */

    function clm_media_usage_v7_mark_stale() {
        update_option( 'clm_media_usage_v7_stale', 1, false );
    }

    function clm_media_usage_v7_maybe_mark_stale_meta( $meta_id, $object_id, $meta_key ) {
        if ( '_thumbnail_id' === $meta_key ) {
            update_option( 'clm_media_usage_v7_stale', 1, false );
        }
    }

    /* ------------------------------------------------------------------ */
    /*  SIZE RECALC                                                       */
    /* ------------------------------------------------------------------ */

    function clm_media_usage_v7_handle_recalc_size() {
        if ( ! isset( $_GET['clm_media_size_recalc'] ) || '1' !== $_GET['clm_media_size_recalc'] ) {
            return;
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Accès refusé' );
        }
        if ( ! clm_media_usage_v7_validate_rebuild_token() ) {
            wp_die( 'Le lien suivi est expiré. Veuillez réessayer.' );
        }
        delete_transient( 'clm_media_size_total_v7' );
        wp_safe_redirect( remove_query_arg( array( 'clm_media_size_recalc', 'clm_media_usage_token', 'clm_media_usage_rebuild', 'offset' ) ) );
        exit;
    }
}
