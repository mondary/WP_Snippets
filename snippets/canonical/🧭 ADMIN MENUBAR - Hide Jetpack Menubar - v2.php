<?php
/*
 * 🧭 ADMIN MENUBAR - Hide Jetpack Menubar - v2
 * Masque le menu Jetpack de la barre d'admin et du frontend.
 */

add_action('admin_head', 'hide_jetpack_stats_styles');
add_action('wp_head', 'hide_jetpack_stats_styles');

function hide_jetpack_stats_styles() {
    echo '<style>
        /* Hide Jetpack stats menu item from admin bar */
        #wp-admin-bar-jetpack-stats, #wp-admin-bar-stats {
            display: none !important;
        }
    </style>';
}

// Remove the menu item from the admin bar programmatically (both frontend and backend)
add_action('admin_bar_menu', 'remove_jetpack_stats_menu', 999);

function remove_jetpack_stats_menu($wp_admin_bar) {
    $wp_admin_bar->remove_node('jetpack-stats');
    $wp_admin_bar->remove_node('stats');
}
