<?php
/**
 * Plugin pour définir automatiquement l'image à la une comme image par défaut pour Jetpack.
 */

// Action pour définir l'image à la une comme image par défaut pour Jetpack
add_action('save_post', 'set_jetpack_default_image', 10, 2);

function set_jetpack_default_image($post_id, $post) {
    // Vérifie si le post est un article et s'il est publié
    if ($post->post_type !== 'post' || $post->post_status !== 'publish') {
        return;
    }

    // Vérifie si l'image à la une est définie
    if (has_post_thumbnail($post_id)) {
        // Récupère l'ID de l'image à la une
        $thumbnail_id = get_post_thumbnail_id($post_id);
        
        // Récupère l'URL de l'image à la une
        $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'full');

        // Met à jour l'option Jetpack pour l'image par défaut
        if (function_exists('jetpack_set_default_image')) {
            jetpack_set_default_image($thumbnail_url);
        }
    }
}

// Assurez-vous que l'image par défaut est mise à jour lors de la mise à jour de l'image à la une
add_action('edit_post', 'set_jetpack_default_image', 10, 2);
?>
