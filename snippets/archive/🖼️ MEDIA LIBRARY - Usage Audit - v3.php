<?php
/* CLM-CREATED-AT: 2026-04-21 */
/**
 * Plugin Name: Media Library - Usage Audit
 * Description: Colonne unique "Used In" + filtre orphan/used/featured/content pour retrouver où chaque media est utilise.
 * Version: 3.0.0
 *
 * CHANGELOG
 * 3.0.0 - Unification du nom de snippet, suppression robuste des colonnes MLA, ajout filtre Orphan/Used/Featured/Content.
 * 2.0.0 - Ajout audit d'usage media (featured + content + orphan) avec colonnes dediees.
 * 1.0.0 - Premiere tentative basee MLA (taxonomies + meta), retiree du canonical.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'clm_media_simple_where_used_boot' ) ) {

    function clm_media_simple_where_used_boot() {
        add_filter( 'manage_upload_columns', 'clm_media_simple_where_used_columns', 9999 );
        add_action( 'manage_media_custom_column', 'clm_media_simple_where_used_render', 10, 2 );
        add_action( 'restrict_manage_posts', 'clm_media_simple_filter_dropdown' );
        add_action( 'pre_get_posts', 'clm_media_simple_apply_filter' );
    }
    add_action( 'init', 'clm_media_simple_where_used_boot' );

    function clm_media_simple_where_used_columns( $columns ) {
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

    function clm_media_simple_where_used_render( $column_name, $attachment_id ) {
        if ( 'clm_media_used_in' !== $column_name ) {
            return;
        }

        $usage = clm_media_simple_collect_usage( (int) $attachment_id );

        if ( empty( $usage ) ) {
            echo '<span style="color:#b32d2e;font-weight:600;">Orphan</span>';
            return;
        }

        $lines = array();
        foreach ( $usage as $item ) {
            $post_id = (int) $item['post_id'];
            $type    = $item['type'];
            $title   = get_the_title( $post_id );
            $link    = get_edit_post_link( $post_id );

            if ( ! $link ) {
                continue;
            }

            $label = ( 'featured' === $type ) ? 'Featured' : 'Content';
            $lines[] = '<a href="' . esc_url( $link ) . '">' . esc_html( $title ?: (string) $post_id ) . '</a> <small>(' . esc_html( $label ) . ')</small>';

            if ( count( $lines ) >= 6 ) {
                break;
            }
        }

        if ( empty( $lines ) ) {
            echo '<span style="color:#b32d2e;font-weight:600;">Orphan</span>';
            return;
        }

        echo implode( '<br>', $lines );
    }

    function clm_media_simple_collect_usage( $attachment_id ) {
        $cache_key = 'clm_media_simple_usage_' . $attachment_id;
        $cached = get_transient( $cache_key );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        $featured_ids = clm_media_simple_featured_posts( $attachment_id );
        $content_ids  = clm_media_simple_content_posts( $attachment_id );

        $rows = array();

        foreach ( $featured_ids as $post_id ) {
            $rows[] = array(
                'post_id' => (int) $post_id,
                'type'    => 'featured',
            );
        }

        foreach ( $content_ids as $post_id ) {
            $rows[] = array(
                'post_id' => (int) $post_id,
                'type'    => 'content',
            );
        }

        $rows = clm_media_simple_dedupe_usage( $rows );

        set_transient( $cache_key, $rows, 15 * MINUTE_IN_SECONDS );
        return $rows;
    }

    function clm_media_simple_filter_dropdown() {
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
        </select>
        <?php
    }

    function clm_media_simple_apply_filter( $query ) {
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

        $ids = clm_media_simple_collect_filtered_ids( $filter );
        if ( empty( $ids ) ) {
            $ids = array( 0 );
        }

        $query->set( 'post__in', array_map( 'intval', $ids ) );
    }

    function clm_media_simple_collect_filtered_ids( $filter ) {
        $attachments = get_posts(
            array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'no_found_rows'  => true,
            )
        );

        if ( empty( $attachments ) ) {
            return array();
        }

        $kept = array();
        foreach ( $attachments as $attachment_id ) {
            $usage = clm_media_simple_collect_usage( (int) $attachment_id );

            if ( 'orphan' === $filter && empty( $usage ) ) {
                $kept[] = (int) $attachment_id;
                continue;
            }

            if ( 'used' === $filter && ! empty( $usage ) ) {
                $kept[] = (int) $attachment_id;
                continue;
            }

            if ( in_array( $filter, array( 'featured', 'content' ), true ) ) {
                foreach ( $usage as $row ) {
                    if ( $filter === $row['type'] ) {
                        $kept[] = (int) $attachment_id;
                        break;
                    }
                }
            }
        }

        return array_values( array_unique( $kept ) );
    }

    function clm_media_simple_dedupe_usage( $rows ) {
        $map = array();
        foreach ( $rows as $row ) {
            $post_id = (int) $row['post_id'];
            if ( $post_id <= 0 ) {
                continue;
            }

            if ( ! isset( $map[ $post_id ] ) ) {
                $map[ $post_id ] = $row;
                continue;
            }

            if ( 'featured' === $row['type'] ) {
                $map[ $post_id ] = $row;
            }
        }

        return array_values( $map );
    }

    function clm_media_simple_featured_posts( $attachment_id ) {
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
             LIMIT 40",
            $attachment_id
        );

        return array_map( 'intval', (array) $wpdb->get_col( $sql ) );
    }

    function clm_media_simple_content_posts( $attachment_id ) {
        global $wpdb;

        $url = wp_get_attachment_url( $attachment_id );
        if ( ! $url ) {
            return array();
        }

        $path = (string) wp_parse_url( $url, PHP_URL_PATH );
        $id_token = 'wp-image-' . $attachment_id;

        $like_id = '%' . $wpdb->esc_like( $id_token ) . '%';
        $like_url = '%' . $wpdb->esc_like( $url ) . '%';
        $like_path = $path ? '%' . $wpdb->esc_like( $path ) . '%' : '';

        $sql = "SELECT ID, post_content
                FROM {$wpdb->posts}
                WHERE post_type NOT IN ('attachment', 'revision', 'nav_menu_item')
                  AND post_status NOT IN ('auto-draft', 'trash', 'inherit')
                  AND (post_content LIKE %s OR post_content LIKE %s";

        $params = array( $like_id, $like_url );

        if ( $like_path ) {
            $sql .= ' OR post_content LIKE %s';
            $params[] = $like_path;
        }

        $sql .= ') ORDER BY post_date DESC LIMIT 120';

        $prepared = $wpdb->prepare( $sql, ...$params );
        $rows = (array) $wpdb->get_results( $prepared );

        $ids = array();
        foreach ( $rows as $row ) {
            $content = (string) $row->post_content;
            if (
                str_contains( $content, $id_token ) ||
                str_contains( $content, $url ) ||
                ( $path && str_contains( $content, $path ) )
            ) {
                $ids[] = (int) $row->ID;
            }
        }

        return array_values( array_unique( $ids ) );
    }
}
