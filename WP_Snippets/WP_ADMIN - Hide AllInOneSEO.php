<?php
/**
 * Hide All in One SEO menu from WordPress admin bar (both frontend and backend)
 * Description: This script removes the All in One SEO menu item from the WordPress admin bar everywhere
 * Version: 1.1
 */

// Add CSS to both admin and frontend
add_action('admin_head', 'hide_aioseo_styles');
add_action('wp_head', 'hide_aioseo_styles');

function hide_aioseo_styles() {
    echo '<style>
        /* Hide All in One SEO menu item from admin bar */
        #wp-admin-bar-aioseo-main {
            display: none !important;
        }
        
        /* Hide All in One SEO submenu items if they exist */
        .wp-submenu a[href*="aioseo"] {
            display: none !important;
        }
    </style>';
}

// Remove the menu item from the admin bar programmatically (both frontend and backend)
add_action('admin_bar_menu', 'remove_aioseo_menu', 999);

function remove_aioseo_menu($wp_admin_bar) {
    $wp_admin_bar->remove_node('aioseo-main');
}