<?php
/**
 * Plugin Name: WP PK Premium
 * Plugin URI: https://mondary.design/
 * Description: Gestion premium Mondary. Reprend l'aperçu futur et servira de base pour les abonnements.
 * Version: 1.15
 * Author: Clement Mondary
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PKPREMIUM_VERSION', '1.15');
define('PKPREMIUM_FILE', __FILE__);
define('PKPREMIUM_DIR', plugin_dir_path(__FILE__));
define('PKPREMIUM_URL', plugin_dir_url(__FILE__));
define('PKPREMIUM_OPTION_KEY', 'pkpremium_settings');
define('PKPREMIUM_BASENAME', plugin_basename(__FILE__));

register_activation_hook(PKPREMIUM_FILE, function () {
    require_once PKPREMIUM_DIR . 'includes/future-preview.php';
    require_once PKPREMIUM_DIR . 'includes/admin.php';
    require_once PKPREMIUM_DIR . 'includes/sync.php';

    if (function_exists('pkpremium_future_site_register_access_role')) {
        pkpremium_future_site_register_access_role();
    }

    if (function_exists('pkpremium_register_default_settings')) {
        pkpremium_register_default_settings();
    }

    if (function_exists('pkpremium_register_login_rewrites')) {
        pkpremium_register_login_rewrites();
    }

    flush_rewrite_rules(false);
});

require_once PKPREMIUM_DIR . 'includes/future-preview.php';
require_once PKPREMIUM_DIR . 'includes/admin.php';
require_once PKPREMIUM_DIR . 'includes/sync.php';
