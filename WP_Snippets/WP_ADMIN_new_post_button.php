<?php
// Ajoute un bouton "Nouvel article" dans l'éditeur Gutenberg
add_action('admin_footer', 'wp_admin_ajouter_bouton_nouvel_article_gutenberg');

function wp_admin_ajouter_bouton_nouvel_article_gutenberg() {
    $screen = get_current_screen();
    // Affiche le bouton uniquement sur les écrans d'édition ou de création d'article
    if ($screen && $screen->post_type === 'post' && in_array($screen->base, ['post', 'post-new'])) {
        ?>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var bouton = document.createElement('a');
            bouton.href = '<?php echo admin_url('post-new.php'); ?>';
            bouton.className = 'components-button is-primary';
            bouton.style.marginLeft = '10px';
            bouton.innerText = 'Nouvel article';
            var barre = document.querySelector('.edit-post-header-toolbar');
            if (barre) {
                barre.appendChild(bouton);
            }
        });
        </script>
        <?php
    }
}