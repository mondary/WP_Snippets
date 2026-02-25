/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/050__id-108__post-new-post-button.php
 * Display name: POST - New post button
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 108
 * Online modified: 2025-05-07 13:21:14
 * Online revision: 5
 * Exact duplicate group: oui (d32a28bf31ce…, 2 membres)
 * Canonical exact group ID: 138
 * Version family: DUP POST - New post button (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/050__id-108__post-new-post-button.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_footer
 * Fonctions clefs: wp_admin_ajouter_bouton_nouvel_article_gutenberg
 * Lignes / octets (brut): 37 / 1437
 * Hash code normalise (sha256): d32a28bf31ce4d8725f4087fd48d6a958a9de869e17578892bff24482f3b996a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__post-new-post-button__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-new-post-button__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: integration Gutenberg, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: gutenberg, css-ui, footer-head-injection
 * Dependances probables: Gutenberg JS
 * Hooks WP: admin_footer
 * Fonctions clefs: wp_admin_ajouter_bouton_nouvel_article_gutenberg
 * Selecteurs / IDs: .edit-post-header-toolbar
 * APIs WP detectees: add_action, wp_admin_ajouter_bouton_nouvel_article_gutenberg, get_current_screen, admin_url
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 51 / 2121
 * Empreinte code (sha256): 92345b184084f2ea1f36f3ce9063fc6dde03ca645399a97545ab39de6f93d276
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__post-new-post-button__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-new-post-button__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: gutenberg_editor
 * Clusters secondaires: post_footer_ui, frontend_ui_widget
 * Domaine: post-front
 * Confiance: medium
 * Scores (top): gutenberg_editor=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: gutenberg
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

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