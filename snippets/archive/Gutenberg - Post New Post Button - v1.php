
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN_new_post_button.php
 * Display name: WP_ADMIN_new_post_button
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (d32a28bf31ce…, 2 membres)
 * Canonical exact group ID: 138
 * Version family: DUP POST - New post button (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN_new_post_button.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_footer
 * Fonctions clefs: wp_admin_ajouter_bouton_nouvel_article_gutenberg
 * Lignes / octets (brut): 25 / 1048
 * Hash code normalise (sha256): d32a28bf31ce4d8725f4087fd48d6a958a9de869e17578892bff24482f3b996a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-new-post-button__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-new-post-button__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: gutenberg_editor
 * Clusters secondaires: post_footer_ui
 * Domaine: post-front
 * Confiance: medium
 * Scores (top): gutenberg_editor=6, post_footer_ui=5
 * Raisons principales: gutenberg
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

FEATURES-DESCRIPTION:END */

/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN_new_post_button.php
 * Display name: WP_ADMIN_new_post_button
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (d32a28bf31ce…, 2 membres)
 * Canonical exact group ID: 138
 * Version family: DUP POST - New post button (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN_new_post_button.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_footer
 * Fonctions clefs: wp_admin_ajouter_bouton_nouvel_article_gutenberg
 * Lignes / octets (brut): 25 / 1048
 * Hash code normalise (sha256): d32a28bf31ce4d8725f4087fd48d6a958a9de869e17578892bff24482f3b996a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

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