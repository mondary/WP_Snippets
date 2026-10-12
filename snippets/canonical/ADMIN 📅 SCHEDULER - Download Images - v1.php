<?php
/*
 * ADMIN 📅 SCHEDULER - Download Images - v1
 * Téléchargement d'images distantes — ancien nom, service assuré par #211.
 */

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
