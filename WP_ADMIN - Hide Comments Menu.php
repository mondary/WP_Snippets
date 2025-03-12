<?php
/**
 * Hide WordPress Comments Menu from Admin Bar
 * Description: This script removes the Comments menu item from the WordPress admin bar
 * Version: 1.0
 */

add_action('admin_head', function() {
    echo '<style>
        /* Hide Comments menu item from admin bar */
        #wp-admin-bar-comments {
            display: none !important;
        }
        
        /* Hide Comments menu item from admin sidebar */
        #menu-comments {
            display: none !important;
        }
        
        /* Hide Comments submenu items if they exist */
        .wp-submenu a[href*="edit-comments"] {
            display: none !important;
        }
    </style>';
});

// Also remove the menu item from the admin bar programmatically
add_action('admin_bar_menu', function($wp_admin_bar) {
    $wp_admin_bar->remove_node('comments');
}, 999);