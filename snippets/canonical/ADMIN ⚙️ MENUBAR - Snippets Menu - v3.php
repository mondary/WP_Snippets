<?php
/*
 * ADMIN ⚙️ MENUBAR - Snippets Menu - v3
 * Icône « snippets » dans la barre d'admin vers la page Code Snippets.
 */

add_action('admin_bar_menu', function($admin_bar) {
    $admin_bar->add_node([
        'id'    => 'wpcode_snippets',
        'title' => '<span class="ab-icon dashicons dashicons-editor-code"></span>',
        'href'  => admin_url('admin.php?page=snippets&status=active'),
        'meta'  => [
            'title' => 'Accéder aux snippets',
        ],
    ]);
}, 100);
