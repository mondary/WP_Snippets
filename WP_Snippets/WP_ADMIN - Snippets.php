<?php
/**
 * Ajoute un bouton avec une icône de ciseaux dans la barre d'administration
 * menant vers la page des snippets WPCode
 */

add_action('admin_bar_menu', function($admin_bar) {
    $admin_bar->add_node([
        'id'    => 'wpcode_snippets',
        'title' => '<span class="ab-icon dashicons dashicons-editor-code"></span>',
        'href'  => admin_url('admin.php?page=snippets&status=active'),
        'meta'  => [
            'title' => 'Accéder aux snippets WPCode',
        ],
    ]);
}, 100);
?>