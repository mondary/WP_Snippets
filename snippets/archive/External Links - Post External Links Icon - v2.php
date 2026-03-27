/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_POST - External Links Icon.php
 * Display name: WP_POST - External Links Icon
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST - External Links Icon (1 variantes)
 * Version: v2
 * Recommended latest in family: archives/WP_POST - External Links Icon.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 46 / 2904
 * Hash code normalise (sha256): 378c90a630f2aadbe57dfbb70d6219a2d6573d2a5e2f9681b25c8d93cfed9332
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: LOCAL__front-end__wp-post-external-links-icon__v1__src-archives.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__wp-post-external-links-icon__v1__src-archives.php
 * Resume fonctionnalites: UI frontend (CSS/HTML)
 * Features detectees: css-ui, svg-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 68 / 3673
 * Empreinte code (sha256): 87fbfab41d0a6afae557f445bc668e1fca38fd090faea6b1369b58d648923390
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__front-end__wp-post-external-links-icon__v1__src-archives.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__wp-post-external-links-icon__v1__src-archives.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: links_external
 * Clusters secondaires: frontend_ui_widget
 * Domaine: post-front
 * Confiance: low
 * Scores (top): links_external=5, frontend_ui_widget=4
 * Raisons principales: external links
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

<script>
    // Ajoute la classe CSS "external-link" aux liens externes
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

    // Injecte le CSS pour les liens externes dans la page
    var style = document.createElement('style');
    style.textContent = `
        /* Style pour les liens externes avec icône visible */
        .external-link {
            position: relative; /* Nécessaire pour positionner l'icône après le lien */
            color: #0000FF; /* Couleur du lien en bleu */
            text-decoration: none; /* Supprime la décoration de soulignement */
        }

        /* Ajoute une icône SVG de lien externe après le texte du lien */
        .external-link::after {
            content: ""; /* Contenu vide pour l'icône */
            display: inline-block; /* Affiche en ligne */
            width: 16px; /* Largeur de l'icône */
            height: 16px; /* Hauteur de l'icône */
            margin-left: 5px; /* Marge à gauche de l'icône */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%231168C2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'%3E%3C/path%3E%3Cpolyline points='15 3 21 3 21 9'%3E%3C/polyline%3E%3Cline x1='10' y1='14' x2='21' y2='3'%3E%3C/line%3E%3C/svg%3E"); /* Icône SVG encodée */
            background-size: contain; /* Ajuste la taille de l'image */
            background-repeat: no-repeat; /* Ne répète pas l'image */
            vertical-align: middle; /* Aligne verticalement au milieu */
            transition: background-color 0.3s ease; /* Transition douce pour l'icône */
        }

        /* Au hover, l'icône devient blanche */
        .external-link:hover::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23FFFFFF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'%3E%3C/path%3E%3Cpolyline points='15 3 21 3 21 9'%3E%3C/polyline%3E%3Cline x1='10' y1='14' x2='21' y2='3'%3E%3C/line%3E%3C/svg%3E"); /* Icône SVG blanche au hover */
        }
    `;
    document.head.appendChild(style);
</script>
