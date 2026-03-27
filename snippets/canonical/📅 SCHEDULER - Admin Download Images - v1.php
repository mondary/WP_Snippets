/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/068__id-127__admin-download-images.php
 * Display name: ADMIN - Download images
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 127
 * Online modified: 2025-08-17 10:20:09
 * Online revision: 3
 * Exact duplicate group: non
 * Version family: ADMIN - Download images (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/068__id-127__admin-download-images.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, admin_notices, admin_menu
 * Fonctions clefs: mondary_auto_download_images, mondary_is_local_image, mondary_download_external_image, mondary_images_download_notice
 * Lignes / octets (brut): 224 / 7870
 * Hash code normalise (sha256): 491b5e932c2cd34abc21d91970eb7d3fcd002482a9974a84d487951a23b37116
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-download-images__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-download-images__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, automatisation date/programmation, 3 hook(s) WP, 4 fonction(s) clef
 * Features detectees: admin-ui, scheduler-date, svg-ui, cache-transient
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, admin_notices, admin_menu
 * Fonctions clefs: mondary_auto_download_images, mondary_is_local_image, mondary_download_external_image, mondary_images_download_notice
 * APIs WP detectees: add_action, wp_is_post_revision, wp_is_post_autosave, wp_get_attachment_url, wp_update_post, get_site_url, wp_remote_get, get_bloginfo, is_wp_error, wp_remote_retrieve_body, wp_upload_bits, wp_check_filetype, wp_insert_attachment, wp_generate_attachment_metadata, wp_update_attachment_metadata … (+3)
 * Signatures contenu: html-markup
 * Lignes / octets: 236 / 8496
 * Empreinte code (sha256): 4f9277bfc29ce7c07e6396b52fcff8d73208d566f47e3572c8cb5ecde43e32a8
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-download-images__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-download-images__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: performance_optimization, admin_ui_settings, media_images
 * Domaine: global
 * Confiance: medium
 * Scores (top): scheduler_posts=8, performance_optimization=5, admin_ui_settings=4, media_images=4, frontend_ui_widget=2
 * Raisons principales: scheduler-date, schedule
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Auto-download external images to media library
 * Ce snippet télécharge automatiquement toutes les images externes 
 * lors de la sauvegarde d'un article et met à jour les URLs
 */

// Hook sur la sauvegarde des articles
add_action('save_post', 'mondary_auto_download_images', 10, 2);

function mondary_auto_download_images($post_id, $post) {
    // Éviter les révisions et les sauvegardes automatiques
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    
    // Éviter les boucles infinies
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Vérifier les permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Traiter uniquement les articles (posts)
    if ($post->post_type !== 'post') {
        return;
    }
    
    $content = $post->post_content;
    $updated_content = $content;
    $images_downloaded = 0;
    
    // Pattern pour détecter les images markdown et HTML
    $patterns = [
        // Markdown: ![alt](url)
        '/!\[([^\]]*)\]\(([^)]+\.(jpg|jpeg|png|gif|webp|svg))[^)]*\)/i',
        // HTML: <img src="url">
        '/<img[^>]+src=["\']([^"\']+\.(jpg|jpeg|png|gif|webp|svg))[^"\']*["\']/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (strpos($pattern, 'img') !== false) {
                    // HTML img tag
                    $full_match = $match[0];
                    $image_url = $match[1];
                } else {
                    // Markdown format
                    $full_match = $match[0];
                    $alt_text = $match[1];
                    $image_url = $match[2];
                }
                
                // Vérifier que c'est une URL externe
                if (filter_var($image_url, FILTER_VALIDATE_URL) && 
                    !mondary_is_local_image($image_url)) {
                    
                    // Télécharger l'image
                    $attachment_id = mondary_download_external_image($image_url, $post_id);
                    
                    if ($attachment_id) {
                        $new_url = wp_get_attachment_url($attachment_id);
                        
                        if ($new_url) {
                            // Remplacer l'URL dans le contenu
                            $updated_content = str_replace($image_url, $new_url, $updated_content);
                            $images_downloaded++;
                            
                            // Log pour debug
                            error_log("Mondary: Image téléchargée - {$image_url} -> {$new_url}");
                        }
                    }
                }
            }
        }
    }
    
    // Mettre à jour le contenu si des images ont été téléchargées
    if ($images_downloaded > 0) {
        // Éviter une nouvelle sauvegarde en boucle
        remove_action('save_post', 'mondary_auto_download_images', 10);
        
        wp_update_post([
            'ID' => $post_id,
            'post_content' => $updated_content
        ]);
        
        // Remettre le hook
        add_action('save_post', 'mondary_auto_download_images', 10, 2);
        
        // Notification
        set_transient('mondary_images_downloaded_' . $post_id, $images_downloaded, 60);
    }
}

function mondary_is_local_image($url) {
    $site_url = get_site_url();
    return strpos($url, $site_url) === 0;
}

function mondary_download_external_image($image_url, $post_id = 0) {
    // Vérifications de sécurité
    if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // Vérifier la taille et le type
    $image_data = wp_remote_get($image_url, [
        'timeout' => 30,
        'headers' => [
            'User-Agent' => 'Mozilla/5.0 (compatible; WordPress/' . get_bloginfo('version') . ')'
        ]
    ]);
    
    if (is_wp_error($image_data)) {
        return false;
    }
    
    $image_body = wp_remote_retrieve_body($image_data);
    $image_size = strlen($image_body);
    
    // Limite de taille (5MB max)
    if ($image_size > 5 * 1024 * 1024) {
        error_log("Mondary: Image trop lourde - {$image_url} ({$image_size} bytes)");
        return false;
    }
    
    // Déterminer l'extension
    $pathinfo = pathinfo(parse_url($image_url, PHP_URL_PATH));
    $extension = isset($pathinfo['extension']) ? $pathinfo['extension'] : 'jpg';
    
    // Créer un nom de fichier unique
    $filename = sanitize_file_name(
        'external-' . md5($image_url) . '.' . $extension
    );
    
    // Upload vers la médiathèque
    $upload = wp_upload_bits($filename, null, $image_body);
    
    if ($upload['error']) {
        error_log("Mondary: Erreur upload - {$upload['error']}");
        return false;
    }
    
    // Créer l'attachment
    $attachment = [
        'guid' => $upload['url'],
        'post_mime_type' => wp_check_filetype($filename)['type'],
        'post_title' => 'Image externe - ' . basename($image_url),
        'post_content' => '',
        'post_status' => 'inherit'
    ];
    
    $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
    
    if ($attachment_id) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attachment_data);
        
        // Ajouter des meta pour traçage
        update_post_meta($attachment_id, '_mondary_original_url', $image_url);
        update_post_meta($attachment_id, '_mondary_downloaded_date', current_time('mysql'));
        
        return $attachment_id;
    }
    
    return false;
}

// Notification dans l'admin
add_action('admin_notices', 'mondary_images_download_notice');

function mondary_images_download_notice() {
    global $post;
    
    if (is_admin() && isset($post->ID)) {
        $images_count = get_transient('mondary_images_downloaded_' . $post->ID);
        
        if ($images_count) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>Mondary:</strong> ' . $images_count . ' image(s) externe(s) téléchargée(s) automatiquement dans la médiathèque.</p>';
            echo '</div>';
            
            delete_transient('mondary_images_downloaded_' . $post->ID);
        }
    }
}

// Option pour désactiver si besoin
add_action('admin_menu', function() {
    add_options_page(
        'Mondary Auto Images',
        'Mondary Auto Images', 
        'manage_options',
        'mondary-auto-images',
        function() {
            echo '<div class="wrap">';
            echo '<h1>Mondary - Téléchargement automatique d\'images</h1>';
            echo '<p>Ce plugin télécharge automatiquement toutes les images externes lors de la sauvegarde d\'articles.</p>';
            echo '<p><strong>Formats supportés:</strong> jpg, jpeg, png, gif, webp, svg</p>';
            echo '<p><strong>Limite de taille:</strong> 5MB par image</p>';
            echo '<p><strong>Fonctionnement:</strong> Les URLs sont automatiquement remplacées par les versions locales.</p>';
            echo '</div>';
        }
    );
});
