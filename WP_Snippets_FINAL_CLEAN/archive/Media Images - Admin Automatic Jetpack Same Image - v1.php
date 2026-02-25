
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_ADMIN - Automatic jetpack same image.php
 * Display name: WP_ADMIN - Automatic jetpack same image
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Automatic jetpack same image (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Automatic jetpack same image.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: jetpack
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, edit_post
 * Fonctions clefs: set_jetpack_default_image
 * Lignes / octets (brut): 32 / 1162
 * Hash code normalise (sha256): a13086345bd7fa877d66ec6c73a174eedbb9c077e9a33ace41cd715345d3ea74
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__admin__wp-admin-automatic-jetpack-same-image__v1__src-archives.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__admin__wp-admin-automatic-jetpack-same-image__v1__src-archives.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: media_images
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: low
 * Scores (top): media_images=4
 * Raisons principales: image
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

inal: canonical
 * Source root: archives
 * Source path: archives/WP_ADMIN - Automatic jetpack same image.php
 * Display name: WP_ADMIN - Automatic jetpack same image
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Automatic jetpack same image (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Automatic jetpack same image.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: jetpack
 * Dependances probables: WordPress core hooks
 * Hooks WP: save_post, edit_post
 * Fonctions clefs: set_jetpack_default_image
 * Lignes / octets (brut): 32 / 1162
 * Hash code normalise (sha256): a13086345bd7fa877d66ec6c73a174eedbb9c077e9a33ace41cd715345d3ea74
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

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
