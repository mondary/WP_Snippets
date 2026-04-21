<?php
/* CLM-CREATED-AT: 2026-04-20 */
/**
 * Plugin Name: Media Usage Audit (Featured/Content/Orphans)
 * Description: Affiche l'usage réel des médias (image mise en avant, image dans le contenu) et détecte les médias orphelins dans la médiathèque.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'clm_media_usage_boot' ) ) {

    function clm_media_usage_boot() {
        add_filter( 'manage_upload_columns', 'clm_media_usage_add_columns' );
        add_action( 'manage_media_custom_column', 'clm_media_usage_render_column', 10, 2 );
        add_filter( 'media_row_actions', 'clm_media_usage_row_actions', 10, 2 );

        add_action( 'restrict_manage_posts', 'clm_media_usage_filter_dropdown' );
        add_action( 'pre_get_posts', 'clm_media_usage_apply_filter' );

        add_action( 'save_post', 'clm_media_usage_flush_cache', 20 );
        add_action( 'deleted_post', 'clm_media_usage_flush_cache', 20 );
        add_action( 'add_attachment', 'clm_media_usage_flush_cache', 20 );
        add_action( 'delete_attachment', 'clm_media_usage_flush_cache', 20 );
        add_action( 'updated_postmeta', 'clm_media_usage_maybe_flush_on_thumbnail', 10, 4 );
        add_action( 'added_post_meta', 'clm_media_usage_maybe_flush_on_thumbnail', 10, 4 );
        add_action( 'deleted_post_meta', 'clm_media_usage_maybe_flush_on_thumbnail', 10, 4 );
    }
    add_action( 'init', 'clm_media_usage_boot' );

    function clm_media_usage_add_columns( $columns ) {
        $columns['clm_media_featured'] = 'Featured Image';
        $columns['clm_media_content']  = 'In Content';
        $columns['clm_media_status']   = 'Usage Status';
        return $columns;
    }

    function clm_media_usage_render_column( $column_name, $attachment_id ) {
        $usage = clm_media_usage_get( (int) $attachment_id );

        if ( 'clm_media_featured' === $column_name ) {
            echo clm_media_usage_render_post_links( $usage['featured_in'], 'No' );
            return;
        }

        if ( 'clm_media_content' === $column_name ) {
            echo clm_media_usage_render_post_links( $usage['content_in'], 'No' );
            return;
        }

        if ( 'clm_media_status' === $column_name ) {
            if ( $usage['is_orphan'] ) {
                echo '<strong style="color:#b32d2e;">Orphan</strong>';
            } else {
                echo '<strong style="color:#1d6f42;">Used</strong>';
            }

            if ( $usage['post_parent'] > 0 ) {
                $parent_link = get_edit_post_link( $usage['post_parent'] );
                $parent_title = get_the_title( $usage['post_parent'] );
                if ( $parent_link ) {
                    echo '<br><small>Uploaded to: <a href="' . esc_url( $parent_link ) . '">' . esc_html( $parent_title ?: (string) $usage['post_parent'] ) . '</a></small>';
                }
            }
        }
    }

    function clm_media_usage_render_post_links( $post_ids, $empty_label ) {
        if ( empty( $post_ids ) ) {
            return esc_html( $empty_label );
        }

        $items = array();
        $max   = 4;
        $count = 0;

        foreach ( $post_ids as $post_id ) {
            $post_id = (int) $post_id;
            if ( $post_id <= 0 ) {
                continue;
            }
            $edit_link = get_edit_post_link( $post_id );
            if ( ! $edit_link ) {
                continue;
            }
            $title = get_the_title( $post_id );
            $items[] = '<a href="' . esc_url( $edit_link ) . '">' . esc_html( $title ?: (string) $post_id ) . '</a>';
            $count++;
            if ( $count >= $max ) {
                break;
            }
        }

        if ( empty( $items ) ) {
            return esc_html( $empty_label );
        }

        $html = implode( '<br>', $items );
        if ( count( $post_ids ) > $max ) {
            $html .= '<br><small>+' . ( count( $post_ids ) - $max ) . ' more</small>';
        }

        return $html;
    }

    function clm_media_usage_row_actions( $actions, $post ) {
        if ( ! ( $post instanceof WP_Post ) || 'attachment' !== $post->post_type ) {
            return $actions;
        }

        $usage = clm_media_usage_get( (int) $post->ID );

        if ( $usage['is_orphan'] ) {
            $actions['clm_orphan_flag'] = '<span style="color:#b32d2e;font-weight:600;">Orphan media</span>';
        } else {
            $actions['clm_orphan_flag'] = '<span style="color:#1d6f42;font-weight:600;">Used media</span>';
        }

        return $actions;
    }

    function clm_media_usage_filter_dropdown() {
        global $pagenow, $typenow;
        if ( 'upload.php' !== $pagenow || 'attachment' !== $typenow ) {
            return;
        }

        $current = isset( $_GET['clm_media_usage_filter'] ) ? sanitize_key( wp_unslash( $_GET['clm_media_usage_filter'] ) ) : '';
        ?>
        <select name="clm_media_usage_filter">
            <option value="" <?php selected( $current, '' ); ?>>All media</option>
            <option value="orphan" <?php selected( $current, 'orphan' ); ?>>Orphan only</option>
            <option value="used" <?php selected( $current, 'used' ); ?>>Used only</option>
            <option value="featured" <?php selected( $current, 'featured' ); ?>>Used as featured image</option>
            <option value="content" <?php selected( $current, 'content' ); ?>>Used in post content</option>
        </select>
        <?php
    }

    function clm_media_usage_apply_filter( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        global $pagenow;
        if ( 'upload.php' !== $pagenow ) {
            return;
        }

        $filter = isset( $_GET['clm_media_usage_filter'] ) ? sanitize_key( wp_unslash( $_GET['clm_media_usage_filter'] ) ) : '';
        if ( ! $filter ) {
            return;
        }

        $ids = clm_media_usage_filter_attachment_ids( $filter );
        if ( empty( $ids ) ) {
            $ids = array( 0 );
        }

        $query->set( 'post__in', array_map( 'intval', $ids ) );
    }

    function clm_media_usage_filter_attachment_ids( $filter ) {
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
            $usage = clm_media_usage_get( (int) $attachment_id );

            if ( 'orphan' === $filter && $usage['is_orphan'] ) {
                $kept[] = (int) $attachment_id;
            }
            if ( 'used' === $filter && ! $usage['is_orphan'] ) {
                $kept[] = (int) $attachment_id;
            }
            if ( 'featured' === $filter && ! empty( $usage['featured_in'] ) ) {
                $kept[] = (int) $attachment_id;
            }
            if ( 'content' === $filter && ! empty( $usage['content_in'] ) ) {
                $kept[] = (int) $attachment_id;
            }
        }

        return $kept;
    }

    function clm_media_usage_get( $attachment_id ) {
        static $runtime_cache = array();

        if ( isset( $runtime_cache[ $attachment_id ] ) ) {
            return $runtime_cache[ $attachment_id ];
        }

        $cache_key = 'clm_media_usage_' . $attachment_id;
        $cached = get_transient( $cache_key );
        if ( is_array( $cached ) ) {
            $runtime_cache[ $attachment_id ] = $cached;
            return $cached;
        }

        $featured = clm_media_usage_find_featured_posts( $attachment_id );
        $content  = clm_media_usage_find_content_posts( $attachment_id );

        $usage = array(
            'featured_in' => array_values( array_unique( array_map( 'intval', $featured ) ) ),
            'content_in'  => array_values( array_unique( array_map( 'intval', $content ) ) ),
            'post_parent' => (int) wp_get_post_parent_id( $attachment_id ),
            'is_orphan'   => empty( $featured ) && empty( $content ),
        );

        set_transient( $cache_key, $usage, 15 * MINUTE_IN_SECONDS );
        $runtime_cache[ $attachment_id ] = $usage;

        return $usage;
    }

    function clm_media_usage_find_featured_posts( $attachment_id ) {
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

    function clm_media_usage_find_content_posts( $attachment_id ) {
        global $wpdb;

        $attachment_url = wp_get_attachment_url( $attachment_id );
        if ( ! $attachment_url ) {
            return array();
        }

        $url_path = (string) wp_parse_url( $attachment_url, PHP_URL_PATH );
        $id_token = 'wp-image-' . (int) $attachment_id;

        $like_id   = '%' . $wpdb->esc_like( $id_token ) . '%';
        $like_url  = '%' . $wpdb->esc_like( $attachment_url ) . '%';
        $like_path = $url_path ? '%' . $wpdb->esc_like( $url_path ) . '%' : '';

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

            $has_id_token = str_contains( $content, $id_token );
            $has_url = str_contains( $content, $attachment_url );
            $has_path = $url_path && str_contains( $content, $url_path );

            if ( $has_id_token || $has_url || $has_path ) {
                $ids[] = (int) $row->ID;
            }
        }

        return array_values( array_unique( $ids ) );
    }

    function clm_media_usage_maybe_flush_on_thumbnail( $meta_id, $object_id, $meta_key ) {
        if ( '_thumbnail_id' !== $meta_key ) {
            return;
        }
        clm_media_usage_flush_cache( $object_id );
    }

    function clm_media_usage_flush_cache( $post_id = 0 ) {
        global $wpdb;

        $keys = (array) $wpdb->get_col(
            "SELECT option_name
             FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_clm_media_usage_%'
                OR option_name LIKE '_transient_timeout_clm_media_usage_%'"
        );

        foreach ( $keys as $key ) {
            $transient = str_replace( array( '_transient_', '_transient_timeout_' ), '', (string) $key );
            delete_transient( $transient );
        }
    }
}
