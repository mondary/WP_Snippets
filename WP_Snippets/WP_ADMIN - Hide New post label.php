<?php
/**
 * Hide the 'Create' text label next to the '+' icon in WordPress admin bar
 * while keeping the icon visible
 */

add_action('admin_head', function() {
    echo '<style>
        /* Hide text but keep the icon */
        #wp-admin-bar-new-content .ab-label {
            display: none !important;
        }
        
        /* Adjust spacing for better visual appearance */
        #wp-admin-bar-new-content .ab-icon {
            margin-right: 0 !important;
        }
    </style>';
});