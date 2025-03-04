<?php
/**
 * Script pour réorganiser les icônes de la barre d'administration WordPress avec drag and drop
 */

// Ajouter les scripts et styles nécessaires pour le drag and drop
add_action('admin_enqueue_scripts', 'enqueue_adminbar_scripts');
add_action('wp_enqueue_scripts', 'enqueue_adminbar_scripts');

function enqueue_adminbar_scripts() {
    if (!is_admin_bar_showing()) return;
    
    wp_enqueue_script('jquery-ui-sortable');
    wp_add_inline_style('admin-bar', '
        #wpadminbar .quicklinks {
            position: relative;
        }
        #wpadminbar .quicklinks > ul > li.sortable-placeholder {
            border: 2px dashed #ccc;
            background: rgba(255,255,255,0.2);
            margin: 0 2px;
        }
        #wpadminbar .quicklinks > ul > li.ui-sortable-helper {
            background: #2c3338;
            opacity: 0.8;
        }
    ');
}

// Ajouter le script JavaScript pour le drag and drop
add_action('wp_footer', 'add_adminbar_sort_script');
add_action('admin_footer', 'add_adminbar_sort_script');

function add_adminbar_sort_script() {
    if (!is_admin_bar_showing() || !current_user_can('manage_options')) return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        var $adminBar = $('#wpadminbar .quicklinks > ul');
        
        // Restore order from localStorage if available
        var savedOrder = localStorage.getItem('adminbar_items_order');
        if (savedOrder) {
            try {
                var orderArray = JSON.parse(savedOrder);
                orderArray.forEach(function(id) {
                    var $item = $('#' + id);
                    if ($item.length) {
                        $item.appendTo($adminBar);
                    }
                });
            } catch(e) {
                console.error('Error restoring admin bar order:', e);
            }
        }

        $adminBar.sortable({
            items: '> li:not(#wp-admin-bar-menu-toggle, #wp-admin-bar-wp-logo)',
            axis: 'x',
            placeholder: 'sortable-placeholder',
            containment: 'parent',
            update: function(event, ui) {
                var order = $(this).sortable('toArray', {attribute: 'id'});
                // Save to localStorage for immediate persistence
                localStorage.setItem('adminbar_items_order', JSON.stringify(order));
                // Save to WordPress database for long-term persistence
                $.post(ajaxurl, {
                    action: 'save_adminbar_order',
                    order: order,
                    nonce: '<?php echo wp_create_nonce("save-adminbar-order"); ?>'
                });
            }
        });
    });
    </script>
    <?php
}

// Sauvegarder l'ordre des éléments
add_action('wp_ajax_save_adminbar_order', function() {
    if (!current_user_can('manage_options')) {
        wp_die(-1);
    }

    check_ajax_referer('save-adminbar-order', 'nonce');

    if (isset($_POST['order'])) {
        $order = array_map('sanitize_text_field', $_POST['order']);
        update_option('adminbar_items_order', $order);
    }

    wp_die('1');
});

// Appliquer l'ordre personnalisé
add_action('wp_before_admin_bar_render', 'apply_custom_adminbar_order', 999);
add_action('admin_bar_menu', 'apply_custom_adminbar_order', 999);

function apply_custom_adminbar_order() {
    global $wp_admin_bar;
    
    if (!is_object($wp_admin_bar)) return;

    $saved_order = get_option('adminbar_items_order', array());
    if (empty($saved_order)) return;

    // Récupérer tous les noeuds actuels
    $nodes = $wp_admin_bar->get_nodes();
    if (!$nodes) return;

    // Stocker temporairement tous les noeuds
    $temp_nodes = array();
    foreach ($nodes as $node_id => $node) {
        if (!in_array($node_id, array('wp-admin-bar-menu-toggle', 'wp-admin-bar-wp-logo'))) {
            $temp_nodes[$node_id] = clone $node;
            $wp_admin_bar->remove_node($node_id);
        }
    }

    // Réorganiser les noeuds selon l'ordre sauvegardé
    $position = 1;
    foreach ($saved_order as $node_id) {
        $node_id = sanitize_text_field($node_id);
        if (isset($temp_nodes[$node_id])) {
            $node = $temp_nodes[$node_id];
            $node->priority = $position * 100;
            $wp_admin_bar->add_node($node);
            unset($temp_nodes[$node_id]);
            $position++;
        }
    }

    // Ajouter les noeuds restants à la fin
    foreach ($temp_nodes as $node_id => $node) {
        $node->priority = ($position++) * 100;
        $wp_admin_bar->add_node($node);
    }
}