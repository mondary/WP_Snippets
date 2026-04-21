<?php
/* CLM-CREATED-AT: 2026-04-20 */
/**
 * Plugin Name: Media Library Assistant Light (Snippet)
 * Description: Ajoute des shortcodes type MLA, taxonomies pour pièces jointes, extraction de métadonnées (EXIF/IPTC/XMP/PDF) et édition rapide/masse.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'clm_mla_boot' ) ) {

    function clm_mla_boot() {
        clm_mla_register_taxonomies();
        clm_mla_register_admin_columns();
        clm_mla_register_shortcodes();
        clm_mla_register_metadata_processing();
        clm_mla_register_quick_and_bulk_edit();
    }
    add_action( 'init', 'clm_mla_boot', 20 );

    function clm_mla_register_taxonomies() {
        register_taxonomy(
            'media_category',
            'attachment',
            array(
                'label'             => 'Media Categories',
                'public'            => false,
                'show_ui'           => true,
                'show_admin_column' => false,
                'hierarchical'      => true,
                'show_in_rest'      => true,
                'rewrite'           => false,
            )
        );

        register_taxonomy(
            'media_tag',
            'attachment',
            array(
                'label'             => 'Media Tags',
                'public'            => false,
                'show_ui'           => true,
                'show_admin_column' => false,
                'hierarchical'      => false,
                'show_in_rest'      => true,
                'rewrite'           => false,
            )
        );
    }

    function clm_mla_register_admin_columns() {
        add_filter(
            'manage_upload_columns',
            static function( $columns ) {
                $columns['media_category'] = 'Media Categories';
                $columns['media_tag']      = 'Media Tags';
                $columns['mla_meta']       = 'MLA Meta';
                return $columns;
            }
        );

        add_action(
            'manage_media_custom_column',
            static function( $column_name, $post_id ) {
                if ( 'media_category' === $column_name ) {
                    $terms = get_the_terms( $post_id, 'media_category' );
                    echo esc_html( $terms ? implode( ', ', wp_list_pluck( $terms, 'name' ) ) : '-' );
                }

                if ( 'media_tag' === $column_name ) {
                    $terms = get_the_terms( $post_id, 'media_tag' );
                    echo esc_html( $terms ? implode( ', ', wp_list_pluck( $terms, 'name' ) ) : '-' );
                }

                if ( 'mla_meta' === $column_name ) {
                    $meta = get_post_meta( $post_id, '_mla_meta_cache', true );
                    if ( empty( $meta ) || ! is_array( $meta ) ) {
                        echo '-';
                        return;
                    }

                    $parts = array();
                    if ( ! empty( $meta['camera'] ) ) {
                        $parts[] = 'Camera: ' . $meta['camera'];
                    }
                    if ( ! empty( $meta['created_timestamp'] ) ) {
                        $parts[] = 'Date: ' . gmdate( 'Y-m-d', (int) $meta['created_timestamp'] );
                    }
                    if ( ! empty( $meta['pdf_pages'] ) ) {
                        $parts[] = 'PDF pages: ' . (int) $meta['pdf_pages'];
                    }
                    if ( ! empty( $meta['xmp_subject'] ) ) {
                        $parts[] = 'XMP subject';
                    }

                    echo esc_html( $parts ? implode( ' | ', $parts ) : '-' );
                }
            },
            10,
            2
        );
    }

    function clm_mla_register_shortcodes() {
        if ( ! shortcode_exists( 'mla_gallery' ) ) {
            add_shortcode( 'mla_gallery', 'clm_mla_gallery_shortcode' );
        }

        if ( ! shortcode_exists( 'mla_tag_cloud' ) ) {
            add_shortcode( 'mla_tag_cloud', 'clm_mla_tag_cloud_shortcode' );
        }

        if ( ! shortcode_exists( 'mla_term_list' ) ) {
            add_shortcode( 'mla_term_list', 'clm_mla_term_list_shortcode' );
        }

        if ( ! shortcode_exists( 'mla_custom_list' ) ) {
            add_shortcode( 'mla_custom_list', 'clm_mla_custom_list_shortcode' );
        }

        if ( ! shortcode_exists( 'mla_archive_list' ) ) {
            add_shortcode( 'mla_archive_list', 'clm_mla_archive_list_shortcode' );
        }
    }

    function clm_mla_build_media_query( $atts ) {
        $args = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => isset( $atts['posts_per_page'] ) ? (int) $atts['posts_per_page'] : 12,
            'orderby'        => ! empty( $atts['orderby'] ) ? sanitize_key( $atts['orderby'] ) : 'date',
            'order'          => ! empty( $atts['order'] ) ? strtoupper( sanitize_text_field( $atts['order'] ) ) : 'DESC',
        );

        $tax_query = array();

        if ( ! empty( $atts['media_category'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'media_category',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $atts['media_category'] ) ) ),
            );
        }

        if ( ! empty( $atts['media_tag'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'media_tag',
                'field'    => 'slug',
                'terms'    => array_map( 'sanitize_title', array_map( 'trim', explode( ',', $atts['media_tag'] ) ) ),
            );
        }

        if ( $tax_query ) {
            $args['tax_query'] = $tax_query;
        }

        if ( ! empty( $atts['mime_type'] ) ) {
            $args['post_mime_type'] = sanitize_text_field( $atts['mime_type'] );
        }

        return $args;
    }

    function clm_mla_gallery_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'posts_per_page' => 12,
                'size'           => 'thumbnail',
                'link'           => 'file',
                'columns'        => 4,
                'media_category' => '',
                'media_tag'      => '',
                'mime_type'      => '',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ),
            $atts,
            'mla_gallery'
        );

        $q = new WP_Query( clm_mla_build_media_query( $atts ) );
        if ( ! $q->have_posts() ) {
            return '';
        }

        $columns = max( 1, min( 8, (int) $atts['columns'] ) );
        $style   = 'display:grid;gap:12px;grid-template-columns:repeat(' . $columns . ',minmax(0,1fr));';
        $html    = '<div class="mla-gallery" style="' . esc_attr( $style ) . '">';

        while ( $q->have_posts() ) {
            $q->the_post();
            $id    = get_the_ID();
            $thumb = wp_get_attachment_image( $id, $atts['size'], false, array( 'loading' => 'lazy' ) );
            $url   = ( 'attachment' === $atts['link'] ) ? get_attachment_link( $id ) : wp_get_attachment_url( $id );
            $html .= '<a href="' . esc_url( $url ) . '" class="mla-gallery-item">' . $thumb . '</a>';
        }
        wp_reset_postdata();

        $html .= '</div>';
        return $html;
    }

    function clm_mla_tag_cloud_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'taxonomy'  => 'media_tag',
                'smallest'  => 10,
                'largest'   => 20,
                'unit'      => 'px',
                'number'    => 30,
                'show_count'=> 1,
            ),
            $atts,
            'mla_tag_cloud'
        );

        $terms = get_terms(
            array(
                'taxonomy'   => sanitize_key( $atts['taxonomy'] ),
                'hide_empty' => true,
                'number'     => (int) $atts['number'],
            )
        );

        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return '';
        }

        $out = '<div class="mla-tag-cloud">';
        foreach ( $terms as $term ) {
            $size = rand( (int) $atts['smallest'], (int) $atts['largest'] );
            $url  = esc_url( add_query_arg( array( sanitize_key( $atts['taxonomy'] ) => $term->slug ), admin_url( 'upload.php' ) ) );
            $out .= '<a href="' . $url . '" style="font-size:' . (int) $size . esc_attr( $atts['unit'] ) . ';margin-right:8px;display:inline-block;">' . esc_html( $term->name );
            if ( (int) $atts['show_count'] ) {
                $out .= ' (' . (int) $term->count . ')';
            }
            $out .= '</a>';
        }
        $out .= '</div>';

        return $out;
    }

    function clm_mla_term_list_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'taxonomy'   => 'media_category',
                'hide_empty' => 0,
            ),
            $atts,
            'mla_term_list'
        );

        $terms = get_terms(
            array(
                'taxonomy'   => sanitize_key( $atts['taxonomy'] ),
                'hide_empty' => (bool) $atts['hide_empty'],
            )
        );

        if ( is_wp_error( $terms ) || ! $terms ) {
            return '';
        }

        $html = '<ul class="mla-term-list">';
        foreach ( $terms as $term ) {
            $url   = esc_url( add_query_arg( array( sanitize_key( $atts['taxonomy'] ) => $term->slug ), admin_url( 'upload.php' ) ) );
            $html .= '<li><a href="' . $url . '">' . esc_html( $term->name ) . '</a> (' . (int) $term->count . ')</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    function clm_mla_custom_list_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'posts_per_page' => 20,
                'meta_key'       => '',
                'media_category' => '',
                'media_tag'      => '',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ),
            $atts,
            'mla_custom_list'
        );

        $args = clm_mla_build_media_query( $atts );
        if ( ! empty( $atts['meta_key'] ) ) {
            $args['meta_key'] = sanitize_key( $atts['meta_key'] );
        }

        $q = new WP_Query( $args );
        if ( ! $q->have_posts() ) {
            return '';
        }

        $html = '<ul class="mla-custom-list">';
        while ( $q->have_posts() ) {
            $q->the_post();
            $id    = get_the_ID();
            $title = get_the_title() ? get_the_title() : basename( get_attached_file( $id ) );
            $url   = wp_get_attachment_url( $id );
            $html .= '<li><a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a></li>';
        }
        wp_reset_postdata();
        $html .= '</ul>';

        return $html;
    }

    function clm_mla_archive_list_shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'limit' => 24,
            ),
            $atts,
            'mla_archive_list'
        );

        global $wpdb;
        $limit = max( 1, min( 120, (int) $atts['limit'] ) );

        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DATE_FORMAT(post_date, '%%Y-%%m') AS ym, COUNT(ID) AS total
                 FROM {$wpdb->posts}
                 WHERE post_type = 'attachment' AND post_status = 'inherit'
                 GROUP BY ym
                 ORDER BY ym DESC
                 LIMIT %d",
                $limit
            )
        );

        if ( ! $rows ) {
            return '';
        }

        $html = '<ul class="mla-archive-list">';
        foreach ( $rows as $row ) {
            $html .= '<li>' . esc_html( $row->ym ) . ' (' . (int) $row->total . ')</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    function clm_mla_register_metadata_processing() {
        add_action( 'add_attachment', 'clm_mla_capture_attachment_metadata', 20 );
    }

    function clm_mla_capture_attachment_metadata( $attachment_id ) {
        $file = get_attached_file( $attachment_id );
        if ( ! $file || ! file_exists( $file ) ) {
            return;
        }

        $mime = get_post_mime_type( $attachment_id );
        $meta = array(
            'mime_type' => $mime,
        );

        if ( str_starts_with( (string) $mime, 'image/' ) ) {
            $img = wp_read_image_metadata( $file );
            if ( is_array( $img ) ) {
                if ( ! empty( $img['camera'] ) ) {
                    $meta['camera'] = $img['camera'];
                }
                if ( ! empty( $img['created_timestamp'] ) ) {
                    $meta['created_timestamp'] = (int) $img['created_timestamp'];
                }
                if ( ! empty( $img['title'] ) ) {
                    $meta['iptc_title'] = $img['title'];
                }
                if ( ! empty( $img['caption'] ) ) {
                    $meta['iptc_caption'] = $img['caption'];
                }
                if ( ! empty( $img['credit'] ) ) {
                    $meta['iptc_credit'] = $img['credit'];
                }
            }

            $xmp = clm_mla_extract_xmp_subject( $file );
            if ( $xmp ) {
                $meta['xmp_subject'] = $xmp;
            }
        }

        if ( 'application/pdf' === $mime ) {
            $pages = clm_mla_count_pdf_pages( $file );
            if ( $pages > 0 ) {
                $meta['pdf_pages'] = $pages;
            }
        }

        update_post_meta( $attachment_id, '_mla_meta_cache', $meta );
    }

    function clm_mla_extract_xmp_subject( $file ) {
        $handle = fopen( $file, 'rb' );
        if ( ! $handle ) {
            return '';
        }

        $chunk = fread( $handle, 300000 );
        fclose( $handle );

        if ( ! $chunk ) {
            return '';
        }

        if ( preg_match( '/<dc:subject>.*?<rdf:li>(.*?)<\/rdf:li>.*?<\/dc:subject>/si', $chunk, $m ) ) {
            return wp_strip_all_tags( $m[1] );
        }

        return '';
    }

    function clm_mla_count_pdf_pages( $file ) {
        $content = @file_get_contents( $file, false, null, 0, 800000 );
        if ( false === $content ) {
            return 0;
        }

        if ( preg_match_all( '/\/Type\s*\/Page\b/', $content, $matches ) ) {
            return count( $matches[0] );
        }

        return 0;
    }

    function clm_mla_register_quick_and_bulk_edit() {
        add_action( 'quick_edit_custom_box', 'clm_mla_quick_edit_fields', 10, 2 );
        add_action( 'admin_footer-upload.php', 'clm_mla_quick_edit_script' );
        add_action( 'save_post_attachment', 'clm_mla_save_quick_edit' );

        add_filter( 'bulk_actions-upload', 'clm_mla_register_bulk_action' );
        add_filter( 'handle_bulk_actions-upload', 'clm_mla_handle_bulk_action', 10, 3 );
    }

    function clm_mla_quick_edit_fields( $column_name, $post_type ) {
        if ( 'attachment' !== $post_type || 'media_category' !== $column_name ) {
            return;
        }
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-group">
                <label>
                    <span class="title">Media Category (slug)</span>
                    <span class="input-text-wrap">
                        <input type="text" name="clm_mla_media_category" value="" />
                    </span>
                </label>
                <label>
                    <span class="title">Media Tags (slug, comma)</span>
                    <span class="input-text-wrap">
                        <input type="text" name="clm_mla_media_tag" value="" />
                    </span>
                </label>
            </div>
        </fieldset>
        <?php
    }

    function clm_mla_quick_edit_script() {
        ?>
        <script>
        (function($){
            var $wpInlineEdit = inlineEditPost.edit;
            inlineEditPost.edit = function( id ) {
                $wpInlineEdit.apply( this, arguments );
                var postId = 0;
                if ( typeof(id) === 'object' ) {
                    postId = parseInt( this.getId(id), 10 );
                }
                if ( postId > 0 ) {
                    var $editRow = $( '#edit-' + postId );
                    $editRow.find('input[name="clm_mla_media_category"]').val('');
                    $editRow.find('input[name="clm_mla_media_tag"]').val('');
                }
            };
        })(jQuery);
        </script>
        <?php
    }

    function clm_mla_save_quick_edit( $post_id ) {
        if ( ! isset( $_REQUEST['_inline_edit'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_inline_edit'] ) ), 'inlineeditnonce' ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_REQUEST['clm_mla_media_category'] ) ) {
            $category = sanitize_title( wp_unslash( $_REQUEST['clm_mla_media_category'] ) );
            if ( $category ) {
                wp_set_object_terms( $post_id, array( $category ), 'media_category', false );
            }
        }

        if ( isset( $_REQUEST['clm_mla_media_tag'] ) ) {
            $tags_raw = sanitize_text_field( wp_unslash( $_REQUEST['clm_mla_media_tag'] ) );
            $tags     = array_filter( array_map( 'sanitize_title', array_map( 'trim', explode( ',', $tags_raw ) ) ) );
            if ( $tags ) {
                wp_set_object_terms( $post_id, $tags, 'media_tag', false );
            }
        }
    }

    function clm_mla_register_bulk_action( $actions ) {
        $actions['clm_mla_set_tag'] = 'Set Media Tag: featured';
        return $actions;
    }

    function clm_mla_handle_bulk_action( $redirect_to, $doaction, $post_ids ) {
        if ( 'clm_mla_set_tag' !== $doaction ) {
            return $redirect_to;
        }

        $changed = 0;
        foreach ( $post_ids as $post_id ) {
            if ( 'attachment' !== get_post_type( $post_id ) ) {
                continue;
            }
            wp_set_object_terms( (int) $post_id, array( 'featured' ), 'media_tag', true );
            $changed++;
        }

        return add_query_arg( 'clm_mla_bulk_updated', $changed, $redirect_to );
    }

    add_action(
        'admin_notices',
        static function() {
            if ( ! isset( $_GET['clm_mla_bulk_updated'] ) ) {
                return;
            }
            $count = (int) $_GET['clm_mla_bulk_updated'];
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $count ) . ' media item(s) updated.</p></div>';
        }
    );
}
