/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/073__id-133__admin-hide-menu-left-gemini.php
 * Display name: ADMIN - hide menu left (gemini)
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 133
 * Online modified: 2025-09-01 12:50:58
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: ADMIN - hide menu left (gemini) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/073__id-133__admin-hide-menu-left-gemini.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_footer
 * Fonctions clefs: gemini_force_gutenberg_sidebar_state
 * Lignes / octets (brut): 63 / 2288
 * Hash code normalise (sha256): c33852e5718432d5ab4ac334c2c5e1f94a86a15d043012249e3d599f33dc8def
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-hide-menu-left-gemini__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-menu-left-gemini__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: integration Gutenberg, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: gutenberg, css-ui, footer-head-injection
 * Dependances probables: Gutenberg JS
 * Hooks WP: admin_footer
 * Fonctions clefs: gemini_force_gutenberg_sidebar_state
 * APIs WP detectees: get_current_screen, is_block_editor, add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 76 / 2907
 * Empreinte code (sha256): 26a049e657fa44cb993e2ef0cc109009417be583c57dc4dad5cd9cbcbd2fe104
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-hide-menu-left-gemini__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-menu-left-gemini__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: gutenberg_editor
 * Clusters secondaires: post_footer_ui, frontend_ui_widget
 * Domaine: global
 * Confiance: medium
 * Scores (top): gutenberg_editor=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: gutenberg
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Troisième tentative, plus directe, pour contrôler les panneaux de Gutenberg.
 * Ce script est injecté dans le pied de page de l'admin pour s'assurer 
 * qu'il s'exécute après tous les autres scripts de la page.
 */
function gemini_force_gutenberg_sidebar_state() {
    
    // On s'assure qu'on est bien sur une page utilisant l'éditeur de blocs
    $screen = get_current_screen();
    if ( ! $screen || ! $screen->is_block_editor() ) {
        return;
    }
    ?>
    <script id="gemini-force-sidebar-script">
        window.addEventListener('load', function() {
            const forceSidebarState = () => {
                // On attend que wp.data soit disponible
                if (typeof wp === 'undefined' || typeof wp.data === 'undefined') {
                    setTimeout(forceSidebarState, 100);
                    return;
                }

                const { select, dispatch } = wp.data;

                // On attend que l'éditeur soit prêt
                const editorReady = select('core/editor') && select('core/editor').isEditorReady();
                if (!editorReady) {
                    setTimeout(forceSidebarState, 100);
                    return;
                }

                // Actions finales
                // 1. Ouvre le panneau de droite
                dispatch('core/edit-post').openGeneralSidebar('edit-post/document');
                
                // 2. Ferme le panneau de gauche
                if (select('core/edit-post').isInserterOpened()) {
                    dispatch('core/edit-post').toggleInserterOpened();
                }
            };

            // On lance notre fonction
            forceSidebarState();
        });
    </script>
    <?php
}

// On accroche notre fonction au hook 'admin_footer', qui se charge très tard.
add_action( 'admin_footer', 'gemini_force_gutenberg_sidebar_state' );
