<?php
/*
 * 🧭 ADMIN MENUBAR - Hide New Post Label - v2
 * Masque le libellé « + Nouveau » de la barre d'administration.
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
