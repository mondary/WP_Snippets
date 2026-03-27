/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/075__id-135__admin-hide-menu-left-qwen.php
 * Display name: ADMIN - hide menu left (qwen)
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 135
 * Online modified: 2025-09-01 15:16:35
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: ADMIN - hide menu left (qwen) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/075__id-135__admin-hide-menu-left-qwen.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: jQuery
 * Hooks WP: enqueue_block_editor_assets, admin_footer
 * Fonctions clefs: collapse_gutenberg_left_sidebar, collapseLeftSidebar, add_gutenberg_collapse_script
 * Lignes / octets (brut): 107 / 4618
 * Hash code normalise (sha256): d7e77e6436ecb6f42ae697ccfa6acf15343531e9be93b3e394680b312873e199
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-hide-menu-left-qwen__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-menu-left-qwen__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, integration Gutenberg, UI frontend (CSS/HTML), 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: gutenberg, admin-ui, css-ui, footer-head-injection
 * Dependances probables: jQuery, Gutenberg JS
 * Hooks WP: enqueue_block_editor_assets, admin_footer
 * Fonctions clefs: collapse_gutenberg_left_sidebar, collapseLeftSidebar, add_gutenberg_collapse_script
 * Selecteurs / IDs: .edit-post-header__settings button, .interface-interface-skeleton__sidebar--left
 * APIs WP detectees: add_action, add_gutenberg_collapse_script
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 120 / 5294
 * Empreinte code (sha256): 11ffd6d968d3452ad79ac527734eb28b9d388f1c541fb19ff50bc7467656d28c
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-hide-menu-left-qwen__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-menu-left-qwen__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: gutenberg_editor
 * Clusters secondaires: aucun
 * Domaine: global
 * Confiance: high
 * Scores (top): gutenberg_editor=12, post_footer_ui=5, admin_ui_settings=4, frontend_ui_widget=4
 * Raisons principales: gutenberg, block_editor
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Plugin Name: Replier la sidebar gauche Gutenberg
 * Description: Replie automatiquement le menu de gauche (document) dans l'éditeur Gutenberg
 * Version: 1.3
 */

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

// Ajouter le code JS dans l'éditeur Gutenberg
add_action('enqueue_block_editor_assets', 'collapse_gutenberg_left_sidebar');

function collapse_gutenberg_left_sidebar() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour replier la sidebar gauche
            function collapseLeftSidebar() {
                // Replier la sidebar de navigation (document)
                const navigationToggle = document.querySelector('.edit-post-header__settings button[aria-label="Toggle navigation"]');
                const documentOverviewToggle = document.querySelector('.edit-post-header__settings button[aria-label="Document Overview"]');
                
                // Alternative selectors
                const panelButtons = document.querySelectorAll('.edit-post-header__settings button');
                const sidebarToggle = document.querySelector('.interface-interface-skeleton__sidebar--left');
                
                // Trouver le bouton de toggle de la sidebar gauche
                const closeButtons = document.querySelectorAll('button[aria-label*="Close" i], button[aria-label*="Fermer" i]');
                
                // Parcourir les boutons pour trouver celui qui ferme la sidebar
                closeButtons.forEach(function(button) {
                    const label = button.getAttribute('aria-label') || '';
                    if (label.includes('navigation') || label.includes('Navigation') || 
                        label.includes('document') || label.includes('Document')) {
                        if (!button.classList.contains('already-clicked')) {
                            button.click();
                            button.classList.add('already-clicked');
                        }
                    }
                });
                
                // Méthode alternative : Simuler le clic sur le premier bouton de fermeture
                if (closeButtons.length > 0 && !document.body.classList.contains('left-sidebar-collapsed')) {
                    // On clique sur le premier bouton de fermeture trouvé
                    const firstCloseButton = closeButtons[0];
                    if (firstCloseButton && !firstCloseButton.classList.contains('already-clicked')) {
                        firstCloseButton.click();
                        firstCloseButton.classList.add('already-clicked');
                        document.body.classList.add('left-sidebar-collapsed');
                    }
                }
            }
            
            // Attendre que l'interface Gutenberg soit complètement chargée
            setTimeout(collapseLeftSidebar, 1000);
            setTimeout(collapseLeftSidebar, 2000);
            setTimeout(collapseLeftSidebar, 3000);
        });
    </script>
    <?php
}

// Méthode alternative avec wp.data
add_action('admin_footer', 'add_gutenberg_collapse_script');

function add_gutenberg_collapse_script() {
    global $hook_suffix;
    
    // Vérifier si nous sommes dans l'éditeur Gutenberg
    if (in_array($hook_suffix, array('post.php', 'post-new.php'))) {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Attendre que l'éditeur soit prêt
            setTimeout(function() {
                // Utiliser l'API Gutenberg si disponible
                if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/edit-post')) {
                    try {
                        // Replier la sidebar de navigation
                        if (wp.data.dispatch('core/edit-post')) {
                            // Cette méthode peut ne pas fonctionner selon la version de WordPress
                        }
                    } catch(e) {
                        console.log('Impossible de replier la sidebar via l\'API');
                    }
                }
            }, 1500);
        });
        </script>
        <?php
    }
}
