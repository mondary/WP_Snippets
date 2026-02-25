/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/074__id-134__admin-hide-menu-left-opencode.php
 * Display name: ADMIN - Hide menu left (opencode)
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 134
 * Online modified: 2025-09-12 13:53:56
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: ADMIN - Hide menu left (opencode) (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/074__id-134__admin-hide-menu-left-opencode.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: enqueue_block_editor_assets
 * Fonctions clefs: collapse_gutenberg_left_sidebar
 * Lignes / octets (brut): 47 / 1787
 * Hash code normalise (sha256): 6a1f53853377c0d3feaca2ea04dfd930da5b5cb8f4012f5f1006ae8e0804b206
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-hide-menu-left-opencode__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-menu-left-opencode__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, integration Gutenberg, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: gutenberg, admin-ui, css-ui
 * Dependances probables: Gutenberg JS
 * Hooks WP: enqueue_block_editor_assets
 * Fonctions clefs: collapse_gutenberg_left_sidebar
 * APIs WP detectees: add_action, wp_add_inline_script
 * Signatures contenu: inline-script
 * Lignes / octets: 59 / 2316
 * Empreinte code (sha256): 4627936ce877e1ed39f79be858dfb159b5ceca6de1c8ac1de3e497ac49152d00
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-hide-menu-left-opencode__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-hide-menu-left-opencode__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: gutenberg_editor
 * Clusters secondaires: aucun
 * Domaine: global
 * Confiance: high
 * Scores (top): gutenberg_editor=12, admin_ui_settings=4, frontend_ui_widget=2
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
    // Ajouter le script en ligne (pas de dépendances externes nécessaires)
    wp_add_inline_script('wp-edit-post', '
        wp.domReady(function() {
            setTimeout(function() {
                // Trouver le bouton de toggle de la sidebar document
                const toggleButton = document.querySelector(\'button[aria-label*="Settings" i], button[aria-label*="Réglages" i], button[aria-label*="Document" i]\');

                // Vérifier si la sidebar est ouverte
                const sidebar = document.querySelector(\'.interface-interface-skeleton__sidebar\');

                // Si le bouton existe et la sidebar est ouverte, cliquer pour la fermer
                if (toggleButton && sidebar && sidebar.offsetParent !== null) {
                    toggleButton.click();
                }
            }, 1500);
        });
    ');
}