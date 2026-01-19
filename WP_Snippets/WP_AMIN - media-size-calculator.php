<?php
/**
 * Plugin Name: Calculateur de Poids pour la Médiathèque
 * Description: Affiche la taille totale de tous les fichiers dans la médiathèque WordPress.
 * Version: 1.1
 * Author: Gemini
 */

// Sécurité : empêche l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Calcule la taille totale du dossier des uploads.
 * Utilise un "transient" (cache) pour ne pas ralentir le site en recalculant à chaque fois.
 *
 * @return int La taille totale en octets (bytes).
 */
function get_media_library_total_size() {
    // Nom du cache (transient)
    $transient_name = 'media_library_total_size';

    // 1. On essaie de récupérer la valeur depuis le cache
    $total_size = get_transient( $transient_name );

    // 2. Si le cache est vide ou a expiré, on recalcule
    if ( false === $total_size ) {
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['basedir'];
        $total_size = 0;

        try {
            // Itérateur pour parcourir tous les fichiers et sous-dossiers
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($upload_path, FilesystemIterator::SKIP_DOTS)
            );

            foreach ( $iterator as $file ) {
                // On s'assure que c'est bien un fichier
                if ( $file->isFile() ) {
                    $total_size += $file->getSize();
                }
            }
        } catch ( Exception $e ) {
            // Gérer une éventuelle erreur si le dossier n'est pas lisible
            return 0;
        }

        // 3. On stocke le nouveau résultat dans le cache pour 1 heure (3600 secondes)
        set_transient( $transient_name, $total_size, 3600 );
    }

    return (int) $total_size;
}

/**
 * Affiche la taille totale sur la page de la médiathèque.
 */
function display_media_library_total_size() {
    // On s'assure d'être sur la bonne page de l'admin (upload.php = médiathèque)
    global $pagenow;
    if ( 'upload.php' !== $pagenow ) {
        return;
    }

    // Récupère la taille calculée
    $size_in_bytes = get_media_library_total_size();

    // Si la taille est nulle, on n'affiche rien
    if ( $size_in_bytes <= 0 ) {
        return;
    }

    // WordPress a une super fonction pour formater les octets en Ko, Mo, Go...
    $formatted_size = size_format( $size_in_bytes );

    // Affiche le message dans une "notice" WordPress
    echo '<div class="notice notice-info inline">';
    echo '<p style="margin: 0.5em 0;">';
    echo '🖼️&nbsp; <strong>Taille totale de la médiathèque :</strong> ' . esc_html( $formatted_size );
    echo '</p></div>';
}

// On accroche notre fonction au hook 'admin_notices' qui s'exécute sur les pages d'admin
add_action( 'admin_notices', 'display_media_library_total_size' );

/**
 * Ajoute un bouton pour forcer le recalcul du cache.
 */
function add_recalculate_button_to_media_page( $views ) {
    global $pagenow;
    if ( 'upload.php' !== $pagenow ) {
        return $views;
    }

    $recalc_url = add_query_arg( 'recalculate_media_size', 'true' );
    $views['recalculate_size'] = '<a href="' . esc_url( $recalc_url ) . '">Forcer le recalcul de la taille</a>';
    
    return $views;
}

// On accroche le bouton aux filtres de la médiathèque
add_filter('views_upload', 'add_recalculate_button_to_media_page');

/**
 * Gère la demande de recalcul.
 */
function handle_recalculate_request() {
    if ( isset( $_GET['recalculate_media_size'] ) && 'true' === $_GET['recalculate_media_size'] ) {
        // On supprime le cache pour forcer le recalcul au prochain chargement
        delete_transient( 'media_library_total_size' );
        
        // On redirige pour nettoyer l'URL
        wp_safe_redirect( remove_query_arg( 'recalculate_media_size' ) );
        exit;
    }
}

add_action( 'admin_init', 'handle_recalculate_request' );
