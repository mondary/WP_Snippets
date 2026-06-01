<?php
/*
 * Display name: MEDIA IMAGES - Admin Media Size - v2
 * Scope: global
 */
if (!defined('ABSPATH')) exit;
if (!function_exists('clm_media_size_total_v2')) {
function clm_media_size_total_v2() {
    $k = 'clm_media_size_total_v2';
    $size = get_transient($k);
    if ($size !== false) return (int) $size;
    $u = wp_upload_dir();
    $base = isset($u['basedir']) ? $u['basedir'] : '';
    if (!$base || !is_dir($base) || !is_readable($base)) return 0;
    $size = 0;
    try {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) if ($f->isFile()) $size += (int) $f->getSize();
    } catch (Throwable $e) {
        return 0;
    }
    set_transient($k, (int) $size, HOUR_IN_SECONDS);
    return (int) $size;
}}
if (!function_exists('clm_media_size_notice_v2')) {
function clm_media_size_notice_v2() {
    global $pagenow;
    if ($pagenow !== 'upload.php' || !current_user_can('upload_files')) return;
    $size = clm_media_size_total_v2();
    if ($size <= 0) return;
    echo '<div class="notice notice-info inline"><p style="margin:.5em 0"><strong>Taille totale de la médiathèque :</strong> ' . esc_html(size_format($size)) . '</p></div>';
}}
add_action('admin_notices', 'clm_media_size_notice_v2');
if (!function_exists('clm_media_size_view_link_v2')) {
function clm_media_size_view_link_v2($views) {
    global $pagenow;
    if ($pagenow !== 'upload.php' || !current_user_can('manage_options')) return $views;
    $url = wp_nonce_url(add_query_arg('clm_media_size_recalc', '1'), 'clm_media_size_recalc_v2');
    $views['clm_media_size_recalc'] = '<a href="' . esc_url($url) . '">Recalculer la taille</a>';
    return $views;
}}
add_filter('views_upload', 'clm_media_size_view_link_v2');
if (!function_exists('clm_media_size_handle_recalc_v2')) {
function clm_media_size_handle_recalc_v2() {
    if (!isset($_GET['clm_media_size_recalc']) || $_GET['clm_media_size_recalc'] !== '1') return;
    if (!current_user_can('manage_options')) wp_die('Accès refusé');
    check_admin_referer('clm_media_size_recalc_v2');
    delete_transient('clm_media_size_total_v2');
    wp_safe_redirect(remove_query_arg(array('_wpnonce', 'clm_media_size_recalc')));
    exit;
}}
add_action('admin_init', 'clm_media_size_handle_recalc_v2');
