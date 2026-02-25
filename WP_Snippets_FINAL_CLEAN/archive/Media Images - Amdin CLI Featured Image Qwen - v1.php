/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/069__id-129__amdin-cli-featured-image-qwen.php
 * Display name: AMDIN - CLI featured image qwen
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 129
 * Online modified: 2025-08-26 14:56:06
 * Online revision: 2
 * Exact duplicate group: non
 * Version family: AMDIN - CLI featured image qwen (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/069__id-129__amdin-cli-featured-image-qwen.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, xmlrpc_call_success_wp_newPost
 * Fonctions clefs: auto_set_featured_image_from_url, download_and_set_featured_image, xmlrpc_set_featured_image_from_url
 * Lignes / octets (brut): 121 / 3850
 * Hash code normalise (sha256): 1ab64ebe5ad533638d52ce623afac4f92c0990dab4de98018b8e273ab937e3bf
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__amdin-cli-featured-image-qwen__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__amdin-cli-featured-image-qwen__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: automatisation date/programmation, 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, xmlrpc_call_success_wp_newPost
 * Fonctions clefs: auto_set_featured_image_from_url, download_and_set_featured_image, xmlrpc_set_featured_image_from_url
 * APIs WP detectees: add_action, wp_is_post_revision, wp_is_post_autosave, get_post_meta, is_wp_error, get_error_message, get_post
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 134 / 4563
 * Empreinte code (sha256): 4ae5291f23e8fcc0c06bf28761328237ebe26a6e878adb6479390f9bba5940ef
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__amdin-cli-featured-image-qwen__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__amdin-cli-featured-image-qwen__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: media_images
 * Clusters secondaires: scheduler_posts
 * Domaine: global
 * Confiance: high
 * Scores (top): media_images=12, scheduler_posts=8
 * Raisons principales: featured image, featured-image, image
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * WPCode Function: Set Featured Image from URL
 * 
 * This function automatically sets a featured image from a URL stored in post meta.
 * To use: Add this to WPCode and activate it.
 * 
 * Usage in your script:
 * 1. Create post with custom field 'featured_image_url'
 * 2. This function will automatically download and set the featured image
 */

// Hook into post save/update
add_action('save_post', 'auto_set_featured_image_from_url', 10, 2);

function auto_set_featured_image_from_url($post_id, $post) {
    // Only process posts, not revisions or autosaves
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    
    // Only process if no featured image is already set
    if (has_post_thumbnail($post_id)) {
        return;
    }
    
    // Get the featured image URL from custom field
    $featured_image_url = get_post_meta($post_id, 'featured_image_url', true);
    
    if (empty($featured_image_url) || !filter_var($featured_image_url, FILTER_VALIDATE_URL)) {
        return;
    }
    
    // Download and set the featured image
    $image_id = download_and_set_featured_image($featured_image_url, $post_id);
    
    if ($image_id) {
        // Remove the temporary meta field
        delete_post_meta($post_id, 'featured_image_url');
        
        // Log success
        error_log("Featured image set successfully for post {$post_id}: {$featured_image_url}");
    }
}

function download_and_set_featured_image($image_url, $post_id) {
    // Include WordPress file handling functions
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    
    // Download the image
    $temp_file = download_url($image_url);
    
    if (is_wp_error($temp_file)) {
        error_log("Failed to download image: " . $temp_file->get_error_message());
        return false;
    }
    
    // Get file info
    $file_array = array();
    $file_array['name'] = basename($image_url);
    $file_array['tmp_name'] = $temp_file;
    
    // Upload to WordPress media library
    $image_id = media_handle_sideload($file_array, $post_id);
    
    // Clean up temp file
    if (file_exists($temp_file)) {
        unlink($temp_file);
    }
    
    if (is_wp_error($image_id)) {
        error_log("Failed to upload image: " . $image_id->get_error_message());
        return false;
    }
    
    // Set as featured image
    $result = set_post_thumbnail($post_id, $image_id);
    
    if ($result) {
        return $image_id;
    }
    
    return false;
}

// Alternative: Direct function for XML-RPC calls
function xmlrpc_set_featured_image_from_url($post_id, $image_url) {
    if (empty($image_url) || !filter_var($image_url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    return download_and_set_featured_image($image_url, $post_id);
}

// Hook for XML-RPC post creation
add_action('xmlrpc_call_success_wp_newPost', function($post_id, $args) {
    // Check if featured_image_url is in post content or meta
    $post = get_post($post_id);
    
    // Look for featured image URL in post meta or content
    $featured_image_url = get_post_meta($post_id, 'featured_image_url', true);
    
    if ($featured_image_url) {
        download_and_set_featured_image($featured_image_url, $post_id);
        delete_post_meta($post_id, 'featured_image_url');
    }
}, 10, 2);
