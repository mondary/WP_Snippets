/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/070__id-130__admin-cli-featured-fixed.php
 * Display name: ADMIN - CLI featured fixed ✅
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 130
 * Online modified: 2025-08-27 15:48:15
 * Online revision: 6
 * Exact duplicate group: non
 * Version family: ADMIN - CLI featured fixed ✅ (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/070__id-130__admin-cli-featured-fixed.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, post_thumbnail_html
 * Fonctions clefs: mondary_auto_featured_image_from_url, mondary_download_and_set_featured_image, mondary_display_featured_image_fallback
 * Lignes / octets (brut): 105 / 3379
 * Hash code normalise (sha256): 4cb044e713c27aa63b6641ea65d0c35c92e683631ede26985d0e737c59224336
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-cli-featured-fixed__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-cli-featured-fixed__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, post_thumbnail_html
 * Fonctions clefs: mondary_auto_featured_image_from_url, mondary_download_and_set_featured_image, mondary_display_featured_image_fallback
 * APIs WP detectees: add_action, get_post_meta, is_wp_error, add_filter, get_the_title
 * Signatures contenu: html-markup
 * Lignes / octets: 117 / 4000
 * Empreinte code (sha256): 010e163de0f78c91057227dc90a96828bc7f78b445268a473c5fdfe25e5747b1
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-cli-featured-fixed__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-cli-featured-fixed__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: media_images
 * Clusters secondaires: aucun
 * Domaine: global
 * Confiance: low
 * Scores (top): media_images=4
 * Raisons principales: image
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * WPCode Function: Featured Image from URL
 * 
 * This function handles featured images from custom field URLs.
 * Automatically downloads images and sets them as WordPress featured images.
 */

// Hook into post save to process featured image URLs
add_action( 'save_post', 'mondary_auto_featured_image_from_url', 10, 2 );
function mondary_auto_featured_image_from_url( $post_id, $post ) {
    // Skip if this is an autosave or revision
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    
    // Only process posts
    if ( $post->post_type !== 'post' ) {
        return;
    }
    
    // Skip if already has featured image
    if ( has_post_thumbnail( $post_id ) ) {
        return;
    }
    
    // Get the custom field URL
    $image_url = get_post_meta( $post_id, 'featured_image_url', true );
    
    if ( empty( $image_url ) ) {
        return;
    }
    
    // Download and set featured image
    $attachment_id = mondary_download_and_set_featured_image( $image_url, $post_id );
    
    if ( $attachment_id ) {
        set_post_thumbnail( $post_id, $attachment_id );
        // Clean up the custom field after successful upload
        delete_post_meta( $post_id, 'featured_image_url' );
    }
}

function mondary_download_and_set_featured_image( $image_url, $post_id ) {
    // Include WordPress media functions
    if ( ! function_exists( 'media_handle_sideload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
    }
    
    // Download image to temp file
    $temp_file = download_url( $image_url );
    
    if ( is_wp_error( $temp_file ) ) {
        return false;
    }
    
    // File array for media_handle_sideload
    $file_array = array(
        'name' => basename( $image_url ),
        'tmp_name' => $temp_file
    );
    
    // Upload to media library
    $attachment_id = media_handle_sideload( $file_array, $post_id );
    
    // Clean up temp file
    @unlink( $temp_file );
    
    if ( is_wp_error( $attachment_id ) ) {
        return false;
    }
    
    return $attachment_id;
}

// Fallback: Display image from URL if no featured image is set
add_filter( 'post_thumbnail_html', 'mondary_display_featured_image_fallback', 10, 5 );
function mondary_display_featured_image_fallback( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
    // Only provide fallback if no actual featured image
    if ( empty( $html ) ) {
        $image_url = get_post_meta( $post_id, 'featured_image_url', true );
        
        if ( ! empty( $image_url ) ) {
            $alt_text = get_the_title( $post_id );
            $html = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt_text ) . '" />';
        }
    }
    
    return $html;
}
