<?php
/*
 * Display name: ADMIN ⚙️ MENUBAR - Search - v5
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Title: Recherche globale dans la barre d'administration (v5)
 * Description: v5 - champ de recherche visible dans la barre admin
 *              WordPress en front et dans wp-admin. La soumission ouvre
 *              la liste des articles avec la recherche WordPress native.
 *              Formulaire et CSS reconstruits sans le bloc PHP/CSS
 *              mal ferme present dans le v4 local/en ligne.
 * Hooks WP: admin_bar_menu, wp_head, admin_head
 * Fonctions clefs: clm_admin_search_bar_v5, clm_admin_search_styles_v5
 */

add_action('admin_bar_menu', 'clm_admin_search_bar_v5', 35);
function clm_admin_search_bar_v5($admin_bar) {
    if (!is_admin_bar_showing() || !current_user_can('edit_posts')) return;

    $form = '<form class="clm-admin-search-form" role="search" method="get" action="' . esc_url(admin_url('edit.php')) . '">'
        . '<label class="screen-reader-text" for="clm-admin-search-v5">Rechercher des articles</label>'
        . '<input type="search" id="clm-admin-search-v5" name="s" value="" placeholder="Recherche" autocomplete="off">'
        . '<input type="hidden" name="post_type" value="post">'
        . '<button type="submit" aria-label="Lancer la recherche">⌕</button>'
        . '</form>';

    $admin_bar->add_node(array(
        'id'    => 'clm-admin-search-v5',
        'title' => $form,
        'meta'  => array('title' => 'Recherche des articles'),
    ));
}

add_action('wp_head', 'clm_admin_search_styles_v5');
add_action('admin_head', 'clm_admin_search_styles_v5');
function clm_admin_search_styles_v5() {
    if (!is_admin_bar_showing() || !current_user_can('edit_posts')) return;
    ?>
<style>
#wpadminbar #wp-admin-bar-clm-admin-search-v5{padding:0;margin-left:8px}
#wpadminbar #wp-admin-bar-clm-admin-search-v5:hover{background:transparent}
#wpadminbar #wp-admin-bar-clm-admin-search-v5>.ab-item{height:auto;padding:0!important}
#wpadminbar .clm-admin-search-form{display:flex;align-items:center;gap:3px;height:32px;margin:0;padding:0 6px}
#wpadminbar .clm-admin-search-form input[type=search]{box-sizing:border-box;width:150px;height:25px;margin:0;padding:0 7px;border:1px solid #565b60;border-radius:3px;background:#2c3338;color:#fff;font-family:inherit;font-size:12px;line-height:23px}
#wpadminbar .clm-admin-search-form input[type=search]::placeholder{color:#c3c4c7;opacity:1}
#wpadminbar .clm-admin-search-form input[type=search]:focus{width:190px;border-color:#72aee6;outline:2px solid transparent;box-shadow:0 0 0 1px #72aee6}
#wpadminbar .clm-admin-search-form button{width:25px;height:25px;padding:0;border:0;border-radius:3px;background:#3c434a;color:#fff;font-size:19px;line-height:23px;cursor:pointer}
#wpadminbar .clm-admin-search-form button:hover,#wpadminbar .clm-admin-search-form button:focus{background:#50575e}
#wpadminbar .clm-admin-search-form .screen-reader-text{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
@media screen and (max-width:782px){#wpadminbar #wp-admin-bar-clm-admin-search-v5{display:none}}
</style>
    <?php
}
