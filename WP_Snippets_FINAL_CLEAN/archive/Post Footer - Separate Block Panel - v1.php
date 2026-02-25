
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_POST inspector/WP_POST separate-block-panel.php
 * Display name: WP_POST separate-block-panel
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST separate-block-panel (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_POST inspector/WP_POST separate-block-panel.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_enqueue_scripts, admin_footer
 * Fonctions clefs: CustomBlockPanel
 * Lignes / octets (brut): 297 / 12917
 * Hash code normalise (sha256): 06d5d8ac8a382aaec18edc26c8e64a1d599bf3f3500cf09799ab20761a3e8d94
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__front-end__wp-post-separate-block-panel__v1__src-a-trier.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__wp-post-separate-block-panel__v1__src-a-trier.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: post_footer_ui
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: low
 * Scores (top): post_footer_ui=5
 * Raisons principales: footer
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

ine_script, wp_add_inline_style
 * Signatures contenu: php-open-tag, inline-style, inline-script, html-markup
 * Lignes / octets: 319 / 13763
 * Empreinte code (sha256): 77d1ceb64a9f0f6d64bf27ee3ceaa338a17f5f1df2c51ead90efc6468ed7a437
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_POST inspector/WP_POST separate-block-panel.php
 * Display name: WP_POST separate-block-panel
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST separate-block-panel (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_POST inspector/WP_POST separate-block-panel.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_enqueue_scripts, admin_footer
 * Fonctions clefs: CustomBlockPanel
 * Lignes / octets (brut): 297 / 12917
 * Hash code normalise (sha256): 06d5d8ac8a382aaec18edc26c8e64a1d599bf3f3500cf09799ab20761a3e8d94
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/*
Plugin Name: Separate Block Panel
Description: Sépare le panneau des blocs dans l'éditeur WordPress avec sa propre icône
Version: 1.0
Author: Your Name
*/

// Enregistrer et charger notre script JavaScript
add_action('admin_enqueue_scripts', function() {
    if (!function_exists('get_current_screen') || !get_current_screen()->is_block_editor) {
        return;
    }

    wp_add_inline_script('wp-blocks', '
        wp.domReady(function() {
            const { select, dispatch } = wp.data;
            const { registerPlugin } = wp.plugins;
            const { PluginSidebarMoreMenuItem, PluginSidebar } = wp.editPost;
            const { createElement } = wp.element;
            const { BlockList } = wp.blockEditor;

            // Déplacer le panneau des blocs
            function CustomBlockPanel() {
                return createElement(
                    "div",
                    { className: "custom-block-panel" },
                    [
                        createElement(
                            PluginSidebarMoreMenuItem,
                            {
                                target: "custom-block-panel",
                                icon: "plus-alt2"
                            },
                            "Blocs"
                        ),
                        createElement(
                            PluginSidebar,
                            {
                                name: "custom-block-panel",
                                title: "Blocs",
                                icon: "plus-alt2"
                            },
                            createElement(BlockList)
                        )
                    ]
                );
            }

            // Enregistrer notre panneau personnalisé
            registerPlugin("custom-block-panel", {
                render: CustomBlockPanel,
                icon: "plus-alt2"
            });

            // Masquer le panneau original des blocs
            const style = document.createElement("style");
            style.innerHTML = `
                .interface-interface-skeleton__left-sidebar {
                    display: none !important;
                }
            `;
            document.head.appendChild(style);
        });
    ');

    // Styles pour le nouveau panneau
    wp_add_inline_style('wp-edit-blocks', '
        .custom-block-panel .block-editor-block-list {
            padding: 16px;
        }
    ');
});

add_action('admin_footer', function() {
    if (!function_exists('get_current_screen') || !get_current_screen()->is_block_editor) {
        return;
    }
    ?>
    <script>
        wp.domReady(function() {
            const waitForEditor = setInterval(function() {
                const toolbar = document.querySelector('.edit-post-header__toolbar');
                const blockInspector = document.querySelector('.block-editor-block-inspector');
                
                if (!toolbar || !blockInspector) return;
                
                clearInterval(waitForEditor);
                
                // Créer le nouveau bouton pour les options de bloc
                const blockOptionsButton = document.createElement('button');
                blockOptionsButton.className = 'components-button has-icon';
                blockOptionsButton.setAttribute('aria-label', 'Options du bloc');
                blockOptionsButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M10.2 3.28c-3.53 0-6.43 2.61-6.92 6h2.14c.46-2.28 2.48-4 4.78-4 1.35 0 2.57.58 3.44 1.5l-2.38 2.38 6 1.56-1.56-6-2.11 2.11c-1.17-1.17-2.7-1.77-4.27-1.77zm2.5 13.41-2.11 2.11c-.87.92-2.09 1.5-3.44 1.5-2.3 0-4.32-1.72-4.78-4H0c.49 3.39 3.39 6 6.92 6 1.57 0 3.1-.6 4.27-1.77l2.38 2.38 1.56-6-6 1.56 2.57 2.22z"/>
                    </svg>
                `;
                
                // Ajouter des styles
                const style = document.createElement('style');
                style.textContent = `
                    .block-options-button {
                        display: flex;
                        align-items: center;
                        padding: 8px;
                        margin-left: 8px;
                        border: none;
                        background: none;
                        cursor: pointer;
                    }
                    .block-options-button.is-active {
                        color: #007cba;
                    }
                    .block-options-button svg {
                        width: 20px;
                        height: 20px;
                    }
                    .block-editor-block-inspector {
                        position: fixed;
                        right: 0;
                        top: 56px;
                        bottom: 0;
                        width: 280px;
                        background: white;
                        border-left: 1px solid #e0e0e0;
                        overflow-y: auto;
                        z-index: 1000;
                        padding: 16px;
                        transform: translateX(100%);
                        transition: transform 0.3s ease;
                    }
                    .block-editor-block-inspector.is-visible {
                        transform: translateX(0);
                    }
                    /* Ajuster l'espace pour le contenu principal */
                    .interface-interface-skeleton__content {
                        margin-right: 0;
                        transition: margin-right 0.3s ease;
                    }
                    .interface-interface-skeleton__content.with-inspector {
                        margin-right: 280px;
                    }
                `;
                document.head.appendChild(style);
                
                // Ajouter la classe pour le style
                blockOptionsButton.classList.add('block-options-button');
                
                // Gérer l'affichage/masquage du panneau
                let isInspectorVisible = false;
                blockOptionsButton.addEventListener('click', function() {
                    isInspectorVisible = !isInspectorVisible;
                    blockInspector.classList.toggle('is-visible', isInspectorVisible);
                    document.querySelector('.interface-interface-skeleton__content')
                        .classList.toggle('with-inspector', isInspectorVisible);
                    blockOptionsButton.classList.toggle('is-active', isInspectorVisible);
                });
                
                // Ajouter le bouton à la barre d'outils
                toolbar.appendChild(blockOptionsButton);
                
                // Déplacer l'inspecteur de bloc
                document.body.appendChild(blockInspector);
            }, 100);
        });
    </script>
    <script>
        wp.domReady(function() {
            const waitForEditor = setInterval(function() {
                const toolbar = document.querySelector('.edit-post-header__toolbar');
                const postSettings = document.querySelectorAll('.components-panel__body');
                
                if (!toolbar || !postSettings.length) return;
                
                clearInterval(waitForEditor);
                
                // Créer le panneau des options d'article
                const articlePanel = document.createElement('div');
                articlePanel.className = 'article-settings-panel';
                
                // Déplacer les paramètres de l'article dans le nouveau panneau
                postSettings.forEach(panel => {
                    const title = panel.querySelector('.components-panel__body-title');
                    if (!title) return;
                    
                    const titleText = title.textContent.toLowerCase();
                    if (
                        titleText.includes('statut') ||
                        titleText.includes('visibilité') ||
                        titleText.includes('extrait') ||
                        titleText.includes('catégories') ||
                        titleText.includes('étiquettes') ||
                        titleText.includes('discussion') ||
                        titleText.includes('planification')
                    ) {
                        articlePanel.appendChild(panel.cloneNode(true));
                        panel.style.display = 'none';
                    }
                });
                
                // Créer le bouton pour le panneau d'article
                const articleButton = document.createElement('button');
                articleButton.className = 'components-button has-icon';
                articleButton.setAttribute('aria-label', 'Options de l\'article');
                articleButton.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M20.1 5.1L16.9 2 6.2 12.7l-1.3 4.4 4.5-1.3L20.1 5.1zM4 20.8h8v-1.5H4v1.5z"/>
                    </svg>
                `;
                
                // Ajouter les styles
                const style = document.createElement('style');
                style.textContent = `
                    .article-settings-panel {
                        position: fixed;
                        right: 0;
                        top: 56px;
                        bottom: 0;
                        width: 280px;
                        background: white;
                        border-left: 1px solid #e0e0e0;
                        overflow-y: auto;
                        z-index: 1000;
                        padding: 16px;
                        transform: translateX(100%);
                        transition: transform 0.3s ease;
                        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
                    }
                    .article-settings-panel.is-visible {
                        transform: translateX(0);
                    }
                    .article-button {
                        display: flex;
                        align-items: center;
                        padding: 8px;
                        margin-left: 8px;
                        border: none;
                        background: none;
                        cursor: pointer;
                    }
                    .article-button.is-active {
                        color: #007cba;
                    }
                    .article-button svg {
                        width: 20px;
                        height: 20px;
                    }
                    /* Ajuster l'espace pour le contenu principal */
                    .interface-interface-skeleton__content {
                        margin-right: 0;
                        transition: margin-right 0.3s ease;
                    }
                    .interface-interface-skeleton__content.with-article-panel {
                        margin-right: 280px;
                    }
                `;
                document.head.appendChild(style);
                
                // Ajouter la classe pour le style
                articleButton.classList.add('article-button');
                
                // Gérer l'affichage/masquage du panneau
                let isPanelVisible = false;
                articleButton.addEventListener('click', function() {
                    isPanelVisible = !isPanelVisible;
                    articlePanel.classList.toggle('is-visible', isPanelVisible);
                    document.querySelector('.interface-interface-skeleton__content')
                        .classList.toggle('with-article-panel', isPanelVisible);
                    articleButton.classList.toggle('is-active', isPanelVisible);
                });
                
                // Ajouter le bouton à la barre d'outils
                toolbar.appendChild(articleButton);
                
                // Ajouter le panneau au document
                document.body.appendChild(articlePanel);
                
                // Réinitialiser les événements sur les panneaux clonés
                const initPanelEvents = () => {
                    const panels = articlePanel.querySelectorAll('.components-panel__body');
                    panels.forEach(panel => {
                        const toggle = panel.querySelector('.components-panel__body-toggle');
                        if (toggle) {
                            toggle.addEventListener('click', () => {
                                panel.classList.toggle('is-opened');
                            });
                        }
                    });
                };
                
                initPanelEvents();
            }, 100);
        });
    </script>
    <?php
});
