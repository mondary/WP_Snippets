<?php
/*
 * Display name: ADMIN SETTINGS - Fusion OutilsReglages
 * Scope: global
 */
if (!defined('ABSPATH')) exit;
if (!function_exists('clm_merge_tools_into_settings_v4')) {
function clm_merge_tools_into_settings_v4() {
    global $submenu;
    if (empty($submenu['tools.php']) || empty($submenu['options-general.php'])) return;
    $dest =& $submenu['options-general.php'];
    $src = $submenu['tools.php'];
    $dest[] = array(__('Outils'), 'manage_options', 'tools.php');
    foreach ($src as $item) if (is_array($item) && !empty($item[2])) $dest[] = $item;
    $seen = array();
    $dedup = array();
    foreach ($dest as $item) {
        $slug = isset($item[2]) ? (string) $item[2] : '';
        if ($slug === '' || isset($seen[$slug])) continue;
        $seen[$slug] = true;
        $dedup[] = $item;
    }
    usort($dedup, function($a, $b) {
        $ta = isset($a[0]) ? wp_strip_all_tags((string) $a[0]) : '';
        $tb = isset($b[0]) ? wp_strip_all_tags((string) $b[0]) : '';
        return strcasecmp($ta, $tb);
    });
    $submenu['options-general.php'] = $dedup;
    remove_menu_page('tools.php');
}}
add_action('admin_menu', 'clm_merge_tools_into_settings_v4', 999);
