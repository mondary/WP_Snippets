/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/019__id-33__post-external-links-icon-php.php
 * Display name: POST - External Links Icon PHP
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 33
 * Online modified: 2025-01-15 14:47:02
 * Online revision: 5
 * Exact duplicate group: non
 * Version family: POST - External Links Icon PHP (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/019__id-33__post-external-links-icon-php.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: ajouter_script_liens_externes
 * Lignes / octets (brut): 77 / 3609
 * Hash code normalise (sha256): 2d98c491bc959af3167323d84433bea08226e35c4fa48d2fc2bec62960c595ca
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__post-external-links-icon-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-external-links-icon-php__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection, svg-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_footer
 * Fonctions clefs: ajouter_script_liens_externes
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 90 / 4217
 * Empreinte code (sha256): a8e145bfff40582476bb9761267face41a7cce181b69923302858eaf026db42c
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__post-external-links-icon-php__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-external-links-icon-php__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: frontend_ui_widget
 * Clusters secondaires: links_external, post_footer_ui
 * Domaine: post-front
 * Confiance: medium
 * Scores (top): frontend_ui_widget=6, links_external=5, post_footer_ui=5
 * Raisons principales: css-ui, svg-ui, footer-head-injection
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function ajouter_script_liens_externes() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var links = document.querySelectorAll('a'); // Sélectionne tous les liens sur la page

            links.forEach(function (link) {
                var href = link.getAttribute('href');
                if (href && href.startsWith('http') && !href.includes(window.location.hostname)) {
                    // Ajoute la classe "external-link" pour les liens externes
                    var className = link.getAttribute('class');
                    link.setAttribute('class', className ? className + ' external-link' : 'external-link');
                }
            });
        });

        var style = document.createElement('style');
        style.textContent = `
            .external-link {
                position: relative;
                color: #0000FF;
                text-decoration: none;
            }

            .external-link::after {
                content: "";
                display: inline-block;
                width: 16px;
                height: 16px;
                margin-left: 5px;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231168C2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'%3E%3C/path%3E%3Cpolyline points='15 3 21 3 21 9'%3E%3C/polyline%3E%3Cline x1='10' y1='14' x2='21' y2='3'%3E%3C/line%3E%3C/svg%3E");
                background-size: contain;
                background-repeat: no-repeat;
                vertical-align: middle;
            }

            .external-link:hover::after {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23FFFFFF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'%3E%3C/path%3E%3Cpolyline points='15 3 21 3 21 9'%3E%3C/polyline%3E%3Cline x1='10' y1='14' x2='21' y2='3'%3E%3C/line%3E%3C/svg%3E");
            }
        `;
        document.head.appendChild(style);

        var observer = new MutationObserver(function (mutationsList) {
            mutationsList.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.tagName === 'A') {
                            var href = node.getAttribute('href');
                            if (href && href.startsWith('http') && !href.includes(window.location.hostname)) {
                                var className = node.getAttribute('class');
                                node.setAttribute('class', className ? className + ' external-link' : 'external-link');
                            }
                        }
                    });
                }
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    </script>
    <?php
}

add_action('wp_footer', 'ajouter_script_liens_externes');
