<?php
/**
 * PKpremium remote sync endpoints.
 */

if (!defined('ABSPATH')) {
    exit;
}

function pkpremium_sync_can_manage_plugin() {
    return current_user_can('activate_plugins') || current_user_can('manage_options');
}

function pkpremium_sync_normalize_relative_path($path) {
    $path = wp_normalize_path((string) $path);
    $path = ltrim($path, '/');

    if ($path === '' || strpos($path, '../') !== false) {
        return '';
    }

    return $path;
}

function pkpremium_sync_collect_local_files($base_dir, $relative_dir = '') {
    $results = array();
    $dir = trailingslashit($base_dir . ($relative_dir !== '' ? '/' . $relative_dir : ''));

    if (!is_dir($dir)) {
        return $results;
    }

    $entries = scandir($dir);
    if (!is_array($entries)) {
        return $results;
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $relative_path = ltrim($relative_dir . '/' . $entry, '/');
        $full_path = $dir . $entry;

        if (is_dir($full_path)) {
            $results = array_merge($results, pkpremium_sync_collect_local_files($base_dir, $relative_path));
            continue;
        }

        if (!is_file($full_path)) {
            continue;
        }

        $results[] = array(
            'path' => $relative_path,
            'size' => filesize($full_path),
            'sha1' => sha1_file($full_path),
        );
    }

    return $results;
}

function pkpremium_register_sync_routes() {
    register_rest_route('pkpremium/v1', '/sync-plugin/manifest', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'pkpremium_sync_plugin_manifest',
        'permission_callback' => 'pkpremium_sync_can_manage_plugin',
    ));

    register_rest_route('pkpremium/v1', '/sync-plugin', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'pkpremium_sync_plugin_files',
        'permission_callback' => 'pkpremium_sync_can_manage_plugin',
    ));
}

add_action('rest_api_init', 'pkpremium_register_sync_routes');

function pkpremium_sync_plugin_manifest() {
    return new WP_REST_Response(array(
        'success' => true,
        'plugin' => PKPREMIUM_BASENAME,
        'version' => PKPREMIUM_VERSION,
        'base_dir' => basename(PKPREMIUM_DIR),
        'files' => pkpremium_sync_collect_local_files(untrailingslashit(PKPREMIUM_DIR)),
    ), 200);
}

function pkpremium_sync_plugin_files(WP_REST_Request $request) {
    $payload = $request->get_json_params();

    if (!is_array($payload)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => 'Payload JSON invalide.',
        ), 400);
    }

    $files = isset($payload['files']) && is_array($payload['files']) ? $payload['files'] : array();
    $delete_paths = isset($payload['delete_paths']) && is_array($payload['delete_paths']) ? $payload['delete_paths'] : array();
    $dry_run = !empty($payload['dry_run']);
    $activate = array_key_exists('activate', $payload) ? (bool) $payload['activate'] : true;

    $written = array();
    $deleted = array();
    $errors = array();
    $base_dir = untrailingslashit(PKPREMIUM_DIR);

    foreach ($files as $file) {
        $relative_path = isset($file['path']) ? pkpremium_sync_normalize_relative_path($file['path']) : '';
        $content_b64 = isset($file['content_b64']) ? (string) $file['content_b64'] : '';

        if ($relative_path === '' || $content_b64 === '') {
            $errors[] = 'Fichier invalide ignore.';
            continue;
        }

        $decoded = base64_decode($content_b64, true);
        if (!is_string($decoded)) {
            $errors[] = 'Base64 invalide pour ' . $relative_path;
            continue;
        }

        $target = $base_dir . '/' . $relative_path;
        $parent = dirname($target);

        if (!$dry_run && !wp_mkdir_p($parent)) {
            $errors[] = 'Impossible de creer le dossier ' . $relative_path;
            continue;
        }

        if (!$dry_run && file_put_contents($target, $decoded) === false) {
            $errors[] = 'Echec ecriture ' . $relative_path;
            continue;
        }

        $written[] = $relative_path;
    }

    foreach ($delete_paths as $path) {
        $relative_path = pkpremium_sync_normalize_relative_path($path);
        if ($relative_path === '') {
            continue;
        }

        $target = $base_dir . '/' . $relative_path;
        if (!file_exists($target)) {
            continue;
        }

        if (!$dry_run && !is_dir($target) && !unlink($target)) {
            $errors[] = 'Impossible de supprimer ' . $relative_path;
            continue;
        }

        if (!is_dir($target)) {
            $deleted[] = $relative_path;
        }
    }

    if (!$dry_run && $activate && function_exists('activate_plugin')) {
        activate_plugin(PKPREMIUM_BASENAME, '', false, true);
    }

    update_option('pkpremium_last_sync', array(
        'received_at' => current_time('mysql'),
        'written' => $written,
        'deleted' => $deleted,
        'errors' => $errors,
        'dry_run' => $dry_run,
    ), false);

    return new WP_REST_Response(array(
        'success' => empty($errors),
        'message' => empty($errors) ? 'Sync terminee.' : 'Sync terminee avec erreurs.',
        'written' => $written,
        'deleted' => $deleted,
        'errors' => $errors,
        'dry_run' => $dry_run,
        'version' => PKPREMIUM_VERSION,
    ), empty($errors) ? 200 : 207);
}
