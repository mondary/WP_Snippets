<?php
/**
 * Hide the site name in WordPress admin bar
 * while keeping the WordPress icon visible
 */

add_action('admin_head', function() {
    echo '<style>
        /* Hide site name text but keep the icon */
        #wpadminbar #wp-admin-bar-site-name .ab-item:first-child {
            font-size: 0;
            padding-right: 0 !important;
        }
        
        /* Keep the dashicon visible */
        #wpadminbar #wp-admin-bar-site-name > .ab-item:before {
            font-size: 20px;
            width: 20px;
        }
        
        /* Hide the site name in the hover menu */
        #wpadminbar .quicklinks li#wp-admin-bar-site-name.hover > .ab-item {
            font-size: 0;
        }
    </style>';
});